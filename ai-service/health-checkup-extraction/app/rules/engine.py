"""Rules dispatcher: runs every per-family module and returns clustered findings.

Determinism is the whole point. The LLM never sees a panel before this engine
has already produced its conclusions; the LLM only re-states them.
"""
from __future__ import annotations

from app.models.findings import Finding, FindingCluster
from app.models.lab import LabPanel

from . import (
    glucose,
    lipids,
    liver,
    kidney,
    cbc,
    thyroid,
    inflammation,
    electrolytes,
    vitamins,
    anthropometric,
    patterns,
)


_TOPIC_FAMILIES = [
    ("glucose", glucose.evaluate),
    ("lipids", lipids.evaluate),
    ("liver", liver.evaluate),
    ("kidney", kidney.evaluate),
    ("cbc", cbc.evaluate),
    ("thyroid", thyroid.evaluate),
    ("inflammation", inflammation.evaluate),
    ("electrolytes", electrolytes.evaluate),
    ("vitamins", vitamins.evaluate),
    ("anthropometric", anthropometric.evaluate),
]


def evaluate_panel(panel: LabPanel) -> tuple[list[FindingCluster], list[Finding]]:
    """Run all rule families and pattern detection.

    Returns
    -------
    clusters : grouped per topic, in a stable order — useful for RAG retrieval.
    pattern_findings : cross-biomarker patterns, separately so they can be
                       presented as their own section.
    """
    clusters: list[FindingCluster] = []
    for topic, fn in _TOPIC_FAMILIES:
        findings: list[Finding] = fn(panel)
        if findings:
            clusters.append(FindingCluster(topic=topic, findings=findings))
    pattern_findings = patterns.detect_patterns(panel)
    return clusters, pattern_findings
