"""Topic-based retrieval.

Spec calls for "do not retrieve once" — instead, retrieve once per finding
cluster (glucose, lipids, kidney, ...) plus once for diet, exercise, and
safety. The result is a richer, more targeted context.
"""
from __future__ import annotations

from app.models.findings import FindingCluster, Finding
from .store import KnowledgeStore, Passage


def retrieve_for_clusters(
    store: KnowledgeStore,
    clusters: list[FindingCluster],
    pattern_findings: list[Finding],
    *,
    k: int = 3,
) -> dict[str, list[Passage]]:
    """Retrieve passages keyed by topic.

    For each cluster we run a query like "explain LDL elevated, HDL low".
    Plus we always retrieve diet/exercise/safety guidance.
    """
    out: dict[str, list[Passage]] = {}

    for cluster in clusters:
        keywords = ", ".join(
            f"{f.biomarker.value} {f.label}".lower() for f in cluster.findings if f.severity.value != "normal"
        )
        if not keywords:
            continue
        q = f"explain {cluster.topic} findings: {keywords}"
        passages = store.query(q, topic=cluster.topic, k=k)
        if not passages:
            # If the curated KB has no per-topic match, try untyped retrieval.
            passages = store.query(q, topic=None, k=k)
        if passages:
            out[cluster.topic] = passages

    # Pattern-driven extra retrieval
    if pattern_findings:
        q = "metabolic syndrome, mixed dyslipidemia, anemia patterns, combined cardiovascular risk"
        out.setdefault("patterns", store.query(q, topic=None, k=k))

    # Always pull diet, exercise, safety
    out["diet"] = store.query("dietary advice for elevated cholesterol, glucose, blood pressure", topic="diet", k=k)
    out["exercise"] = store.query("aerobic and resistance exercise guidelines for adults", topic="exercise", k=k)
    out["safety"] = store.query("urgent symptoms requiring medical attention", topic="safety", k=k)

    return out
