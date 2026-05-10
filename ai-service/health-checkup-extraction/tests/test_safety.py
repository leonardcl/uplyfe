"""Safety validator tests — diagnostic phrasing must be rewritten,
medication recommendations must be blocked, escalations must be summarized."""
from __future__ import annotations

from app.models import Biomarker, Finding, Severity
from app.safety import validate_text, summarize_emergencies


def _f(escalate=False, sev=Severity.ABNORMAL):
    return Finding(
        biomarker=Biomarker.GLUCOSE_FASTING,
        value=420, unit="mg/dL",
        severity=sev,
        label="Severe hyperglycemia",
        rationale="x",
        source="ADA",
        escalate=escalate,
    )


def test_diagnostic_phrasing_is_rewritten():
    text = "You have diabetes. The patient has heart disease."
    sr = validate_text(text, [])
    assert "you have diabetes" not in sr.text.lower()
    assert "this result is in the range associated with" in sr.text.lower()
    assert sr.rewrites


def test_medication_phrasing_is_blocked():
    text = "You should start taking metformin tomorrow. We recommend taking statin."
    sr = validate_text(text, [])
    assert "metformin" not in sr.text.lower()
    assert "statin" not in sr.text.lower()
    assert "[medication advice removed" in sr.text
    assert sr.blocked


def test_disclaimer_appended():
    sr = validate_text("All looks fine.", [])
    assert "Disclaimer" in sr.text
    assert "not a medical diagnosis" in sr.text


def test_disclaimer_not_duplicated():
    text = "All looks fine.\n\n**Disclaimer.** This is informational only."
    sr = validate_text(text, [])
    assert sr.text.count("**Disclaimer.**") == 1


def test_escalations_listed():
    msgs = summarize_emergencies([_f(escalate=True), _f(sev=Severity.CRITICAL)])
    assert len(msgs) == 2
    assert all("Seek prompt professional evaluation" in m for m in msgs)


def test_confirms_phrasing_softened():
    sr = validate_text("This confirms hypothyroidism.", [])
    assert "confirms" not in sr.text.lower() or "consistent with" in sr.text.lower()
