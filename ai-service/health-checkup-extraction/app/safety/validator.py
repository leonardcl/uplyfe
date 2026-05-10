"""Post-generation safety validator.

The LLM has been told not to diagnose or prescribe. It will sometimes do it
anyway. This module:

  1. Detects diagnostic phrases and prescriptive phrases by regex.
  2. Rewrites the most common offenders deterministically (no LLM round-trip
     unless the user opts in to a rewriter pass).
  3. Surfaces escalation messaging when any finding has escalate=True.
  4. Always appends a disclaimer.

Critically, this is enforced *after* the LLM regardless of what the LLM said,
so the safety guarantees do not depend on the LLM following instructions.
"""
from __future__ import annotations

import re
from dataclasses import dataclass
from typing import Optional

from app.config import get_settings
from app.models.findings import Finding, Severity


# --- Forbidden phrasing patterns ---

# "You have <X>" / "you suffer from" / "this means you have"
_DIAGNOSTIC_RE = re.compile(
    r"\b(you|the\s+patient)\s+(have|has|are\s+suffering\s+from|suffer\s+from)\s+(?!the\s+results?)",
    re.IGNORECASE,
)

# Replace diagnostic phrasing with safer "this result is in the ... range"
_DIAGNOSTIC_REPLACE = "this result is in the range associated with"

# Generic medication-recommendation triggers
_MED_RE = re.compile(
    r"\b("
    r"start(?:ing)?\s+(?:taking\s+)?(?:medication|metformin|statin|atorvastatin|lisinopril|losartan|amlodipine|insulin|glipizide|warfarin|aspirin)|"
    r"(?:i|we|you)\s+recommend\s+(?:taking|starting)\s+\w+|"
    r"prescribe|prescription"
    r")\b",
    re.IGNORECASE,
)

# Phrases that confirm a diagnosis
_CONFIRM_RE = re.compile(
    r"\b(this\s+confirms|this\s+proves|definitively\s+diagnoses?)\b",
    re.IGNORECASE,
)


DEFAULT_DISCLAIMER = (
    "**Disclaimer.** This report is informational only and is not a medical diagnosis or "
    "treatment recommendation. Reference ranges vary by lab, and a single value is not "
    "sufficient to diagnose any condition. Always discuss your results with a qualified "
    "healthcare professional, especially for any abnormal or critical findings."
)


@dataclass
class SafetyReport:
    text: str
    rewrites: list[str]
    blocked: list[str]
    escalation_lines: list[str]


def validate_text(
    text: str,
    findings: list[Finding],
    *,
    append_disclaimer: Optional[bool] = None,
) -> SafetyReport:
    """Sanitize free-form report text and inject escalations + disclaimer.

    `append_disclaimer`:
      * None (default): use the global setting `ALWAYS_INCLUDE_DISCLAIMER`.
      * True / False: explicit override. Use False when the caller plans to
        render the disclaimer once at the report level (avoids duplication).
    """
    rewrites: list[str] = []
    blocked: list[str] = []

    # 1. Strip / rewrite diagnostic phrasing
    def _rewrite_diag(m: re.Match) -> str:
        rewrites.append(f"diagnostic phrasing rewritten: {m.group(0)!r}")
        return _DIAGNOSTIC_REPLACE + " "

    sanitized = _DIAGNOSTIC_RE.sub(_rewrite_diag, text)

    # 2. Block medication recommendations entirely
    def _block_med(m: re.Match) -> str:
        blocked.append(f"medication phrase blocked: {m.group(0)!r}")
        return "[medication advice removed — please consult a clinician]"

    sanitized = _MED_RE.sub(_block_med, sanitized)

    # 3. Strip "this confirms..." style claims
    def _block_conf(m: re.Match) -> str:
        blocked.append(f"diagnostic confirmation blocked: {m.group(0)!r}")
        return "this is consistent with"

    sanitized = _CONFIRM_RE.sub(_block_conf, sanitized)

    # 4. Build escalation lines
    settings = get_settings()
    escalations: list[str] = []
    if settings.emergency_thresholds_enabled:
        for f in findings:
            if f.escalate or f.severity == Severity.CRITICAL:
                escalations.append(
                    f"⚠ {f.biomarker.value} ({f.value} {f.unit}) — {f.label}. "
                    "This warrants prompt professional evaluation."
                )

    # 5. Append disclaimer if not already present
    do_append = (
        settings.always_include_disclaimer if append_disclaimer is None else append_disclaimer
    )
    if do_append and "Disclaimer" not in sanitized:
        sanitized = sanitized.rstrip() + "\n\n" + DEFAULT_DISCLAIMER

    return SafetyReport(text=sanitized, rewrites=rewrites, blocked=blocked, escalation_lines=escalations)


def build_disclaimer() -> str:
    return DEFAULT_DISCLAIMER


def summarize_emergencies(findings: list[Finding]) -> list[str]:
    msgs: list[str] = []
    for f in findings:
        if f.escalate or f.severity == Severity.CRITICAL:
            msgs.append(
                f"{f.biomarker.value} = {f.value} {f.unit} → {f.label}. "
                "Seek prompt professional evaluation."
            )
    return msgs
