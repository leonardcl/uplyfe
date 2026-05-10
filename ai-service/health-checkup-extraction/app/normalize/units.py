"""Unit normalization.

A wrong unit silently turns 110 mg/dL into 110 mmol/L, which is the difference
between a normal fasting glucose and a hospital admission. Every value is
converted into a single canonical unit BEFORE any rule sees it.

References for conversion factors:
  * Glucose: 1 mmol/L = 18.0182 mg/dL  (AACC)
  * Cholesterol/LDL/HDL/non-HDL: 1 mmol/L = 38.67 mg/dL
  * Triglycerides: 1 mmol/L = 88.57 mg/dL
  * Creatinine: 1 µmol/L = 1/88.4 mg/dL
  * Urea (mmol/L) → BUN (mg/dL): BUN = (urea_mg/dL) / 2.14, urea_mg/dL = urea_mmol/L * 6.006
  * Bilirubin: 1 µmol/L = 1/17.1 mg/dL
  * Calcium: 1 mmol/L = 4.008 mg/dL
  * HbA1c IFCC mmol/mol → NGSP %: pct = (mmol/mol / 10.929) + 2.15  (NGSP)
  * Vitamin D 25-OH: 1 nmol/L = 0.4 ng/mL
  * Vitamin B12: 1 pmol/L = 1/0.738 pg/mL
  * CRP: 1 mg/dL = 10 mg/L
"""
from __future__ import annotations

from typing import Optional

from app.models.biomarkers import Biomarker, CANONICAL_UNIT
from app.models.lab import LabPanel, LabValue


# ---------- unit string canonicalization ----------

_UNIT_ALIASES: dict[str, str] = {
    "mg/dl": "mg/dL",
    "mg / dl": "mg/dL",
    "mgdl": "mg/dL",
    "mmol/l": "mmol/L",
    "mmol / l": "mmol/L",
    "g/dl": "g/dL",
    "g / dl": "g/dL",
    "g/l": "g/L",
    "umol/l": "µmol/L",
    "μmol/l": "µmol/L",
    "µmol/l": "µmol/L",
    "u/l": "U/L",
    "iu/l": "U/L",
    "ng/ml": "ng/mL",
    "ng / ml": "ng/mL",
    "pg/ml": "pg/mL",
    "miu/l": "mIU/L",
    "uiu/ml": "mIU/L",  # 1 µIU/mL == 1 mIU/L
    "µiu/ml": "mIU/L",
    "meq/l": "mmol/L",
    "mmhg": "mmHg",
    "mm hg": "mmHg",
    "mm/hr": "mm/hr",
    "mm/h": "mm/hr",
    "10^3/ul": "10^3/uL",
    "10^6/ul": "10^6/uL",
    "k/ul": "10^3/uL",
    "m/ul": "10^6/uL",
    "/ul": "10^3/uL",  # dangerous; flag as ambiguous in validator
    "fl": "fL",
    "kg/m2": "kg/m2",
    "kg/m^2": "kg/m2",
    "%": "%",
    "mmol/mol": "mmol/mol",
    "nmol/l": "nmol/L",
    "pmol/l": "pmol/L",
    "mg/l": "mg/L",
    "cm": "cm",
}


def normalize_unit(unit: str) -> str:
    """Map free-text unit strings to a single canonical spelling."""
    if unit is None:
        return ""
    cleaned = unit.strip()
    return _UNIT_ALIASES.get(cleaned.lower(), cleaned)


# ---------- per-biomarker conversions ----------

_GLUCOSE_LIKE = {
    Biomarker.GLUCOSE_FASTING,
    Biomarker.GLUCOSE_RANDOM,
    Biomarker.GLUCOSE_POSTPRANDIAL,
}
_CHOL_LIKE = {
    Biomarker.TOTAL_CHOLESTEROL,
    Biomarker.LDL,
    Biomarker.HDL,
    Biomarker.NON_HDL,
}


# When the unit string is missing or unrecognized, we try to infer it from the
# value range. Format: biomarker → (direction, threshold, inferred_unit).
#   "low" : if value < threshold, infer the alternate unit
#   "high": if value > threshold, infer the alternate unit
# Thresholds are chosen so that any plausible report-in-canonical-unit value
# stays on the canonical side, and any plausible report-in-alternate-unit
# value lands on the inference side.
_UNIT_INFERENCE: dict[Biomarker, tuple[str, float, str]] = {
    # Cholesterol family: mg/dL ranges 50–800 vs mmol/L ranges 1–12.
    Biomarker.TOTAL_CHOLESTEROL: ("low", 50, "mmol/L"),
    Biomarker.LDL: ("low", 20, "mmol/L"),
    Biomarker.HDL: ("low", 10, "mmol/L"),
    Biomarker.NON_HDL: ("low", 30, "mmol/L"),
    # Triglycerides: mg/dL 30–5000 vs mmol/L 0.3–55.
    Biomarker.TRIGLYCERIDES: ("low", 25, "mmol/L"),
    # Glucose: mg/dL 50–1500 vs mmol/L 3–80. 30 mg/dL is dangerous-but-real,
    # so use 28 to leave a tiny buffer for severe hypoglycemia readings.
    Biomarker.GLUCOSE_FASTING: ("low", 28, "mmol/L"),
    Biomarker.GLUCOSE_RANDOM: ("low", 28, "mmol/L"),
    Biomarker.GLUCOSE_POSTPRANDIAL: ("low", 28, "mmol/L"),
    # Creatinine: mg/dL 0.4–10 vs µmol/L 30–800.
    Biomarker.CREATININE: ("high", 20, "µmol/L"),
    # Bilirubin: mg/dL 0.1–30 vs µmol/L 1.7–500.
    Biomarker.BILIRUBIN_TOTAL: ("high", 30, "µmol/L"),
    Biomarker.BILIRUBIN_DIRECT: ("high", 25, "µmol/L"),
    # Calcium: mg/dL 4–18 vs mmol/L 1–4.5.
    Biomarker.CALCIUM: ("low", 5, "mmol/L"),
    # Vitamin D 25-OH: ng/mL 1–200 vs nmol/L 2.5–500.
    Biomarker.VITAMIN_D_25OH: ("high", 200, "nmol/L"),
}


def _try_infer_alt_unit(biomarker: Biomarker, value: float) -> Optional[str]:
    rule = _UNIT_INFERENCE.get(biomarker)
    if not rule:
        return None
    direction, threshold, alt_unit = rule
    if direction == "low" and value < threshold:
        return alt_unit
    if direction == "high" and value > threshold:
        return alt_unit
    return None


def _convert(biomarker: Biomarker, value: float, unit: str) -> tuple[float, str, Optional[str]]:
    """Return (value_canonical, unit_canonical, warning).

    `warning` is None when the conversion is unambiguous; otherwise a short
    string the validator should surface to the user.
    """
    canon_unit = CANONICAL_UNIT[biomarker]
    unit_n = normalize_unit(unit)

    if unit_n == canon_unit:
        return value, canon_unit, None

    # Glucose & cholesterol-like: mmol/L ↔ mg/dL
    if biomarker in _GLUCOSE_LIKE and unit_n == "mmol/L":
        return round(value * 18.0182, 1), canon_unit, None
    if biomarker in _CHOL_LIKE and unit_n == "mmol/L":
        return round(value * 38.67, 1), canon_unit, None
    if biomarker == Biomarker.TRIGLYCERIDES and unit_n == "mmol/L":
        return round(value * 88.57, 1), canon_unit, None

    # Creatinine
    if biomarker == Biomarker.CREATININE and unit_n == "µmol/L":
        return round(value / 88.4, 2), canon_unit, None

    # BUN / urea
    if biomarker == Biomarker.BUN:
        if unit_n == "mmol/L":
            urea_mgdl = value * 6.006  # urea mg/dL
            return round(urea_mgdl / 2.14, 1), canon_unit, "Converted from urea mmol/L → BUN mg/dL"
        if unit_n in {"mg/dL"}:
            return value, canon_unit, None

    # Bilirubin
    if biomarker in {Biomarker.BILIRUBIN_TOTAL, Biomarker.BILIRUBIN_DIRECT} and unit_n == "µmol/L":
        return round(value / 17.1, 2), canon_unit, None

    # Calcium
    if biomarker == Biomarker.CALCIUM and unit_n == "mmol/L":
        return round(value * 4.008, 2), canon_unit, None

    # Albumin
    if biomarker == Biomarker.ALBUMIN and unit_n == "g/L":
        return round(value / 10, 2), canon_unit, None

    # Hemoglobin
    if biomarker == Biomarker.HEMOGLOBIN and unit_n == "g/L":
        return round(value / 10, 1), canon_unit, None

    # Hematocrit (decimal fraction → %)
    if biomarker == Biomarker.HEMATOCRIT and unit_n in {"L/L", "fraction", ""} and value <= 1.0:
        return round(value * 100, 1), canon_unit, "Hematocrit fraction interpreted as %."

    # HbA1c IFCC → NGSP
    if biomarker == Biomarker.HBA1C and unit_n == "mmol/mol":
        return round((value / 10.929) + 2.15, 2), canon_unit, "HbA1c converted IFCC mmol/mol → NGSP %"

    # Vitamin D
    if biomarker == Biomarker.VITAMIN_D_25OH and unit_n == "nmol/L":
        return round(value * 0.4, 1), canon_unit, None

    # Vitamin B12
    if biomarker == Biomarker.VITAMIN_B12 and unit_n == "pmol/L":
        return round(value / 0.738, 1), canon_unit, None

    # CRP
    if biomarker == Biomarker.CRP and unit_n == "mg/dL":
        return round(value * 10, 2), canon_unit, None

    # Electrolytes — mEq/L is identical to mmol/L for monovalent ions
    if biomarker in {Biomarker.SODIUM, Biomarker.POTASSIUM, Biomarker.CHLORIDE} and unit_n == "mmol/L":
        return value, canon_unit, None

    # Inference fallback: when the unit was missing or unrecognized AND the
    # value is implausibly out-of-range for the canonical unit, try inferring
    # the alternate unit (mmol/L for cholesterol/glucose, µmol/L for
    # creatinine/bilirubin) from the value itself, then convert.
    inferred = _try_infer_alt_unit(biomarker, value)
    if inferred is not None:
        try:
            v_conv, canon_conv, _ = _convert(biomarker, value, inferred)
            return v_conv, canon_conv, (
                f"Unit was missing or unrecognized; inferred {inferred} from the value "
                f"range and converted to {canon_unit}."
            )
        except Exception:
            pass

    # Failed to convert — return original value, but flag a warning.
    return value, canon_unit, (
        f"Could not safely convert {biomarker.value} from {unit_n!r} to {canon_unit!r}. "
        "Value passed through unchanged; please verify the unit on the original report."
    )


def to_canonical(panel: LabPanel) -> tuple[LabPanel, list[str]]:
    """Return a new panel with values in canonical units, plus warnings.

    Pure function — does not mutate the input panel.
    """
    new_values: list[LabValue] = []
    warnings: list[str] = []
    for v in panel.values:
        new_value, canon_unit, warning = _convert(v.biomarker, v.value, v.unit)
        new_values.append(
            LabValue(
                biomarker=v.biomarker,
                value=new_value,
                unit=canon_unit,
                reference_low=v.reference_low,
                reference_high=v.reference_high,
                note=v.note,
                original_value=v.value if (new_value != v.value or canon_unit != v.unit) else None,
                original_unit=v.unit if (new_value != v.value or canon_unit != v.unit) else None,
            )
        )
        if warning:
            warnings.append(f"{v.biomarker.value}: {warning}")
    return panel.model_copy(update={"values": new_values}), warnings
