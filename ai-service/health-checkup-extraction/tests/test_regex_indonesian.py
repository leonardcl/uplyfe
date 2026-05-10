"""Indonesian and bilingual lab report extraction tests."""
from __future__ import annotations

from app.models import Biomarker
from app.parsers import regex_extract_panel
from app.parsers.regex_extractor import extract_blood_pressure


PRODIA_STYLE = """
HASIL PEMERIKSAAN LABORATORIUM

  Pemeriksaan              Hasil    Satuan      Nilai Rujukan
  Glukosa Puasa             110     mg/dL       70-99
  Kolesterol Total          230     mg/dL       <200
  LDL                       160     mg/dL       <100
  HDL                        35     mg/dL       >40
  Trigliserida              210     mg/dL       <150
  SGPT (ALT)                 55     U/L         <40
  SGOT (AST)                 40     U/L         <40
  Ureum                      28     mg/dL       17-43
  Kreatinin                 1.0     mg/dL       0.7-1.3
  Asam Urat                 6.8     mg/dL       3.5-7.2
  Hemoglobin               14.5     g/dL        13.5-17.5
  Hematokrit                 44     %           41-53
  Eritrosit                 5.0     10^6/uL     4.5-5.9
  Leukosit                  7.0     10^3/uL     4.5-11
  Trombosit                 250     10^3/uL     150-450
  Natrium                   140     mmol/L      135-145
  Kalium                    4.2     mmol/L      3.5-5.0
  Klorida                   102     mmol/L      98-107

  Tekanan Darah: 132/84 mmHg
"""


def test_indonesian_extracts_core_biomarkers():
    values = regex_extract_panel(PRODIA_STYLE, language="id")
    keys = {v.biomarker for v in values}
    expected = {
        Biomarker.GLUCOSE_FASTING,
        Biomarker.TOTAL_CHOLESTEROL,
        Biomarker.TRIGLYCERIDES,
        Biomarker.ALT,
        Biomarker.AST,
        Biomarker.BUN,            # ureum
        Biomarker.CREATININE,     # kreatinin
        Biomarker.URIC_ACID,      # asam urat
        Biomarker.HEMOGLOBIN,
        Biomarker.HEMATOCRIT,     # hematokrit
        Biomarker.RBC,            # eritrosit
        Biomarker.WBC,            # leukosit
        Biomarker.PLATELETS,      # trombosit
        Biomarker.SODIUM,         # natrium
        Biomarker.POTASSIUM,      # kalium
        Biomarker.CHLORIDE,       # klorida
    }
    missing = expected - keys
    assert not missing, f"Missing biomarkers from ID extraction: {missing}"


def test_indonesian_values_correct():
    values = regex_extract_panel(PRODIA_STYLE, language="id")
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.GLUCOSE_FASTING].value == 110
    assert by_b[Biomarker.TOTAL_CHOLESTEROL].value == 230
    assert by_b[Biomarker.TRIGLYCERIDES].value == 210
    assert by_b[Biomarker.URIC_ACID].value == 6.8
    assert by_b[Biomarker.SODIUM].value == 140
    assert by_b[Biomarker.POTASSIUM].value == 4.2


def test_indonesian_blood_pressure():
    sys, dia = extract_blood_pressure(PRODIA_STYLE)
    assert sys == 132 and dia == 84


def test_auto_mode_handles_bilingual():
    bilingual = """
    Glucose / Glukosa            110     mg/dL    70-99
    Cholesterol / Kolesterol     230     mg/dL    <200
    Triglycerides / Trigliserida 210     mg/dL    <150
    Hemoglobin                   14.5    g/dL     13.5-17.5
    Platelets / Trombosit        250     10^3/uL  150-450
    """
    values = regex_extract_panel(bilingual, language="auto")
    keys = {v.biomarker for v in values}
    assert Biomarker.GLUCOSE_FASTING not in keys  # not "fasting"-marked
    # The first biomarker label seen wins in the inline form, so we get a
    # single hit per biomarker, not duplicates.
    assert len(values) == len({v.biomarker for v in values})


def test_id_mode_still_matches_english_codes():
    """Real Indonesian lab reports use English biomarker codes (LDL, HDL, ALT,
    AST, TSH) alongside Indonesian descriptive labels. 'id' mode must still
    match those codes — only the 'en' mode is exclusive of Indonesian terms."""
    text = """
    LDL              160     mg/dL    <100
    HDL               35     mg/dL    >40
    SGPT              55     U/L      <40
    """
    values = regex_extract_panel(text, language="id")
    keys = {v.biomarker for v in values}
    assert Biomarker.LDL in keys
    assert Biomarker.HDL in keys
    assert Biomarker.ALT in keys


def test_en_mode_does_not_match_indonesian_only_terms():
    """'en' mode should not pick up Indonesian-only terms like 'Trigliserida'."""
    text = """
    Trigliserida    210   mg/dL   <150
    Eritrosit       5.0   10^6/uL 4.5-5.9
    """
    values = regex_extract_panel(text, language="en")
    keys = {v.biomarker for v in values}
    assert Biomarker.TRIGLYCERIDES not in keys
    assert Biomarker.RBC not in keys


def test_comma_decimal_indonesian():
    text = "Kreatinin   1,15   mg/dL   0,7-1,3"
    values = regex_extract_panel(text, language="id")
    by_b = {v.biomarker: v for v in values}
    assert by_b[Biomarker.CREATININE].value == 1.15
