"""Inflammation markers — CRP and ESR.

CRP (high-sensitivity, AHA cardiovascular framing):
  <1.0 mg/L low CV risk; 1.0–3.0 average; >3.0 high.
  CRP >10 mg/L typically indicates acute infection / non-cardiac inflammation.
ESR (typical adult):
  men <50: ≤15; men ≥50: ≤20; women <50: ≤20; women ≥50: ≤30.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "AHA/CDC hs-CRP scientific statement; AACC ESR reference"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    crp = panel.value_of(Biomarker.CRP)
    if crp is not None:
        if crp > 10:
            f = make(panel, Biomarker.CRP, Severity.ABNORMAL,
                     "Elevated CRP — acute inflammation pattern",
                     f"CRP {crp} mg/L is >10, often reflecting active infection or another acute inflammatory process. "
                     "Not specific for cardiovascular risk in this range.",
                     SOURCE, related_topics=["inflammation"])
        elif crp > 3.0:
            f = make(panel, Biomarker.CRP, Severity.BORDERLINE,
                     "High hs-CRP (above-average CV risk)",
                     f"hs-CRP {crp} mg/L is in the higher cardiovascular-risk band (>3).",
                     SOURCE, related_topics=["inflammation", "lipids"])
        elif crp >= 1.0:
            f = make(panel, Biomarker.CRP, Severity.NORMAL,
                     "Average hs-CRP",
                     f"hs-CRP {crp} mg/L is in the average cardiovascular-risk band (1–3).",
                     SOURCE, related_topics=["inflammation"])
        else:
            f = make(panel, Biomarker.CRP, Severity.NORMAL,
                     "Low hs-CRP",
                     f"hs-CRP {crp} mg/L is in the lower cardiovascular-risk band (<1).",
                     SOURCE, related_topics=["inflammation"])
        if f:
            findings.append(f)

    esr = panel.value_of(Biomarker.ESR)
    if esr is not None:
        if panel.sex == "female":
            limit = 20 if panel.age < 50 else 30
        else:
            limit = 15 if panel.age < 50 else 20
        if esr > limit * 3:
            f = make(panel, Biomarker.ESR, Severity.ABNORMAL,
                     "Markedly elevated ESR",
                     f"ESR {esr} mm/hr is well above the typical upper limit (~{limit}).",
                     SOURCE, related_topics=["inflammation"])
        elif esr > limit:
            f = make(panel, Biomarker.ESR, Severity.BORDERLINE,
                     "Elevated ESR",
                     f"ESR {esr} mm/hr is above the typical upper limit (~{limit} for {panel.sex or 'adults'}).",
                     SOURCE, related_topics=["inflammation"])
        else:
            f = make(panel, Biomarker.ESR, Severity.NORMAL,
                     "Normal ESR", f"ESR {esr} mm/hr within typical range (≤{limit}).",
                     SOURCE, related_topics=["inflammation"])
        if f:
            findings.append(f)

    return findings
