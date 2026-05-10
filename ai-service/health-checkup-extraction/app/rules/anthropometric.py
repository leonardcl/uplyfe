"""Anthropometric & vital signs — BMI, waist, blood pressure.

BMI (CDC/WHO adult):
  <18.5 underweight
  18.5–24.9 normal
  25.0–29.9 overweight
  30.0–34.9 obesity class I
  35.0–39.9 obesity class II
  ≥40 obesity class III
Note: WHO recommends lower cutoffs for some Asian populations
(overweight ≥23, obesity ≥27.5). We surface this as an informational note.

Waist circumference (NCEP ATP III): elevated central adiposity at
  men ≥102 cm (40 in), women ≥88 cm (35 in).

Blood pressure (ACC/AHA 2017):
  <120 / <80    Normal
  120–129 / <80 Elevated
  130–139 / 80–89 Stage 1 hypertension
  ≥140 / ≥90    Stage 2 hypertension
  ≥180 / ≥120   Hypertensive crisis (escalate)
"""
from __future__ import annotations

from typing import Optional

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel


SOURCE_BMI = "CDC adult BMI; WHO classification"
SOURCE_WAIST = "NCEP ATP III"
SOURCE_BP = "ACC/AHA 2017 Hypertension Guideline"


def _compute_bmi(panel: LabPanel) -> Optional[float]:
    if panel.height_cm and panel.weight_kg:
        h_m = panel.height_cm / 100
        return round(panel.weight_kg / (h_m * h_m), 1)
    return None


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    # --- BMI ---
    bmi = panel.value_of(Biomarker.BMI) or _compute_bmi(panel)
    if bmi is not None:
        if bmi < 18.5:
            sev, label = Severity.ABNORMAL, "Underweight"
        elif bmi < 25:
            sev, label = Severity.NORMAL, "Normal weight"
        elif bmi < 30:
            sev, label = Severity.BORDERLINE, "Overweight"
        elif bmi < 35:
            sev, label = Severity.ABNORMAL, "Obesity class I"
        elif bmi < 40:
            sev, label = Severity.ABNORMAL, "Obesity class II"
        else:
            sev, label = Severity.ABNORMAL, "Obesity class III"
        findings.append(
            Finding(
                biomarker=Biomarker.BMI,
                value=bmi,
                unit="kg/m2",
                severity=sev,
                label=label,
                rationale=(
                    f"BMI = {bmi}. WHO note: lower cutoffs (overweight ≥23, obesity ≥27.5) "
                    "are recommended in some Asian populations."
                ),
                source=SOURCE_BMI,
                related_topics=["anthropometric"],
            )
        )

    # --- Waist ---
    waist = panel.value_of(Biomarker.WAIST_CM) or panel.waist_cm
    if waist is not None:
        threshold = 102 if panel.sex != "female" else 88
        if waist >= threshold:
            findings.append(
                Finding(
                    biomarker=Biomarker.WAIST_CM,
                    value=waist,
                    unit="cm",
                    severity=Severity.ABNORMAL,
                    label="Elevated central adiposity",
                    rationale=(
                        f"Waist circumference {waist} cm is at or above the {threshold} cm threshold "
                        f"for {panel.sex or 'adults'} — a marker of central adiposity and metabolic risk."
                    ),
                    source=SOURCE_WAIST,
                    related_topics=["anthropometric", "lipids", "glucose"],
                )
            )
        else:
            findings.append(
                Finding(
                    biomarker=Biomarker.WAIST_CM,
                    value=waist,
                    unit="cm",
                    severity=Severity.NORMAL,
                    label="Waist within typical range",
                    rationale=f"Waist {waist} cm is below the {threshold} cm threshold for {panel.sex or 'adults'}.",
                    source=SOURCE_WAIST,
                    related_topics=["anthropometric"],
                )
            )

    # --- BP ---
    sys = panel.value_of(Biomarker.BP_SYSTOLIC)
    dia = panel.value_of(Biomarker.BP_DIASTOLIC)
    if sys is not None and dia is not None:
        if sys >= 180 or dia >= 120:
            sev, label = Severity.CRITICAL, "Hypertensive crisis"
            esc = True
            rat = (
                f"Blood pressure {sys}/{dia} mmHg is in the hypertensive-crisis range (≥180/120). "
                "If accompanied by chest pain, shortness of breath, vision changes, or neurologic symptoms, "
                "this is a medical emergency."
            )
        elif sys >= 140 or dia >= 90:
            sev, label, esc = Severity.ABNORMAL, "Stage 2 hypertension", False
            rat = f"Blood pressure {sys}/{dia} mmHg meets stage-2 hypertension criteria (≥140 or ≥90)."
        elif sys >= 130 or dia >= 80:
            sev, label, esc = Severity.BORDERLINE, "Stage 1 hypertension", False
            rat = f"Blood pressure {sys}/{dia} mmHg meets stage-1 hypertension criteria."
        elif sys >= 120:
            sev, label, esc = Severity.BORDERLINE, "Elevated blood pressure", False
            rat = f"Blood pressure {sys}/{dia} mmHg is in the elevated range (120–129/<80)."
        else:
            sev, label, esc = Severity.NORMAL, "Normal blood pressure", False
            rat = f"Blood pressure {sys}/{dia} mmHg is in the normal range."
        findings.append(
            Finding(
                biomarker=Biomarker.BP_SYSTOLIC,
                value=sys,
                unit="mmHg",
                severity=sev,
                label=label,
                rationale=rat + " (Reading should be confirmed across multiple visits before any clinical decision.)",
                source=SOURCE_BP,
                escalate=esc,
                related_topics=["anthropometric", "safety" if esc else "anthropometric"],
            )
        )

    return findings
