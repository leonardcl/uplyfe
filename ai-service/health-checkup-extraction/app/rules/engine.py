"""Rules dispatcher: runs every per-family module and returns clustered findings.

Determinism is the whole point. The LLM never sees a panel before this engine
has already produced its conclusions; the LLM only re-states them.

When the input panel includes printed reference ranges (e.g. an Indian lab
report that prints `Range: 3.9-5.5` next to each value), those ranges take
priority over our built-in global thresholds for in/out-of-range
classification. The lab knows the local population and method better than any
single hardcoded threshold.
"""
from __future__ import annotations

from app.models.findings import Finding, FindingCluster, Severity
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


def _fmt_range(low, high) -> str:
    if low is not None and high is not None:
        return f"{_fmt_num(low)}–{_fmt_num(high)}"
    if low is not None:
        return f"≥{_fmt_num(low)}"
    if high is not None:
        return f"≤{_fmt_num(high)}"
    return "—"


def _fmt_num(v: float) -> str:
    if v == int(v):
        return str(int(v))
    return f"{v:g}"


def _apply_printed_range_overrides(
    panel: LabPanel, clusters: list[FindingCluster]
) -> list[FindingCluster]:
    """Override severity to NORMAL for any finding whose value is within the
    lab's printed reference range. We trust the lab's interpretation of
    'normal' over our built-in thresholds when both are available.

    We DON'T do the inverse (escalate normal → abnormal) here — if our rules
    didn't flag it but the lab did, the printed range is informational; we
    leave the user's report unchanged rather than risk false abnormals from
    a misparsed range.
    """
    panel_by_b = {v.biomarker: v for v in panel.values}

    new_clusters: list[FindingCluster] = []
    for c in clusters:
        new_findings: list[Finding] = []
        for f in c.findings:
            lv = panel_by_b.get(f.biomarker)
            if lv is None or (lv.reference_low is None and lv.reference_high is None):
                new_findings.append(f)
                continue

            lo, hi = lv.reference_low, lv.reference_high
            in_range = True
            if lo is not None and lv.value < lo:
                in_range = False
            if hi is not None and lv.value > hi:
                in_range = False

            if in_range and f.severity in (Severity.ABNORMAL, Severity.BORDERLINE):
                new_findings.append(Finding(
                    biomarker=f.biomarker,
                    value=f.value,
                    unit=f.unit,
                    severity=Severity.NORMAL,
                    label=f"Within lab's printed reference range",
                    rationale=(
                        f"{f.biomarker.value} {_fmt_num(lv.value)} {lv.unit} is within the "
                        f"lab's printed reference range ({_fmt_range(lo, hi)}). "
                        "(Note: a global guideline threshold would have flagged this — your "
                        "lab's range was used because it's tuned to your local population/method.)"
                    ),
                    source=f.source,
                    related_topics=f.related_topics,
                    escalate=False,
                ))
            else:
                new_findings.append(f)
        if new_findings:
            new_clusters.append(FindingCluster(topic=c.topic, findings=new_findings))
    return new_clusters


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
    clusters = _apply_printed_range_overrides(panel, clusters)
    pattern_findings = patterns.detect_patterns(panel)
    return clusters, pattern_findings
