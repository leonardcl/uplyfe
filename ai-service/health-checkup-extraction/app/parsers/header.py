"""Parse age and sex from the patient-info header of a lab report.

Real-world lab reports almost always print the patient's age and sex at the
top, in formats that vary by locale:

    English:    "Age: 45"  •  "45 years"  •  "Sex: M"  •  "Gender: Female"
    Indonesian: "Usia: 45 tahun"  •  "Umur 45"  •  "Jenis Kelamin: Laki-laki"
    Mixed:      "Age/Usia: 45"

We also accept date-of-birth fields and compute age from them when an explicit
age field isn't found.

Pure function, no I/O. Returns (age, sex), either of which may be None when
nothing reliable was matched.
"""
from __future__ import annotations

import re
from datetime import date
from typing import Literal, Optional

Sex = Literal["male", "female", "other", "unknown"]


# --- AGE ---------------------------------------------------------------

_AGE_LABEL_RE = re.compile(
    r"\b(?:age|usia|umur)\s*[:/]?\s*(\d{1,3})\b",
    re.IGNORECASE,
)
_AGE_TRAIL_RE = re.compile(
    r"\b(\d{1,3})\s*(?:years?|tahun|yrs?|y\.?o\.?|y/o)\b",
    re.IGNORECASE,
)

# Disclaimer-style sentences that mention an age but aren't telling us the
# patient's age (e.g. "not ideal for individuals less than 20 years of age").
_AGE_DISCLAIMER_HINTS = (
    "less than", "under ", "below ", "above ", "over ", "more than",
    "older than", "younger than", "any age", "all ages",
    "minor", "adult", "individual", "individuals", "person", "people",
    "kurang dari", "lebih dari", "minimal", "maksimal",
)
_DOB_RE = re.compile(
    r"\b(?:DOB|date\s+of\s+birth|tgl\s+lahir|tanggal\s+lahir|birth\s+date)\s*[:/]?\s*"
    r"(\d{1,2})[/\-.](\d{1,2})[/\-.](\d{2,4})",
    re.IGNORECASE,
)


def _looks_like_age_disclaimer(line: str) -> bool:
    """True if the line has an age-shaped phrase but is generic/disclaimer
    text (e.g. 'not ideal for individuals less than 20 years of age')."""
    low = line.lower()
    return any(hint in low for hint in _AGE_DISCLAIMER_HINTS)


def _find_age(text: str) -> Optional[int]:
    # Prefer explicit "Age:" label hits.
    for line in text.splitlines()[:100]:  # only scan the report header
        if _looks_like_age_disclaimer(line):
            continue
        m = _AGE_LABEL_RE.search(line)
        if m:
            try:
                age = int(m.group(1))
            except ValueError:
                continue
            if 0 < age <= 130:
                return age
    # "45 years" anywhere in the header.
    for line in text.splitlines()[:100]:
        if _looks_like_age_disclaimer(line):
            continue
        m = _AGE_TRAIL_RE.search(line)
        if m:
            try:
                age = int(m.group(1))
            except ValueError:
                continue
            if 0 < age <= 130:
                return age
    # DOB → compute age in completed years vs today.
    m = _DOB_RE.search(text[:8000])
    if m:
        try:
            d, mth, y = int(m.group(1)), int(m.group(2)), int(m.group(3))
            if y < 100:
                y += 1900 if y > 30 else 2000
            # Two common orderings: dd/mm/yyyy and mm/dd/yyyy. Use whichever
            # produces a valid date; prefer dd/mm/yyyy when ambiguous (most of
            # the world).
            for day, month in ((d, mth), (mth, d)):
                if 1 <= month <= 12 and 1 <= day <= 31:
                    try:
                        dob = date(y, month, day)
                        today = date.today()
                        age = today.year - dob.year - (
                            (today.month, today.day) < (dob.month, dob.day)
                        )
                        if 0 < age <= 130:
                            return age
                    except ValueError:
                        continue
        except ValueError:
            pass
    return None


# --- SEX ---------------------------------------------------------------

_SEX_LABEL_RE = re.compile(
    r"\b(?:sex|gender|jenis\s+kelamin|jk)\s*[:/]?\s*"
    r"(male|female|laki[- ]?laki|perempuan|pria|wanita|m|f|l|p)\b",
    re.IGNORECASE,
)


def _find_sex(text: str) -> Optional[Sex]:
    for line in text.splitlines()[:100]:
        m = _SEX_LABEL_RE.search(line)
        if not m:
            continue
        token = m.group(1).strip().lower().replace(" ", "")
        if token in ("male", "m", "laki-laki", "lakilaki", "pria", "l"):
            return "male"
        if token in ("female", "f", "perempuan", "wanita", "p"):
            return "female"
    return None


# --- public API --------------------------------------------------------


def extract_age_sex(text: str) -> tuple[Optional[int], Optional[Sex]]:
    """Find age and sex in the lab-report header. Either may be None."""
    if not text:
        return None, None
    return _find_age(text), _find_sex(text)
