"""_bulletize picker — must reject sentence fragments and prefer imperatives."""
from __future__ import annotations

from dataclasses import dataclass

from app.pipeline.orchestrator import _bulletize


@dataclass
class _Passage:
    text: str
    source: str = "test"
    title: str = "test"


def test_rejects_continuation_fragment():
    """Real-world bug from the live RAG output we saw: bulletizer used to grab
    'For substantial health benefits, adults should do EITHER:' as the first
    bullet because it was >= 30 chars."""
    passage = _Passage(text="""
    Exercise guidelines

    For substantial health benefits, adults should do EITHER:
    Aim for at least 150 minutes per week of moderate-intensity aerobic activity.
    Add muscle-strengthening activities on at least 2 days per week.
    """.strip())
    out = _bulletize([passage], default=["DEFAULT"])
    assert out
    assert "EITHER:" not in out[0]
    assert out[0].lower().startswith(("aim", "add"))


def test_prefers_imperative_verb():
    passage = _Passage(text="""
    Lipid management

    Saturated fat intake exceeds recommendations in many adults.
    Limit saturated fat to less than 6% of total calories.
    """.strip())
    out = _bulletize([passage], default=["DEFAULT"])
    assert out[0].lower().startswith("limit")


def test_default_returned_when_no_passages():
    out = _bulletize([], default=["fallback bullet 1", "fallback bullet 2"])
    assert out == ["fallback bullet 1", "fallback bullet 2"]


def test_skips_metadata_lines():
    passage = _Passage(text="""
    # Heading
    Source: AHA 2018
    Citation: see ref
    Limit alcohol intake even moderate amounts can substantially raise triglyceride levels.
    """.strip())
    out = _bulletize([passage], default=["DEFAULT"])
    assert "Source:" not in out[0]
    assert "limit alcohol" in out[0].lower()
