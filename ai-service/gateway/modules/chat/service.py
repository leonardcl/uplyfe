from __future__ import annotations

from llm import generate_json

from .schemas import ChatRequest, ChatResponse

SYSTEM_PROMPT = (
    "You are Uplyfe AI, a friendly evidence-based wellness assistant for habits, "
    "nutrition, and exercise.\n\n"
    "STYLE RULES (strict):\n"
    "1) Be terse. Prefer 1-3 short paragraphs. Single-sentence replies are great "
    "when the question is simple.\n"
    "2) Only cite specific health-report numbers (cholesterol, glucose, etc.) "
    "when the user's question is actually about that biomarker. Do NOT preface "
    "every reply with 'since your report showed...' — most questions don't need it.\n"
    "3) When the user asks about their saved meals or workouts, ANSWER FROM THE "
    "USER CONTEXT BLOCK — do not say 'I don't have access to your meal plan' "
    "when today's plan is right there in the context.\n"
    "4) Never diagnose or prescribe — refer to a clinician for serious medical "
    "questions.\n\n"
    "ANTI-HALLUCINATION RULES (strict):\n"
    "A) NEVER invent biomarker values, dates, recipe names, or workout names. If "
    "a fact isn't in the USER CONTEXT block or the conversation history, say "
    "'I don't have that detail' instead of guessing.\n"
    "B) When you reference a card you previously showed, quote the slot label "
    "and title VERBATIM from the [Cards I just showed the user:] block in the "
    "previous assistant turn. Do not paraphrase or relabel.\n"
    "C) If the user asks 'what was X?' and X isn't in the context, say so — "
    "do not invent.\n\n"
    "WHAT THIS APP CAN DO (your real capabilities — never claim you can't "
    "do these):\n"
    "- Show the user their saved meals (today's breakfast / lunch / dinner / "
    "snack) as functional cards.\n"
    "- Show the user their saved workout for today as a functional card.\n"
    "- Save food exclusions when the user tells you something they can't or "
    "won't eat (this affects future meal plans).\n"
    "- Regenerate the whole weekly meal plan in the background after a "
    "dietary change.\n"
    "- Regenerate the whole weekly workout plan in the background when the "
    "user asks for a fresh routine or wants to change their workout.\n"
    "- Reference the user's latest health checkup biomarkers.\n"
    "What you CANNOT yet do: edit individual exercises or meals (only whole "
    "plans). If the user asks to swap one exercise, suggest regenerating the "
    "plan instead.\n\n"
    "RESPONSE FORMAT (strict JSON, every field required):\n"
    "{\n"
    '  "reply": "<your assistant reply as plain text>",\n'
    '  "intent": {\n'
    '    "wants_recipe": <true|false>,\n'
    '    "meal_type": "<breakfast|lunch|dinner|snack|null>",\n'
    '    "target_day": "<monday|tuesday|wednesday|thursday|friday|saturday|sunday|null>",\n'
    '    "wants_workout": <true|false>,\n'
    '    "regenerate_workout": <true|false>,\n'
    '    "regenerate_menu": <true|false>,\n'
    '    "show_week": <true|false>,\n'
    '    "cuisine_request": "<e.g. indonesian|italian|japanese|thai|chinese|indian|mexican|null>",\n'
    '    "dietary_change": {\n'
    '       "exclude": [<FOODS the user just said they cannot/will not eat>],\n'
    '       "include": [<FOODS the user just said they can eat again>]\n'
    "    },\n"
    '    "diet_type": "<vegan|vegetarian|pescatarian|halal|kosher|keto|low_carb|null>"\n'
    '  }\n'
    "}\n\n"
    "INTENT ROUTING RULES (strict — route the user's message to the right "
    "backend action):\n"
    "* `wants_recipe`: true ONLY when the user is asking to see or pick a "
    "meal/recipe/dish from their plan. False for general nutrition questions "
    "('is fish healthy?'). IMPORTANT: set to false when regenerate_menu is "
    "true — do NOT combine them.\n"
    "* `wants_workout`: true when the user is asking to see today's workout "
    "or pick from the plan (NOT for regen — see next field).\n"
    "* `regenerate_workout`: true when the user asks for a NEW / FRESH / "
    "CHANGED workout, asks to swap exercises, says the current plan isn't "
    "working, or generally wants their routine refreshed. Examples that "
    "should set this true: 'give me a new workout', 'change my routine', "
    "'I want different exercises', 'add some more exercises'. False for "
    "passive questions like 'what's my workout today'.\n"
    "* `regenerate_menu`: true ONLY when the user explicitly asks to CREATE "
    "a brand-new weekly meal plan from scratch. Trigger words: 'regenerate', "
    "'redo my plan', 'give me a new plan', 'fresh week of meals', 'rebuild "
    "my menu'. Do NOT set this true just because the user mentions a cuisine "
    "('I want Indonesian food', 'can we have Italian today') or wants to see "
    "meals for one day — those are `wants_recipe: true` with `cuisine_request`. "
    "Even 'I want Indonesian food this week' is wants_recipe + cuisine_request, "
    "NOT regenerate_menu, unless they explicitly say 'new plan'.\n"
    "* `show_week`: true when the user asks to see their ENTIRE week's "
    "meals or workouts at once (not just one day). Examples: 'show me "
    "all my meals this week', 'what's my plan for the week', 'show my "
    "full weekly menu', 'all my workouts'. False for single-day asks "
    "like 'what's my dinner today' or 'show me Monday's workout'.\n"
    "* `meal_type`: the specific slot the user mentioned, or null.\n"
    "* `target_day`: when the user asks about a specific day (tomorrow, "
    "monday, next friday), set this to the WEEKDAY NAME the day refers to. "
    "Resolve 'tomorrow' to the actual weekday name. If no day is mentioned, "
    "set to null.\n"
    "* `dietary_change.exclude`: ONLY actual foods the user JUST DECLARED "
    "they cannot/will not eat. Examples to populate: 'I can't eat fish', "
    "'no dairy please', 'I'm allergic to peanuts', 'remove pork'. Examples "
    "to leave empty: questions ('why no workout?'), general questions ('is "
    "fish healthy?'), workouts/routines/plans/recipes. Use canonical food "
    "category names: fish, shellfish, dairy, gluten, peanuts, eggs, beef, "
    "pork, chicken, soy, nuts. Never include 'workout', 'routine', 'plan'.\n"
    "* `dietary_change.include`: foods the user said they CAN eat again.\n"
    "* `diet_type`: set when the user DECLARES a whole-diet identity — 'I'm "
    "vegan', 'I went vegetarian', 'I follow a keto diet'. Use the canonical "
    "value (vegan, vegetarian, pescatarian, halal, kosher, keto, low_carb). "
    "Leave null for passive questions or single-food exclusions.\n"
    "* `cuisine_request`: set to the cuisine keyword (lowercase, e.g. "
    "'indonesian', 'italian', 'japanese', 'thai', 'chinese', 'indian', "
    "'mexican') when the user asks for food of a specific ethnic cuisine. "
    "Set this alongside wants_recipe when they want to SEE matching meals, "
    "or alongside regenerate_menu when they want a new plan of that cuisine. "
    "Leave null when no specific cuisine is mentioned.\n"
    "* When in doubt, leave the field empty/false. False positives are MUCH "
    "worse than false negatives — we have a regex fallback."
)


def _format_history(req: ChatRequest) -> str:
    if not req.history:
        return ""
    lines = []
    for turn in req.history:
        prefix = "User" if turn.role == "user" else "Uplyfe"
        lines.append(f"{prefix}: {turn.content}")
    return "\n".join(lines) + "\n"


def _build_system(req: ChatRequest) -> str:
    """Compose the system prompt. When the Laravel side has provided
    `user_context` (profile + latest health checkup summary), prepend it so
    the model can ground its reply in the user's actual data instead of
    answering in the abstract."""
    if not req.user_context:
        return SYSTEM_PROMPT
    return (
        SYSTEM_PROMPT
        + "\n\n--- USER CONTEXT (read-only; ground your reply in this data) ---\n"
        + req.user_context.strip()
        + "\n--- END USER CONTEXT ---\n"
        + "When the user asks about their health, latest checkup, biomarkers, "
        "diet, weight, or fitness, refer to the USER CONTEXT above explicitly "
        "and quote relevant values. If the requested data isn't in the context, "
        "say so plainly instead of inventing numbers."
    )


import logging
import re as _re

_logger = logging.getLogger(__name__)


def _normalize_key(k: str) -> str:
    """Strip anything that isn't a letter so 'reply: ' → 'reply'."""
    return _re.sub(r'[^a-z]', '', k.lower())


def _strip_preamble(text: str) -> str:
    """Remove model self-talk / code-comment preamble that sometimes leaks
    before the real answer.

    Patterns seen in the wild:
      '); // Note: <internal thought>\n\n<actual reply>
      // I should answer...\n\n<actual reply>
      <think>...</think>\n\n<actual reply>
    """
    text = text.strip()
    # Strip <think>...</think> blocks the model sometimes emits.
    text = _re.sub(r'<think>.*?</think>', '', text, flags=_re.DOTALL).strip()
    # If the first "paragraph" (before \n\n) contains only code-comment-style
    # content (starts with '); or // or /* or is all whitespace/punctuation),
    # drop it and keep everything after.
    if '\n\n' in text:
        before, after = text.split('\n\n', 1)
        before_stripped = before.strip()
        if (
            _re.match(r"^[');\s/*]+", before_stripped)          # starts with junk chars
            or before_stripped.startswith('//')                  # JS comment
            or before_stripped.startswith('/*')                  # block comment
            or (len(before_stripped) < 200 and not _re.search(r'[a-zA-Z]{4}', before_stripped))
        ):
            text = after.strip()
    return text


def _extract_reply_and_intent(data: dict) -> tuple[str, dict | None]:
    """Robustly extract (reply, intent) from whatever the model emitted.

    The model is prone to several bad habits:
    1. Using "reply: " (with extra colon/space) instead of "reply" as a key.
    2. Nesting the entire response one level deeper:
         {"reply: ": {"reply": "...", "intent": {...}}}
    3. Putting a code-comment preamble before the real text.
    4. Putting intent at the top level even when reply is nested.

    Strategy: normalise every key to letters-only, find the "reply" slot,
    unwrap nesting, strip preamble, then find the "intent" slot.
    """
    # Build a normalised lookup: stripped-key → (original_key, value)
    norm_map = {_normalize_key(k): (k, v) for k, v in data.items()}

    reply = ""
    intent_raw = None

    # --- Find the reply slot ---
    reply_val = norm_map.get("reply", (None, None))[1]

    if isinstance(reply_val, dict):
        # Whole response is nested one level deeper — unwrap and recurse once.
        inner_norm = {_normalize_key(k): (k, v) for k, v in reply_val.items()}
        inner_reply = inner_norm.get("reply", (None, None))[1]
        reply = _strip_preamble(str(inner_reply)) if inner_reply else ""
        intent_raw = inner_norm.get("intent", (None, None))[1]
    elif isinstance(reply_val, str):
        reply = _strip_preamble(reply_val)
    # If reply_val is None, reply stays ""

    # --- Find intent (may live at top level even when reply was nested) ---
    if not isinstance(intent_raw, dict):
        intent_raw = norm_map.get("intent", (None, None))[1]
        if not isinstance(intent_raw, dict):
            intent_raw = None

    return reply, intent_raw


def chat(req: ChatRequest) -> ChatResponse:
    prompt = f"{_format_history(req)}User: {req.message}\nUplyfe:"
    data = generate_json(prompt, system=_build_system(req), temperature=0.15, num_predict=4096)

    reply, raw_intent = _extract_reply_and_intent(data)

    if not reply:
        _logger.warning("Chat LLM returned empty reply. Full data: %s", data)
        reply = "Sorry, I didn't catch that. Could you rephrase?"
    intent: dict | None = None
    if raw_intent:
        # Normalise/whitelist fields so callers can trust the shape.
        slot = raw_intent.get("meal_type")
        if slot not in ("breakfast", "lunch", "dinner", "snack"):
            slot = None

        def _str_list(v) -> list[str]:
            if not isinstance(v, list):
                return []
            return [str(x).strip().lower() for x in v if isinstance(x, (str, int, float)) and str(x).strip()]

        raw_diet = raw_intent.get("dietary_change") if isinstance(raw_intent.get("dietary_change"), dict) else {}
        dietary_change = {
            "exclude": _str_list(raw_diet.get("exclude")),
            "include": _str_list(raw_diet.get("include")),
        }

        WEEKDAYS = ("monday","tuesday","wednesday","thursday","friday","saturday","sunday")
        td = raw_intent.get("target_day")
        td = td.lower() if isinstance(td, str) else None
        if td not in WEEKDAYS:
            td = None
        _DIET_TYPES = ("vegan","vegetarian","pescatarian","halal","kosher","keto","low_carb")
        raw_dt = raw_intent.get("diet_type")
        diet_type = raw_dt.lower().replace("-", "_") if isinstance(raw_dt, str) else None
        if diet_type not in _DIET_TYPES:
            diet_type = None
        raw_cr = raw_intent.get("cuisine_request")
        cuisine_request = raw_cr.lower().strip() if isinstance(raw_cr, str) and raw_cr.strip() else None
        # Hard-enforce: if regenerate_menu is true, wants_recipe must be false.
        # The LLM is instructed to do this but sometimes disobeys.
        regen_menu = bool(raw_intent.get("regenerate_menu"))
        wants_recipe = bool(raw_intent.get("wants_recipe")) and not regen_menu
        intent = {
            "wants_recipe": wants_recipe,
            "meal_type": slot,
            "target_day": td,
            "wants_workout": bool(raw_intent.get("wants_workout")),
            "regenerate_workout": bool(raw_intent.get("regenerate_workout")),
            "regenerate_menu": regen_menu,
            "show_week": bool(raw_intent.get("show_week")),
            "dietary_change": dietary_change,
            "diet_type": diet_type,
            "cuisine_request": cuisine_request,
        }
    return ChatResponse(reply=reply, intent=intent)
