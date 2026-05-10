"""Language detection for lab report text.

A bare-bones keyword counter — counts distinctive Indonesian medical and report
terms vs their English equivalents. No ML; lab vocabulary is small and
unambiguous enough that this is reliable and trivially testable.

Returns one of:
    "id"     — Indonesian-only or Indonesian-dominant
    "en"     — English-only or English-dominant
    "mixed"  — bilingual report or no signal at all (safe default for callers)
"""
from __future__ import annotations

import re
from typing import Literal

Language = Literal["id", "en", "mixed"]


# Distinctive ID terms (must NOT appear in English lab reports).
# Word-boundary anchored so generic substrings don't get false hits.
_ID_TERMS = [
    r"\bglukosa\b", r"\bgula\s+darah\b", r"\bGDP\b", r"\bGDS\b",
    r"\bkolesterol\b", r"\btrigliserida\b", r"\basam\s+urat\b",
    r"\bkreatinin\b", r"\bureum\b",
    r"\beritrosit\b", r"\bleukosit\b", r"\btrombosit\b", r"\bhematokrit\b",
    r"\bnatrium\b", r"\bkalium\b", r"\bklorida\b", r"\bkalsium\b",
    r"\bhasil\b", r"\bnilai\b", r"\brujukan\b", r"\bsatuan\b",
    r"\btinggi\b", r"\brendah\b",
]

# Distinctive EN terms (avoid the universal Latin-rooted ones like
# "hemoglobin", "creatinine" that show up in both languages).
_EN_TERMS = [
    r"\bglucose\b", r"\bcholesterol\b", r"\btriglycerides?\b",
    r"\buric\s+acid\b",
    r"\bplatelets?\b", r"\bhematocrit\b",
    r"\bsodium\b", r"\bpotassium\b", r"\bchloride\b", r"\bcalcium\b",
    r"\bresult\b", r"\breference\b", r"\bvalue\b", r"\bunit\b",
    r"\bhigh\b", r"\blow\b", r"\bnormal\b",
]

_ID_RE = re.compile("|".join(_ID_TERMS), re.IGNORECASE)
_EN_RE = re.compile("|".join(_EN_TERMS), re.IGNORECASE)


def detect_language(text: str) -> Language:
    """Detect the dominant language of a lab report text.

    Heuristic:
      * count distinct ID-only and EN-only term hits
      * "id" if id_count >= 2 * en_count and id_count > 0
      * "en" if en_count >= 2 * id_count and en_count > 0
      * "mixed" otherwise (including all-zero — safe default)
    """
    if not text:
        return "mixed"
    id_count = len(_ID_RE.findall(text))
    en_count = len(_EN_RE.findall(text))

    if id_count == 0 and en_count == 0:
        return "mixed"
    if id_count >= 2 * en_count and id_count > 0:
        return "id"
    if en_count >= 2 * id_count and en_count > 0:
        return "en"
    return "mixed"
