"""Tests for the anti-hallucination guards: numeric fidelity + citation enforcement."""
from __future__ import annotations

from app.models import Biomarker, Severity
from app.models.findings import Finding
from app.safety import (
    apply_fidelity_guards,
    check_numeric_fidelity,
    enforce_bullet_citations,
)


def _f(biomarker, value, unit, severity=Severity.ABNORMAL, label="X", source="rule"):
    return Finding(
        biomarker=biomarker, value=value, unit=unit,
        severity=severity, label=label, rationale="r", source=source,
    )


def test_numeric_fidelity_keeps_correct_value():
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    text = "Your LDL of 172 mg/dL is in the high range."
    r = check_numeric_fidelity(text, findings, "")
    assert "172" in r.text
    assert r.issues == []


def test_numeric_fidelity_strips_hallucinated_value():
    """LLM hallucinated 'LDL of 175' when real value was 172. Must drop."""
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    text = "Your LDL of 175 mg/dL is in the high range."
    r = check_numeric_fidelity(text, findings, "")
    assert "175" not in r.text
    assert any(i.kind == "numeric_mismatch" for i in r.issues)


def test_numeric_fidelity_accepts_threshold_from_rag_passage():
    """If the RAG passage says '<6% saturated fat', the LLM may quote 6%."""
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    passages = "Limit saturated fat to less than 6% of total calories per AHA."
    text = "Limit saturated fat to less than 6% of total calories."
    r = check_numeric_fidelity(text, findings, passages)
    assert "6%" in r.text
    assert r.issues == []


def test_numeric_fidelity_tolerates_minor_rounding():
    """Allow 172 ↔ 172.0 ↔ 172.1 — within ±2%."""
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    r = check_numeric_fidelity("LDL is 172.0 mg/dL.", findings, "")
    assert "172.0" in r.text


def test_numeric_fidelity_keeps_unrelated_sentences():
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    # No clinical numbers in this sentence — should pass through.
    text = "Discuss any concerns with your clinician at the next visit."
    r = check_numeric_fidelity(text, findings, "")
    assert text in r.text


def test_citation_enforcement_keeps_cited_diet_bullet():
    text = """
2. Diet Suggestions
- Replace saturated fat with unsaturated fat from olive oil and nuts. [source: AHA 2021]
- Increase soluble fiber from oats and beans. [source: AHA 2021]
3. Exercise Suggestions
- Aim for 150 minutes per week of moderate aerobic activity. [source: 2018 PA Guidelines]
"""
    r = enforce_bullet_citations(text)
    assert "Replace saturated" in r.text
    assert "soluble fiber" in r.text
    assert "150 minutes" in r.text
    assert r.issues == []


def test_citation_enforcement_drops_uncited_diet_bullet():
    text = """
2. Diet Suggestions
- Replace saturated fat with unsaturated fat. [source: AHA 2021]
- Eat more colorful vegetables — feels right.
- Drink green tea every day.
3. Exercise Suggestions
- Walk 150 minutes per week. [source: 2018 PA]
"""
    r = enforce_bullet_citations(text)
    assert "Replace saturated" in r.text  # cited → kept
    assert "colorful vegetables" not in r.text  # uncited → dropped
    assert "green tea" not in r.text  # uncited → dropped
    assert "150 minutes" in r.text  # cited → kept
    assert len([i for i in r.issues if i.kind == "uncited_bullet"]) == 2


def test_citation_topic_pipe_format_accepted():
    """The orchestrator already injects passages as '[topic | source]' —
    that format counts as a citation."""
    text = """
2. Diet Suggestions
- Limit alcohol intake. [diet | AHA 2021 Dietary Guidance]
"""
    r = enforce_bullet_citations(text)
    assert "Limit alcohol" in r.text
    assert r.issues == []


def test_other_sections_dont_require_citations():
    """Overall summary, recheck etc. don't need citations — they describe
    the deterministic findings directly."""
    text = """
1. Overall Health Summary
This panel shows borderline-high cholesterol with otherwise normal markers.
4. What to Recheck
- Recheck lipids in 3-6 months.
"""
    r = enforce_bullet_citations(text)
    assert "borderline-high" in r.text
    assert "Recheck lipids" in r.text


def test_combined_pipeline_strips_both_classes_of_problems():
    findings = [_f(Biomarker.LDL, 172, "mg/dL")]
    text = """
1. Overall Health Summary
Your LDL of 175 mg/dL is high.
2. Diet Suggestions
- Replace saturated fat with unsaturated fat. [source: AHA 2021]
- Eat more raw garlic.
"""
    r = apply_fidelity_guards(text, findings, "")
    assert "175" not in r.text  # numeric drift stripped
    assert "raw garlic" not in r.text  # uncited bullet stripped
    assert "Replace saturated fat" in r.text  # legitimate, cited
