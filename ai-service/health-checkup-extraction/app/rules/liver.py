"""Liver enzyme rules — common adult reference ranges.

Sources: AACC, AASLD guidance. Lab-to-lab variation exists; thresholds below are
typical adult ranges and should be considered screening-level, not diagnostic.

ALT: men ≤40, women ≤35 U/L (some guidance uses ≤30/≤19; we use ≤40/≤35 conservatively).
AST: ≤40 U/L (both sexes).
ALP: 40–129 U/L.
GGT: men ≤55, women ≤38 U/L.
Bilirubin total: 0.1–1.2 mg/dL.
Bilirubin direct: 0.0–0.3 mg/dL.
Albumin: 3.5–5.0 g/dL.

Critical: any aminotransferase ≥10× ULN (≈ ≥400 U/L) → urgent evaluation.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "AACC reference intervals; AASLD 2017 ALT guidance"


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    alt = panel.value_of(Biomarker.ALT)
    alt_ul = 40 if panel.sex != "female" else 35
    if alt is not None:
        if alt >= 400:
            f = make(panel, Biomarker.ALT, Severity.CRITICAL,
                     "Markedly elevated ALT",
                     f"ALT {alt} U/L is ≥10× the upper reference limit; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["liver", "safety"])
        elif alt > 2 * alt_ul:
            f = make(panel, Biomarker.ALT, Severity.ABNORMAL,
                     "Elevated ALT (>2× upper limit)",
                     f"ALT {alt} U/L is more than twice the typical upper limit ({alt_ul}).",
                     SOURCE, related_topics=["liver"])
        elif alt > alt_ul:
            f = make(panel, Biomarker.ALT, Severity.BORDERLINE,
                     "Mildly elevated ALT",
                     f"ALT {alt} U/L is mildly above the typical upper limit (~{alt_ul}).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.ALT, Severity.NORMAL,
                     "Normal ALT", f"ALT {alt} U/L within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    ast = panel.value_of(Biomarker.AST)
    if ast is not None:
        if ast >= 400:
            f = make(panel, Biomarker.AST, Severity.CRITICAL,
                     "Markedly elevated AST",
                     f"AST {ast} U/L is severely elevated; urgent evaluation is recommended.",
                     SOURCE, escalate=True, related_topics=["liver", "safety"])
        elif ast > 80:
            f = make(panel, Biomarker.AST, Severity.ABNORMAL,
                     "Elevated AST (>2× upper limit)",
                     f"AST {ast} U/L is more than twice the typical upper limit (~40).",
                     SOURCE, related_topics=["liver"])
        elif ast > 40:
            f = make(panel, Biomarker.AST, Severity.BORDERLINE,
                     "Mildly elevated AST",
                     f"AST {ast} U/L is mildly above the typical upper limit (~40).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.AST, Severity.NORMAL,
                     "Normal AST", f"AST {ast} U/L within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    # AST/ALT ratio — De Ritis (informational only)
    if alt is not None and ast is not None and alt > 0:
        ratio = ast / alt
        if ast > alt_ul or alt > alt_ul:
            if ratio >= 2.0:
                findings.append(
                    Finding(
                        biomarker=Biomarker.AST,
                        value=round(ratio, 2),
                        unit="ratio",
                        severity=Severity.BORDERLINE,
                        label="AST/ALT ratio ≥ 2 (alcoholic-pattern)",
                        rationale=(
                            f"AST/ALT ratio is {ratio:.2f}. A ratio ≥2 with elevated enzymes is classically "
                            "associated with alcohol-related liver disease, but is not diagnostic on its own."
                        ),
                        source="De Ritis (1957); AASLD",
                        related_topics=["liver"],
                    )
                )

    alp = panel.value_of(Biomarker.ALP)
    if alp is not None:
        if alp > 300:
            f = make(panel, Biomarker.ALP, Severity.ABNORMAL,
                     "Markedly elevated alkaline phosphatase",
                     f"ALP {alp} U/L is well above the typical adult range (40–129).",
                     SOURCE, related_topics=["liver"])
        elif alp > 129:
            f = make(panel, Biomarker.ALP, Severity.BORDERLINE,
                     "Mildly elevated alkaline phosphatase",
                     f"ALP {alp} U/L is above the typical adult range (40–129).",
                     SOURCE, related_topics=["liver"])
        elif alp < 40:
            f = make(panel, Biomarker.ALP, Severity.BORDERLINE,
                     "Low alkaline phosphatase",
                     f"ALP {alp} U/L is below the typical adult range (40–129).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.ALP, Severity.NORMAL,
                     "Normal alkaline phosphatase", f"ALP {alp} U/L within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    ggt = panel.value_of(Biomarker.GGT)
    if ggt is not None:
        ggt_ul = 38 if panel.sex == "female" else 55
        if ggt > 3 * ggt_ul:
            f = make(panel, Biomarker.GGT, Severity.ABNORMAL,
                     "Elevated GGT",
                     f"GGT {ggt} U/L is well above the typical upper limit (~{ggt_ul}).",
                     SOURCE, related_topics=["liver"])
        elif ggt > ggt_ul:
            f = make(panel, Biomarker.GGT, Severity.BORDERLINE,
                     "Mildly elevated GGT",
                     f"GGT {ggt} U/L is above the typical upper limit (~{ggt_ul}).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.GGT, Severity.NORMAL,
                     "Normal GGT", f"GGT {ggt} U/L within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    bili = panel.value_of(Biomarker.BILIRUBIN_TOTAL)
    if bili is not None:
        if bili > 3.0:
            f = make(panel, Biomarker.BILIRUBIN_TOTAL, Severity.ABNORMAL,
                     "Markedly elevated total bilirubin",
                     f"Total bilirubin {bili} mg/dL is well above the typical upper limit (~1.2). "
                     "Jaundice may be present at this level.",
                     SOURCE, escalate=bili > 5.0, related_topics=["liver", "safety"])
        elif bili > 1.2:
            f = make(panel, Biomarker.BILIRUBIN_TOTAL, Severity.BORDERLINE,
                     "Mildly elevated total bilirubin",
                     f"Total bilirubin {bili} mg/dL is above the typical upper limit (~1.2).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.BILIRUBIN_TOTAL, Severity.NORMAL,
                     "Normal total bilirubin", f"Total bilirubin {bili} mg/dL within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    alb = panel.value_of(Biomarker.ALBUMIN)
    if alb is not None:
        if alb < 3.0:
            f = make(panel, Biomarker.ALBUMIN, Severity.ABNORMAL,
                     "Low albumin",
                     f"Albumin {alb} g/dL is below the typical range (3.5–5.0). Possible causes include "
                     "liver synthetic dysfunction, malnutrition, or protein loss.",
                     SOURCE, related_topics=["liver"])
        elif alb < 3.5:
            f = make(panel, Biomarker.ALBUMIN, Severity.BORDERLINE,
                     "Mildly low albumin",
                     f"Albumin {alb} g/dL is mildly below the typical range (3.5–5.0).",
                     SOURCE, related_topics=["liver"])
        else:
            f = make(panel, Biomarker.ALBUMIN, Severity.NORMAL,
                     "Normal albumin", f"Albumin {alb} g/dL within typical range.",
                     SOURCE, related_topics=["liver"])
        if f:
            findings.append(f)

    return findings
