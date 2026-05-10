"""Tiny shared helpers for rule modules."""
from __future__ import annotations

from typing import Optional

from app.models.biomarkers import Biomarker, CANONICAL_UNIT
from app.models.findings import Finding, Severity
from app.models.lab import LabPanel


def make(
    panel: LabPanel,
    biomarker: Biomarker,
    severity: Severity,
    label: str,
    rationale: str,
    source: str,
    *,
    escalate: bool = False,
    related_topics: Optional[list[str]] = None,
) -> Optional[Finding]:
    """Build a Finding from a panel value, or None if the biomarker is absent."""
    v = panel.get(biomarker)
    if v is None:
        return None
    return Finding(
        biomarker=biomarker,
        value=v.value,
        unit=v.unit or CANONICAL_UNIT[biomarker],
        severity=severity,
        label=label,
        rationale=rationale,
        source=source,
        escalate=escalate,
        related_topics=related_topics or [],
    )
