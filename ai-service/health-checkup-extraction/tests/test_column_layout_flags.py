"""Regression tests for the column scanner's flag-line tolerance.

Real Indonesian lab reports (Medifarma, Kimia Farma) wedge a single-letter
abnormality flag ("L", "H") on its own line BETWEEN the biomarker label and
the actual value. The previous column scanner stopped at the flag line and
silently dropped the entire reading."""
from __future__ import annotations

from app.models import Biomarker
from app.parsers import regex_extract_panel


def test_glucose_with_low_flag_in_column_layout():
    """Medifarma format:
        Glukosa Puasa
         L
        71
        mg/dL
        74 - 99 : Normal
        100 - 125 : ...
    """
    text = """  Glukosa Puasa
 L
71
mg/dL
74 -  99  : Normal
100 - 125 : Berisiko
Diabetes melitus
>=126     : Diabetes
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.GLUCOSE_FASTING]
    assert v.value == 71
    assert v.unit == "mg/dL"
    # Take FIRST range only (74-99 = "Normal"), not subsequent risk-tier ranges.
    assert v.reference_low == 74
    assert v.reference_high == 99


def test_ureum_with_low_flag_indonesian():
    text = """  Ureum Darah
 L
10
mg/dL
13 - 43
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.BUN]
    assert v.value == 10
    assert v.reference_low == 13
    assert v.reference_high == 43


def test_high_flag_in_column_layout():
    text = """Trigliserida
H
210
mg/dL
< 150
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.TRIGLYCERIDES]
    assert v.value == 210
    assert v.reference_high == 150


def test_egfr_unit_with_kdigo_format():
    """eGFR uses 'mL/min/1.73 m^2' which has slashes and a body-surface-area
    suffix — must match as a single unit token."""
    text = """eGFR
111
mL/min/1.73 m^2
>=90 : Normal
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.EGFR]
    assert v.value == 111


def test_bracketed_flag_in_column_layout():
    text = """Hemoglobin
[L]
10.5
g/dL
13.5 - 17.5
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.HEMOGLOBIN]
    assert v.value == 10.5


def test_normal_value_no_flag_still_works():
    """Sanity check - flag-skip logic must not break the normal case."""
    text = """Kreatinin
1.05
mg/dL
0.7 - 1.3
"""
    [v] = [x for x in regex_extract_panel(text) if x.biomarker == Biomarker.CREATININE]
    assert v.value == 1.05
    assert v.reference_low == 0.7
    assert v.reference_high == 1.3
