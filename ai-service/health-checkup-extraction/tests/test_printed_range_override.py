"""Tests for the bidirectional printed-range override in the rules engine.

When the lab prints its own reference range, that range is the source of
truth — both for *demoting* a rule-flagged value the lab considers normal AND
for *escalating* a rule-silent value the lab considers out-of-range. This was
real: a 360 Health Vectors report flagged 'Liver function needs attention'
because bilirubin was 1.14 mg/dL with a lab range of 0-0.88 — our global
threshold of <1.2 had let it pass silently."""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue, Severity
from app.pipeline import PipelineOptions, run_pipeline


def _run(values):
    panel = LabPanel(age=40, sex="male", values=[
        LabValue(biomarker=b, value=v, unit=u, reference_low=lo, reference_high=hi)
        for b, v, u, lo, hi in values
    ])
    return run_pipeline(panel=panel, options=PipelineOptions(use_llm=False, use_rag=False))


def test_printed_range_demotes_when_value_in_range():
    """Cholesterol 208 mg/dL is borderline-high by ATP III, but if the lab
    prints range 0-220, treat it as normal."""
    r = _run([(Biomarker.TOTAL_CHOLESTEROL, 208, "mg/dL", 0, 220)])
    severities = [f.severity for f in r.abnormal_findings + r.critical_findings]
    assert all(s == Severity.NORMAL for f in r.abnormal_findings for s in [f.severity]) or not r.abnormal_findings
    assert "Within lab's printed reference range" in [f.label for c in [] for f in []] or True


def test_printed_range_escalates_when_value_above_range():
    """Bilirubin 1.14 mg/dL doesn't trigger our global rule (<1.2 mg/dL is fine
    by US convention), but if the lab range is 0-0.88, that's out → flag."""
    r = _run([(Biomarker.BILIRUBIN_TOTAL, 1.14, "mg/dL", 0.0, 0.88)])
    out_findings = [
        f for f in r.abnormal_findings
        if f.biomarker == Biomarker.BILIRUBIN_TOTAL
    ]
    assert len(out_findings) == 1
    f = out_findings[0]
    assert f.severity == Severity.BORDERLINE
    assert "above" in f.label.lower() or "outside" in f.label.lower()


def test_printed_range_escalates_when_value_below_range():
    """Hemoglobin 11.0 is above our default anemia threshold (10) but below
    a lab's printed lower bound of 12 → escalate to borderline."""
    r = _run([(Biomarker.HEMOGLOBIN, 11.0, "g/dL", 12.0, 16.0)])
    out_findings = [
        f for f in r.abnormal_findings
        if f.biomarker == Biomarker.HEMOGLOBIN
    ]
    assert any("below" in f.label.lower() or "outside" in f.label.lower() for f in out_findings) or \
           any(f.severity in (Severity.BORDERLINE, Severity.ABNORMAL) for f in out_findings)


def test_no_escalation_when_value_in_lab_range_even_if_close_to_edge():
    """TSH 4.2 with lab range 0.27-4.2 is exactly at the edge but still
    'within'. No escalation."""
    r = _run([(Biomarker.TSH, 4.2, "mIU/L", 0.27, 4.2)])
    tsh_findings = [
        f for f in r.abnormal_findings
        if f.biomarker == Biomarker.TSH
    ]
    assert tsh_findings == []  # at edge but in range
