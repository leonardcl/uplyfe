"""Pipeline smoke test — runs end-to-end without LLM and without RAG.

Confirms the deterministic spine produces a structured FinalReport even when
no external service is available.
"""
from __future__ import annotations

import json
from pathlib import Path

from app.models import LabPanel, Severity
from app.pipeline import PipelineOptions, run_pipeline


SAMPLE = Path(__file__).resolve().parents[1] / "data" / "samples" / "manual_input.json"


def test_pipeline_no_llm_no_rag_produces_report():
    panel = LabPanel.model_validate(json.loads(SAMPLE.read_text()))
    report = run_pipeline(
        panel=panel,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    # The sample is constructed to exercise multiple flags.
    assert report.overall_severity != Severity.NORMAL
    assert any(f.biomarker.value == "ldl" for f in report.abnormal_findings)
    assert any(f.biomarker.value == "hdl" for f in report.abnormal_findings)
    assert report.diet_advice
    assert report.exercise_advice
    assert report.disclaimer
    md = report.to_markdown()
    assert "Health Checkup Report" in md
    assert "Disclaimer" in md


def test_pipeline_handles_unknown_unit_passthrough():
    panel = LabPanel(
        age=30, sex="female",
        values=[
            {"biomarker": "ldl", "value": 999, "unit": "parsec"},
            {"biomarker": "hba1c", "value": 5.5, "unit": "%"},
        ],  # type: ignore[list-item]
    )
    report = run_pipeline(
        panel=panel,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    # We expect a validation issue surfaced, but the pipeline does not crash.
    assert any(i.kind in ("unit", "implausible") for i in report.validation_issues)


def test_pipeline_metabolic_pattern_surfaced_in_report():
    panel = LabPanel(
        age=45, sex="male", waist_cm=104,
        values=[
            {"biomarker": "triglycerides", "value": 200, "unit": "mg/dL"},
            {"biomarker": "hdl", "value": 35, "unit": "mg/dL"},
            {"biomarker": "bp_systolic", "value": 132, "unit": "mmHg"},
            {"biomarker": "bp_diastolic", "value": 86, "unit": "mmHg"},
            {"biomarker": "glucose_fasting", "value": 110, "unit": "mg/dL"},
        ],  # type: ignore[list-item]
    )
    report = run_pipeline(
        panel=panel, options=PipelineOptions(use_llm=False, use_rag=False)
    )
    assert any("metabolic syndrome" in p.lower() for p in report.pattern_notes)
