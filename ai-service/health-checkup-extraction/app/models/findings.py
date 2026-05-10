"""What the rules engine emits."""
from __future__ import annotations

from enum import Enum
from typing import Optional

from pydantic import BaseModel

from .biomarkers import Biomarker


class Severity(str, Enum):
    NORMAL = "normal"
    BORDERLINE = "borderline"   # near a clinical cutoff, watchful
    ABNORMAL = "abnormal"       # outside reference, needs attention
    CRITICAL = "critical"       # urgent — clinician now


SEVERITY_RANK = {
    Severity.NORMAL: 0,
    Severity.BORDERLINE: 1,
    Severity.ABNORMAL: 2,
    Severity.CRITICAL: 3,
}


class Finding(BaseModel):
    """One deterministic conclusion from the rules engine.

    Important: the LLM never authors these. The LLM only *explains* them.
    """

    biomarker: Biomarker
    value: float
    unit: str
    severity: Severity
    label: str                           # e.g. "Prediabetes range"
    rationale: str                       # plain-English reason, deterministic
    source: str                          # citation, e.g. "ADA Standards of Care 2024"
    escalate: bool = False               # safety: must trigger urgent-care advice
    related_topics: list[str] = []       # for RAG retrieval


class FindingCluster(BaseModel):
    """Grouped findings (e.g. all glucose findings, all lipid findings)."""

    topic: str
    findings: list[Finding]

    @property
    def max_severity(self) -> Severity:
        if not self.findings:
            return Severity.NORMAL
        return max(self.findings, key=lambda f: SEVERITY_RANK[f.severity]).severity
