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

from app.models.biomarkers import Biomarker
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
    """Reconcile rule-engine findings with the lab's printed reference range.

    Two-way override when a printed range is present:

      * Within range AND rule said abnormal/borderline → demote to NORMAL.
        The lab's interpretation of "normal" is trusted over our built-in
        thresholds (different populations, different assay methods).

      * Outside range AND rule said normal (or rule was silent) → escalate to
        BORDERLINE. The lab knows their own assay's reference window. If they
        flagged it, we should too.

    We don't promote past BORDERLINE on lab-range-only signals — only the
    rule engine, with its source-cited thresholds, escalates to ABNORMAL or
    CRITICAL. This keeps emergency escalation tied to authoritative guidelines.
    """
    panel_by_b = {v.biomarker: v for v in panel.values}
    findings_by_b: dict[Biomarker, Finding] = {}
    cluster_topic_for_b: dict[Biomarker, str] = {}
    new_clusters: list[FindingCluster] = []

    for c in clusters:
        new_findings: list[Finding] = []
        for f in c.findings:
            findings_by_b[f.biomarker] = f
            cluster_topic_for_b[f.biomarker] = c.topic
            lv = panel_by_b.get(f.biomarker)
            if lv is None or (lv.reference_low is None and lv.reference_high is None):
                new_findings.append(f)
                continue

            lo, hi = lv.reference_low, lv.reference_high
            below = lo is not None and lv.value < lo
            above = hi is not None and lv.value > hi
            in_range = not (below or above)

            if in_range and f.severity in (Severity.ABNORMAL, Severity.BORDERLINE):
                # Demote: rule flagged it, lab range says fine.
                new_findings.append(Finding(
                    biomarker=f.biomarker,
                    value=f.value,
                    unit=f.unit,
                    severity=Severity.NORMAL,
                    label="Within lab's printed reference range",
                    rationale=(
                        f"{f.biomarker.value} {_fmt_num(lv.value)} {lv.unit} is within the "
                        f"lab's printed reference range ({_fmt_range(lo, hi)})."
                    ),
                    source=f.source,
                    related_topics=f.related_topics,
                    escalate=False,
                ))
            elif (below or above) and f.severity == Severity.NORMAL:
                # Escalate: rule said fine, lab range says out.
                direction = "below" if below else "above"
                new_findings.append(Finding(
                    biomarker=f.biomarker,
                    value=f.value,
                    unit=f.unit,
                    severity=Severity.BORDERLINE,
                    label=f"Outside lab's printed reference range ({direction})",
                    rationale=(
                        f"{f.biomarker.value} {_fmt_num(lv.value)} {lv.unit} is {direction} the "
                        f"lab's printed reference range ({_fmt_range(lo, hi)})."
                    ),
                    source=f.source,
                    related_topics=f.related_topics,
                    escalate=False,
                ))
            else:
                new_findings.append(f)
        if new_findings:
            new_clusters.append(FindingCluster(topic=c.topic, findings=new_findings))

    # Catch biomarkers the rule engine never produced a finding for, but whose
    # printed range was violated. These wouldn't show up otherwise.
    for v in panel.values:
        if v.biomarker in findings_by_b:
            continue
        if v.reference_low is None and v.reference_high is None:
            continue
        below = v.reference_low is not None and v.value < v.reference_low
        above = v.reference_high is not None and v.value > v.reference_high
        if not (below or above):
            continue
        topic = _topic_for(v.biomarker)
        direction = "below" if below else "above"
        f = Finding(
            biomarker=v.biomarker,
            value=v.value,
            unit=v.unit,
            severity=Severity.BORDERLINE,
            label=f"Outside lab's printed reference range ({direction})",
            rationale=(
                f"{v.biomarker.value} {_fmt_num(v.value)} {v.unit} is {direction} the "
                f"lab's printed reference range "
                f"({_fmt_range(v.reference_low, v.reference_high)})."
            ),
            source="Lab's printed reference range",
            related_topics=[topic],
            escalate=False,
        )
        # Append into the existing cluster for that topic, or make a new one.
        existing = next((c for c in new_clusters if c.topic == topic), None)
        if existing is not None:
            existing.findings.append(f)
        else:
            new_clusters.append(FindingCluster(topic=topic, findings=[f]))
    return new_clusters


def _topic_for(biomarker: Biomarker) -> str:
    from app.models.biomarkers import topic_for
    return topic_for(biomarker)


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
