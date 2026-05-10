"""Lipid rules — NCEP ATP III thresholds, AHA/ACC 2018 framing.

Total cholesterol: <200 desirable, 200–239 borderline-high, ≥240 high.
LDL: <100 optimal, 100–129 near-optimal, 130–159 borderline-high, 160–189 high, ≥190 very high.
HDL: <40 (men) / <50 (women) low; ≥60 protective.
Triglycerides: <150 normal, 150–199 borderline-high, 200–499 high, ≥500 very high (pancreatitis risk).
Non-HDL = TC − HDL: <130 desirable, 130–159 above desirable, 160–189 borderline,
                    190–219 high, ≥220 very high.
"""
from __future__ import annotations

from app.models.biomarkers import Biomarker
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel
from ._helpers import make


SOURCE = "NCEP ATP III; AHA/ACC 2018 Cholesterol Guideline"


def _hdl_low_threshold(sex: str) -> int:
    return 50 if sex == "female" else 40


def evaluate(panel: LabPanel) -> list[Finding]:
    findings: list[Finding] = []

    tc = panel.value_of(Biomarker.TOTAL_CHOLESTEROL)
    if tc is not None:
        if tc >= 240:
            sev, label = Severity.ABNORMAL, "High total cholesterol"
        elif tc >= 200:
            sev, label = Severity.BORDERLINE, "Borderline-high total cholesterol"
        else:
            sev, label = Severity.NORMAL, "Desirable total cholesterol"
        f = make(panel, Biomarker.TOTAL_CHOLESTEROL, sev, label,
                 f"Total cholesterol {tc} mg/dL.",
                 SOURCE, related_topics=["lipids"])
        if f:
            findings.append(f)

    ldl = panel.value_of(Biomarker.LDL)
    if ldl is not None:
        if ldl >= 190:
            sev, label = Severity.ABNORMAL, "Very high LDL"
        elif ldl >= 160:
            sev, label = Severity.ABNORMAL, "High LDL"
        elif ldl >= 130:
            sev, label = Severity.BORDERLINE, "Borderline-high LDL"
        elif ldl >= 100:
            sev, label = Severity.BORDERLINE, "Near-optimal LDL"
        else:
            sev, label = Severity.NORMAL, "Optimal LDL"
        f = make(panel, Biomarker.LDL, sev, label,
                 f"LDL cholesterol {ldl} mg/dL.",
                 SOURCE, related_topics=["lipids"])
        if f:
            findings.append(f)

    hdl = panel.value_of(Biomarker.HDL)
    if hdl is not None:
        low_t = _hdl_low_threshold(panel.sex)
        if hdl < low_t:
            sev, label = Severity.ABNORMAL, "Low HDL"
            rationale = (
                f"HDL {hdl} mg/dL is below the {low_t} mg/dL threshold for {panel.sex or 'adults'}. "
                "Lower HDL is associated with higher cardiovascular risk."
            )
        elif hdl >= 60:
            sev, label = Severity.NORMAL, "Protective HDL"
            rationale = f"HDL {hdl} mg/dL (≥60) is associated with lower cardiovascular risk."
        else:
            sev, label = Severity.NORMAL, "Acceptable HDL"
            rationale = f"HDL {hdl} mg/dL."
        f = make(panel, Biomarker.HDL, sev, label, rationale, SOURCE, related_topics=["lipids"])
        if f:
            findings.append(f)

    tg = panel.value_of(Biomarker.TRIGLYCERIDES)
    if tg is not None:
        if tg >= 1000:
            sev, label = Severity.CRITICAL, "Severe hypertriglyceridemia (pancreatitis risk)"
            esc = True
        elif tg >= 500:
            sev, label = Severity.ABNORMAL, "Very high triglycerides"
            esc = False
        elif tg >= 200:
            sev, label = Severity.ABNORMAL, "High triglycerides"
            esc = False
        elif tg >= 150:
            sev, label = Severity.BORDERLINE, "Borderline-high triglycerides"
            esc = False
        else:
            sev, label = Severity.NORMAL, "Normal triglycerides"
            esc = False
        f = make(panel, Biomarker.TRIGLYCERIDES, sev, label,
                 f"Triglycerides {tg} mg/dL.",
                 SOURCE, escalate=esc, related_topics=["lipids", "safety" if esc else "lipids"])
        if f:
            findings.append(f)

    # Non-HDL — derive if not present, finding only if it adds info.
    non_hdl = panel.value_of(Biomarker.NON_HDL)
    if non_hdl is None and tc is not None and hdl is not None:
        non_hdl = round(tc - hdl, 1)
    if non_hdl is not None and tc is not None:
        if non_hdl >= 220:
            sev, label = Severity.ABNORMAL, "Very high non-HDL cholesterol"
        elif non_hdl >= 190:
            sev, label = Severity.ABNORMAL, "High non-HDL cholesterol"
        elif non_hdl >= 160:
            sev, label = Severity.BORDERLINE, "Borderline non-HDL cholesterol"
        elif non_hdl >= 130:
            sev, label = Severity.BORDERLINE, "Above-desirable non-HDL cholesterol"
        else:
            sev, label = Severity.NORMAL, "Desirable non-HDL cholesterol"
        # We only attach a finding if non-HDL was supplied OR it's actionable.
        if panel.get(Biomarker.NON_HDL) is not None or sev != Severity.NORMAL:
            findings.append(
                Finding(
                    biomarker=Biomarker.NON_HDL,
                    value=non_hdl,
                    unit="mg/dL",
                    severity=sev,
                    label=label,
                    rationale=f"Non-HDL cholesterol = {non_hdl} mg/dL (computed as TC − HDL when not directly reported).",
                    source=SOURCE,
                    related_topics=["lipids"],
                )
            )

    return findings
