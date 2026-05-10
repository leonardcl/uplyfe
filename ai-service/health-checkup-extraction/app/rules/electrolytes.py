"""Electrolytes — typical adult ranges.

Sodium (mmol/L): 135–145; <120 or >160 = critical.
Potassium (mmol/L): 3.5–5.0; <2.5 or >6.5 = critical.
Chloride (mmol/L): 96–106.
Calcium total (mg/dL): 8.5–10.5; <7 or >13 = critical.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "AACC adult electrolyte reference intervals"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    na = panel.value_of(Biomarker.SODIUM)
    if na is not None:
        if na < 120 or na > 160:
            f = make(panel, Biomarker.SODIUM, Severity.CRITICAL,
                     "Severe sodium derangement",
                     f"Sodium {na} mmol/L is outside the safe range; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["electrolytes", "safety"])
        elif na < 135:
            f = make(panel, Biomarker.SODIUM, Severity.ABNORMAL,
                     "Hyponatremia (low sodium)",
                     f"Sodium {na} mmol/L is below the typical range (135–145).",
                     SOURCE, related_topics=["electrolytes"])
        elif na > 145:
            f = make(panel, Biomarker.SODIUM, Severity.ABNORMAL,
                     "Hypernatremia (high sodium)",
                     f"Sodium {na} mmol/L is above the typical range (135–145).",
                     SOURCE, related_topics=["electrolytes"])
        else:
            f = make(panel, Biomarker.SODIUM, Severity.NORMAL,
                     "Normal sodium", f"Sodium {na} mmol/L within typical range.",
                     SOURCE, related_topics=["electrolytes"])
        if f:
            findings.append(f)

    k = panel.value_of(Biomarker.POTASSIUM)
    if k is not None:
        if k < 2.5 or k > 6.5:
            f = make(panel, Biomarker.POTASSIUM, Severity.CRITICAL,
                     "Severe potassium derangement",
                     f"Potassium {k} mmol/L is outside the safe range; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["electrolytes", "safety"])
        elif k < 3.5:
            f = make(panel, Biomarker.POTASSIUM, Severity.ABNORMAL,
                     "Hypokalemia (low potassium)",
                     f"Potassium {k} mmol/L is below the typical range (3.5–5.0).",
                     SOURCE, related_topics=["electrolytes"])
        elif k > 5.0:
            f = make(panel, Biomarker.POTASSIUM, Severity.ABNORMAL,
                     "Hyperkalemia (high potassium)",
                     f"Potassium {k} mmol/L is above the typical range (3.5–5.0).",
                     SOURCE, related_topics=["electrolytes"])
        else:
            f = make(panel, Biomarker.POTASSIUM, Severity.NORMAL,
                     "Normal potassium", f"Potassium {k} mmol/L within typical range.",
                     SOURCE, related_topics=["electrolytes"])
        if f:
            findings.append(f)

    cl = panel.value_of(Biomarker.CHLORIDE)
    if cl is not None:
        if cl < 96 or cl > 106:
            f = make(panel, Biomarker.CHLORIDE, Severity.BORDERLINE,
                     "Chloride out of typical range",
                     f"Chloride {cl} mmol/L is outside the typical range (96–106). "
                     "Often follows sodium and acid-base shifts.",
                     SOURCE, related_topics=["electrolytes"])
        else:
            f = make(panel, Biomarker.CHLORIDE, Severity.NORMAL,
                     "Normal chloride", f"Chloride {cl} mmol/L within typical range.",
                     SOURCE, related_topics=["electrolytes"])
        if f:
            findings.append(f)

    ca = panel.value_of(Biomarker.CALCIUM)
    if ca is not None:
        if ca < 7 or ca > 13:
            f = make(panel, Biomarker.CALCIUM, Severity.CRITICAL,
                     "Severe calcium derangement",
                     f"Calcium {ca} mg/dL is outside the safe range; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["electrolytes", "safety"])
        elif ca < 8.5:
            f = make(panel, Biomarker.CALCIUM, Severity.ABNORMAL,
                     "Hypocalcemia (low calcium)",
                     f"Calcium {ca} mg/dL is below the typical range (8.5–10.5). "
                     "Note: total calcium should be interpreted alongside albumin.",
                     SOURCE, related_topics=["electrolytes"])
        elif ca > 10.5:
            f = make(panel, Biomarker.CALCIUM, Severity.ABNORMAL,
                     "Hypercalcemia (high calcium)",
                     f"Calcium {ca} mg/dL is above the typical range (8.5–10.5).",
                     SOURCE, related_topics=["electrolytes"])
        else:
            f = make(panel, Biomarker.CALCIUM, Severity.NORMAL,
                     "Normal calcium", f"Calcium {ca} mg/dL within typical range.",
                     SOURCE, related_topics=["electrolytes"])
        if f:
            findings.append(f)

    return findings
