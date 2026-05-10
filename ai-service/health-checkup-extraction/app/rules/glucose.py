"""Glucose & HbA1c rules — ADA Standards of Care 2024.

Thresholds:
  Fasting plasma glucose: <100 normal, 100–125 prediabetes (IFG), ≥126 diabetes range.
  2-hr OGTT / postprandial: <140 normal, 140–199 IGT/prediabetes, ≥200 diabetes range.
  Random glucose ≥200 with hyperglycemia symptoms is diagnostic in clinical practice.
  HbA1c: <5.7% normal, 5.7–6.4% prediabetes, ≥6.5% diabetes range.
  Critical: any plasma glucose ≥400 mg/dL → urgent evaluation.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "ADA Standards of Care in Diabetes (2024)"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    fpg = panel.value_of(Biomarker.GLUCOSE_FASTING)
    if fpg is not None:
        if fpg >= 400:
            f = make(panel, Biomarker.GLUCOSE_FASTING, Severity.CRITICAL,
                     "Severe hyperglycemia",
                     f"Fasting glucose {fpg} mg/dL is severely elevated; this requires urgent medical evaluation.",
                     SOURCE, escalate=True, related_topics=["glucose", "safety"])
        elif fpg >= 126:
            f = make(panel, Biomarker.GLUCOSE_FASTING, Severity.ABNORMAL,
                     "Diabetes range",
                     f"Fasting glucose {fpg} mg/dL is in the diabetes range (≥126); confirm with a clinician — a single value is not a diagnosis.",
                     SOURCE, related_topics=["glucose"])
        elif fpg >= 100:
            f = make(panel, Biomarker.GLUCOSE_FASTING, Severity.BORDERLINE,
                     "Prediabetes range (impaired fasting glucose)",
                     f"Fasting glucose {fpg} mg/dL is in the impaired-fasting-glucose range (100–125).",
                     SOURCE, related_topics=["glucose"])
        elif fpg < 70:
            f = make(panel, Biomarker.GLUCOSE_FASTING, Severity.ABNORMAL,
                     "Hypoglycemia range",
                     f"Fasting glucose {fpg} mg/dL is below 70; if symptomatic this should be discussed with a clinician.",
                     SOURCE, escalate=fpg < 54, related_topics=["glucose", "safety"])
        else:
            f = make(panel, Biomarker.GLUCOSE_FASTING, Severity.NORMAL,
                     "Normal fasting glucose",
                     f"Fasting glucose {fpg} mg/dL is in the normal range (<100).",
                     SOURCE, related_topics=["glucose"])
        if f:
            findings.append(f)

    rpg = panel.value_of(Biomarker.GLUCOSE_RANDOM)
    if rpg is not None:
        if rpg >= 400:
            f = make(panel, Biomarker.GLUCOSE_RANDOM, Severity.CRITICAL,
                     "Severe hyperglycemia",
                     f"Random glucose {rpg} mg/dL is severely elevated; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["glucose", "safety"])
        elif rpg >= 200:
            f = make(panel, Biomarker.GLUCOSE_RANDOM, Severity.ABNORMAL,
                     "Random glucose in diabetes range",
                     f"Random glucose {rpg} mg/dL is in the diabetes range (≥200); confirm with a clinician.",
                     SOURCE, related_topics=["glucose"])
        else:
            f = None
        if f:
            findings.append(f)

    pp = panel.value_of(Biomarker.GLUCOSE_POSTPRANDIAL)
    if pp is not None:
        if pp >= 200:
            f = make(panel, Biomarker.GLUCOSE_POSTPRANDIAL, Severity.ABNORMAL,
                     "2-hr postprandial in diabetes range",
                     f"2-hr post-meal glucose {pp} mg/dL is ≥200 (diabetes range on OGTT criteria); confirm with a clinician.",
                     SOURCE, related_topics=["glucose"])
        elif pp >= 140:
            f = make(panel, Biomarker.GLUCOSE_POSTPRANDIAL, Severity.BORDERLINE,
                     "Impaired glucose tolerance",
                     f"2-hr post-meal glucose {pp} mg/dL is in the impaired-glucose-tolerance range (140–199).",
                     SOURCE, related_topics=["glucose"])
        else:
            f = make(panel, Biomarker.GLUCOSE_POSTPRANDIAL, Severity.NORMAL,
                     "Normal 2-hr postprandial",
                     f"2-hr post-meal glucose {pp} mg/dL is in the normal range (<140).",
                     SOURCE, related_topics=["glucose"])
        if f:
            findings.append(f)

    a1c = panel.value_of(Biomarker.HBA1C)
    if a1c is not None:
        if a1c >= 10.0:
            f = make(panel, Biomarker.HBA1C, Severity.CRITICAL,
                     "Markedly elevated HbA1c",
                     f"HbA1c {a1c}% is markedly elevated; this should be discussed with a clinician promptly.",
                     SOURCE, escalate=True, related_topics=["glucose", "safety"])
        elif a1c >= 6.5:
            f = make(panel, Biomarker.HBA1C, Severity.ABNORMAL,
                     "HbA1c in diabetes range",
                     f"HbA1c {a1c}% is in the diabetes range (≥6.5%); confirm with a clinician — a single value is not a diagnosis.",
                     SOURCE, related_topics=["glucose"])
        elif a1c >= 5.7:
            f = make(panel, Biomarker.HBA1C, Severity.BORDERLINE,
                     "HbA1c in prediabetes range",
                     f"HbA1c {a1c}% is in the prediabetes range (5.7–6.4%).",
                     SOURCE, related_topics=["glucose"])
        else:
            f = make(panel, Biomarker.HBA1C, Severity.NORMAL,
                     "Normal HbA1c",
                     f"HbA1c {a1c}% is in the normal range (<5.7%).",
                     SOURCE, related_topics=["glucose"])
        if f:
            findings.append(f)

    return findings
