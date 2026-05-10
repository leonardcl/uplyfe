"""Unit-conversion tests — these are the bug-class the spec calls out:
   '110 mg/dL ≠ 110 mmol/L'."""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue
from app.normalize import to_canonical


def _panel(values: list[LabValue]) -> LabPanel:
    return LabPanel(age=40, sex="male", values=values)


def test_glucose_mmol_to_mgdl():
    panel = _panel([LabValue(biomarker=Biomarker.GLUCOSE_FASTING, value=6.1, unit="mmol/L")])
    canonical, warnings = to_canonical(panel)
    fpg = canonical.get(Biomarker.GLUCOSE_FASTING)
    assert fpg.unit == "mg/dL"
    # 6.1 mmol/L * 18.0182 ≈ 109.9
    assert 108 < fpg.value < 112


def test_hba1c_ifcc_to_ngsp():
    panel = _panel([LabValue(biomarker=Biomarker.HBA1C, value=42, unit="mmol/mol")])
    canonical, warnings = to_canonical(panel)
    a1c = canonical.get(Biomarker.HBA1C)
    # 42 / 10.929 + 2.15 ≈ 6.0
    assert 5.9 < a1c.value < 6.1
    assert a1c.unit == "%"
    assert any("HbA1c converted" in w for w in warnings)


def test_creatinine_umol_to_mgdl():
    panel = _panel([LabValue(biomarker=Biomarker.CREATININE, value=88, unit="µmol/L")])
    canonical, warnings = to_canonical(panel)
    cr = canonical.get(Biomarker.CREATININE)
    # 88 / 88.4 ≈ 0.995
    assert abs(cr.value - 1.0) < 0.05
    assert cr.unit == "mg/dL"


def test_calcium_mmol_to_mgdl():
    panel = _panel([LabValue(biomarker=Biomarker.CALCIUM, value=2.4, unit="mmol/L")])
    canonical, _ = to_canonical(panel)
    # 2.4 * 4.008 ≈ 9.6
    assert 9.4 < canonical.get(Biomarker.CALCIUM).value < 9.8


def test_no_change_when_already_canonical():
    panel = _panel([LabValue(biomarker=Biomarker.LDL, value=130, unit="mg/dL")])
    canonical, warnings = to_canonical(panel)
    assert canonical.get(Biomarker.LDL).value == 130
    assert warnings == []


def test_unknown_unit_passes_through_with_warning():
    panel = _panel([LabValue(biomarker=Biomarker.LDL, value=999, unit="parsec")])
    canonical, warnings = to_canonical(panel)
    assert canonical.get(Biomarker.LDL).value == 999
    assert any("Could not safely convert" in w for w in warnings)
