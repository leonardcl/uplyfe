"""The 3-stage RAG pipeline.

Stage 1 — Health assessment: pulls textbook chunks (ACSM/NSCA) relevant to
          the user's profile, asks the LLM to flag exercise types to avoid
          given their condition.

Stage 2 — Weekly structure: drafts a day-by-day outline (focus, duration,
          equipment) based on profile + assessment.

Stage 3 — Exercise selection: retrieves matching exercises from the dataset
          and asks the LLM to assemble the final JSON plan, citing exercises
          by their dataset `id`.

Each stage is small and pure-ish — easy to call in isolation when debugging.
The orchestrator (`generate_routine`) stitches them together.
"""
from __future__ import annotations

import json
from typing import Optional

from exercise_routine_generator.chroma_store import retrieve_exercises, retrieve_pdf
from exercise_routine_generator.equipment import decipher_equipment
from exercise_routine_generator.llm import LLMUnavailableError, generate
from exercise_routine_generator.models import GenerateRequest, UserProfile, WeeklyPlan, WorkoutDay, WorkoutExercise


# --- helpers ---


def _profile_block(p: UserProfile) -> str:
    """Render the profile as labelled lines for the LLM."""
    fields = {
        "body_weight": p.body_weight,
        "height": p.height,
        "age": p.age,
        "sex": p.sex,
        "fitness_goals": p.fitness_goals,
        "exercise_preference": p.exercise_preference,
        "time_available": p.time_available,
        "available_days": p.available_days,
        "equipment_available": p.equipment_available,
        "body_part_focus": p.body_part_focus,
        "limitations": p.limitations,
    }
    return "\n".join(f"{k}: {v if v is not None else '—'}" for k, v in fields.items())


# --- stages ---


def stage_health_assessment(profile: UserProfile, query: str) -> str:
    """Stage 1. Retrieves textbook context then asks the LLM for a short
    general assessment + exercise types to avoid."""
    pdf_chunks = retrieve_pdf(_profile_block(profile) + "\n" + (query or ""))
    knowledge = (
        "\n\n".join(f"[Page {p.metadata.get('page', '?')}]\n{p.text}" for p in pdf_chunks)
        if pdf_chunks
        else "(no textbook context available — produce a generic assessment)"
    )

    prompt = f"""\
You are a fitness expert.

User profile:
{_profile_block(profile)}

Knowledge passages:
{knowledge}

Task: write a short general assessment of this person's health and the
types of exercise they should avoid or perform with care given their
profile. Be specific and conservative. Do NOT diagnose disease. Do NOT
recommend medications.
"""
    return generate(prompt)


def stage_weekly_structure(profile: UserProfile, assessment: str) -> str:
    """Stage 2. Drafts the week's outline before exercise selection."""
    prompt = f"""\
Design a weekly workout outline for this user.

User profile:
{_profile_block(profile)}

Expert notes from stage 1:
{assessment}

Draft a rough weekly structure that the next stage will use to pick
specific exercises. Cover, for EACH active day:
- The type(s) of training (strength, cardio, mobility, …)
- The body parts / muscle groups to target
- Approximate intensity
- Total session duration
- The equipment available (verbatim: {profile.equipment_available or 'unspecified'})

Use markdown headings per day. Keep it concise.
"""
    return generate(prompt)


_FINAL_PROMPT_RULES = """\
Output rules — STRICT:
- Return ONLY a single valid JSON object. No markdown, no prose, no commentary.
- Use the schema below verbatim. All string fields must be strings.
- "weekly_workout_plan" is an array; one object per active day.
- Every "exercise_id" must come from the "Available Exercises" list below.
- Do not invent exercises that aren't in the list.
- Do not repeat the same exercise in a single day.

JSON schema:
{
  "weekly_workout_plan": [
    {
      "day_label": "string (e.g. 'Monday' or 'Day 1')",
      "heading": "string (short overall focus, e.g. 'Upper body push')",
      "title": "string (more specific theme, e.g. 'Chest + Shoulders')",
      "duration": "string (e.g. '45 minutes')",
      "description": "string (1-2 sentences)",
      "exercises": [
        {
          "exercise_id": "string (from the dataset)",
          "name": "string",
          "detail": "string (sets x reps, rest)",
          "description": "string (key technique cues)",
          "duration": "string"
        }
      ]
    }
  ]
}
"""


def stage_exercise_selection(
    profile: UserProfile,
    weekly_structure: str,
    canonical_equipment: list[str],
) -> dict:
    """Stage 3. Retrieves matching exercises then asks the LLM for the
    final structured plan. Returns a dict matching the JSON schema."""
    eq_str = ", ".join(canonical_equipment) or "body weight"
    exercise_query = f"""\
Find exercises that match the following constraints:

Equipment: {eq_str}
Goal: {profile.fitness_goals or 'general fitness'}
Focus: {profile.body_part_focus or 'full body'}
Preference: {profile.exercise_preference or 'mixed'}

Weekly plan context:
{weekly_structure}

Rules:
- PRIORITIZE equipment compatibility ({eq_str}).
- If equipment is only 'body weight' → only bodyweight exercises.
- Match the user's goal and focus.

Return exercises relevant to this query.
"""
    exercise_chunks = retrieve_exercises(exercise_query)
    catalog = "\n\n---\n\n".join(p.text for p in exercise_chunks)

    prompt = f"""\
You are an evidence-based fitness coach. Use ONLY exercises from the
dataset below to build the weekly plan.

{_FINAL_PROMPT_RULES}

Weekly plan from stage 2:
{weekly_structure}

User profile:
{_profile_block(profile)}

Canonical available equipment: {eq_str}

Available exercises (each is a dataset entry, separated by '---'):
{catalog}
"""

    try:
        raw = generate(prompt, format_json=True)
    except LLMUnavailableError as e:
        raise
    try:
        data = json.loads(raw)
    except json.JSONDecodeError:
        start, end = raw.find("{"), raw.rfind("}")
        if start == -1 or end == -1:
            raise ValueError("Stage-3 LLM did not return parseable JSON.")
        data = json.loads(raw[start : end + 1])

    if not isinstance(data, dict) or "weekly_workout_plan" not in data:
        # The model occasionally wraps the plan in an outer object; recover
        # if the array sits under a different key.
        for v in (data.values() if isinstance(data, dict) else []):
            if isinstance(v, list):
                data = {"weekly_workout_plan": v}
                break
    return data


# --- orchestrator ---


def generate_routine(req: GenerateRequest) -> WeeklyPlan:
    """End-to-end: assessment → weekly structure → exercise selection.

    Returns a `WeeklyPlan` Pydantic object. Never raises for "no PDF context"
    (stage 1 handles that). Will raise `LLMUnavailableError` if Ollama is
    unreachable, and `ValueError` if stage 3 returns an unparseable shape.
    """
    profile = req.profile
    query = req.query or "Create a weekly exercise routine"

    # Resolve equipment up front so retrieval (stage 3) gets canonical labels.
    canonical_equipment = decipher_equipment(profile.equipment_available or "")

    assessment = stage_health_assessment(profile, query)
    structure = stage_weekly_structure(profile, assessment)
    raw_plan = stage_exercise_selection(profile, structure, canonical_equipment)

    days: list[WorkoutDay] = []
    for d in raw_plan.get("weekly_workout_plan", []):
        try:
            day = WorkoutDay(
                day_label=str(d.get("day_label") or d.get("day") or "Day"),
                heading=d.get("heading"),
                title=d.get("title"),
                duration=d.get("duration"),
                description=d.get("description"),
                exercises=[
                    WorkoutExercise(
                        exercise_id=str(e.get("exercise_id") or e.get("id") or ""),
                        name=str(e.get("name") or ""),
                        detail=e.get("detail"),
                        description=e.get("description"),
                        duration=e.get("duration"),
                        body_part=e.get("body_part"),
                        equipment=e.get("equipment"),
                        target=e.get("target"),
                    )
                    for e in (d.get("exercises") or [])
                ],
            )
            days.append(day)
        except Exception:
            continue

    return WeeklyPlan(
        assessment=assessment,
        weekly_structure=structure,
        weekly_workout_plan=days,
        equipment_resolved=canonical_equipment,
    )
