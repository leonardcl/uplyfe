"""Threshold tests — verify the rule engine maps the right value to the right severity.

These are intentionally boundary-heavy: ADA's published cutoffs (5.7, 6.5),
NCEP's cutoffs (130, 160, 190 for LDL), and the BP guideline cutoffs.
"""
from __future__ import annotations

import pytest

from app.models import Biomarker, LabPanel, LabValue, Severity
from app.rules import evaluate_panel


def _panel(age=40, sex="male", **values) -> LabPanel:
    lvs = []
    for biomarker_str, value in values.items():
        b = Biomarker(biomarker_str)
        unit = "mg/dL"
        if b == Biomarker.HBA1C:
            unit = "%"
        elif b in (Biomarker.BP_SYSTOLIC, Biomarker.BP_DIASTOLIC):
            unit = "mmHg"
        elif b in (Biomarker.ALT, Biomarker.AST):
            unit = "U/L"
        lvs.append(LabValue(biomarker=b, value=value, unit=unit))
    return LabPanel(age=age, sex=sex, values=lvs)


def _findings_for(panel, biomarker):
    clusters, _ = evaluate_panel(panel)
    out = []
    for c in clusters:
        for f in c.findings:
            if f.biomarker == biomarker:
                out.append(f)
    return out


# ---------- ADA HbA1c boundaries ----------

@pytest.mark.parametrize("value,expected", [
    (5.6, Severity.NORMAL),
    (5.7, Severity.BORDERLINE),  # exactly the prediabetes lower bound
    (6.4, Severity.BORDERLINE),  # last value in prediabetes range
    (6.5, Severity.ABNORMAL),    # first value in diabetes range
    (10.0, Severity.CRITICAL),
])
def test_hba1c_boundaries(value, expected):
    panel = _panel(hba1c=value)
    fs = _findings_for(panel, Biomarker.HBA1C)
    assert fs and fs[0].severity == expected


# ---------- ADA fasting glucose boundaries ----------

@pytest.mark.parametrize("value,expected", [
    (99, Severity.NORMAL),
    (100, Severity.BORDERLINE),
    (125, Severity.BORDERLINE),
    (126, Severity.ABNORMAL),
    (400, Severity.CRITICAL),
])
def test_fasting_glucose_boundaries(value, expected):
    panel = _panel(glucose_fasting=value)
    fs = _findings_for(panel, Biomarker.GLUCOSE_FASTING)
    assert fs and fs[0].severity == expected


# ---------- NCEP LDL boundaries ----------

@pytest.mark.parametrize("value,expected_label_substr", [
    (99, "Optimal"),
    (130, "Borderline-high"),
    (160, "High"),
    (190, "Very high"),
])
def test_ldl_labels(value, expected_label_substr):
    panel = _panel(ldl=value)
    fs = _findings_for(panel, Biomarker.LDL)
    assert fs and expected_label_substr.lower() in fs[0].label.lower()


# ---------- HDL sex-specific cutoffs ----------

def test_hdl_low_male_cutoff():
    panel = _panel(sex="male", hdl=39)
    fs = _findings_for(panel, Biomarker.HDL)
    assert fs and fs[0].severity == Severity.ABNORMAL

def test_hdl_low_female_cutoff():
    panel = _panel(sex="female", hdl=49)
    fs = _findings_for(panel, Biomarker.HDL)
    assert fs and fs[0].severity == Severity.ABNORMAL

def test_hdl_protective():
    panel = _panel(hdl=65)
    fs = _findings_for(panel, Biomarker.HDL)
    assert fs and "Protective" in fs[0].label


# ---------- BP boundaries (ACC/AHA 2017) ----------

@pytest.mark.parametrize("sys,dia,expected", [
    (118, 76, Severity.NORMAL),
    (122, 76, Severity.BORDERLINE),    # elevated
    (132, 84, Severity.BORDERLINE),    # stage 1
    (146, 92, Severity.ABNORMAL),      # stage 2
    (185, 122, Severity.CRITICAL),     # crisis
])
def test_bp_boundaries(sys, dia, expected):
    panel = _panel(bp_systolic=sys, bp_diastolic=dia)
    fs = _findings_for(panel, Biomarker.BP_SYSTOLIC)
    assert fs and fs[0].severity == expected


# ---------- Critical escalation flags ----------

def test_critical_glucose_escalates():
    panel = _panel(glucose_fasting=420)
    fs = _findings_for(panel, Biomarker.GLUCOSE_FASTING)
    assert fs and fs[0].escalate is True

def test_critical_bp_escalates():
    panel = _panel(bp_systolic=200, bp_diastolic=130)
    fs = _findings_for(panel, Biomarker.BP_SYSTOLIC)
    assert fs and fs[0].escalate is True


# ---------- Pattern detection ----------

def test_metabolic_syndrome_detected():
    panel = LabPanel(
        age=45, sex="male", waist_cm=104,
        values=[
            LabValue(biomarker=Biomarker.TRIGLYCERIDES, value=180, unit="mg/dL"),
            LabValue(biomarker=Biomarker.HDL, value=35, unit="mg/dL"),
            LabValue(biomarker=Biomarker.BP_SYSTOLIC, value=132, unit="mmHg"),
            LabValue(biomarker=Biomarker.BP_DIASTOLIC, value=86, unit="mmHg"),
            LabValue(biomarker=Biomarker.GLUCOSE_FASTING, value=110, unit="mg/dL"),
        ],
    )
    _, patterns = evaluate_panel(panel)
    assert any("metabolic syndrome" in p.label.lower() for p in patterns)


def test_no_metabolic_syndrome_when_only_one_criterion():
    panel = LabPanel(
        age=30, sex="male",
        values=[LabValue(biomarker=Biomarker.HDL, value=30, unit="mg/dL")],
    )
    _, patterns = evaluate_panel(panel)
    assert not any("metabolic syndrome" in p.label.lower() for p in patterns)


# ---------- eGFR derivation ----------

def test_egfr_derived_when_missing():
    panel = LabPanel(
        age=70, sex="male",
        values=[LabValue(biomarker=Biomarker.CREATININE, value=1.4, unit="mg/dL")],
    )
    clusters, _ = evaluate_panel(panel)
    egfr_findings = [f for c in clusters for f in c.findings if f.biomarker == Biomarker.EGFR]
    assert egfr_findings, "eGFR should be derived from creatinine"
