"""Tests for age and sex extraction from lab-report headers."""
from __future__ import annotations

from app.parsers.header import extract_age_sex


def test_explicit_age_label():
    text = "Patient: John Doe\nAge: 45\nSex: M"
    age, sex = extract_age_sex(text)
    assert age == 45 and sex == "male"


def test_indonesian_usia_label():
    text = "Pasien: Andi\nUsia: 38 tahun\nJenis Kelamin: Laki-laki"
    age, sex = extract_age_sex(text)
    assert age == 38 and sex == "male"


def test_age_in_years_phrase():
    text = "John Doe, 56 years\nMale"
    age, sex = extract_age_sex(text)
    assert age == 56


def test_disclaimer_age_phrase_is_ignored():
    """'less than 20 years of age' is a generic disclaimer, not the patient's
    age. This was a real false-positive on the 360 Health Vectors template."""
    text = """
    Personal Health Report
    The analyzed information in the Smart Report is not ideal for
    individuals less than 20 years of age.
    """
    age, _ = extract_age_sex(text)
    assert age is None


def test_age_with_label_takes_precedence_over_disclaimer():
    text = """
    Disclaimer: not for individuals less than 20 years of age.
    Age: 47
    """
    age, _ = extract_age_sex(text)
    assert age == 47


def test_female_indonesian():
    text = "Jenis Kelamin: Perempuan"
    _, sex = extract_age_sex(text)
    assert sex == "female"


def test_no_signal_returns_none():
    age, sex = extract_age_sex("Cholesterol 200 mg/dL\nGlucose 95 mg/dL")
    assert age is None and sex is None
