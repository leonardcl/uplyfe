from llm import generate_json

from .schemas import DailyMenu, RecipeRequest

_SYSTEM = (
    "You are a nutrition-aware home cook AI. You produce balanced, realistic daily menus that respect "
    "the user's diet, allergies, and cuisine preferences. You never give medical or therapeutic advice. "
    "Respond ONLY with a single JSON object that matches the requested schema."
)

_SCHEMA_HINT = """
{
  "summary": "string, 1-2 sentences",
  "total_estimated_calories": int,
  "meals": [
    {
      "name": "breakfast" | "lunch" | "dinner" | "snack",
      "title": "string",
      "estimated_calories": int,
      "prep_minutes": int|null,
      "ingredients": [{"name": "string", "quantity": "string"}],
      "steps": ["string", ...],
      "notes": "string|null"
    }
  ],
  "grocery_list": [{"name": "string", "quantity": "string"}]
}
""".strip()


def build_menu(req: RecipeRequest) -> DailyMenu:
    allergies = ", ".join(req.allergies) or "none reported"
    cuisines = ", ".join(req.cuisine_preferences) or "no preference"
    user = (
        f"Plan a one-day menu of breakfast, lunch, dinner, and one snack.\n"
        f"- Target calories: ~{req.target_calories} kcal total\n"
        f"- Servings per meal: {req.servings}\n"
        f"- Diet: {req.diet}\n"
        f"- Allergies / must-avoid: {allergies}\n"
        f"- Cuisine preferences: {cuisines}\n"
        f"- Extra notes: {req.notes or 'none'}\n\n"
        f"The grocery_list should aggregate ingredients across all meals.\n"
        f"Return JSON in exactly this shape:\n{_SCHEMA_HINT}"
    )
    raw = generate_json(user, system=_SYSTEM, temperature=0.6)
    return DailyMenu(**raw)
