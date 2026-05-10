"""Complete Blood Count rules — typical adult ranges (NIH/MedlinePlus, AACC).

Hemoglobin: men 13.5–17.5, women 12.0–15.5 g/dL. <8 = severe anemia (escalate).
Hematocrit: men 38.8–50.0, women 34.9–44.5 %.
RBC: men 4.7–6.1, women 4.2–5.4 ×10^6/µL.
WBC: 4.5–11.0 ×10^3/µL. <2 or >30 = critical.
Platelets: 150–450 ×10^3/µL. <50 or >1000 = critical.
MCV: 80–100 fL.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "MedlinePlus / AACC adult CBC reference ranges"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    hb = panel.value_of(Biomarker.HEMOGLOBIN)
    if hb is not None:
        if panel.sex == "female":
            lo, hi = 12.0, 15.5
        else:
            lo, hi = 13.5, 17.5
        if hb < 8.0:
            f = make(panel, Biomarker.HEMOGLOBIN, Severity.CRITICAL,
                     "Severe anemia",
                     f"Hemoglobin {hb} g/dL is severely low; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["cbc", "safety"])
        elif hb < lo:
            f = make(panel, Biomarker.HEMOGLOBIN, Severity.ABNORMAL,
                     "Anemia range",
                     f"Hemoglobin {hb} g/dL is below the typical range for {panel.sex or 'adults'} ({lo}–{hi}).",
                     SOURCE, related_topics=["cbc"])
        elif hb > hi + 2:
            f = make(panel, Biomarker.HEMOGLOBIN, Severity.ABNORMAL,
                     "Markedly elevated hemoglobin",
                     f"Hemoglobin {hb} g/dL is well above the typical range. Possible causes: dehydration, polycythemia, smoking.",
                     SOURCE, related_topics=["cbc"])
        elif hb > hi:
            f = make(panel, Biomarker.HEMOGLOBIN, Severity.BORDERLINE,
                     "Mildly elevated hemoglobin",
                     f"Hemoglobin {hb} g/dL is above the typical range ({lo}–{hi}).",
                     SOURCE, related_topics=["cbc"])
        else:
            f = make(panel, Biomarker.HEMOGLOBIN, Severity.NORMAL,
                     "Normal hemoglobin",
                     f"Hemoglobin {hb} g/dL within typical range ({lo}–{hi}).",
                     SOURCE, related_topics=["cbc"])
        if f:
            findings.append(f)

    wbc = panel.value_of(Biomarker.WBC)
    if wbc is not None:
        if wbc < 2.0:
            f = make(panel, Biomarker.WBC, Severity.CRITICAL,
                     "Severe leukopenia",
                     f"WBC {wbc} ×10^3/µL is severely low; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["cbc", "safety"])
        elif wbc > 30.0:
            f = make(panel, Biomarker.WBC, Severity.CRITICAL,
                     "Markedly elevated WBC",
                     f"WBC {wbc} ×10^3/µL is markedly elevated; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["cbc", "safety"])
        elif wbc < 4.5:
            f = make(panel, Biomarker.WBC, Severity.ABNORMAL,
                     "Low WBC (leukopenia)",
                     f"WBC {wbc} ×10^3/µL is below the typical range (4.5–11.0).",
                     SOURCE, related_topics=["cbc"])
        elif wbc > 11.0:
            f = make(panel, Biomarker.WBC, Severity.BORDERLINE,
                     "Elevated WBC (leukocytosis)",
                     f"WBC {wbc} ×10^3/µL is above the typical range (4.5–11.0).",
                     SOURCE, related_topics=["cbc"])
        else:
            f = make(panel, Biomarker.WBC, Severity.NORMAL,
                     "Normal WBC", f"WBC {wbc} ×10^3/µL within typical range.",
                     SOURCE, related_topics=["cbc"])
        if f:
            findings.append(f)

    plt = panel.value_of(Biomarker.PLATELETS)
    if plt is not None:
        if plt < 50:
            f = make(panel, Biomarker.PLATELETS, Severity.CRITICAL,
                     "Severe thrombocytopenia",
                     f"Platelets {plt} ×10^3/µL — bleeding risk; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["cbc", "safety"])
        elif plt > 1000:
            f = make(panel, Biomarker.PLATELETS, Severity.CRITICAL,
                     "Severe thrombocytosis",
                     f"Platelets {plt} ×10^3/µL — markedly elevated; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["cbc", "safety"])
        elif plt < 150:
            f = make(panel, Biomarker.PLATELETS, Severity.ABNORMAL,
                     "Low platelets (thrombocytopenia)",
                     f"Platelets {plt} ×10^3/µL is below the typical range (150–450).",
                     SOURCE, related_topics=["cbc"])
        elif plt > 450:
            f = make(panel, Biomarker.PLATELETS, Severity.BORDERLINE,
                     "Elevated platelets",
                     f"Platelets {plt} ×10^3/µL is above the typical range (150–450).",
                     SOURCE, related_topics=["cbc"])
        else:
            f = make(panel, Biomarker.PLATELETS, Severity.NORMAL,
                     "Normal platelets", f"Platelets {plt} ×10^3/µL within typical range.",
                     SOURCE, related_topics=["cbc"])
        if f:
            findings.append(f)

    mcv = panel.value_of(Biomarker.MCV)
    if mcv is not None:
        if mcv < 80:
            f = make(panel, Biomarker.MCV, Severity.BORDERLINE,
                     "Microcytic (low MCV)",
                     f"MCV {mcv} fL is below 80 — microcytic indices, often associated with iron-deficiency or thalassemia.",
                     SOURCE, related_topics=["cbc"])
        elif mcv > 100:
            f = make(panel, Biomarker.MCV, Severity.BORDERLINE,
                     "Macrocytic (high MCV)",
                     f"MCV {mcv} fL is above 100 — macrocytic indices, often associated with B12/folate deficiency or alcohol use.",
                     SOURCE, related_topics=["cbc"])
        else:
            f = make(panel, Biomarker.MCV, Severity.NORMAL,
                     "Normocytic MCV", f"MCV {mcv} fL within typical range (80–100).",
                     SOURCE, related_topics=["cbc"])
        if f:
            findings.append(f)

    return findings
