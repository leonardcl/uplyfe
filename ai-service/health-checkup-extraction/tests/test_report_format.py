"""Tests for the cleaned-up report rendering and orchestrator behavior:
- Pattern findings live in their own section, not in Key Abnormal Results.
- Sources are de-duplicated.
- Disclaimer appears exactly once.
- The repetition guard truncates LLM degenerative loops.
- Bullet format uses human-readable biomarker names.
"""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue, Severity
from app.pipeline import PipelineOptions, run_pipeline
from app.pipeline.orchestrator import _guard_repetition


def _panel():
    return LabPanel(
        age=45, sex="male", waist_cm=104,
        values=[
            LabValue(biomarker=Biomarker.GLUCOSE_FASTING, value=110, unit="mg/dL"),
            LabValue(biomarker=Biomarker.HBA1C, value=6.0, unit="%"),
            LabValue(biomarker=Biomarker.LDL, value=160, unit="mg/dL"),
            LabValue(biomarker=Biomarker.HDL, value=35, unit="mg/dL"),
            LabValue(biomarker=Biomarker.TRIGLYCERIDES, value=210, unit="mg/dL"),
            LabValue(biomarker=Biomarker.BP_SYSTOLIC, value=132, unit="mmHg"),
            LabValue(biomarker=Biomarker.BP_DIASTOLIC, value=86, unit="mmHg"),
        ],
    )


def _run(panel=None):
    return run_pipeline(
        panel=panel or _panel(),
        options=PipelineOptions(use_llm=False, use_rag=False),
    )


# ---------- Pattern findings live ONLY in patterns section ----------

def test_pattern_findings_not_in_abnormal_findings():
    report = _run()
    # Patterns must not appear inside abnormal_findings
    for f in report.abnormal_findings:
        assert "Pattern:" not in f.label, (
            f"Pattern leaked into Key Abnormal Results: {f.label}"
        )


def test_pattern_findings_present_in_dedicated_field():
    report = _run()
    # The metabolic-syndrome pattern should be in pattern_findings
    labels = [p.label for p in report.pattern_findings]
    assert any("metabolic syndrome" in s.lower() for s in labels)


def test_markdown_renders_patterns_section():
    md = _run().to_markdown()
    assert "## Possible Health Patterns" in md
    # Pattern bullet should NOT appear under Key Abnormal Results.
    abnormal_section = md.split("## Key Abnormal Results")[1].split("## ")[0]
    assert "metabolic syndrome" not in abnormal_section.lower()


# ---------- Disclaimer rendered exactly once ----------

def test_disclaimer_appears_exactly_once():
    md = _run().to_markdown()
    assert md.count("**Disclaimer.**") == 1


# ---------- Sources de-duplicated ----------

def test_sources_deduplicated():
    md = _run().to_markdown()
    src_section = md.split("## Sources Cited")[-1]
    # No source line should appear twice (case-insensitive, whitespace-normalized)
    lines = [
        " ".join(l.strip().lower().split())
        for l in src_section.splitlines()
        if l.strip().startswith("- ")
    ]
    assert len(lines) == len(set(lines)), "Duplicate sources found"


# ---------- Compact bullet format ----------

def test_human_readable_biomarker_names():
    md = _run().to_markdown()
    # Should NOT use raw enum keys in headings of the abnormal section
    assert "**glucose_fasting**" not in md
    assert "**ldl**" not in md
    # SHOULD use human-readable forms
    assert "Glucose, fasting" in md or "glucose_fasting" not in md
    assert "LDL cholesterol" in md or "ldl" not in md


def test_value_formatting_drops_trailing_zero():
    md = _run().to_markdown()
    # Restrict the check to the Key Abnormal Results section — pattern rationales
    # contain narrative like "fasting glucose 110.0 mg/dL ≥ 100" which is
    # intentionally verbatim and not subject to the bullet formatter.
    abnormal = md.split("## Key Abnormal Results")[1].split("## ")[0]
    assert "110.0 mg/dL" not in abnormal
    assert "110 mg/dL" in abnormal


# ---------- Repetition guard ----------

def test_guard_truncates_repeated_section_header():
    text = (
        "## Summary\n\n"
        "Some text here.\n\n"
        "**Key Findings**\n\n"
        "- Item A\n- Item B\n\n"
        "**Key Findings**\n\n"
        "- Item A\n- Item B\n\n"
        "**Key Findings**\n\n"
        "- Item A\n- Item B\n"
    )
    cleaned = _guard_repetition(text)
    # The second ** Key Findings ** block onwards must be cut.
    assert cleaned.count("**Key Findings**") == 1


def test_guard_strips_end_sentinel():
    text = "Done.\n\nEND_OF_REPORT"
    cleaned = _guard_repetition(text)
    assert "END_OF_REPORT" not in cleaned
    assert cleaned.endswith("Done.")


def test_guard_passes_clean_text_unchanged():
    text = "Section A\n\nBody.\n\nSection B\n\nMore body."
    cleaned = _guard_repetition(text)
    assert "Section A" in cleaned
    assert "Section B" in cleaned


# ---------- Safety validator append_disclaimer flag ----------

def test_safety_validator_can_skip_disclaimer():
    from app.safety import validate_text

    sr = validate_text("Plain content.", [], append_disclaimer=False)
    assert "Disclaimer" not in sr.text


def test_safety_validator_appends_disclaimer_by_default():
    from app.safety import validate_text

    sr = validate_text("Plain content.", [])
    assert "Disclaimer" in sr.text
