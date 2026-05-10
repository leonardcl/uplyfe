"""Regex-based first-pass extractor.

Why regex first? Because lab reports overwhelmingly have one of two layouts:

  1. Inline:
        Glucose, Fasting        110     mg/dL    70-99
        HbA1c                   6.0     %        <5.7
        LDL Cholesterol         160     mg/dL    <100

  2. Column (common in PDFs whose visual table gets serialized line-by-line):
        FT4
        11.6
        pmol/L
        11.5-19.6

We handle both. A small lookup table catches most reports without paying an LLM
call. The LLM extractor is only used as a fallback for things this misses.

Multilingual: the alias tables are split by language so callers can opt into
ID-only or EN-only matching when the language is known. Default is "auto",
which uses both — bilingual reports work out of the box.
"""
from __future__ import annotations

import re
from typing import Literal, Optional

from app.models.biomarkers import Biomarker
from app.models.lab import LabValue


Language = Literal["id", "en", "mixed", "auto"]


# Patterns: (Biomarker, list of regex aliases)
# Aliases must match the *line label*, not the value. Matched case-insensitively.
_ALIASES_EN: list[tuple[Biomarker, list[str]]] = [
    (Biomarker.GLUCOSE_FASTING, [r"fasting\s*(blood)?\s*glucose", r"glucose,?\s*fasting", r"FBS\b", r"FPG\b"]),
    (Biomarker.GLUCOSE_RANDOM, [r"random\s*glucose", r"glucose,?\s*random", r"RBS\b"]),
    (Biomarker.GLUCOSE_POSTPRANDIAL, [r"postprandial", r"PP\s*(2\s*h)?\s*glucose", r"2\s*h(our)?\s*glucose"]),
    (Biomarker.HBA1C, [r"HbA1c", r"glycated\s+hemoglobin", r"\bA1C\b"]),
    (Biomarker.TOTAL_CHOLESTEROL, [r"total\s+cholesterol", r"\bcholesterol,?\s*total\b"]),
    (Biomarker.LDL, [r"\bLDL\b", r"low[- ]density\s+lipoprotein"]),
    (Biomarker.HDL, [r"\bHDL\b", r"high[- ]density\s+lipoprotein"]),
    (Biomarker.TRIGLYCERIDES, [r"triglycerides?", r"\bTG\b"]),
    (Biomarker.ALT, [r"\bALT\b", r"SGPT", r"alanine\s+amino"]),
    (Biomarker.AST, [r"\bAST\b", r"SGOT", r"aspartate\s+amino"]),
    (Biomarker.ALP, [r"alkaline\s+phosphatase", r"\bALP\b"]),
    (Biomarker.GGT, [r"\bGGT\b", r"gamma[- ]glutamyl"]),
    (Biomarker.BILIRUBIN_TOTAL, [r"bilirubin,?\s*total", r"total\s+bilirubin"]),
    (Biomarker.BILIRUBIN_DIRECT, [r"bilirubin,?\s*direct", r"direct\s+bilirubin"]),
    (Biomarker.ALBUMIN, [r"\balbumin\b"]),
    (Biomarker.CREATININE, [r"creatinine", r"\bSCr\b"]),
    (Biomarker.BUN, [r"\bBUN\b", r"blood\s+urea\s+nitrogen", r"\burea\b"]),
    (Biomarker.EGFR, [r"\beGFR\b"]),
    (Biomarker.URIC_ACID, [r"uric\s+acid"]),
    (Biomarker.HEMOGLOBIN, [r"hemoglobin", r"\bHGB\b", r"\bHb\b"]),
    (Biomarker.HEMATOCRIT, [r"hematocrit", r"\bHCT\b"]),
    (Biomarker.RBC, [r"\bRBC\b", r"red\s+blood\s+cell"]),
    (Biomarker.WBC, [r"\bWBC\b", r"white\s+blood\s+cell"]),
    (Biomarker.PLATELETS, [r"\bplatelets?\b", r"\bPLT\b"]),
    (Biomarker.MCV, [r"\bMCV\b"]),
    (Biomarker.TSH, [r"\bTSH\b", r"thyroid[- ]stimulating"]),
    (Biomarker.FREE_T4, [r"free\s*T4", r"\bFT4\b"]),
    (Biomarker.FREE_T3, [r"free\s*T3", r"\bFT3\b"]),
    (Biomarker.CRP, [r"\bCRP\b", r"c[- ]reactive\s+protein"]),
    (Biomarker.ESR, [r"\bESR\b", r"sed\s+rate"]),
    (Biomarker.SODIUM, [r"\bsodium\b", r"\bNa\b"]),
    (Biomarker.POTASSIUM, [r"\bpotassium\b", r"\bK\+?\b"]),
    (Biomarker.CHLORIDE, [r"\bchloride\b", r"\bCl\b"]),
    (Biomarker.CALCIUM, [r"\bcalcium\b", r"\bCa\b"]),
    (Biomarker.VITAMIN_D_25OH, [r"25.?OH.?vitamin\s*D", r"25\(OH\)D", r"vitamin\s*D,?\s*25"]),
    (Biomarker.VITAMIN_B12, [r"vitamin\s*B12", r"cobalamin"]),
]


# Indonesian aliases — distinctive ID terms only. Universal Latin-rooted terms
# (hemoglobin, creatinine, albumin) appear in both lists, but we keep them only
# in EN to avoid double-counting; the "auto" / "mixed" path uses both lists.
_ALIASES_ID: list[tuple[Biomarker, list[str]]] = [
    (Biomarker.GLUCOSE_FASTING, [r"glukosa\s*puasa", r"gula\s+darah\s+puasa", r"\bGDP\b"]),
    (Biomarker.GLUCOSE_RANDOM, [r"gula\s+darah\s+sewaktu", r"\bGDS\b", r"glukosa\s*sewaktu"]),
    (Biomarker.HBA1C, [r"\bA1c\b"]),
    (Biomarker.TOTAL_CHOLESTEROL, [r"kolesterol\s+total", r"kolesterol,?\s*total"]),
    (Biomarker.TRIGLYCERIDES, [r"trigliserida"]),
    (Biomarker.ALT, [r"\bSGPT\b"]),
    (Biomarker.AST, [r"\bSGOT\b"]),
    (Biomarker.BILIRUBIN_TOTAL, [r"bilirubin\s+total"]),
    (Biomarker.BILIRUBIN_DIRECT, [r"bilirubin\s+(?:direk|direct)"]),
    (Biomarker.CREATININE, [r"kreatinin"]),
    (Biomarker.BUN, [r"\bureum\b"]),
    (Biomarker.URIC_ACID, [r"asam\s+urat"]),
    (Biomarker.HEMOGLOBIN, [r"hemoglobin"]),
    (Biomarker.HEMATOCRIT, [r"hematokrit"]),
    (Biomarker.RBC, [r"eritrosit"]),
    (Biomarker.WBC, [r"leukosit"]),
    (Biomarker.PLATELETS, [r"trombosit"]),
    (Biomarker.SODIUM, [r"natrium"]),
    (Biomarker.POTASSIUM, [r"\bkalium\b"]),
    (Biomarker.CHLORIDE, [r"klorida"]),
    (Biomarker.CALCIUM, [r"kalsium"]),
]


# Recognized unit tokens — used both inside _VALUE_RE and as a standalone-line
# fingerprint when scanning column-layout PDFs.
#
# Indonesian additions:
#   * `juta/uL` (= 10^6/uL) for RBC counts in Indonesian reports
#   * `mm/jam` (= mm/hr) for ESR
#   * Bare `/uL` for raw counts (used with thousand-sep numbers like 8.500/uL)
_UNIT_PATTERN = (
    r"%|mg/?d[Ll]|mmol/?L|µ?u?mol/?L|umol/?L|U/?L|g/?d[Ll]|g/?L|"
    r"ng/?m[Ll]|pg/?m[Ll]|mIU/?L|µ?IU/?m[Ll]|m[mE]q/?L|nmol/?L|"
    r"pmol/?L|fL|10\^[369]/[uµ]?[Ll]|10\^9/L|K/[uµ][Ll]|M/[uµ][Ll]|"
    r"juta/?[uµ]?[Ll]|/[uµ][Ll]|"
    r"mm/?hr?|mm/?jam|mmHg|mg/?L|mmol/?mol|kg/?m\^?2|cm"
)
_UNIT_LINE_RE = re.compile(rf"^\s*({_UNIT_PATTERN})\s*$", re.IGNORECASE)

# Number pattern. Matches:
#   * Plain integers/decimals: 110, 5.4, 13,8
#   * Thousand-separated: 8.500, 245.000, 1,234.56, 1.234.567
# The actual value (decimal vs integer with thousand-seps) is decoded by
# `_parse_number` below — based on whether the LAST separator is followed by
# exactly 3 digits.
_NUMBER_PATTERN = (
    r"-?\d{1,3}(?:[.,]\d{3})+(?:[.,]\d{1,2})?"  # 1.234[.567] or 1,234.56
    r"|-?\d{1,7}(?:[.,]\d{1,3})?"               # plain int / decimal
)

# value pattern: number (int / float) optionally followed by unit token
_VALUE_RE = re.compile(
    rf"""
    (?P<value>{_NUMBER_PATTERN})
    \s*
    (?P<unit>{_UNIT_PATTERN})?
    """,
    re.VERBOSE | re.IGNORECASE,
)

# A bare-number line: "5.40", "Value:  5.40", "Result:  5.40", "300", "8.500"
_BARE_VALUE_RE = re.compile(
    rf"^\s*(?:value|result|hasil)?\s*:?\s*({_NUMBER_PATTERN})\s*$",
    re.IGNORECASE,
)


def _parse_number(s: str) -> float:
    """Decode a number string with mixed thousand-sep / decimal conventions.

    Disambiguation:
      * Multiple separators (e.g. "1.234.567", "1,234.56") → all but the last
        are thousand-separators; the last is decimal iff its trailing digits
        are 1–2 (otherwise it's a thousand-sep too).
      * Single separator with 3-digit trailing AND 2+ digit integer part
        (e.g. "245.000", "12,345") → thousand-separator. (245 mg/dL would be
        cholesterol, 245000 fits platelet count.)
      * Single separator with 3-digit trailing AND single-digit integer part
        (e.g. "0.400", "4.200", "8.500") → DECIMAL (3 decimal places). This
        is the common case for hematocrit fractions and 3-precision hormone
        values like TSH 4.200 mIU/L. Mis-parsing these as thousand-separated
        was the source of "TSH 4200" and "Hct 400%" classification errors.
      * Otherwise the last separator is the decimal point.

    Examples:
        "0.400"      → 0.4      (1-digit int + 3-digit decimal)
        "4.200"      → 4.2      (1-digit int + 3-digit decimal — TSH)
        "245.000"    → 245000   (3-digit int + 3-digit thousand-sep — platelets)
        "1.05"       → 1.05     (2-digit trailing → decimal)
        "13,8"       → 13.8
        "5,40"       → 5.4
        "1.234.567"  → 1234567  (multi-sep, last trailing 3 → thousand-sep)
        "1,234.56"   → 1234.56  (mixed: comma thousand-sep, period decimal)
    """
    s = s.strip().replace(" ", "")
    if not s:
        raise ValueError("empty number")
    seps = [(i, c) for i, c in enumerate(s) if c in ".,"]
    if not seps:
        return float(s)
    last_pos, _last_char = seps[-1]
    after = s[last_pos + 1 :]
    before = s[:seps[0][0]].lstrip("-")  # integer part before the FIRST separator

    # Multi-separator → drop all but the last; last is thousand-sep iff trailing 3.
    if len(seps) >= 2:
        if len(after) == 3 and after.isdigit():
            return float(re.sub(r"[.,]", "", s))
        chars: list[str] = []
        for i, ch in enumerate(s):
            if ch in ".,":
                if i == last_pos:
                    chars.append(".")
            else:
                chars.append(ch)
        return float("".join(chars))

    # Single separator with 3-digit trailing.
    if len(after) == 3 and after.isdigit():
        # Single-digit integer → decimal (e.g. 0.400, 4.200, 8.500).
        # 2+ digit integer → thousand-sep (e.g. 245.000, 12.345).
        if len(before) <= 1:
            return float("-" + before + "." + after if s.startswith("-") else before + "." + after)
        return float(re.sub(r"[.,]", "", s))

    # Single separator with 1- or 2-digit trailing → decimal.
    return float(s.replace(",", "."))

# A reference-range line: "70-99", "<200", ">40", "150-400", "0.27-4.2", also "70 - 99"
_RANGE_LINE_RE = re.compile(
    r"^\s*(?:range|rujukan|nilai\s+rujukan|reference)?\s*:?\s*"
    r"(?:[<>]\s*\d|[-–]?\d+(?:[.,]\d+)?\s*[-–]\s*\d+(?:[.,]\d+)?)",
    re.IGNORECASE,
)

# Pull (low, high) out of a range expression. Handles three shapes:
#   "70-99"          → (70, 99)
#   "0.7 - 1.3"      → (0.7, 1.3)
#   "<200"           → (None, 200)
#   "<= 5.17"        → (None, 5.17)
#   ">40"            → (40, None)
#   "Range: 70-99"   → (70, 99)   (label prefix tolerated)
_RANGE_PARSE_RE = re.compile(
    r"(?:range|rujukan|nilai\s+rujukan|reference)?\s*:?\s*"
    r"(?:"
    r"(?P<op><=?|>=?)\s*(?P<bound>-?\d+(?:[.,]\d+)?)"
    r"|"
    r"(?P<low>-?\d+(?:[.,]\d+)?)\s*[-–]\s*(?P<high>-?\d+(?:[.,]\d+)?)"
    r")",
    re.IGNORECASE,
)


def _parse_reference_range(text: str) -> tuple[Optional[float], Optional[float]]:
    """Parse a printed reference-range string into (low, high).

    Either bound may be None (one-sided constraint). Whitespace and trailing
    units after the range are tolerated.
    """
    if not text:
        return None, None
    m = _RANGE_PARSE_RE.search(text)
    if not m:
        return None, None
    if m.group("low") is not None:
        try:
            low = _parse_number(m.group("low"))
            high = _parse_number(m.group("high"))
        except ValueError:
            return None, None
        return low, high
    if m.group("op") is not None:
        try:
            bound = _parse_number(m.group("bound"))
        except ValueError:
            return None, None
        op = m.group("op")
        if op.startswith("<"):
            return None, bound
        return bound, None
    return None, None

# Page indicators like "19/26" — never a lab value.
_PAGE_INDICATOR_RE = re.compile(r"^\s*\d{1,3}\s*/\s*\d{1,3}\s*$")

# Lines that scream "narrative, not a lab row" — used to skip false positives like
# bibliography references ("Chapter 111: Disorders of Platelets...").
_NARRATIVE_PREFIXES = ("chapter ", "section ", "page ", "ref:", "reference ", "see ")

# Bracketed/symbolic flags that some labs intersperse between value and unit:
#   "110 [H] mg/dL"  →  "110 mg/dL"
#   "5.4 ↑ %"        →  "5.4 %"
# Stripped globally before line-level extraction. Word-form flags (H, L,
# Tinggi, Rendah, High, Low) are NOT stripped — too risky to remove globally.
_INLINE_FLAG_RE = re.compile(r"\s*(?:\[\s*[HLhl*↑↓]\s*\]|[↑↓])\s*")


def _is_narrative(line: str) -> bool:
    s = line.strip().lower()
    if not s:
        return False
    if any(s.startswith(p) for p in _NARRATIVE_PREFIXES):
        return True
    # Long line that ends with a sentence terminator and has many words → prose.
    if len(s) > 60 and s.endswith((".", ":", ";")) and len(s.split()) > 8:
        return True
    return False


def _find_value_after(
    line: str, after_pos: int
) -> Optional[tuple[float, str, Optional[float], Optional[float]]]:
    """Pull (value, unit, ref_low, ref_high) starting AFTER `after_pos` in `line`.

    Searching after the label match prevents grabbing digits that are *part of
    the label itself* — e.g. "HbA1c" → 1, "FT4" → 4. The reference-range tail
    (e.g. "70-99" or "<200") is optional; if absent, ref bounds are None.
    """
    m = _VALUE_RE.search(line, pos=after_pos)
    if not m:
        return None
    try:
        v = _parse_number(m.group("value"))
    except ValueError:
        return None
    unit = (m.group("unit") or "").strip()
    ref_low, ref_high = _parse_reference_range(line[m.end():])
    return v, unit, ref_low, ref_high


def _next_nonempty(lines: list[str], start: int, limit: int = 6) -> list[tuple[int, str]]:
    """Return up to `limit` non-empty lines starting AFTER `start`."""
    out: list[tuple[int, str]] = []
    for i in range(start + 1, min(len(lines), start + 1 + limit * 2)):
        s = lines[i].strip()
        if not s:
            continue
        out.append((i, s))
        if len(out) >= limit:
            break
    return out


def _find_column_value(
    lines: list[str], label_idx: int
) -> Optional[tuple[float, str, Optional[float], Optional[float]]]:
    """Handle column-layout PDFs: label / value / unit / range, each on its own line.

    To avoid false positives (e.g. a page indicator like "19/26" sitting under a
    label), we only accept the value when we see *corroborating context* nearby:
    a recognized unit token on the next line, or a value preceded by
    "Value:"/"Result:", or a reference-range line in the next few lines.

    Returns (value, unit, ref_low, ref_high). Refs may be None when no range
    line is found in the column block.
    """
    nxt = _next_nonempty(lines, label_idx, limit=4)
    if not nxt:
        return None

    # Page indicator immediately under the label → not a lab value.
    if _PAGE_INDICATOR_RE.match(nxt[0][1]):
        return None

    value: Optional[float] = None
    unit: str = ""
    ref_low: Optional[float] = None
    ref_high: Optional[float] = None
    saw_corroboration = False

    for offset, (_, ln) in enumerate(nxt):
        # Pattern A: "Value: 5.40" or "Result: 300" — strong signal.
        m = _BARE_VALUE_RE.match(ln)
        if m and value is None:
            try:
                value = _parse_number(m.group(1))
            except ValueError:
                continue
            if ln.strip().lower().startswith(("value", "result", "hasil")):
                saw_corroboration = True
            continue
        # Pattern B: standalone unit line — confirms the previous value was real.
        if _UNIT_LINE_RE.match(ln):
            unit = ln.strip()
            saw_corroboration = True
            continue
        # Pattern C: reference-range line — also corroborating, AND we capture
        # the printed range so the rules engine can use it instead of our
        # built-in defaults.
        if _RANGE_LINE_RE.match(ln):
            lo, hi = _parse_reference_range(ln)
            if lo is not None or hi is not None:
                ref_low, ref_high = lo, hi
            saw_corroboration = True
            continue
        # Anything else: stop scanning — we've left the value block.
        break

    if value is None or not saw_corroboration:
        return None
    return value, unit, ref_low, ref_high


def _select_alias_tables(language: Language) -> list[tuple[Biomarker, list[str]]]:
    # Real-world Indonesian lab reports (Prodia, Kimia Farma) routinely use
    # English biomarker codes (LDL, HDL, TSH, ALT, AST) alongside Indonesian
    # descriptive labels — so "id" must include EN aliases too. Only "en" is
    # English-exclusive.
    if language == "en":
        return _ALIASES_EN
    return _ALIASES_EN + _ALIASES_ID


def regex_extract_panel(text: str, language: Language = "auto") -> list[LabValue]:
    """Extract LabValues from raw lab-report text using label aliases.

    Handles both inline (`Glucose 110 mg/dL 70-99`) and column-layout
    (`FT4` / `11.6` / `pmol/L` / `11.5-19.6`) reports.

    `language="auto"` (default) uses both ID and EN alias tables, which makes
    bilingual reports work without a language hint. Pass `"en"` or `"id"` to
    restrict — useful when you've already detected the language and want to
    avoid spurious cross-language matches.
    """
    text = _INLINE_FLAG_RE.sub(" ", text)
    aliases = _select_alias_tables(language)

    found: dict[Biomarker, LabValue] = {}
    lines = text.splitlines()

    for i, line in enumerate(lines):
        stripped = line.strip()
        if not stripped:
            continue
        if _is_narrative(stripped):
            continue
        for biomarker, patterns in aliases:
            if biomarker in found:
                continue
            for pat in patterns:
                m = re.search(pat, stripped, flags=re.IGNORECASE)
                if not m:
                    continue
                # Try inline value (after the label match, never inside it).
                parsed = _find_value_after(line, after_pos=m.end())
                if parsed is None:
                    parsed = _find_column_value(lines, i)
                if parsed is None:
                    break
                value, unit, ref_low, ref_high = parsed
                # Reject standalone year-like values with no unit (e.g. "2024").
                if 1900 < value < 2100 and not unit:
                    break
                found[biomarker] = LabValue(
                    biomarker=biomarker,
                    value=value,
                    unit=unit,
                    reference_low=ref_low,
                    reference_high=ref_high,
                )
                break
    return list(found.values())


def extract_blood_pressure(text: str) -> tuple[Optional[float], Optional[float]]:
    """Find a "BP 132/88" or "Tekanan Darah 132/88" style reading anywhere in the text."""
    m = re.search(
        r"\b(?:BP|blood\s+pressure|tekanan\s+darah|TD)[^0-9]{0,15}(\d{2,3})\s*/\s*(\d{2,3})",
        text,
        re.IGNORECASE,
    )
    if not m:
        return None, None
    return float(m.group(1)), float(m.group(2))
