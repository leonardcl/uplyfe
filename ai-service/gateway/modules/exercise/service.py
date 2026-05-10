from llm import generate_json

from .schemas import ExercisePlan, ExerciseRequest

_SYSTEM = (
    "You are a fitness coach AI. You produce safe, structured weekly workout plans. "
    "You never recommend extreme regimens, never diagnose, and always include sensible safety notes. "
    "Respond ONLY with a single JSON object that matches the requested schema."
)

_SCHEMA_HINT = """
{
  "summary": "string, 1-2 sentences on the plan's focus",
  "weekly_plan": [
    {
      "day": "Day 1" | weekday name,
      "focus": "string",
      "warmup": ["string", ...],
      "workout": [
        {"name": "string", "sets": int|null, "reps": "string|null", "rest_seconds": int|null, "notes": "string|null"}
      ],
      "cooldown": ["string", ...]
    }
  ],
  "safety_notes": ["string", ...]
}
""".strip()


def build_plan(req: ExerciseRequest) -> ExercisePlan:
    equipment = ", ".join(req.equipment) or "bodyweight only"
    limitations = "; ".join(req.limitations) or "none reported"
    user = (
        f"Build a {req.days_per_week}-day weekly workout plan.\n"
        f"- Goal: {req.goal}\n"
        f"- Level: {req.level}\n"
        f"- Session length: {req.minutes_per_session} minutes\n"
        f"- Available equipment: {equipment}\n"
        f"- Limitations / injuries: {limitations}\n\n"
        f"Return JSON in exactly this shape:\n{_SCHEMA_HINT}"
    )
    raw = generate_json(user, system=_SYSTEM, temperature=0.5)
    return ExercisePlan(**raw)
