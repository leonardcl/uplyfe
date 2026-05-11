"""Equipment normalization — map a free-text description like
'home gym with dumbbells and bands' onto the dataset's canonical equipment
labels so retrieval finds matching exercises.

Mirrors `DecipherEquipment` from `exercise7.py` but isolated so it can be
unit-tested without spinning up the rest of the pipeline.
"""
from __future__ import annotations

import json

from exercise_routine_generator.llm import LLMUnavailableError, generate_small


# Canonical equipment labels supported by the dataset.
VALID_EQUIPMENT = [
    "assisted", "band", "barbell", "body weight", "bosu ball", "cable",
    "dumbbell", "elliptical machine", "ez barbell", "hammer", "kettlebell",
    "leverage machine", "medicine ball", "olympic barbell", "pull-up bar",
    "resistance band", "roller", "rope", "skierg machine", "sled machine",
    "smith machine", "stability ball", "stationary bike", "stepmill machine",
    "tire", "trap bar", "upper body ergometer", "weighted", "wheel roller",
]


_PROMPT = """\
Map the user's available equipment into ONLY the valid equipment labels below.

VALID EQUIPMENT LIST:
{valid}

Rules:
- Return ONLY a JSON array of strings (e.g. ["dumbbell", "barbell"])
- Multiple equipment allowed
- Infer intent / synonyms:
    "home gym" -> may include dumbbell, barbell, resistance band
    "nothing" or "no equipment" -> body weight
    "pullup station" -> pull-up bar
    "bands" -> band OR resistance band
- Never invent new labels
- Output must be valid JSON

User input:
{user_input}
"""


def decipher_equipment(user_input: str) -> list[str]:
    """Return a list of canonical equipment labels for the user's input.

    Defaults to ['body weight'] when the LLM is unavailable or the response
    can't be parsed — never raises.
    """
    if not user_input or not user_input.strip():
        return ["body weight"]

    prompt = _PROMPT.format(
        valid="\n".join("- " + e for e in VALID_EQUIPMENT),
        user_input=user_input.strip(),
    )

    try:
        raw = generate_small(prompt)
    except LLMUnavailableError:
        return ["body weight"]

    try:
        parsed = json.loads(raw)
    except (json.JSONDecodeError, TypeError):
        # Try to scrape a JSON array out of the response.
        start, end = raw.find("["), raw.rfind("]")
        if start == -1 or end == -1:
            return ["body weight"]
        try:
            parsed = json.loads(raw[start : end + 1])
        except json.JSONDecodeError:
            return ["body weight"]

    cleaned: list[str] = []
    seen: set[str] = set()
    if isinstance(parsed, list):
        for item in parsed:
            label = str(item).strip().lower()
            if label in VALID_EQUIPMENT and label not in seen:
                cleaned.append(label)
                seen.add(label)

    return cleaned or ["body weight"]
