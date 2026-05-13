"""End-to-end meal-plan generator.

Pipeline:
  1. Build a free-text retrieval query from the user's request.
  2. Vector-search the `recipes` ChromaDB collection for top-K candidates.
  3. Filter by diet / allergies on metadata where possible.
  4. Pick one recipe per meal (breakfast/lunch/dinner/snack) avoiding repeats.
  5. For each picked recipe, ask the LLM to shape it into the rich Meal JSON
     the frontend renders. The LLM never invents recipe data — it only
     formats and adds short descriptions.
"""
from __future__ import annotations

import random
import re
from typing import Optional

from recipe_generator.chroma_store import RecipeMatch, fetch_curated, recipe_count, search
from recipe_generator.llm import LLMUnavailableError, generate_json
from recipe_generator.models import (
    DayPlan,
    Meal,
    MealIngredient,
    MealPlanRequest,
    MealPlanResponse,
    MealTag,
)


# Map a `days` count to either weekday names or numbered day labels.
_WEEKDAYS = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]


def _retrieval_query(req: MealPlanRequest) -> str:
    """Build a positive-only retrieval query. We intentionally OMIT
    `req.allergies` here: vector search can't negate, so phrasing like
    "without fish" pulls fish-heavy results back in. Exclusions are
    enforced strictly in `_filter_candidates` instead."""
    parts: list[str] = []
    if req.query:
        parts.append(req.query.strip())
    if req.diet and req.diet != "none":
        parts.append(req.diet.replace("_", " "))
    if req.cuisine_preferences:
        parts.append(", ".join(req.cuisine_preferences))
    if req.target_calories:
        parts.append(f"around {req.target_calories} kcal balanced day")
    if req.notes:
        parts.append(req.notes.strip())
    return " ; ".join(parts) or "balanced healthy meals"


def _filter_candidates(matches: list[RecipeMatch], req: MealPlanRequest) -> list[RecipeMatch]:
    """Drop candidates that violate the user's constraints. Allergy
    exclusions are HARD — when the user said "no fish" we never serve
    fish, even at the cost of an empty pool. The empty-pool case is
    handled upstream by widening the retrieval k (see build_meal_plan)."""
    allergies_low = [a.lower() for a in req.allergies]

    def ok(r: RecipeMatch) -> bool:
        meta = r.metadata
        if req.diet == "vegetarian" and meta.get("contains_meat") is True:
            return False
        if req.diet == "vegan" and (
            meta.get("contains_meat") is True or meta.get("contains_dairy") is True
        ):
            return False
        if allergies_low:
            haystack = " ".join([
                str(meta.get("name", "")),
                str(meta.get("ingredients", "")),
                r.document or "",
            ]).lower()
            for a in allergies_low:
                if a and a in haystack:
                    return False
        return True

    filtered = [m for m in matches if ok(m)]
    # Only fall back to the unfiltered list when the user has no allergies
    # at all — otherwise we'd silently serve them food they said they can't eat.
    if not filtered and not allergies_low:
        return matches
    return filtered


# Keywords that disqualify a recipe from a specific meal slot. Kept short
# and deliberate — overzealous filtering empties the candidate pool.
_DISALLOW_KEYWORDS: dict[str, tuple[str, ...]] = {
    "breakfast": (
        "cake", "brownie", "frosting", "icing", "cocktail",
        "wine", "beer", "liqueur", "mojito", "margarita",
        "fried chicken", "lasagna", "curry", "biryani",
    ),
    "snack": (
        "lasagna", "curry", "biryani", "roast beef", "pot roast",
        "wine", "beer", "liqueur", "cocktail",
    ),
}

# Per-meal calorie caps so we don't slot a 2900-kcal "date bar" as breakfast.
_MEAL_CAL_CAPS: dict[str, int] = {
    "breakfast": 700,
    "lunch": 900,
    "dinner": 1100,
    "snack": 400,
}


def _name_for(recipe: RecipeMatch) -> str:
    return str(recipe.metadata.get("name") or "").lower()


def _calories_of(recipe: RecipeMatch) -> Optional[float]:
    meta = recipe.metadata
    raw = meta.get("calories_per_serving") or meta.get("calories")
    if raw is None or raw == "":
        return None
    try:
        return float(raw)
    except (TypeError, ValueError):
        return None


def _slot_ok(recipe: RecipeMatch, meal_type: str) -> bool:
    name = _name_for(recipe)
    for kw in _DISALLOW_KEYWORDS.get(meal_type, ()):
        if kw in name:
            return False
    cap = _MEAL_CAL_CAPS.get(meal_type)
    cals = _calories_of(recipe)
    if cap and cals is not None and cals > cap:
        return False
    return True


def _is_curated(recipe: RecipeMatch) -> bool:
    meta = recipe.metadata or {}
    v = meta.get("curated")
    return v in (True, "true", "True", 1, "1")


def _slot_matches_metadata(recipe: RecipeMatch, meal_type: str) -> bool:
    meta = recipe.metadata or {}
    return str(meta.get("meal_type", "")).lower() == meal_type.lower()


def _pick(candidates: list[RecipeMatch], used: set[str], meal_type: Optional[str] = None) -> RecipeMatch:
    """Random pick avoiding repeats and (optionally) respecting meal-slot
    constraints. Tiered preference order so good recipes win when present:
       1. curated + matching meal_type metadata
       2. curated (any)
       3. metadata meal_type match
       4. passes _slot_ok keyword/calorie heuristics
       5. anything available
    Falls back through tiers when one is empty (or only contains repeats)."""
    if not candidates:
        raise ValueError("empty candidate pool")

    tier1 = []  # curated + matching meal_type
    tier2 = []  # curated, any meal_type
    tier3 = []  # metadata meal_type match
    tier4 = []  # passes heuristics
    for c in candidates:
        curated = _is_curated(c)
        meta_ok = bool(meal_type) and _slot_matches_metadata(c, meal_type)
        slot_ok = (not meal_type) or _slot_ok(c, meal_type)
        if curated and meta_ok:
            tier1.append(c)
        if curated:
            tier2.append(c)
        if meta_ok:
            tier3.append(c)
        if slot_ok:
            tier4.append(c)

    for pool in (tier1, tier2, tier3, tier4, candidates):
        if not pool:
            continue
        fresh = [c for c in pool if c.metadata.get("name", "") not in used]
        if fresh:
            return random.choice(fresh)
    # Every candidate already used — just pick something.
    return random.choice(candidates)


def _build_meal_prompt(recipe: RecipeMatch, meal_type: str) -> str:
    meta = recipe.metadata
    return f"""\
You are a nutrition-aware home cook AI.

Convert this recipe into structured JSON for a {meal_type} meal.

RECIPE NAME: {meta.get('name', 'Unknown')}
CUISINE: {meta.get('cuisine', '')}
CALORIES PER SERVING (if known): {meta.get('calories_per_serving', meta.get('calories', ''))}
PROTEIN g (if known): {meta.get('protein_per_serving', meta.get('protein', ''))}
CARBS g (if known): {meta.get('carbs_per_serving', meta.get('carbs', ''))}

RECIPE FULL TEXT:
\"\"\"
{recipe.document}
\"\"\"

OUTPUT — return ONLY this JSON object, no markdown, no prose:
{{
  "title": "<recipe name>",
  "subtitle": "{meal_type.title()}",
  "badge": "Balanced Meal",
  "calories": "<number as string, take from recipe metadata or estimate>",
  "protein": "<number as string>",
  "carbs": "<number as string>",
  "description": "<one short sentence describing the meal>",
  "benefits": ["<up to 3 short bullets, e.g. High-protein, Low-sodium>"],
  "tags": [{{"text": "<benefit>", "color": "green"}}],
  "ingredients": [{{"name": "<ingredient>", "quantity": "<qty as string, e.g. '1 cup'>"}}],
  "instructions": ["<step 1>", "<step 2>", ...],
  "tips": "<one short cooking or prep tip>"
}}

HARD RULES:
- Use REAL ingredients and steps from the recipe above.
- No placeholders, no Lorem ipsum.
- Never invent ingredients that aren't suggested by the recipe text.
- Keep `instructions` as an array of short imperative strings.
"""


def _meal_from_recipe(recipe: RecipeMatch, meal_type: str) -> Meal:
    """Ask the LLM to shape `recipe` into a Meal. On any failure, fall back
    to a minimal Meal built directly from the recipe metadata so the
    frontend always renders something."""
    prompt = _build_meal_prompt(recipe, meal_type)
    try:
        data = generate_json(prompt)
        # Validate against our schema; coerce ingredient strings if the
        # model returned them as plain strings instead of objects.
        ingredients = data.get("ingredients") or []
        if ingredients and isinstance(ingredients[0], str):
            ingredients = [{"name": s, "quantity": ""} for s in ingredients]
        tags = data.get("tags") or []
        if tags and isinstance(tags[0], str):
            tags = [{"text": s, "color": "green"} for s in tags]
        return Meal(
            title=str(data.get("title") or recipe.metadata.get("name") or meal_type.title()),
            subtitle=str(data.get("subtitle") or meal_type.title()),
            badge=str(data.get("badge") or "Balanced Meal"),
            calories=str(data.get("calories") or recipe.metadata.get("calories_per_serving") or "0"),
            protein=str(data.get("protein") or recipe.metadata.get("protein_per_serving") or "0"),
            carbs=str(data.get("carbs") or recipe.metadata.get("carbs_per_serving") or "0"),
            description=str(data.get("description") or ""),
            benefits=[str(b) for b in (data.get("benefits") or [])][:6],
            tags=[MealTag(**t) if isinstance(t, dict) else MealTag(text=str(t)) for t in tags][:6],
            ingredients=[MealIngredient(**i) for i in ingredients[:30]],
            instructions=[str(s) for s in (data.get("instructions") or [])][:20],
            tips=str(data.get("tips") or ""),
        )
    except (LLMUnavailableError, ValueError, KeyError, TypeError):
        # Fallback: minimal Meal from the recipe metadata, so the user still
        # sees the recipe even if the LLM hiccups.
        return _meal_fallback(recipe, meal_type)


def _meal_fallback(recipe: RecipeMatch, meal_type: str) -> Meal:
    meta = recipe.metadata
    raw_ingredients = meta.get("ingredients") or recipe.document or ""
    if isinstance(raw_ingredients, list):
        ingredient_lines = raw_ingredients[:10]
    else:
        ingredient_lines = [
            line.strip("-• ").strip()
            for line in re.split(r"[\n,]", str(raw_ingredients))
            if line.strip()
        ][:10]
    instructions = meta.get("instructions") or ""
    if isinstance(instructions, list):
        step_lines = instructions[:10]
    else:
        step_lines = [s.strip() for s in re.split(r"(?<=[.!?])\s+", str(instructions)) if s.strip()][:10]
    return Meal(
        title=str(meta.get("name") or meal_type.title()),
        subtitle=meal_type.title(),
        badge="Simple",
        calories=str(meta.get("calories_per_serving") or meta.get("calories") or "0"),
        protein=str(meta.get("protein_per_serving") or meta.get("protein") or "0"),
        carbs=str(meta.get("carbs_per_serving") or meta.get("carbs") or "0"),
        description="Direct from your recipe collection (LLM shaping unavailable).",
        benefits=["From your recipe collection"],
        tags=[MealTag(text="Direct", color="gray")],
        ingredients=[MealIngredient(name=str(s)) for s in ingredient_lines],
        instructions=step_lines or ["Follow your recipe steps."],
        tips="",
    )


def _build_day(candidates: list[RecipeMatch], used: set[str]) -> DayPlan:
    """Build a 4-meal day (breakfast, lunch, dinner, snack) avoiding repeats
    and using meal-slot filters (e.g. no 2900-kcal "date bars" for breakfast)."""
    picks: dict[str, Meal] = {}
    for meal in ("breakfast", "lunch", "dinner", "snack"):
        recipe = _pick(candidates, used, meal_type=meal)
        used.add(recipe.metadata.get("name", "") or recipe.document[:40])
        picks[meal] = _meal_from_recipe(recipe, meal)
    title = picks["dinner"].title or picks["lunch"].title
    return DayPlan(title=title, **picks)


def build_meal_plan(req: MealPlanRequest) -> MealPlanResponse:
    """Public entry point — search recipes and build a 1- to 7-day plan."""
    if recipe_count() == 0:
        raise RuntimeError(
            "No recipes ingested yet. Drop JSON files into the `recipes/` folder "
            "and run `make ingest`."
        )

    query = _retrieval_query(req)
    # Pull a bigger candidate set so allergy filtering still leaves a usable
    # pool. With the filter going strict on exclusions, a tight k=50 could
    # leave us with 0 acceptable recipes; 250 gives breathing room.
    matches = search(query, k=max(req.top_k_recipes if hasattr(req, "top_k_recipes") else 250, 250))
    # Always merge in the small curated pool so it has a chance to win the
    # tiered _pick selection. Without this, vector search alone drowns 32
    # curated recipes in 200k bulk ones.
    curated = fetch_curated(limit=200)
    seen_names: set[str] = set()
    all_matches: list[RecipeMatch] = []
    for m in (curated + matches):  # curated first so de-dup keeps the curated copy
        name = (m.metadata or {}).get("name", "")
        if name and name in seen_names:
            continue
        if name:
            seen_names.add(name)
        all_matches.append(m)
    candidates = _filter_candidates(all_matches, req)

    days_label = _WEEKDAYS if req.days >= 7 else [f"day_{i+1}" for i in range(req.days)]
    if req.days <= 7 and req.days > 0 and len(days_label) != req.days:
        days_label = _WEEKDAYS[: req.days] if req.days <= 7 else days_label

    plan: dict[str, DayPlan] = {}
    used: set[str] = set()
    for label in days_label[: req.days]:
        plan[label] = _build_day(candidates, used)

    summary = (
        f"{req.days}-day plan from your recipe collection, targeting "
        f"~{req.target_calories} kcal/day"
        + (f", {req.diet.replace('_', ' ')}" if req.diet != "none" else "")
        + (f", avoiding {', '.join(req.allergies)}" if req.allergies else "")
        + "."
    )

    return MealPlanResponse(
        summary=summary,
        plan=plan,
        sources_used=len(candidates),
    )
