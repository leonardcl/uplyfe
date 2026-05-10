"""Tests that the regex extractor captures printed reference ranges into
LabValue.reference_low / reference_high — they're then used by the rules
engine to override our built-in thresholds with the lab's own."""
from __future__ import annotations

from app.models import Biomarker
from app.parsers import regex_extract_panel


def test_inline_range_extracted():
    text = "Total Cholesterol     230   mg/dL    <200"
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.TOTAL_CHOLESTEROL]
    assert v.value == 230
    assert v.reference_low is None
    assert v.reference_high == 200


def test_inline_range_two_sided():
    text = "Glucose, Fasting    110   mg/dL    70-99"
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.GLUCOSE_FASTING]
    assert v.reference_low == 70
    assert v.reference_high == 99


def test_inline_range_greater_than():
    text = "HDL Cholesterol     35   mg/dL    >40"
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.HDL]
    assert v.reference_low == 40
    assert v.reference_high is None


def test_column_layout_with_value_and_range_labels():
    """360 Health Vectors-style layout — 'Value:' / 'Range:' as separate lines."""
    text = """
    Total Cholesterol
    Value:   5.40
    Range:   0.0-5.17
    """
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.TOTAL_CHOLESTEROL]
    assert v.reference_low == 0.0
    assert v.reference_high == 5.17


def test_column_value_with_range_unit_in_alternate_unit():
    """Indian/SI lab template: cholesterol 5.40 with range 0-5.17 — unit
    string is missing from the column block, but range_high=5.17 is too low
    for mg/dL so unit inference (driven by the value+range) decides mmol/L."""
    text = """
    Total Cholesterol
    Value:   5.40
    Range:   0.0-5.17
    """
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.TOTAL_CHOLESTEROL]
    # The regex alone shouldn't infer units — that happens during normalization.
    # We just confirm the range comes through cleanly.
    assert v.value == 5.40
    assert v.reference_high == 5.17
