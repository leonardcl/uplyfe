"""Vitamin D (25-OH) and B12.

25-OH Vitamin D (NIH ODS):
  <12 ng/mL — deficiency, risk of rickets/osteomalacia
  12–20 ng/mL — inadequate for bone/overall health in many adults
  20–50 ng/mL — generally considered adequate
  >50 ng/mL — possibly high; >100 ng/mL — potential adverse effects.

Vitamin B12 (NIH ODS):
  <200 pg/mL — deficiency
  200–300 pg/mL — borderline
  ≥300 pg/mL — adequate
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "NIH Office of Dietary Supplements (ODS)"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    vd = panel.value_of(Biomarker.VITAMIN_D_25OH)
    if vd is not None:
        if vd < 12:
            f = make(panel, Biomarker.VITAMIN_D_25OH, Severity.ABNORMAL,
                     "Vitamin D deficiency",
                     f"25-OH Vitamin D {vd} ng/mL is in the deficiency range (<12).",
                     SOURCE, related_topics=["vitamins"])
        elif vd < 20:
            f = make(panel, Biomarker.VITAMIN_D_25OH, Severity.BORDERLINE,
                     "Vitamin D inadequate",
                     f"25-OH Vitamin D {vd} ng/mL is in the inadequate range (12–19).",
                     SOURCE, related_topics=["vitamins"])
        elif vd > 100:
            f = make(panel, Biomarker.VITAMIN_D_25OH, Severity.ABNORMAL,
                     "Possibly high Vitamin D",
                     f"25-OH Vitamin D {vd} ng/mL is high; very high levels can have adverse effects.",
                     SOURCE, related_topics=["vitamins"])
        else:
            f = make(panel, Biomarker.VITAMIN_D_25OH, Severity.NORMAL,
                     "Adequate Vitamin D",
                     f"25-OH Vitamin D {vd} ng/mL is in the adequate range (≥20).",
                     SOURCE, related_topics=["vitamins"])
        if f:
            findings.append(f)

    b12 = panel.value_of(Biomarker.VITAMIN_B12)
    if b12 is not None:
        if b12 < 200:
            f = make(panel, Biomarker.VITAMIN_B12, Severity.ABNORMAL,
                     "Vitamin B12 deficiency range",
                     f"Vitamin B12 {b12} pg/mL is in the deficiency range (<200).",
                     SOURCE, related_topics=["vitamins"])
        elif b12 < 300:
            f = make(panel, Biomarker.VITAMIN_B12, Severity.BORDERLINE,
                     "Borderline Vitamin B12",
                     f"Vitamin B12 {b12} pg/mL is borderline (200–299).",
                     SOURCE, related_topics=["vitamins"])
        else:
            f = make(panel, Biomarker.VITAMIN_B12, Severity.NORMAL,
                     "Adequate Vitamin B12",
                     f"Vitamin B12 {b12} pg/mL is in the adequate range (≥300).",
                     SOURCE, related_topics=["vitamins"])
        if f:
            findings.append(f)

    return findings
