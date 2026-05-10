"""Tests for unit-inference fallback in the normalizer.

Real Indonesian lab reports sometimes report cholesterol/glucose in mmol/L but
the unit string fails to extract from the PDF. Without inference, the value
flows through as if it were already in mg/dL — turning a perfectly normal
5.4 mmol/L cholesterol into a critically-low '5.4 mg/dL' reading.
"""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue
from app.normalize import to_canonical


def _panel(values):
    return LabPanel(age=40, sex="male", values=[
        LabValue(biomarker=b, value=v, unit=u) for b, v, u in values
    ])


def test_orphan_cholesterol_in_mmol_range_is_inferred():
    """5.4 with no unit → must be treated as 5.4 mmol/L → ~209 mg/dL,
    not '5.4 mg/dL' (which would be biologically implausible)."""
    panel = _panel([(Biomarker.TOTAL_CHOLESTEROL, 5.4, "")])
    canon, warnings = to_canonical(panel)
    chol = canon.values[0]
    assert chol.value > 100  # converted to mg/dL
    assert chol.value < 300
    assert chol.unit == "mg/dL"
    assert any("inferred" in w.lower() for w in warnings)


def test_orphan_ldl_inferred():
    panel = _panel([(Biomarker.LDL, 4.4, "")])
    canon, _ = to_canonical(panel)
    assert canon.values[0].value > 100
    assert canon.values[0].value < 250


def test_orphan_glucose_inferred():
    panel = _panel([(Biomarker.GLUCOSE_FASTING, 6.1, "")])
    canon, _ = to_canonical(panel)
    # 6.1 mmol/L * 18.0182 = 109.9 mg/dL
    assert 100 < canon.values[0].value < 120


def test_orphan_creatinine_in_umol_range_is_inferred():
    """High value (88) with no unit suggests µmol/L → ~1.0 mg/dL."""
    panel = _panel([(Biomarker.CREATININE, 88, "")])
    canon, _ = to_canonical(panel)
    assert 0.8 < canon.values[0].value < 1.2


def test_in_range_value_left_alone():
    """A clearly mg/dL value (200) must NOT be inferred as mmol/L."""
    panel = _panel([(Biomarker.TOTAL_CHOLESTEROL, 200, "")])
    canon, _ = to_canonical(panel)
    assert canon.values[0].value == 200


def test_explicit_unit_takes_precedence():
    """An explicit 'mg/dL' should never trigger inference even for low values."""
    panel = _panel([(Biomarker.TOTAL_CHOLESTEROL, 45, "mg/dL")])
    canon, _ = to_canonical(panel)
    # 45 mg/dL is implausibly low cholesterol, but we trust the explicit unit.
    assert canon.values[0].value == 45


def test_explicit_mmol_l_still_works():
    """The pre-existing mmol/L conversion path should still work — inference
    is a fallback, not a replacement."""
    panel = _panel([(Biomarker.TOTAL_CHOLESTEROL, 5.4, "mmol/L")])
    canon, _ = to_canonical(panel)
    assert canon.values[0].value > 100  # converted
    assert canon.values[0].unit == "mg/dL"
