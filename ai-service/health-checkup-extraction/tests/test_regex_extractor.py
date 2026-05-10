"""Regex extractor smoke tests — typical lab-report line shapes."""
from __future__ import annotations

from app.parsers import regex_extract_panel
from app.parsers.regex_extractor import extract_blood_pressure
from app.models import Biomarker


SAMPLE_REPORT = """
COMPREHENSIVE METABOLIC PANEL

Glucose, Fasting           110     mg/dL    70-99
HbA1c                       6.0    %        <5.7
Total Cholesterol           230    mg/dL    <200
LDL Cholesterol             160    mg/dL    <100
HDL Cholesterol              35    mg/dL    >40
Triglycerides               210    mg/dL    <150
ALT (SGPT)                   55    U/L      <40
AST (SGOT)                   40    U/L      <40
Creatinine                  1.0    mg/dL    0.7-1.3
Hemoglobin                 14.5    g/dL     13.5-17.5
WBC                         7.0    10^3/uL  4.5-11
Platelets                   250    10^3/uL  150-450

Vitals: BP 132/84 mmHg
"""


def test_regex_finds_core_biomarkers():
    values = regex_extract_panel(SAMPLE_REPORT)
    keys = {v.biomarker for v in values}
    expected = {
        Biomarker.GLUCOSE_FASTING, Biomarker.HBA1C, Biomarker.TOTAL_CHOLESTEROL,
        Biomarker.LDL, Biomarker.HDL, Biomarker.TRIGLYCERIDES, Biomarker.ALT,
        Biomarker.AST, Biomarker.CREATININE, Biomarker.HEMOGLOBIN, Biomarker.WBC,
        Biomarker.PLATELETS,
    }
    assert expected.issubset(keys)


def test_regex_blood_pressure():
    sys, dia = extract_blood_pressure(SAMPLE_REPORT)
    assert sys == 132 and dia == 84


def test_regex_picks_correct_value_not_reference_range():
    values = regex_extract_panel(SAMPLE_REPORT)
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.LDL].value == 160
    assert by_b[Biomarker.HDL].value == 35
