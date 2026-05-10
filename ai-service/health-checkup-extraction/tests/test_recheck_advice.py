"""Tests that recheck advice consolidates topics into one sentence rather
than repeating the same sentence template per topic."""
from __future__ import annotations

from app.models import Biomarker, LabPanel, LabValue
from app.pipeline import PipelineOptions, run_pipeline


def _build(values):
    return LabPanel(age=45, sex="male", values=[
        LabValue(biomarker=b, value=v, unit=u) for b, v, u in values
    ])


def _run(panel):
    return run_pipeline(panel=panel, options=PipelineOptions(use_llm=False, use_rag=False))


def test_single_topic_one_bullet():
    r = _run(_build([(Biomarker.GLUCOSE_FASTING, 110, "mg/dL")]))
    assert len(r.recheck_advice) == 1
    assert "glucose" in r.recheck_advice[0].lower()


def test_multiple_topics_consolidated_into_one_bullet():
    """Five abnormal topics must produce ONE bullet, not five."""
    r = _run(_build([
        (Biomarker.GLUCOSE_FASTING, 130, "mg/dL"),     # glucose
        (Biomarker.LDL, 175, "mg/dL"),                 # lipids
        (Biomarker.ALT, 65, "U/L"),                    # liver
        (Biomarker.CREATININE, 1.5, "mg/dL"),          # kidney
        (Biomarker.HEMOGLOBIN, 10.5, "g/dL"),          # cbc (anemia)
    ]))
    assert len(r.recheck_advice) == 1
    text = r.recheck_advice[0].lower()
    assert "glucose" in text
    assert "lipids" in text
    # The earlier per-topic version emitted "Recheck X markers" five times —
    # the new one says "Recheck your A, B, C, D, and E in 3–6 months..." once.
    assert text.count("recheck") == 1


def test_no_findings_default_message():
    r = _run(_build([(Biomarker.HEMOGLOBIN, 14.5, "g/dL")]))
    assert r.recheck_advice == [
        "No specific recheck flagged. Continue routine annual checkups."
    ]


def test_two_topics_uses_and():
    r = _run(_build([
        (Biomarker.GLUCOSE_FASTING, 130, "mg/dL"),
        (Biomarker.LDL, 175, "mg/dL"),
    ]))
    [bullet] = r.recheck_advice
    assert " and " in bullet
