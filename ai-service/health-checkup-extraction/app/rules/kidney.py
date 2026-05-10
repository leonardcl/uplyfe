"""Kidney rules — KDIGO 2024 + CKD-EPI 2021 (race-free) eGFR.

eGFR (CKD-EPI 2021):
  females:  142 * min(SCr/0.7, 1)^-0.241 * max(SCr/0.7, 1)^-1.200 * 0.9938^age * 1.012
  males:    142 * min(SCr/0.9, 1)^-0.302 * max(SCr/0.9, 1)^-1.200 * 0.9938^age

Categories (KDIGO):
  ≥90 G1 normal/high; 60–89 G2 mildly decreased; 45–59 G3a; 30–44 G3b;
  15–29 G4; <15 G5 (kidney failure).
"""
from __future__ import annotations

from typing import Optional

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "KDIGO 2024 CKD Guideline; Inker et al. NEJM 2021 (CKD-EPI)"


def compute_egfr(creatinine_mg_dl: float, age: int, sex: str) -> Optional[float]:
    """CKD-EPI 2021 (race-free) eGFR."""
    if creatinine_mg_dl <= 0 or age <= 0:
        return None
    if sex == "female":
        kappa, alpha, factor = 0.7, -0.241, 1.012
    else:
        kappa, alpha, factor = 0.9, -0.302, 1.0
    scr_k = creatinine_mg_dl / kappa
    egfr = 142 * (min(scr_k, 1) ** alpha) * (max(scr_k, 1) ** -1.200) * (0.9938 ** age) * factor
    return round(egfr, 1)


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    cr = panel.value_of(Biomarker.CREATININE)
    if cr is not None:
        # Sex-specific reference (typical adult)
        if panel.sex == "female":
            lo, hi = 0.6, 1.1
        else:
            lo, hi = 0.7, 1.3
        if cr > hi:
            f = make(panel, Biomarker.CREATININE, Severity.ABNORMAL,
                     "Elevated creatinine",
                     f"Creatinine {cr} mg/dL is above the typical upper limit (~{hi}). "
                     "Elevated creatinine can reflect reduced kidney filtration, dehydration, or other causes.",
                     SOURCE, escalate=cr > 4.0, related_topics=["kidney"])
        elif cr < lo:
            f = make(panel, Biomarker.CREATININE, Severity.BORDERLINE,
                     "Low creatinine",
                     f"Creatinine {cr} mg/dL is below the typical lower limit (~{lo}). "
                     "Low creatinine is often non-pathologic (low muscle mass).",
                     SOURCE, related_topics=["kidney"])
        else:
            f = make(panel, Biomarker.CREATININE, Severity.NORMAL,
                     "Normal creatinine",
                     f"Creatinine {cr} mg/dL within typical range ({lo}–{hi}).",
                     SOURCE, related_topics=["kidney"])
        if f:
            findings.append(f)

    # eGFR — use reported value if present, otherwise compute.
    egfr = panel.value_of(Biomarker.EGFR)
    derived = False
    if egfr is None and cr is not None:
        egfr = compute_egfr(cr, panel.age, panel.sex)
        derived = True

    if egfr is not None:
        if egfr < 15:
            sev, label = Severity.CRITICAL, "Kidney failure range (G5)"
            esc = True
        elif egfr < 30:
            sev, label = Severity.ABNORMAL, "Severely decreased eGFR (G4)"
            esc = True
        elif egfr < 45:
            sev, label = Severity.ABNORMAL, "Moderately decreased eGFR (G3b)"
            esc = False
        elif egfr < 60:
            sev, label = Severity.ABNORMAL, "Moderately decreased eGFR (G3a)"
            esc = False
        elif egfr < 90:
            sev, label = Severity.BORDERLINE, "Mildly decreased eGFR (G2)"
            esc = False
        else:
            sev, label = Severity.NORMAL, "Normal eGFR (G1)"
            esc = False
        rat = (
            f"eGFR {egfr} mL/min/1.73m² ({'CKD-EPI 2021 derived from creatinine' if derived else 'reported'}). "
            f"KDIGO category: {label}."
        )
        findings.append(
            Finding(
                biomarker=Biomarker.EGFR,
                value=egfr,
                unit="mL/min/1.73m2",
                severity=sev,
                label=label,
                rationale=rat,
                source=SOURCE,
                escalate=esc,
                related_topics=["kidney", "safety" if esc else "kidney"],
            )
        )

    bun = panel.value_of(Biomarker.BUN)
    if bun is not None:
        if bun > 50:
            f = make(panel, Biomarker.BUN, Severity.ABNORMAL,
                     "Markedly elevated BUN",
                     f"BUN {bun} mg/dL is well above the typical range (7–20). "
                     "Possible causes include kidney dysfunction, dehydration, or high-protein states.",
                     SOURCE, related_topics=["kidney"])
        elif bun > 20:
            f = make(panel, Biomarker.BUN, Severity.BORDERLINE,
                     "Mildly elevated BUN",
                     f"BUN {bun} mg/dL is above the typical range (7–20).",
                     SOURCE, related_topics=["kidney"])
        elif bun < 7:
            f = make(panel, Biomarker.BUN, Severity.BORDERLINE,
                     "Low BUN",
                     f"BUN {bun} mg/dL is below the typical range (7–20). "
                     "Often non-pathologic (low protein intake, pregnancy).",
                     SOURCE, related_topics=["kidney"])
        else:
            f = make(panel, Biomarker.BUN, Severity.NORMAL,
                     "Normal BUN", f"BUN {bun} mg/dL within typical range (7–20).",
                     SOURCE, related_topics=["kidney"])
        if f:
            findings.append(f)

    ua = panel.value_of(Biomarker.URIC_ACID)
    if ua is not None:
        ua_high = 7.0 if panel.sex != "female" else 6.0
        if ua > ua_high + 2.0:
            f = make(panel, Biomarker.URIC_ACID, Severity.ABNORMAL,
                     "High uric acid (hyperuricemia)",
                     f"Uric acid {ua} mg/dL is well above the typical upper limit (~{ua_high}). "
                     "Hyperuricemia is associated with gout and kidney stones.",
                     SOURCE, related_topics=["kidney"])
        elif ua > ua_high:
            f = make(panel, Biomarker.URIC_ACID, Severity.BORDERLINE,
                     "Mildly high uric acid",
                     f"Uric acid {ua} mg/dL is above the typical upper limit (~{ua_high}).",
                     SOURCE, related_topics=["kidney"])
        else:
            f = make(panel, Biomarker.URIC_ACID, Severity.NORMAL,
                     "Normal uric acid", f"Uric acid {ua} mg/dL within typical range.",
                     SOURCE, related_topics=["kidney"])
        if f:
            findings.append(f)

    return findings
