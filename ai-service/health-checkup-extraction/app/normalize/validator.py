"""Sanity check on the canonicalized panel before rules run.

This is data-quality validation, not clinical interpretation. We flag values
that are *implausible for any human alive*, missing pairs (e.g. systolic with
no diastolic), and entries whose post-conversion magnitude looks suspicious.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.lab import LabPanel, ValidationIssue


# Biological-plausibility limits — anything outside this range is almost
# certainly a unit error or a transcription error, not a true reading.
_PLAUSIBLE: dict[Biomarker, tuple[float, float]] = {
    Biomarker.GLUCOSE_FASTING: (20, 1500),       # mg/dL — diabetic ketoacidosis can be >1000
    Biomarker.GLUCOSE_RANDOM: (20, 1500),
    Biomarker.GLUCOSE_POSTPRANDIAL: (20, 1500),
    Biomarker.HBA1C: (3.0, 20.0),                # %
    Biomarker.TOTAL_CHOLESTEROL: (50, 800),      # mg/dL
    Biomarker.LDL: (10, 700),
    Biomarker.HDL: (5, 200),
    Biomarker.TRIGLYCERIDES: (10, 5000),
    Biomarker.NON_HDL: (10, 800),
    Biomarker.ALT: (1, 5000),
    Biomarker.AST: (1, 5000),
    Biomarker.ALP: (5, 5000),
    Biomarker.GGT: (1, 5000),
    Biomarker.BILIRUBIN_TOTAL: (0.05, 50),
    Biomarker.BILIRUBIN_DIRECT: (0.0, 30),
    Biomarker.ALBUMIN: (0.5, 7.0),
    Biomarker.CREATININE: (0.1, 25),
    Biomarker.BUN: (1, 300),
    Biomarker.EGFR: (1, 200),
    Biomarker.URIC_ACID: (0.5, 25),
    Biomarker.HEMOGLOBIN: (2, 25),
    Biomarker.HEMATOCRIT: (5, 80),
    Biomarker.RBC: (1, 9),
    Biomarker.WBC: (0.1, 200),
    Biomarker.PLATELETS: (1, 2000),
    Biomarker.MCV: (40, 130),
    Biomarker.TSH: (0.001, 200),
    Biomarker.FREE_T4: (0.05, 10),
    Biomarker.FREE_T3: (0.5, 30),
    Biomarker.CRP: (0.01, 500),
    Biomarker.ESR: (0, 200),
    Biomarker.SODIUM: (100, 200),
    Biomarker.POTASSIUM: (1.5, 9),
    Biomarker.CHLORIDE: (70, 130),
    Biomarker.CALCIUM: (4, 18),
    Biomarker.VITAMIN_D_25OH: (1, 200),
    Biomarker.VITAMIN_B12: (50, 5000),
    Biomarker.BMI: (8, 90),
    Biomarker.WAIST_CM: (30, 250),
    Biomarker.BP_SYSTOLIC: (50, 260),
    Biomarker.BP_DIASTOLIC: (20, 200),
}


def validate_panel(panel: LabPanel, conversion_warnings: list[str]) -> list[ValidationIssue]:
    issues: list[ValidationIssue] = []

    # Carry forward conversion warnings
    for w in conversion_warnings:
        issues.append(ValidationIssue(kind="unit", message=w))

    # Plausibility check
    for v in panel.values:
        bounds = _PLAUSIBLE.get(v.biomarker)
        if bounds is None:
            continue
        lo, hi = bounds
        if v.value < lo or v.value > hi:
            issues.append(
                ValidationIssue(
                    biomarker=v.biomarker,
                    kind="implausible",
                    message=(
                        f"{v.biomarker.value}={v.value} {v.unit} is outside biologically plausible "
                        f"range [{lo}, {hi}]. Likely a unit or transcription error — please verify."
                    ),
                )
            )

    # BP pairing
    sys = panel.value_of(Biomarker.BP_SYSTOLIC)
    dia = panel.value_of(Biomarker.BP_DIASTOLIC)
    if (sys is None) != (dia is None):
        issues.append(
            ValidationIssue(
                kind="missing",
                message="Blood pressure: one of systolic/diastolic is missing — provide both for accurate flagging.",
            )
        )
    if sys is not None and dia is not None and dia >= sys:
        issues.append(
            ValidationIssue(
                kind="implausible",
                message=f"Diastolic ({dia}) ≥ systolic ({sys}) — values may be swapped on the report.",
            )
        )

    # BMI cross-check vs height/weight
    bmi = panel.value_of(Biomarker.BMI)
    if bmi is None and panel.height_cm and panel.weight_kg:
        # We only INFORM here; the rules engine will compute BMI from height/weight if missing.
        issues.append(
            ValidationIssue(
                kind="info",
                message="BMI not supplied — will be computed from height and weight in the rules engine.",
            )
        )
    if bmi is not None and panel.height_cm and panel.weight_kg:
        h_m = panel.height_cm / 100
        computed = panel.weight_kg / (h_m * h_m)
        if abs(computed - bmi) > 1.5:
            issues.append(
                ValidationIssue(
                    biomarker=Biomarker.BMI,
                    kind="range",
                    message=(
                        f"Reported BMI {bmi} disagrees with height/weight-derived BMI {computed:.1f}. "
                        "Check inputs."
                    ),
                )
            )

    # Glucose context — fasting flag matters for thresholds
    fasting_glucose = panel.get(Biomarker.GLUCOSE_FASTING)
    if fasting_glucose and panel.fasting is False:
        issues.append(
            ValidationIssue(
                biomarker=Biomarker.GLUCOSE_FASTING,
                kind="info",
                message="A fasting glucose value was provided but the panel is marked non-fasting. ADA fasting thresholds may not apply.",
            )
        )

    return issues
