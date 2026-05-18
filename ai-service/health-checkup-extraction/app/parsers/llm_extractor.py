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
    "ko": (
        "Report language: Korean (한국어). Common Korean biomarker terms map to English keys: "
        "혈색소/헤모글로빈→hemoglobin, 혈당/공복혈당→glucose_fasting, 총콜레스테롤→total_cholesterol, "
        "중성지방→triglycerides, HDL콜레스테롤→hdl_cholesterol, LDL콜레스테롤→ldl_cholesterol, "
        "크레아티닌→creatinine, 요소질소/BUN→bun, 요산/통풍→uric_acid, "
        "적혈구→rbc, 백혈구→wbc, 혈소판→platelets, 혈색소→hgb, 헤마토크리트→hematocrit, "
        "나트륨→sodium, 칼륨→potassium, 염소→chloride, 칼슘→calcium, "
        "SGOT/AST→ast, SGPT/ALT→alt, 감마지티피/γGTP→ggt, "
        "e-GFR/사구체여과율→egfr, 체질량지수/BMI→bmi, 허리둘레→waist_cm, "
        "몸무게→weight_kg, 키→height_cm. "
        "CRITICAL RULES for Korean reports: "
        "(1) 비대상 means the test was NOT PERFORMED — completely omit that biomarker from the values array. Never guess or fill in a number for 비대상 rows. "
        "(2) Numbers like (1-199), (0-149), (60-), (70-99) are REFERENCE RANGES printed next to the result — never use them as the result value. "
        "(3) Each numeric value from the report may only be assigned to ONE biomarker. The number 15.7 belongs to 헤모글로빈 (hemoglobin); do not reuse it for 중성지방 (triglycerides) or any other biomarker. "
        "(4) Typical units: hemoglobin in g/dL (range ~7–20), triglycerides in mg/dL (range ~50–500 for most people), glucose in mg/dL (range ~70–400). A value of 15 mmol/L for triglycerides would equal ~1328 mg/dL — that is an extreme outlier and almost certainly a misread. "
        "정상 means normal, 비만 means obese, 경계 means borderline."
    ),
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
        panel = LabPanel.model_validate(data)
    except Exception:
        return None

    # Plausibility filter — drop any LLM-extracted value that is
    # physiologically impossible. The model sometimes hallucinates extreme
    # numbers (e.g. triglycerides 1328 mg/dL) from 비대상 / reference-range
    # text it misread. Hard limits are set 5-10× above the highest clinically
    # reported values so real (severe) results still pass.
    _MAX_PLAUSIBLE = {
        "glucose_fasting": 1200,    # mg/dL — diabetic ketoacidosis ceiling
        "glucose_random": 1200,
        "total_cholesterol": 1500,  # mg/dL — familial hypercholesterolaemia max
        "triglycerides": 10000,     # mg/dL — severe hypertriglyceridemia max
        "hdl_cholesterol": 200,
        "ldl": 800,
        "hemoglobin": 25,           # g/dL
        "hematocrit": 75,           # %
        "wbc": 500,                 # 10³/µL
        "platelets": 3000,          # 10³/µL
        "creatinine": 50,           # mg/dL
        "bun": 300,                 # mg/dL
        "uric_acid": 30,            # mg/dL
        "sodium": 200,              # mEq/L
        "potassium": 10,            # mEq/L — above 10 is incompatible with life
        "alt": 10000,               # IU/L
        "ast": 10000,               # IU/L
        "ggt": 10000,               # IU/L
        "bmi": 100,
    }
    _MIN_PLAUSIBLE = {
        "potassium": 1.5,           # below 1.5 mEq/L is extremely rare / incompatible
        "sodium": 100,
        "hemoglobin": 2,
        "glucose_fasting": 20,
        "glucose_random": 20,
        "hematocrit": 5,
        "bmi": 10,
    }
    filtered = [
        v for v in panel.values
        if _is_plausible(v.biomarker.value, v.value)
    ]
    if len(filtered) != len(panel.values):
        panel = panel.model_copy(update={"values": filtered})
    return panel


def _is_plausible(biomarker_key: str, value: float) -> bool:
    """Return False if the value is outside physiological bounds for that biomarker."""
    _MAX_PLAUSIBLE = {
        "glucose_fasting": 1200, "glucose_random": 1200,
        "total_cholesterol": 1500, "triglycerides": 10000,
        "hdl_cholesterol": 200, "ldl": 800,
        "hemoglobin": 25, "hematocrit": 75,
        "wbc": 500, "platelets": 3000,
        "creatinine": 50, "bun": 300, "uric_acid": 30,
        "sodium": 200, "potassium": 10,
        "alt": 10000, "ast": 10000, "ggt": 10000,
        "bmi": 100,
    }
    _MIN_PLAUSIBLE = {
        "potassium": 1.5, "sodium": 100,
        "hemoglobin": 2, "hematocrit": 5,
        "glucose_fasting": 20, "glucose_random": 20,
        "bmi": 10,
    }
    if biomarker_key in _MAX_PLAUSIBLE and value > _MAX_PLAUSIBLE[biomarker_key]:
        return False
    if biomarker_key in _MIN_PLAUSIBLE and value < _MIN_PLAUSIBLE[biomarker_key]:
        return False
    return True
