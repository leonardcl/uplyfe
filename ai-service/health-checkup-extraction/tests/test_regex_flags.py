"""Tests that bracketed/symbolic abnormality flags don't corrupt value extraction."""
from __future__ import annotations

from app.models import Biomarker
from app.parsers import regex_extract_panel


def test_bracketed_high_flag_between_value_and_unit():
    text = "Glucose, Fasting   110 [H] mg/dL   70-99"
    values = regex_extract_panel(text)
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.GLUCOSE_FASTING].value == 110
    assert "mg/dL" in by_b[Biomarker.GLUCOSE_FASTING].unit


def test_bracketed_low_flag_between_value_and_unit():
    text = "HDL Cholesterol   35 [L] mg/dL   >40"
    values = regex_extract_panel(text)
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.HDL].value == 35


def test_arrow_flag_between_value_and_unit():
    text = "Trigliserida   210 ↑ mg/dL   <150"
    values = regex_extract_panel(text, language="id")
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.TRIGLYCERIDES].value == 210


def test_asterisk_flag_in_brackets():
    text = "ALT (SGPT)   55 [*] U/L   <40"
    values = regex_extract_panel(text)
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.ALT].value == 55


def test_inline_flag_with_word_form_does_not_break():
    """Word-form flags ('Tinggi', 'High') aren't stripped — but they trail the
    unit, so the inline regex captures (value, unit) before reaching them."""
    text = "Kolesterol Total   230 mg/dL   <200   Tinggi"
    values = regex_extract_panel(text, language="id")
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.TOTAL_CHOLESTEROL].value == 230
    assert "mg/dL" in by_b[Biomarker.TOTAL_CHOLESTEROL].unit
