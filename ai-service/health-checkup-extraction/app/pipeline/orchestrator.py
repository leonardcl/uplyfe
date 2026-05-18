"""End-to-end pipeline.

Inputs:
  * a `LabPanel` (already structured), OR
  * a path to a PDF / image, OR
  * raw text (e.g. pasted from a report).

Outputs: a `FinalReport`. Designed to run usefully even when the LLM is
unreachable — in that mode we return a deterministic-only report.

Determinism boundary: the rules engine produces all severities and labels.
The LLM only re-states findings in plain language; it cannot introduce new
findings, change severities, or invent values. Post-LLM safety + repetition
guards enforce that promise.
"""
from __future__ import annotations

import json
import re
from dataclasses import dataclass
from pathlib import Path
from typing import Optional

from app.config import get_settings
from app.llm import OllamaClient, LLMUnavailableError
from app.llm.prompts import REPORT_SYNTHESIS_PROMPT, SYSTEM_PROMPT
from app.models import (
    Biomarker,
    Finding,
    FindingCluster,
    FinalReport,
    KeyInsight,
    LabPanel,
    ReportSection,
    Severity,
    ValidationIssue,
)
from app.normalize import to_canonical, validate_panel
from app.parsers import (
    chunk_text_by_page,
    detect_language,
    extract_age_sex,
    extract_lab_panel,
    extract_text_from_image,
    extract_text_from_pdf,
    regex_extract_panel,
)
from app.parsers.pdf_parser import EmptyPDFTextError
from app.parsers.regex_extractor import extract_blood_pressure
from app.rag import KnowledgeStore, retrieve_for_clusters
from app.rules import evaluate_panel
from app.safety import (
    apply_fidelity_guards,
    build_disclaimer,
    summarize_emergencies,
    validate_text,
)


# Post-conversion plausibility bounds (all values are now in canonical units).
# These are the same keys as Biomarker.value strings.
_POST_CONV_MAX: dict[str, float] = {
    "glucose_fasting":   1200,   # mg/dL
    "glucose_random":    1200,
    "total_cholesterol": 1500,   # mg/dL
    "triglycerides":      5000,   # mg/dL — severe hypertriglyceridemia true max
    "hdl_cholesterol":    200,   # mg/dL
    "ldl":                800,   # mg/dL
    "hemoglobin":          25,   # g/dL
    "hematocrit":          75,   # %
    "wbc":                500,   # 10³/µL
    "platelets":         3000,   # 10³/µL
    "creatinine":          50,   # mg/dL
    "bun":                300,   # mg/dL
    "uric_acid":           30,   # mg/dL
    "sodium":             200,   # mEq/L
    "potassium":           10,   # mEq/L
    "alt":              10000,   # IU/L
    "ast":              10000,   # IU/L
    "ggt":              10000,   # IU/L
    "bmi":                100,
}
_POST_CONV_MIN: dict[str, float] = {
    "potassium":         1.5,
    "sodium":          100.0,
    "hemoglobin":        2.0,
    "hematocrit":        5.0,
    "glucose_fasting":  20.0,
    "glucose_random":   20.0,
    "bmi":              10.0,
    "triglycerides":     5.0,   # mg/dL — below 5 is essentially impossible after conversion
}


def _post_conversion_plausibility_filter(panel: "LabPanel") -> "LabPanel":
    """Drop any biomarker value that is physiologically impossible AFTER unit
    conversion to canonical units.

    This is the second plausibility gate — the first runs inside llm_extractor
    before conversion. Unit conversion can turn a plausible-looking raw value
    (e.g. triglycerides 15 mmol/L, just below the 10 000 mg/dL pre-conv cap)
    into an absurd canonical value (15 × 88.57 = 1328 mg/dL for a routine
    Korean checkup where 비대상 / reference ranges were misread).
    """
    from app.models.lab import LabValue  # local import to avoid circular

    filtered: list[LabValue] = []
    for v in panel.values:
        key = v.biomarker.value if hasattr(v.biomarker, "value") else str(v.biomarker)
        val = v.value
        if key in _POST_CONV_MAX and val > _POST_CONV_MAX[key]:
            continue  # drop implausible high
        if key in _POST_CONV_MIN and val < _POST_CONV_MIN[key]:
            continue  # drop implausible low
        filtered.append(v)

    if len(filtered) != len(panel.values):
        panel = panel.model_copy(update={"values": filtered})
    return panel


@dataclass
class PipelineOptions:
    use_llm: bool = True
    use_rag: bool = True
    rag_k: int = 3


# ---------- public API ----------


def run_pipeline(
    *,
    panel: Optional[LabPanel] = None,
    pdf_path: Optional[Path | str] = None,
    image_path: Optional[Path | str] = None,
    raw_text: Optional[str] = None,
    options: Optional[PipelineOptions] = None,
) -> FinalReport:
    options = options or PipelineOptions()

    panel = _coerce_panel(panel=panel, pdf_path=pdf_path, image_path=image_path, raw_text=raw_text)

    canonical, conv_warnings = to_canonical(panel)
    canonical = _post_conversion_plausibility_filter(canonical)
    issues: list[ValidationIssue] = validate_panel(canonical, conv_warnings)

    clusters, pattern_findings = evaluate_panel(canonical)

    rag_context: dict[str, list] = {}
    if options.use_rag:
        try:
            store = KnowledgeStore()
            if store.count() > 0:
                rag_context = retrieve_for_clusters(
                    store, clusters, pattern_findings, k=options.rag_k
                )
        except Exception:
            rag_context = {}

    llm_text: Optional[str] = None
    if options.use_llm:
        llm_text = _try_llm_synthesis(canonical, clusters, pattern_findings, rag_context)

    return _assemble_report(
        canonical=canonical,
        clusters=clusters,
        pattern_findings=pattern_findings,
        issues=issues,
        rag_context=rag_context,
        llm_text=llm_text,
    )


# ---------- helpers ----------


def _coerce_panel(
    *,
    panel: Optional[LabPanel],
    pdf_path: Optional[Path | str],
    image_path: Optional[Path | str],
    raw_text: Optional[str],
) -> LabPanel:
    if panel is not None:
        return panel

    text = raw_text or ""
    if pdf_path:
        try:
            text = extract_text_from_pdf(pdf_path)
        except EmptyPDFTextError:
            if image_path:
                text = extract_text_from_image(image_path)
            else:
                raise
    elif image_path:
        text = extract_text_from_image(image_path)

    if not text:
        raise ValueError("No input provided to the pipeline.")

    # Detect language once on the full text — language is a document-level
    # property, not a per-page one.
    language = detect_language(text)

    # Patient demographics from the header. Either may be None.
    age_detected, sex_detected = extract_age_sex(text)

    # Page-level chunking. For short text and manual JSON paths this returns
    # [text]; for multi-page PDFs it returns one chunk per page.
    chunks = chunk_text_by_page(text)

    from app.models.lab import LabValue

    merged: dict[Biomarker, LabValue] = {}
    bp_sys: Optional[float] = None
    bp_dia: Optional[float] = None

    # Korean documents: the regex extractor is designed for English/Indonesian
    # only — running it on Korean text produces false positives (reference
    # range numbers like "1-40" get misread as biomarker values). Skip regex
    # entirely for Korean and let the LLM extractor handle it below.
    skip_regex = (language == "ko")

    for chunk in chunks:
        if not skip_regex:
            for v in regex_extract_panel(chunk, language=language):
                merged[v.biomarker] = v
        if bp_sys is None:
            sys_v, dia_v = extract_blood_pressure(chunk)
            if sys_v is not None and dia_v is not None:
                bp_sys, bp_dia = sys_v, dia_v

    if bp_sys is not None and bp_dia is not None:
        merged[Biomarker.BP_SYSTOLIC] = LabValue(
            biomarker=Biomarker.BP_SYSTOLIC, value=bp_sys, unit="mmHg"
        )
        merged[Biomarker.BP_DIASTOLIC] = LabValue(
            biomarker=Biomarker.BP_DIASTOLIC, value=bp_dia, unit="mmHg"
        )

    if merged and not skip_regex:
        return LabPanel(
            age=age_detected if age_detected is not None else 0,
            sex=sex_detected if sex_detected is not None else "unknown",
            values=list(merged.values()),
        )

    # For Korean (skip_regex=True) or when regex found nothing: use the LLM
    # extractor on the longest chunk so we stay within the per-call cap.
    longest_chunk = max(chunks, key=len) if chunks else text
    allowed_keys = [b.value for b in Biomarker]
    llm_panel = extract_lab_panel(longest_chunk, allowed_keys=allowed_keys, language=language)
    if llm_panel is None:
        raise ValueError(
            "Could not extract any biomarker values from the input. "
            "Try the manual JSON input path, or check that the report text was readable."
        )
    # Overlay the header-detected demographics if the LLM didn't fill them in.
    if age_detected is not None and llm_panel.age == 0:
        llm_panel = llm_panel.model_copy(update={"age": age_detected})
    if sex_detected is not None and llm_panel.sex == "unknown":
        llm_panel = llm_panel.model_copy(update={"sex": sex_detected})
    return llm_panel


# ---------- LLM synthesis ----------


def _findings_to_json_block(clusters: list[FindingCluster]) -> str:
    """Render biomarker findings as a JSON array string for the prompt.

    Forces the model to copy values verbatim instead of summarizing.
    """
    items = []
    for c in clusters:
        for f in c.findings:
            items.append({
                "biomarker": f.biomarker.value,
                "value": f.value,
                "unit": f.unit,
                "severity": f.severity.value,
                "label": f.label,
                "source": f.source,
                "escalate": f.escalate,
            })
    return json.dumps(items, indent=2)


def _try_llm_synthesis(
    panel: LabPanel,
    clusters: list[FindingCluster],
    pattern_findings: list[Finding],
    rag_context: dict,
) -> Optional[str]:
    settings = get_settings()
    client = OllamaClient(settings)
    if not client.is_available():
        return None

    findings_json = _findings_to_json_block(clusters)
    patterns_block = (
        "\n".join(f"- {p.label} — {p.rationale}" for p in pattern_findings)
        or "(no cross-biomarker patterns)"
    )

    context = ""
    for topic, passages in rag_context.items():
        for p in passages:
            context += f"\n[{topic} | {p.source}] {p.text}\n"

    prompt = REPORT_SYNTHESIS_PROMPT.format(
        age=panel.age,
        sex=panel.sex,
        findings_json=findings_json,
        patterns_block=patterns_block,
        context=context.strip() or "(no retrieved passages)",
    )

    try:
        # Single LLM call. The deterministic safety validator runs after.
        # `END_OF_REPORT` sentinel + repeat_penalty kill the runaway-generation bug.
        draft = client.generate(
            prompt,
            system=SYSTEM_PROMPT,
            max_tokens=1500,
            stop=["END_OF_REPORT"],
            repeat_penalty=1.18,
            repeat_last_n=256,
            num_ctx=16384,
        )
    except LLMUnavailableError:
        return None
    if not draft:
        return None

    return _guard_repetition(draft)


# ---------- post-LLM cleanup ----------


def _guard_repetition(text: str) -> str:
    """Truncate after the first detected paragraph- or section-level loop.

    Defense in depth — Ollama's repeat_penalty and stop sentinel should already
    prevent loops, but if a model still emits a repeated section header
    (e.g., "**Key Findings Detail**" written 14 times), we cut the output at
    the second occurrence and keep the report usable.
    """
    if not text:
        return text

    # 1. Cut at second occurrence of any markdown heading line.
    lines = text.splitlines()
    seen_heading: set[str] = set()
    cutoff = None
    for i, line in enumerate(lines):
        m = re.match(r"^(#{1,6}\s+.+|\*\*[^*]+\*\*\s*$)", line.strip())
        if not m:
            continue
        key = line.strip().lower()
        if key in seen_heading:
            cutoff = i
            break
        seen_heading.add(key)
    if cutoff is not None:
        lines = lines[:cutoff]

    # 2. Cut at any paragraph that's been seen 2+ times consecutively.
    out: list[str] = []
    last_para = None
    repeat_count = 0
    para_buf: list[str] = []
    paragraphs: list[str] = []
    for line in lines:
        if line.strip():
            para_buf.append(line)
        else:
            if para_buf:
                paragraphs.append("\n".join(para_buf))
                para_buf = []
            paragraphs.append("")
    if para_buf:
        paragraphs.append("\n".join(para_buf))

    truncated = False
    for p in paragraphs:
        norm = p.strip().lower()
        if norm and norm == last_para:
            repeat_count += 1
            if repeat_count >= 1:  # one repeat is enough; cut.
                truncated = True
                break
        else:
            repeat_count = 0
            last_para = norm if norm else last_para
        out.append(p)

    cleaned = "\n\n".join(s for s in out if s != "" or out.index(s) == 0)
    # Strip trailing whitespace and any leftover sentinel.
    cleaned = re.sub(r"END_OF_REPORT\s*$", "", cleaned).strip()
    return cleaned


# ---------- assembly ----------


_TOPIC_DISPLAY = {
    "glucose": "blood sugar",
    "lipids": "lipid panel",
    "liver": "liver function",
    "kidney": "kidney function",
    "cbc": "complete blood count",
    "thyroid": "thyroid function",
    "inflammation": "inflammation markers",
    "electrolytes": "electrolytes",
    "vitamins": "vitamin levels",
    "anthropometric": "blood pressure / BMI",
}


def _build_healthy_topics(clusters: list[FindingCluster]) -> list[str]:
    """List the topic groups whose ALL findings came back normal — so the
    report can say 'kidney function and thyroid both look healthy' rather
    than just listing what's wrong."""
    abnormal_topics: set[str] = set()
    seen_topics: list[str] = []
    for c in clusters:
        if c.topic not in seen_topics:
            seen_topics.append(c.topic)
        for f in c.findings:
            if f.severity != Severity.NORMAL:
                abnormal_topics.add(c.topic)
                break
    return [
        _TOPIC_DISPLAY.get(t, t)
        for t in seen_topics
        if t not in abnormal_topics
    ]


def _build_key_insights(
    panel: LabPanel,
    biomarker_findings: list[Finding],
) -> list[KeyInsight]:
    """Pick three headline biomarkers for the UI hero cards.

    Order: cholesterol → blood sugar → vitamin D. For each, prefer the most
    relevant biomarker actually present in the panel:
        cholesterol  → total_cholesterol, else ldl
        blood_sugar  → glucose_fasting, else hba1c, else glucose_random
        vitamin_d    → vitamin_d_25oh

    A card is only emitted if its biomarker is in the panel — no insight is
    fabricated from data that wasn't there.
    """
    by_b = {v.biomarker: v for v in panel.values}
    finding_by_b = {f.biomarker: f for f in biomarker_findings}

    insights: list[KeyInsight] = []

    cholesterol_pref = [Biomarker.TOTAL_CHOLESTEROL, Biomarker.LDL]
    sugar_pref = [Biomarker.GLUCOSE_FASTING, Biomarker.HBA1C, Biomarker.GLUCOSE_RANDOM]
    vitamin_pref = [Biomarker.VITAMIN_D_25OH]

    for key, label, prefs in (
        ("cholesterol", "Cholesterol Levels", cholesterol_pref),
        ("blood_sugar", "Blood Sugar", sugar_pref),
        ("vitamin_d", "Vitamin D", vitamin_pref),
    ):
        chosen = next((b for b in prefs if b in by_b), None)
        if chosen is None:
            continue
        v = by_b[chosen]
        f = finding_by_b.get(chosen)
        insights.append(KeyInsight(
            key=key,
            label=label,
            biomarker=chosen,
            value=v.value,
            unit=v.unit,
            status=_insight_status(f),
            summary=_insight_summary(chosen, f),
        ))
    return insights


_LOW_DIRECTION_KEYWORDS = (
    "low ", "below", "deficien", "inadequate", "anemia", "hypogly",
    "rendah",  # Indonesian "low"
)


def _insight_status(f: Optional[Finding]) -> str:
    """Map a biomarker's finding (or absence) to a UI-friendly status keyword.

    Direction (high/low) takes precedence over severity tier — a BORDERLINE
    "vitamin D inadequate" still reads better as "low" than as "borderline".
    """
    if f is None:
        return "optimal"  # in-range — no rule fired
    if f.severity == Severity.CRITICAL:
        return "critical"
    text = f"{f.label} {f.rationale}".lower()
    if any(w in text for w in _LOW_DIRECTION_KEYWORDS):
        return "low"
    if f.severity == Severity.ABNORMAL:
        return "high"
    if f.severity == Severity.BORDERLINE:
        return "borderline"
    return "normal"


def _insight_summary(b: Biomarker, f: Optional[Finding]) -> str:
    """One short sentence per insight; uses the rule label when present."""
    if f is not None and f.label:
        return f.label
    # Generic fallbacks per topic — in-range happy paths.
    if b in (Biomarker.TOTAL_CHOLESTEROL, Biomarker.LDL):
        return "Cholesterol is within healthy range."
    if b in (Biomarker.GLUCOSE_FASTING, Biomarker.HBA1C, Biomarker.GLUCOSE_RANDOM):
        return "Blood sugar is within normal range."
    if b == Biomarker.VITAMIN_D_25OH:
        return "Vitamin D is at a healthy level."
    return "Within reference range."


def _assemble_report(
    *,
    canonical: LabPanel,
    clusters: list[FindingCluster],
    pattern_findings: list[Finding],
    issues: list[ValidationIssue],
    rag_context: dict,
    llm_text: Optional[str],
) -> FinalReport:
    biomarker_findings: list[Finding] = [f for c in clusters for f in c.findings]

    abnormal = [
        f for f in biomarker_findings
        if f.severity in (Severity.ABNORMAL, Severity.BORDERLINE)
    ]
    normal = [
        f for f in biomarker_findings
        if f.severity == Severity.NORMAL
    ]
    healthy_topics = _build_healthy_topics(clusters)
    # Critical pulls from both biomarker findings AND pattern findings, because a
    # CRITICAL pattern (rare but possible) should still surface in the urgent list.
    # Dedup on (biomarker, value, unit, label) — some rule families occasionally
    # emit the same biomarker finding twice when both a critical-level rule and
    # a same-direction abnormal rule fire.
    seen_keys: set[tuple] = set()
    critical: list[Finding] = []
    for f in biomarker_findings + pattern_findings:
        if not (f.severity == Severity.CRITICAL or f.escalate):
            continue
        key = (f.biomarker, f.value, f.unit, f.label)
        if key in seen_keys:
            continue
        seen_keys.add(key)
        critical.append(f)

    overall = Severity.NORMAL
    from app.models.findings import SEVERITY_RANK
    for f in biomarker_findings + pattern_findings:
        if SEVERITY_RANK[f.severity] > SEVERITY_RANK[overall]:
            overall = f.severity

    diet_advice = _bulletize(rag_context.get("diet"), default=[
        "Build meals around vegetables, fruit, whole grains, legumes, nuts, and lean protein.",
        "Limit sugar-sweetened beverages and ultra-processed snacks.",
        "Reduce saturated fat; replace with unsaturated fats from olive oil, nuts, and fatty fish.",
        "Hydrate primarily with water.",
    ])
    exercise_advice = _bulletize(rag_context.get("exercise"), default=[
        "Aim for ≥150 minutes/week of moderate-intensity aerobic activity.",
        "Add muscle-strengthening activities on ≥2 days/week.",
        "Break activity into manageable bouts (e.g., 10–15 min brisk walks).",
        "Progress gradually; pause and seek care for chest pain or severe shortness of breath.",
    ])

    summary = _build_summary(canonical, biomarker_findings, pattern_findings, healthy_topics)
    pattern_notes = [p.label for p in pattern_findings]
    recheck_advice = _build_recheck_advice(biomarker_findings)
    when_to_doctor = summarize_emergencies(biomarker_findings + pattern_findings) or [
        "Discuss any abnormal or borderline result with a clinician at your next visit.",
        "Seek prompt care for chest pain, severe shortness of breath, neurologic symptoms, or sudden severe abdominal pain.",
    ]

    sections: list[ReportSection] = []
    if llm_text:
        # Anti-hallucination guards run BEFORE the safety validator:
        # 1. numeric fidelity — drop any sentence whose clinical-unit number
        #    isn't a real finding value or appears in a retrieved RAG passage
        # 2. citation enforcement — drop diet/exercise bullets without [source]
        rag_passages_text = "\n".join(
            p.text for passages in rag_context.values() for p in passages
        )
        fidelity = apply_fidelity_guards(
            llm_text,
            biomarker_findings + pattern_findings,
            rag_passages_text=rag_passages_text,
        )
        # Annotate validation_issues with anything we stripped, so it's auditable.
        for fi in fidelity.issues:
            issues.append(
                ValidationIssue(
                    kind="info",
                    message=f"LLM output sanitized — {fi.kind}: {fi.detail}",
                )
            )

        # Disclaimer is appended ONCE, at the very end of the markdown report — not
        # per-section. So we tell the safety validator NOT to inject it here.
        sr = validate_text(
            fidelity.text, biomarker_findings + pattern_findings, append_disclaimer=False
        )
        if sr.text.strip():
            sections.append(ReportSection(title="Plain-language summary", body=sr.text))

    sources = _dedup_sources(biomarker_findings + pattern_findings, rag_context)
    key_insights = _build_key_insights(canonical, biomarker_findings)

    return FinalReport(
        panel=canonical,
        overall_severity=overall,
        summary=summary,
        key_insights=key_insights,
        abnormal_findings=abnormal,
        critical_findings=critical,
        normal_findings=normal,
        healthy_topics=healthy_topics,
        pattern_findings=pattern_findings,
        pattern_notes=pattern_notes,
        diet_advice=diet_advice,
        exercise_advice=exercise_advice,
        recheck_advice=recheck_advice,
        when_to_see_doctor=when_to_doctor,
        validation_issues=issues,
        sections=sections,
        disclaimer=build_disclaimer(),
        sources=sources,
    )


_VERB_HINT_RE = re.compile(
    r"\b(?:aim|limit|replace|build|add|reduce|consider|seek|discuss|"
    r"take|eat|drink|avoid|include|prefer|do|try|target|choose|maintain)\b",
    re.IGNORECASE,
)


def _is_sentence_shaped(line: str) -> bool:
    """A bullet should look like a complete imperative sentence, not a fragment.

    Reject lines that:
      * end with a colon, em-dash, "OR", "AND" (continuation markers)
      * are very short
      * are mostly numbers/units
    Prefer lines that contain a recognizable imperative verb.
    """
    s = line.strip()
    if not s or len(s) < 30:
        return False
    tail = s.rstrip().rstrip(".").strip().lower()
    if tail.endswith((":", "—", " or", " and", " either", " ;", ",")):
        return False
    # Lines like "≥150 minutes/week" without a verb are weak; require either a
    # verb hint or sentence-ending punctuation.
    if not _VERB_HINT_RE.search(s) and not s.rstrip().endswith((".", "!")):
        return False
    return True


def _bulletize(passages, *, default: list[str]) -> list[str]:
    """Turn retrieved passages (or default copy) into short bullets.

    Picker prefers sentence-shaped, imperative-style lines and rejects
    continuation fragments like "For substantial health benefits, adults
    should do EITHER:".
    """
    if not passages:
        return default

    def _is_metadata_line(s: str) -> bool:
        s_low = s.lower()
        if not s:
            return True
        if s.startswith(("#", ">")):
            return True
        if s_low.startswith(("source:", "sources:", "url:", "license:", "citation:")):
            return True
        if s.startswith("http://") or s.startswith("https://"):
            return True
        if s_low.startswith(("available at",)):
            return True
        prefaces = (
            "this is a paraphrased",
            "this is a paraphrase",
            "paraphrased summary",
            "us government work",
            "public domain",
            "cdc content is public",
            "nih ods fact sheets are",
        )
        if any(phrase in s_low for phrase in prefaces):
            return True
        return False

    bullets: list[str] = []
    for p in passages:
        # Two-pass picker:
        #   1. Prefer the first imperative-style line (verb hint matched)
        #   2. Otherwise the first sentence-shaped descriptive line
        #   3. Otherwise any non-metadata line of length ≥ 30
        imperative: Optional[str] = None
        sentence: Optional[str] = None
        any_long: Optional[str] = None
        for raw in p.text.splitlines():
            line = raw.strip().lstrip("-•* ").strip()
            if _is_metadata_line(line):
                continue
            if imperative is None and _is_sentence_shaped(line) and _VERB_HINT_RE.search(line):
                imperative = line
                break
            if sentence is None and _is_sentence_shaped(line):
                sentence = line
            if any_long is None and len(line) >= 30 and not line.endswith(":"):
                any_long = line
        chosen = imperative or sentence or any_long
        if chosen:
            bullets.append(chosen[:240])
        if len(bullets) >= 6:
            break

    seen = set()
    uniq = []
    for b in bullets:
        key = b.lower()[:80]
        if key in seen:
            continue
        seen.add(key)
        uniq.append(b)
    return uniq or default


def _build_summary(
    panel: LabPanel,
    findings: list[Finding],
    patterns: list[Finding],
    healthy_topics: list[str] | None = None,
) -> str:
    sev_to_count: dict[Severity, int] = {s: 0 for s in Severity}
    for f in findings:
        sev_to_count[f.severity] = sev_to_count.get(f.severity, 0) + 1

    parts = [
        f"This report summarizes {len(findings)} biomarker findings across the panel."
    ]
    # Lead with what's healthy — patients want to see this first.
    if sev_to_count[Severity.NORMAL]:
        parts.append(f"{sev_to_count[Severity.NORMAL]} are within their reference range.")
    if healthy_topics:
        if len(healthy_topics) == 1:
            joined = healthy_topics[0]
        elif len(healthy_topics) == 2:
            joined = f"{healthy_topics[0]} and {healthy_topics[1]}"
        else:
            joined = ", ".join(healthy_topics[:-1]) + f", and {healthy_topics[-1]}"
        parts.append(f"Whole-group healthy: {joined}.")
    if sev_to_count[Severity.CRITICAL]:
        parts.append(f"{sev_to_count[Severity.CRITICAL]} are critical and warrant prompt evaluation.")
    if sev_to_count[Severity.ABNORMAL]:
        parts.append(f"{sev_to_count[Severity.ABNORMAL]} are clearly outside reference ranges.")
    if sev_to_count[Severity.BORDERLINE]:
        parts.append(f"{sev_to_count[Severity.BORDERLINE]} are near reference cutoffs.")
    if patterns:
        parts.append("Cross-biomarker patterns are present: " + ", ".join(p.label for p in patterns) + ".")
    if not findings:
        parts.append("No biomarkers in the supplied panel triggered a flag.")

    return " ".join(parts)


_TOPIC_LABEL = {
    "glucose": "glucose",
    "lipids": "lipids",
    "liver": "liver",
    "kidney": "kidney",
    "cbc": "complete blood count",
    "thyroid": "thyroid",
    "inflammation": "inflammation markers",
    "electrolytes": "electrolytes",
    "vitamins": "vitamin levels",
    "anthropometric": "blood pressure / BMI",
}


def _build_recheck_advice(findings: list[Finding]) -> list[str]:
    """Consolidate recheck topics into ONE bullet — the previous version
    emitted nearly-identical templated sentences per topic, which read as
    AI-generated repetition. We now list every flagged topic in a single
    grammatical sentence."""
    topics: list[str] = []
    seen: set[str] = set()
    for f in findings:
        if f.severity in (Severity.BORDERLINE, Severity.ABNORMAL):
            for t in f.related_topics:
                if t == "safety" or t in seen:
                    continue
                seen.add(t)
                topics.append(_TOPIC_LABEL.get(t, t))

    if not topics:
        return ["No specific recheck flagged. Continue routine annual checkups."]

    if len(topics) == 1:
        joined = topics[0]
    elif len(topics) == 2:
        joined = f"{topics[0]} and {topics[1]}"
    else:
        joined = ", ".join(topics[:-1]) + f", and {topics[-1]}"

    return [
        f"Recheck your {joined} in 3–6 months (or as your clinician recommends), "
        "alongside any lifestyle adjustments."
    ]


def _dedup_sources(findings: list[Finding], rag_context: dict) -> list[str]:
    """Stable de-dup, preserving first-seen order, with light normalization."""
    seen: dict[str, str] = {}  # normalized -> original
    ordered: list[str] = []

    def _norm(s: str) -> str:
        return re.sub(r"\s+", " ", s.strip().lower())

    def _add(s: str) -> None:
        if not s:
            return
        key = _norm(s)
        if key in seen:
            return
        seen[key] = s
        ordered.append(s)

    for f in findings:
        _add(f.source)
    for passages in rag_context.values():
        for p in passages:
            _add(p.source)
    return ordered
