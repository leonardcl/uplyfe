"""Thyroid rules — generic adult reference ranges.

TSH: 0.4–4.0 mIU/L (some labs 0.5–5.0).
Free T4: 0.8–1.8 ng/dL.
Free T3: 2.3–4.2 pg/mL.

Pregnancy alters thresholds; if `pregnant=True` we soften thresholds slightly
and add a note recommending trimester-specific ranges with a clinician.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "American Thyroid Association adult reference intervals"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    tsh = panel.value_of(Biomarker.TSH)
    if tsh is not None:
        pregnant = bool(panel.pregnant)
        if tsh > 10:
            f = make(panel, Biomarker.TSH, Severity.ABNORMAL,
                     "Markedly elevated TSH",
                     f"TSH {tsh} mIU/L is well above 10 — pattern consistent with overt hypothyroidism on screening.",
                     SOURCE, related_topics=["thyroid"])
        elif tsh > 4.0:
            f = make(panel, Biomarker.TSH, Severity.BORDERLINE,
                     "Mildly elevated TSH",
                     f"TSH {tsh} mIU/L is above the typical upper limit (~4.0). "
                     "Often classified as subclinical hypothyroidism if free T4 is normal.",
                     SOURCE, related_topics=["thyroid"])
        elif tsh < 0.1:
            f = make(panel, Biomarker.TSH, Severity.ABNORMAL,
                     "Markedly suppressed TSH",
                     f"TSH {tsh} mIU/L is markedly suppressed — pattern consistent with overt hyperthyroidism on screening.",
                     SOURCE, related_topics=["thyroid"])
        elif tsh < 0.4:
            f = make(panel, Biomarker.TSH, Severity.BORDERLINE,
                     "Mildly suppressed TSH",
                     f"TSH {tsh} mIU/L is below the typical lower limit (~0.4). "
                     "Often classified as subclinical hyperthyroidism if free T4/T3 are normal.",
                     SOURCE, related_topics=["thyroid"])
        else:
            f = make(panel, Biomarker.TSH, Severity.NORMAL,
                     "Normal TSH", f"TSH {tsh} mIU/L within typical range.",
                     SOURCE, related_topics=["thyroid"])
        if f:
            findings.append(f)
            if pregnant and (f.severity != Severity.NORMAL):
                findings.append(
                    Finding(
                        biomarker=Biomarker.TSH,
                        value=tsh,
                        unit="mIU/L",
                        severity=Severity.BORDERLINE,
                        label="Pregnancy: trimester-specific TSH range applies",
                        rationale=(
                            "Pregnancy uses trimester-specific TSH ranges. Discuss results with an obstetric "
                            "or endocrine clinician for appropriate interpretation."
                        ),
                        source="American Thyroid Association Pregnancy Guideline",
                        related_topics=["thyroid"],
                    )
                )

    ft4 = panel.value_of(Biomarker.FREE_T4)
    if ft4 is not None:
        if ft4 < 0.8:
            f = make(panel, Biomarker.FREE_T4, Severity.ABNORMAL,
                     "Low free T4",
                     f"Free T4 {ft4} ng/dL is below the typical range (0.8–1.8).",
                     SOURCE, related_topics=["thyroid"])
        elif ft4 > 1.8:
            f = make(panel, Biomarker.FREE_T4, Severity.ABNORMAL,
                     "High free T4",
                     f"Free T4 {ft4} ng/dL is above the typical range (0.8–1.8).",
                     SOURCE, related_topics=["thyroid"])
        else:
            f = make(panel, Biomarker.FREE_T4, Severity.NORMAL,
                     "Normal free T4", f"Free T4 {ft4} ng/dL within typical range.",
                     SOURCE, related_topics=["thyroid"])
        if f:
            findings.append(f)

    ft3 = panel.value_of(Biomarker.FREE_T3)
    if ft3 is not None:
        if ft3 < 2.3:
            f = make(panel, Biomarker.FREE_T3, Severity.ABNORMAL,
                     "Low free T3",
                     f"Free T3 {ft3} pg/mL is below the typical range (2.3–4.2).",
                     SOURCE, related_topics=["thyroid"])
        elif ft3 > 4.2:
            f = make(panel, Biomarker.FREE_T3, Severity.ABNORMAL,
                     "High free T3",
                     f"Free T3 {ft3} pg/mL is above the typical range (2.3–4.2).",
                     SOURCE, related_topics=["thyroid"])
        else:
            f = make(panel, Biomarker.FREE_T3, Severity.NORMAL,
                     "Normal free T3", f"Free T3 {ft3} pg/mL within typical range.",
                     SOURCE, related_topics=["thyroid"])
        if f:
            findings.append(f)

    return findings
