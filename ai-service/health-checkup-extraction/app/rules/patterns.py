"""Cross-biomarker patterns that humans typically read across the panel.

These are NOT diagnoses — they are "patterns to discuss with a clinician".
They are deterministic and only fire when their criteria are clearly met.

Patterns implemented:
  * Metabolic syndrome — NCEP ATP III: any 3 of:
      - waist ≥102 (M) / ≥88 (F)
      - triglycerides ≥150
      - HDL <40 (M) / <50 (F)
      - BP ≥130/85 OR diagnosed HTN
      - fasting glucose ≥100
  * Mixed dyslipidemia — high LDL AND high triglycerides AND low HDL.
  * AST/ALT alcoholic pattern — handled in liver.py (kept here as a marker).
  * Microcytic anemia — low hemoglobin + MCV <80.
  * Macrocytic anemia — low hemoglobin + MCV >100 (B12 / folate workup).
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel


def _has(panel: LabPanel, b: Biomarker) -> bool:
    return panel.get(b) is not None


def detect_patterns(panel: LabPanel) -> list[Finding]:
    out: list[Finding] = []

    # --- Metabolic syndrome ---
    crit = 0
    notes: list[str] = []

    waist = panel.value_of(Biomarker.WAIST_CM) or panel.waist_cm
    if waist is not None:
        cutoff = 102 if panel.sex != "female" else 88
        if waist >= cutoff:
            crit += 1
            notes.append(f"waist {waist} cm ≥ {cutoff}")

    tg = panel.value_of(Biomarker.TRIGLYCERIDES)
    if tg is not None and tg >= 150:
        crit += 1
        notes.append(f"triglycerides {tg} mg/dL ≥ 150")

    hdl = panel.value_of(Biomarker.HDL)
    if hdl is not None:
        cutoff = 50 if panel.sex == "female" else 40
        if hdl < cutoff:
            crit += 1
            notes.append(f"HDL {hdl} mg/dL < {cutoff}")

    sys = panel.value_of(Biomarker.BP_SYSTOLIC)
    dia = panel.value_of(Biomarker.BP_DIASTOLIC)
    if sys is not None and dia is not None and (sys >= 130 or dia >= 85):
        crit += 1
        notes.append(f"BP {sys}/{dia} ≥ 130/85")

    fpg = panel.value_of(Biomarker.GLUCOSE_FASTING)
    if fpg is not None and fpg >= 100:
        crit += 1
        notes.append(f"fasting glucose {fpg} mg/dL ≥ 100")

    if crit >= 3:
        out.append(
            Finding(
                biomarker=Biomarker.GLUCOSE_FASTING,  # nominal anchor
                value=float(crit),
                unit="criteria_met",
                severity=Severity.ABNORMAL,
                label="Pattern: metabolic syndrome (NCEP ATP III) criteria met",
                rationale=(
                    f"{crit} of 5 NCEP ATP III criteria met: " + "; ".join(notes) +
                    ". This is a pattern, not a diagnosis — discuss with a clinician."
                ),
                source="NCEP ATP III",
                related_topics=["lipids", "glucose", "anthropometric"],
            )
        )

    # --- Mixed dyslipidemia ---
    ldl = panel.value_of(Biomarker.LDL)
    if ldl is not None and tg is not None and hdl is not None:
        hdl_low = 50 if panel.sex == "female" else 40
        if ldl >= 130 and tg >= 150 and hdl < hdl_low:
            out.append(
                Finding(
                    biomarker=Biomarker.LDL,
                    value=ldl,
                    unit="mg/dL",
                    severity=Severity.ABNORMAL,
                    label="Pattern: mixed dyslipidemia",
                    rationale=(
                        f"LDL {ldl} ≥130, triglycerides {tg} ≥150, and HDL {hdl} < {hdl_low}. "
                        "This combined lipid pattern carries higher cardiovascular risk than any single value alone."
                    ),
                    source="AHA/ACC 2018",
                    related_topics=["lipids"],
                )
            )

    # --- Microcytic / macrocytic anemia patterns ---
    hb = panel.value_of(Biomarker.HEMOGLOBIN)
    mcv = panel.value_of(Biomarker.MCV)
    if hb is not None and mcv is not None:
        hb_low = 12.0 if panel.sex == "female" else 13.5
        if hb < hb_low and mcv < 80:
            out.append(
                Finding(
                    biomarker=Biomarker.HEMOGLOBIN,
                    value=hb,
                    unit="g/dL",
                    severity=Severity.ABNORMAL,
                    label="Pattern: microcytic anemia",
                    rationale=(
                        f"Hemoglobin {hb} g/dL is below the typical lower limit and MCV {mcv} fL is <80. "
                        "Most commonly reflects iron deficiency or thalassemia trait — workup with a clinician is reasonable."
                    ),
                    source="MedlinePlus / AACC",
                    related_topics=["cbc", "vitamins"],
                )
            )
        elif hb < hb_low and mcv > 100:
            out.append(
                Finding(
                    biomarker=Biomarker.HEMOGLOBIN,
                    value=hb,
                    unit="g/dL",
                    severity=Severity.ABNORMAL,
                    label="Pattern: macrocytic anemia",
                    rationale=(
                        f"Hemoglobin {hb} g/dL is below the typical lower limit and MCV {mcv} fL is >100. "
                        "Common workup includes B12 and folate — discuss with a clinician."
                    ),
                    source="MedlinePlus / AACC",
                    related_topics=["cbc", "vitamins"],
                )
            )

    return out
