"""Tests for the key_insights field that drives the UI hero cards."""
from __future__ import annotations

from app.models import Biomarker
from app.pipeline import PipelineOptions, run_pipeline


def _build_panel(values: list[dict]) -> dict:
    return {"age": 35, "sex": "male", "values": values}


def _run(panel_dict: dict):
    from app.models import LabPanel
    panel = LabPanel.model_validate(panel_dict)
    return run_pipeline(panel=panel, options=PipelineOptions(use_llm=False, use_rag=False))


def test_three_cards_when_all_three_biomarkers_present():
    r = _run(_build_panel([
        {"biomarker": "total_cholesterol", "value": 245, "unit": "mg/dL"},
        {"biomarker": "glucose_fasting",   "value": 110, "unit": "mg/dL"},
        {"biomarker": "vitamin_d_25oh",    "value": 18,  "unit": "ng/mL"},
    ]))
    keys = [k.key for k in r.key_insights]
    assert keys == ["cholesterol", "blood_sugar", "vitamin_d"]


def test_card_omitted_when_biomarker_missing():
    r = _run(_build_panel([
        {"biomarker": "total_cholesterol", "value": 180, "unit": "mg/dL"},
    ]))
    keys = [k.key for k in r.key_insights]
    assert keys == ["cholesterol"]


def test_uses_ldl_as_cholesterol_fallback():
    r = _run(_build_panel([
        {"biomarker": "ldl", "value": 165, "unit": "mg/dL"},
    ]))
    [insight] = r.key_insights
    assert insight.key == "cholesterol"
    assert insight.biomarker == Biomarker.LDL
    assert insight.value == 165


def test_uses_hba1c_as_blood_sugar_fallback():
    r = _run(_build_panel([
        {"biomarker": "hba1c", "value": 6.4, "unit": "%"},
    ]))
    [insight] = r.key_insights
    assert insight.key == "blood_sugar"
    assert insight.biomarker == Biomarker.HBA1C
    assert insight.value == 6.4


def test_status_high_for_abnormal_cholesterol():
    r = _run(_build_panel([
        {"biomarker": "total_cholesterol", "value": 280, "unit": "mg/dL"},
    ]))
    [insight] = r.key_insights
    assert insight.status in ("high", "critical")


def test_status_optimal_when_no_finding():
    r = _run(_build_panel([
        # Solidly in-range — should not trigger any rule.
        {"biomarker": "total_cholesterol", "value": 175, "unit": "mg/dL"},
    ]))
    [insight] = r.key_insights
    assert insight.status in ("optimal", "normal")


def test_status_low_for_vitamin_d_deficiency():
    r = _run(_build_panel([
        {"biomarker": "vitamin_d_25oh", "value": 12, "unit": "ng/mL"},
    ]))
    [insight] = r.key_insights
    assert insight.status in ("low", "critical")


def test_status_borderline_for_prediabetes_glucose():
    r = _run(_build_panel([
        {"biomarker": "glucose_fasting", "value": 110, "unit": "mg/dL"},
    ]))
    [insight] = r.key_insights
    # 100–125 is impaired fasting glucose → borderline in our rules.
    assert insight.status == "borderline"


def test_summary_uses_rule_label_when_finding_exists():
    r = _run(_build_panel([
        {"biomarker": "glucose_fasting", "value": 130, "unit": "mg/dL"},
    ]))
    [insight] = r.key_insights
    assert "diabet" in insight.summary.lower() or "elevated" in insight.summary.lower()


def test_empty_panel_yields_no_insights():
    r = _run(_build_panel([
        {"biomarker": "creatinine", "value": 1.0, "unit": "mg/dL"},
    ]))
    assert r.key_insights == []
