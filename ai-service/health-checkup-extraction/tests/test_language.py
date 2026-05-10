from __future__ import annotations

from app.parsers.language import detect_language


def test_empty_string_is_mixed():
    assert detect_language("") == "mixed"


def test_no_signal_is_mixed():
    assert detect_language("Patient ID 12345\nDate 2024-01-15\n42 100 200") == "mixed"


def test_indonesian_dominant():
    text = """
    HASIL PEMERIKSAAN LABORATORIUM
    Glukosa Puasa       110   mg/dL    70-99    Tinggi
    Kolesterol Total    230   mg/dL    <200     Tinggi
    Trigliserida        210   mg/dL    <150     Tinggi
    Asam Urat           6.8   mg/dL    3.5-7.2  Normal
    Kreatinin           1.0   mg/dL    0.7-1.3  Normal
    Hemoglobin         14.5   g/dL     13.5-17  Normal
    Leukosit            7.0   10^3/uL  4.5-11   Normal
    Trombosit           250   10^3/uL  150-450  Normal
    """
    assert detect_language(text) == "id"


def test_english_dominant():
    text = """
    COMPREHENSIVE METABOLIC PANEL
    Glucose, Fasting    110   mg/dL    70-99    High
    Total Cholesterol   230   mg/dL    <200     High
    Triglycerides       210   mg/dL    <150     High
    Uric Acid           6.8   mg/dL    3.5-7.2  Normal
    Platelets           250   10^3/uL  150-450  Normal
    Sodium              140   mmol/L   135-145  Normal
    """
    assert detect_language(text) == "en"


def test_bilingual_balanced_returns_mixed():
    text = """
    Glukosa / Glucose         110  mg/dL   High / Tinggi
    Kolesterol / Cholesterol  230  mg/dL   High / Tinggi
    Trigliserida / Triglycerides  210  mg/dL  High / Tinggi
    """
    assert detect_language(text) == "mixed"


def test_indonesian_only_terms():
    """Pure ID without any EN terms must return 'id'."""
    text = "Glukosa puasa 110. Hasil tinggi. Nilai rujukan 70-99."
    assert detect_language(text) == "id"


def test_english_only_terms():
    """Pure EN without any ID terms must return 'en'."""
    text = "Glucose 110 mg/dL. Result: high. Reference value 70-99."
    assert detect_language(text) == "en"
