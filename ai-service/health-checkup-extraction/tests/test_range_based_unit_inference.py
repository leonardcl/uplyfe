"""Tests for range-based unit inference.

When a lab prints a reference range that's wildly above the canonical-unit
upper bound, the report is in the alternate unit and we should infer it. This
catches cases where value alone is ambiguous (e.g. bilirubin 19.5 plausibly
fits both µmol/L upper of ~21 AND mg/dL critical >15)."""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue
from app.normalize import to_canonical


def _panel(values):
    return LabPanel(age=40, sex="male", values=[
        LabValue(biomarker=b, value=v, unit=u, reference_low=lo, reference_high=hi)
        for b, v, u, lo, hi in values
    ])


def test_bilirubin_with_high_ref_inferred_as_umol():
    """Bilirubin 19.5 is ambiguous (both µmol/L and mg/dL critical interpretations
    exist). Printed reference high of 15.0 is far above mg/dL upper (~1.2),
    so unit must be µmol/L → ~1.14 mg/dL."""
    panel = _panel([(Biomarker.BILIRUBIN_TOTAL, 19.5, "", 0.0, 15.0)])
    canon, _ = to_canonical(panel)
    [v] = canon.values
    assert 0.5 < v.value < 2.0  # ~1.14 mg/dL
    assert v.unit == "mg/dL"


def test_uric_acid_high_range_inferred_as_umol():
    """Uric acid 225 with range 143-339 → µmol/L → ~3.78 mg/dL."""
    panel = _panel([(Biomarker.URIC_ACID, 225, "", 143, 339)])
    canon, _ = to_canonical(panel)
    [v] = canon.values
    assert 2.0 < v.value < 6.0


def test_cholesterol_with_in_range_value_not_overridden_by_range():
    """If the value is plausibly in canonical units AND the printed range
    matches, no inference happens."""
    panel = _panel([(Biomarker.TOTAL_CHOLESTEROL, 200, "mg/dL", 0, 200)])
    canon, _ = to_canonical(panel)
    [v] = canon.values
    assert v.value == 200
    assert v.unit == "mg/dL"


def test_creatinine_with_high_range_inferred_as_umol():
    """Creatinine 52 with range 45-84 → way too high for mg/dL → µmol/L."""
    panel = _panel([(Biomarker.CREATININE, 52, "", 45, 84)])
    canon, _ = to_canonical(panel)
    [v] = canon.values
    assert 0.4 < v.value < 1.0
