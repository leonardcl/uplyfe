"""Anti-hallucination guards for LLM output.

Two independent checks are applied AFTER the LLM has generated its draft and
BEFORE the safety validator strips diagnostic/medication phrasing:

  1. **Numeric fidelity** — every number that appears with a clinical unit
     (mg/dL, mmol/L, U/L, %, mmHg, etc.) must come from one of:
       * a deterministic finding's value
       * a retrieved RAG passage
       * a small whitelist of safe constants (e.g., 100% as in "100% of …")
     A sentence with an unverified clinical number is dropped — better to
     publish silence than a fabricated-but-credible figure.

  2. **Citation enforcement** — every bullet under the "Diet Suggestions" and
     "Exercise Suggestions" headers must end with a `[source: <name>]` or
     `[<topic> | <source>]` style citation. Uncited bullets are dropped.
     This forces the LLM to ground recommendations rather than improvise.

Both checks return the sanitized text plus a list of issues for logging.
"""
from __future__ import annotations

import re
from dataclasses import dataclass, field

from app.models.findings import Finding


# --- Numeric fidelity --------------------------------------------------

_NUMBER_WITH_UNIT_RE = re.compile(
    r"(?<!\w)(?P<num>-?\d{1,5}(?:[.,]\d{1,3})?)\s*"
    r"(?P<unit>"
    r"mg/?d[Ll]|mmol/?L|µ?u?mol/?L|U/?L|g/?d[Ll]|g/?L|"
    r"ng/?m[Ll]|pg/?m[Ll]|mIU/?L|m[mE]q/?L|nmol/?L|pmol/?L|fL|"
    r"10\^[369]/[uµ]?[Ll]|mm/?h(?:r)?|mmHg|"
    r"%|kg/?m\^?2|ng/?dL"
    r")(?!\w)",
    re.IGNORECASE,
)

# Standalone numbers we generally accept without checking — common safe-zone
# guidance values that appear across many passages.
_SAFE_NUMBERS = {
    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,        # small ordinals / single-digit
    15, 20, 30, 45, 60, 90, 120,             # time durations
    150, 300, 1000,                          # exercise-minute targets
    2, 3, 5, 7, 10, 14, 30,                  # day counts
}


@dataclass
class FidelityIssue:
    kind: str           # "numeric_mismatch" | "uncited_bullet"
    detail: str
    sentence: str = ""


@dataclass
class FidelityResult:
    text: str
    issues: list[FidelityIssue] = field(default_factory=list)


def _build_allowed_numbers(
    findings: list[Finding],
    rag_passages_text: str = "",
    *,
    tolerance: float = 0.005,
) -> tuple[set[float], float]:
    """Build the set of numbers the LLM is allowed to reference.

    Returns (allowed_set, tolerance_fraction). Values are matched within
    `tolerance` (default 0.5%) of any allowed number, so genuine rounding
    ("172" vs "172.0") passes but a creative drift ("172" → "175") gets
    flagged. We also accept any value within 0.5 absolute of an allowed
    number, so small-magnitude rounding (e.g., 5.4 ↔ 5.41) still passes.
    """
    allowed: set[float] = set()
    # 1. Every finding's actual value (the user's data).
    for f in findings:
        try:
            allowed.add(float(f.value))
        except (TypeError, ValueError):
            continue
    # 2. Every standalone number that appears in retrieved RAG passages.
    for m in re.finditer(r"-?\d{1,5}(?:[.,]\d{1,3})?", rag_passages_text):
        raw = m.group().replace(",", ".")
        try:
            allowed.add(float(raw))
        except ValueError:
            continue
    # 3. Common safe zones.
    for n in _SAFE_NUMBERS:
        allowed.add(float(n))
    return allowed, tolerance


def _is_allowed(n: float, allowed: set[float], tolerance: float) -> bool:
    if n in allowed:
        return True
    for a in allowed:
        # Absolute-tolerance gate (handles small-magnitude rounding).
        if abs(n - a) <= 0.5:
            return True
        if a == 0:
            continue
        # Relative-tolerance gate (handles larger-magnitude rounding).
        if abs(n - a) / abs(a) <= tolerance:
            return True
    return False


def check_numeric_fidelity(
    text: str,
    findings: list[Finding],
    rag_passages_text: str = "",
) -> FidelityResult:
    """Strip any sentence that contains a clinical-unit number not in the
    allowed set. The allowed set comes from the deterministic findings + the
    retrieved RAG passages + a small safe-numbers list.
    """
    allowed, tol = _build_allowed_numbers(findings, rag_passages_text)
    issues: list[FidelityIssue] = []

    # Split on sentence-terminating punctuation so we can drop one bad sentence
    # without losing the rest of a paragraph.
    sentences = re.split(r"(?<=[.!?])\s+", text)
    out: list[str] = []
    for s in sentences:
        drop = False
        for m in _NUMBER_WITH_UNIT_RE.finditer(s):
            raw = m.group("num").replace(",", ".")
            try:
                n = float(raw)
            except ValueError:
                continue
            if not _is_allowed(n, allowed, tol):
                issues.append(
                    FidelityIssue(
                        kind="numeric_mismatch",
                        detail=f"value {n} {m.group('unit')} not in findings or retrieved passages",
                        sentence=s.strip(),
                    )
                )
                drop = True
                break
        if not drop:
            out.append(s)
    return FidelityResult(text=" ".join(out), issues=issues)


# --- Citation enforcement ----------------------------------------------

# Sections whose bullets MUST end with a citation. Anything else is left alone.
_CITATION_REQUIRED_SECTIONS = ("diet suggestions", "exercise suggestions")

_SECTION_HEADER_RE = re.compile(r"^\s*(?:\d+\.\s*)?(.+?)\s*$")
# A "citation-shaped" tail at the end of a line: [source: …], [topic | source],
# or "(Source: …)". Tolerant of bracket variants.
_CITATION_TAIL_RE = re.compile(
    r"(\[\s*source\s*:\s*[^\]]+\]"
    r"|\[\s*[a-zA-Z_]+\s*\|\s*[^\]]+\]"
    r"|\(\s*source\s*:\s*[^)]+\))\s*[.;,]?\s*$",
    re.IGNORECASE,
)
_BULLET_RE = re.compile(r"^\s*[-*•]\s+(.+?)\s*$")


def enforce_bullet_citations(text: str) -> FidelityResult:
    """Drop bullets that lack a citation tail in citation-required sections.

    Other sections (Overall Health Summary, What to Recheck, etc.) are left
    untouched — they describe the deterministic findings themselves and don't
    need external attribution.
    """
    issues: list[FidelityIssue] = []
    lines = text.splitlines()
    out: list[str] = []
    in_required_section = False
    for line in lines:
        # Detect section headers like "2. Diet Suggestions" or "**Diet Suggestions**".
        stripped = line.strip().lstrip("#").lstrip("*").strip().rstrip(":").rstrip("*").strip()
        m = _SECTION_HEADER_RE.match(stripped) if stripped else None
        header = (m.group(1).lower() if m else "").strip(":").strip()
        # "2. Diet Suggestions" → "diet suggestions"
        header = re.sub(r"^\d+\.\s*", "", header)
        if header in _CITATION_REQUIRED_SECTIONS:
            in_required_section = True
            out.append(line)
            continue
        # Any new section header (numbered list item that isn't a bullet) ends
        # the current required region.
        if re.match(r"^\s*(?:#{1,6}\s+|\*\*.+\*\*\s*$|\d+\.\s+[A-Z])", line) and \
                not _BULLET_RE.match(line):
            in_required_section = False
            out.append(line)
            continue

        bm = _BULLET_RE.match(line)
        if in_required_section and bm:
            content = bm.group(1).strip()
            if not _CITATION_TAIL_RE.search(content):
                issues.append(
                    FidelityIssue(
                        kind="uncited_bullet",
                        detail="diet/exercise bullet missing [source: …] citation",
                        sentence=content,
                    )
                )
                continue  # drop the bullet
        out.append(line)
    return FidelityResult(text="\n".join(out), issues=issues)


# --- Combined entrypoint -----------------------------------------------


def apply_fidelity_guards(
    text: str,
    findings: list[Finding],
    rag_passages_text: str = "",
) -> FidelityResult:
    """Run numeric fidelity then citation enforcement. Combine issues."""
    n_pass = check_numeric_fidelity(text, findings, rag_passages_text)
    c_pass = enforce_bullet_citations(n_pass.text)
    return FidelityResult(
        text=c_pass.text,
        issues=n_pass.issues + c_pass.issues,
    )
