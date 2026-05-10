"""LLM fallback extraction.

The regex extractor handles ~80% of clean reports. For odd templates, foreign
languages, or weird unit formatting, we ask the LLM to produce a strict JSON
LabPanel object and then run it through Pydantic validation.

We never trust the LLM's JSON without:
  1. Pydantic validation (rejects unknown biomarker keys, bad types)
  2. The unit normalizer (converts everything into canonical units)
  3. The validator (plausibility bounds)
"""
from __future__ import annotations

import json
from typing import Literal, Optional

from app.config import get_settings
from app.llm.client import OllamaClient, LLMUnavailableError
from app.models.lab import LabPanel


Language = Literal["id", "en", "mixed", "auto"]


# Per-call character cap. Pages longer than this get truncated; the orchestrator
# is expected to chunk by page before reaching us.
MAX_TEXT_CHARS = 24000


_LANG_HINTS = {
    "id": (
        "Report language: Indonesian (Bahasa Indonesia). Common ID biomarker terms map to the same English keys: "
        "glukosa puasa→glucose_fasting, kolesterol total→total_cholesterol, trigliserida→triglycerides, "
        "kreatinin→creatinine, ureum→bun, asam urat→uric_acid, eritrosit→rbc, leukosit→wbc, "
        "trombosit→platelets, hematokrit→hematocrit, natrium→sodium, kalium→potassium, "
        "klorida→chloride, kalsium→calcium, SGPT→alt, SGOT→ast."
    ),
    "mixed": (
        "Report language: bilingual (Indonesian + English). Use the English biomarker keys in the schema. "
        "ID terms map: kolesterol→cholesterol, trigliserida→triglycerides, kreatinin→creatinine, "
        "ureum→bun, asam urat→uric_acid, eritrosit→rbc, leukosit→wbc, trombosit→platelets, "
        "natrium→sodium, kalium→potassium, klorida→chloride, kalsium→calcium."
    ),
    "en": "Report language: English.",
    "auto": "",
}


PROMPT = """\
You are a medical lab-report extractor. Convert the report text below into JSON.

{language_hint}

Strict rules:
- Output a SINGLE JSON object, no markdown, no explanation.
- Use this schema:
  {{
    "age": int (0..130, default 0 if unknown),
    "sex": "male" | "female" | "other" | "unknown",
    "height_cm": number | null,
    "weight_kg": number | null,
    "waist_cm": number | null,
    "fasting": true | false | null,
    "values": [
      {{ "biomarker": "<one of the allowed keys>", "value": number, "unit": "<unit string>",
         "reference_low": number|null, "reference_high": number|null }}
    ]
  }}
- Allowed biomarker keys: {allowed}
- Do not invent values. If a biomarker is not on the report, omit it.
- Preserve units as written (e.g. "mmol/L" stays "mmol/L"). Conversion is done downstream.
- Decimal commas (e.g. "1,15") are equivalent to decimal points ("1.15"). Use a numeric value either way.

Report text:
\"\"\"
{text}
\"\"\"
"""


def extract_lab_panel(
    text: str,
    *,
    allowed_keys: list[str],
    language: Language = "auto",
) -> Optional[LabPanel]:
    """Try LLM extraction. Returns None if the LLM is unavailable or output is unparseable."""
    settings = get_settings()
    client = OllamaClient(settings)
    prompt = PROMPT.format(
        language_hint=_LANG_HINTS.get(language, ""),
        allowed=", ".join(allowed_keys),
        text=text[:MAX_TEXT_CHARS],
    )
    try:
        raw = client.generate(prompt, format_json=True)
    except LLMUnavailableError:
        return None

    try:
        data = json.loads(raw)
    except json.JSONDecodeError:
        # Try to scrape the first {...} block
        start, end = raw.find("{"), raw.rfind("}")
        if start == -1 or end == -1:
            return None
        try:
            data = json.loads(raw[start : end + 1])
        except json.JSONDecodeError:
            return None

    try:
        return LabPanel.model_validate(data)
    except Exception:
        return None
