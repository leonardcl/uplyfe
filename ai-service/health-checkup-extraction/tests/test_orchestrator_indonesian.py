"""End-to-end orchestrator tests for Indonesian and bilingual lab reports.

Runs the full deterministic pipeline (no LLM, no RAG) so the test is fast and
hermetic. Verifies that the new language-aware extraction pipeline correctly
parses ID-style and multi-page inputs.
"""
from __future__ import annotations

from app.models import Biomarker, Severity
from app.pipeline import PipelineOptions, run_pipeline


PRODIA_REPORT = """
HASIL PEMERIKSAAN LABORATORIUM

Pasien: A.B.C.   Usia: 26 tahun

Pemeriksaan              Hasil    Satuan      Nilai Rujukan
Glukosa Puasa             110     mg/dL       70-99
Kolesterol Total          230     mg/dL       <200
LDL                       160     mg/dL       <100
HDL                        35     mg/dL       >40
Trigliserida              210     mg/dL       <150
SGPT                       55     U/L         <40
SGOT                       40     U/L         <40
Ureum                      28     mg/dL       17-43
Kreatinin                 1.0     mg/dL       0.7-1.3
Asam Urat                 6.8     mg/dL       3.5-7.2
Hemoglobin               14.5     g/dL        13.5-17.5
Hematokrit                 44     %           41-53
Leukosit                  7.0     10^3/uL     4.5-11
Trombosit                 250     10^3/uL     150-450
Natrium                   140     mmol/L      135-145
Kalium                    4.2     mmol/L      3.5-5.0
Klorida                   102     mmol/L      98-107

Tekanan Darah: 132/84 mmHg
"""


def test_indonesian_pipeline_extracts_canonical_biomarkers():
    report = run_pipeline(
        raw_text=PRODIA_REPORT,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    biomarkers = {v.biomarker for v in report.panel.values}
    expected = {
        Biomarker.GLUCOSE_FASTING,
        Biomarker.TOTAL_CHOLESTEROL,
        Biomarker.LDL,
        Biomarker.HDL,
        Biomarker.TRIGLYCERIDES,
        Biomarker.ALT,           # SGPT
        Biomarker.AST,           # SGOT
        Biomarker.BUN,           # ureum
        Biomarker.CREATININE,    # kreatinin
        Biomarker.URIC_ACID,     # asam urat
        Biomarker.HEMOGLOBIN,
        Biomarker.HEMATOCRIT,    # hematokrit
        Biomarker.WBC,           # leukosit
        Biomarker.PLATELETS,     # trombosit
        Biomarker.SODIUM,        # natrium
        Biomarker.POTASSIUM,     # kalium
        Biomarker.CHLORIDE,      # klorida
        Biomarker.BP_SYSTOLIC,
        Biomarker.BP_DIASTOLIC,
    }
    missing = expected - biomarkers
    assert not missing, f"Missing biomarkers in ID pipeline: {missing}"


def test_indonesian_pipeline_flags_abnormalities():
    report = run_pipeline(
        raw_text=PRODIA_REPORT,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    # The synthetic panel includes prediabetes glucose, hypertension stage 1,
    # high LDL, low HDL, high triglycerides — overall must be at least abnormal.
    assert report.overall_severity in (Severity.ABNORMAL, Severity.BORDERLINE, Severity.CRITICAL)
    assert len(report.abnormal_findings) >= 3


def test_multipage_pdf_text_with_form_feed_extracts_across_pages():
    """Simulate a 3-page PDF where biomarkers are spread across pages, joined
    by form-feed (\\f) the way pymupdf does it."""
    page1 = "HASIL PEMERIKSAAN\nPasien: X\n"
    page2 = "Glukosa Puasa   110   mg/dL   70-99\n"
    page3 = "Kreatinin   1.0   mg/dL   0.7-1.3\nAsam Urat   6.8   mg/dL   3.5-7.2\n"
    multi_page = "\f".join([page1, page2, page3])
    report = run_pipeline(
        raw_text=multi_page,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    biomarkers = {v.biomarker for v in report.panel.values}
    assert Biomarker.GLUCOSE_FASTING in biomarkers
    assert Biomarker.CREATININE in biomarkers
    assert Biomarker.URIC_ACID in biomarkers


def test_english_pipeline_still_works_after_refactor():
    """Regression check — the existing English happy path must still extract."""
    en_report = """
    COMPREHENSIVE METABOLIC PANEL
    Glucose, Fasting        110   mg/dL    70-99
    Total Cholesterol       230   mg/dL    <200
    LDL                     160   mg/dL    <100
    HDL                      35   mg/dL    >40
    Triglycerides           210   mg/dL    <150
    Creatinine              1.0   mg/dL    0.7-1.3
    Hemoglobin             14.5   g/dL     13.5-17.5
    BP 132/84 mmHg
    """
    report = run_pipeline(
        raw_text=en_report,
        options=PipelineOptions(use_llm=False, use_rag=False),
    )
    biomarkers = {v.biomarker for v in report.panel.values}
    assert Biomarker.GLUCOSE_FASTING in biomarkers
    assert Biomarker.LDL in biomarkers
    assert Biomarker.HDL in biomarkers
    assert Biomarker.BP_SYSTOLIC in biomarkers
