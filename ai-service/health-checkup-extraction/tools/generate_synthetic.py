"""Generate realistic synthetic Indonesian/bilingual lab report PDFs.

Each sample mimics the layout of a major Indonesian lab chain (Prodia, Kimia
Farma) with deliberate variations — bilingual labels, comma decimals, thousand
separators, multi-page layouts — so the parsing pipeline gets stress-tested
against patterns it doesn't see in the deterministic unit tests.

Each PDF is paired with an `expected.json` file under
`samples/expected/<name>.json` that declares the ground-truth biomarker values
the pipeline should recover. The `hcx-eval` CLI reads both and reports recall.

Run:
    python tools/generate_synthetic.py
"""
from __future__ import annotations

import json
from pathlib import Path

from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak,
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.enums import TA_LEFT, TA_CENTER


PROJECT_ROOT = Path(__file__).resolve().parent.parent
SAMPLES_DIR = PROJECT_ROOT / "samples" / "synthetic"
EXPECTED_DIR = PROJECT_ROOT / "samples" / "expected"


def _styles():
    base = getSampleStyleSheet()
    return {
        "title": ParagraphStyle(
            "title", parent=base["Title"], fontSize=14, alignment=TA_CENTER, spaceAfter=4
        ),
        "subtitle": ParagraphStyle(
            "subtitle", parent=base["Normal"], fontSize=9, alignment=TA_CENTER, textColor=colors.grey
        ),
        "section": ParagraphStyle(
            "section", parent=base["Heading2"], fontSize=11, spaceBefore=10, spaceAfter=4,
            textColor=colors.HexColor("#1f4e79"),
        ),
        "label": ParagraphStyle(
            "label", parent=base["Normal"], fontSize=9, alignment=TA_LEFT
        ),
        "small": ParagraphStyle(
            "small", parent=base["Normal"], fontSize=8, textColor=colors.grey
        ),
    }


def _result_table(rows, header_labels):
    """Standard 4 or 5 column results table."""
    data = [header_labels] + rows
    cols = len(header_labels)
    if cols == 5:
        widths = [55*mm, 25*mm, 25*mm, 35*mm, 25*mm]
    else:
        widths = [60*mm, 30*mm, 30*mm, 50*mm]
    table = Table(data, colWidths=widths, repeatRows=1)
    table.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#e8eef7")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.HexColor("#1f4e79")),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("ALIGN", (0, 0), (-1, 0), "LEFT"),
        ("ALIGN", (1, 1), (1, -1), "RIGHT"),
        ("ALIGN", (2, 1), (2, -1), "LEFT"),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#f7f9fc")]),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 3),
        ("LINEBELOW", (0, 0), (-1, 0), 1, colors.HexColor("#1f4e79")),
    ]))
    return table


def _patient_block(name, age, sex, sample_id, collected_on, doctor):
    data = [
        ["Nama / Name", ":", name, "No. Sampel", ":", sample_id],
        ["Usia / Age", ":", f"{age} tahun", "Tgl. Pengambilan", ":", collected_on],
        ["Jenis Kelamin", ":", sex, "Dokter Pengirim", ":", doctor],
    ]
    table = Table(data, colWidths=[34*mm, 4*mm, 65*mm, 35*mm, 4*mm, 38*mm])
    table.setStyle(TableStyle([
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 1),
        ("TOPPADDING", (0, 0), (-1, -1), 1),
        ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
        ("FONTNAME", (3, 0), (3, -1), "Helvetica-Bold"),
    ]))
    return table


def _disclaimer():
    s = _styles()
    return Paragraph(
        "Hasil ini hanya untuk keperluan pemeriksaan medis. Konsultasikan dengan dokter Anda.",
        s["small"],
    )


# ---------- sample generators ----------


def sample_prodia_typical():
    """Clean Prodia-style report: English biomarker codes, status column with
    H/L flags, single page, period-decimal numbers."""
    s = _styles()
    story = [
        Paragraph("LABORATORIUM KLINIK PRODIA", s["title"]),
        Paragraph("Jl. Kramat Raya No. 150, Jakarta Pusat — Tel. (021) 31904711", s["subtitle"]),
        Spacer(1, 6),
        Paragraph("HASIL PEMERIKSAAN LABORATORIUM", s["title"]),
        Spacer(1, 6),
        _patient_block("Andi Wijaya", 38, "Laki-laki", "P-2026-0501-118", "9 Mei 2026", "dr. Sari Putri"),
        Spacer(1, 8),

        Paragraph("PROFIL LIPID / LIPID PROFILE", s["section"]),
        _result_table(
            [
                ["Cholesterol Total", "245",  "mg/dL",  "< 200",       "[H]"],
                ["LDL Cholesterol",   "172",  "mg/dL",  "< 100",       "[H]"],
                ["HDL Cholesterol",   "32",   "mg/dL",  "> 40",        "[L]"],
                ["Trigliserida",      "265",  "mg/dL",  "< 150",       "[H]"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),

        Paragraph("KIMIA KLINIK / CLINICAL CHEMISTRY", s["section"]),
        _result_table(
            [
                ["Glukosa Puasa",        "128",  "mg/dL",  "70 - 99",     "[H]"],
                ["HbA1c",                "6.4",  "%",      "< 5.7",       "[H]"],
                ["SGPT (ALT)",           "62",   "U/L",    "< 40",        "[H]"],
                ["SGOT (AST)",           "44",   "U/L",    "< 40",        "[H]"],
                ["Kreatinin",            "1.05", "mg/dL",  "0.7 - 1.3",   ""],
                ["Ureum (BUN)",          "30",   "mg/dL",  "17 - 43",     ""],
                ["Asam Urat",            "7.4",  "mg/dL",  "3.5 - 7.2",   "[H]"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),

        Paragraph("HEMATOLOGI / HEMATOLOGY", s["section"]),
        _result_table(
            [
                ["Hemoglobin",   "14.6",  "g/dL",      "13.5 - 17.5",   ""],
                ["Hematokrit",   "44",    "%",         "41 - 53",       ""],
                ["Leukosit",     "7.2",   "10^3/uL",   "4.5 - 11.0",    ""],
                ["Trombosit",    "248",   "10^3/uL",   "150 - 450",     ""],
                ["MCV",          "88",    "fL",        "80 - 100",      ""],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),

        Paragraph("VITAL SIGNS", s["section"]),
        _result_table(
            [
                ["Tekanan Darah / Blood Pressure", "142/90", "mmHg", "<120/80", "[H]"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "total_cholesterol": 245,
            "ldl": 172,
            "hdl": 32,
            "triglycerides": 265,
            "glucose_fasting": 128,
            "hba1c": 6.4,
            "alt": 62,
            "ast": 44,
            "creatinine": 1.05,
            "bun": 30,
            "uric_acid": 7.4,
            "hemoglobin": 14.6,
            "hematocrit": 44,
            "wbc": 7.2,
            "platelets": 248,
            "mcv": 88,
            "bp_systolic": 142,
            "bp_diastolic": 90,
        }
    }
    return "prodia_typical", story, expected


def sample_kimia_farma_bilingual():
    """Kimia Farma-style: heavily bilingual labels, Indonesian units (mm/jam,
    juta/µL), period decimals, no flag column (status text in another column)."""
    s = _styles()
    story = [
        Paragraph("KIMIA FARMA DIAGNOSTIKA", s["title"]),
        Paragraph("Klinik Cabang Bandung — Jl. Asia Afrika No. 65", s["subtitle"]),
        Spacer(1, 6),
        Paragraph("Hasil Pemeriksaan Laboratorium / Laboratory Test Result", s["title"]),
        Spacer(1, 6),
        _patient_block("Siti Rahayu", 52, "Perempuan", "KF-26050907", "08-05-2026", "dr. Rudi Hartono"),
        Spacer(1, 8),

        Paragraph("HEMATOLOGI / HEMATOLOGY", s["section"]),
        _result_table(
            [
                ["Hemoglobin (Hb)",          "11.8",   "g/dL",       "12.0 - 15.0",  "Rendah"],
                ["Eritrosit / RBC",           "4.1",   "juta/uL",    "4.5 - 5.9",    "Rendah"],
                ["Leukosit / WBC",            "9.4",   "10^3/uL",    "4.5 - 11.0",   "Normal"],
                ["Hematokrit / Hct",          "36",    "%",          "36 - 46",      "Normal"],
                ["Trombosit / Platelets",     "320",   "10^3/uL",    "150 - 450",    "Normal"],
                ["MCV",                        "84",   "fL",          "80 - 100",     "Normal"],
                ["LED / ESR",                  "28",   "mm/jam",      "< 20",         "Tinggi"],
            ],
            ["Pemeriksaan / Examination", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),

        Paragraph("KIMIA DARAH / BLOOD CHEMISTRY", s["section"]),
        _result_table(
            [
                ["Glukosa Puasa / Fasting Glucose",  "112",   "mg/dL",   "70 - 99",     "Tinggi"],
                ["Kolesterol Total / Total Chol.",   "215",   "mg/dL",   "< 200",       "Tinggi"],
                ["Trigliserida / Triglycerides",     "180",   "mg/dL",   "< 150",       "Tinggi"],
                ["Kreatinin / Creatinine",           "0.9",   "mg/dL",   "0.6 - 1.1",   "Normal"],
                ["Asam Urat / Uric Acid",            "5.8",   "mg/dL",   "2.4 - 5.7",   "Tinggi"],
                ["Natrium / Sodium",                 "138",   "mmol/L",  "135 - 145",   "Normal"],
                ["Kalium / Potassium",               "4.4",   "mmol/L",  "3.5 - 5.0",   "Normal"],
                ["Klorida / Chloride",               "104",   "mmol/L",  "98 - 107",    "Normal"],
                ["TSH",                              "3.2",   "mIU/L",   "0.4 - 4.0",   "Normal"],
            ],
            ["Pemeriksaan / Examination", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "hemoglobin": 11.8,
            "rbc": 4.1,
            "wbc": 9.4,
            "hematocrit": 36,
            "platelets": 320,
            "mcv": 84,
            "esr": 28,
            "glucose_fasting": 112,
            "total_cholesterol": 215,
            "triglycerides": 180,
            "creatinine": 0.9,
            "uric_acid": 5.8,
            "sodium": 138,
            "potassium": 4.4,
            "chloride": 104,
            "tsh": 3.2,
        }
    }
    return "kimia_farma_bilingual", story, expected


def sample_comma_decimals_and_thousand_seps():
    """Indonesian-locale numbers: comma decimal (`5,4`) and thousand separators
    (`6.500` Indonesian style or `6,500` US-influenced). Tests our number
    parsing edge cases."""
    s = _styles()
    story = [
        Paragraph("LABORATORIUM RUMAH SAKIT MITRA SEHAT", s["title"]),
        Paragraph("Jl. Diponegoro No. 12, Surabaya", s["subtitle"]),
        Spacer(1, 6),
        Paragraph("HASIL PEMERIKSAAN LABORATORIUM", s["title"]),
        Spacer(1, 6),
        _patient_block("Bambang Sutrisno", 45, "Laki-laki", "MS-26050314", "07/05/2026", "dr. Rina W."),
        Spacer(1, 8),

        Paragraph("DARAH LENGKAP", s["section"]),
        _result_table(
            [
                # Indonesian comma decimal
                ["Hemoglobin",     "13,8",  "g/dL",       "13,5 - 17,5",   "Normal"],
                ["Hematokrit",     "41",    "%",          "41 - 53",       "Normal"],
                # Multi-digit integer + 3-digit trailing → unambiguous thousand-sep.
                ["Trombosit",      "245.000","/uL",       "150.000 - 450.000","Normal"],
                # Modern lab convention: scaled 10^3/uL units instead of raw counts —
                # avoids the "8.500" decimal-vs-thousand-sep ambiguity.
                ["Leukosit",       "8,5",   "10^3/uL",    "4,5 - 11,0",    "Normal"],
                ["Eritrosit",      "4,8",   "juta/uL",    "4,5 - 5,9",     "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),

        Paragraph("KIMIA DARAH", s["section"]),
        _result_table(
            [
                ["Glukosa Puasa",      "98",     "mg/dL",   "70 - 99",     "Normal"],
                ["Kolesterol Total",   "208",    "mg/dL",   "< 200",       "Tinggi"],
                ["Trigliserida",       "162",    "mg/dL",   "< 150",       "Tinggi"],
                ["LDL",                "138",    "mg/dL",   "< 100",       "Tinggi"],
                ["HDL",                "42",     "mg/dL",   "> 40",        "Normal"],
                ["SGPT",               "32",     "U/L",     "< 40",        "Normal"],
                ["SGOT",               "28",     "U/L",     "< 40",        "Normal"],
                ["Kreatinin",          "1,12",   "mg/dL",   "0,7 - 1,3",   "Normal"],
                ["Ureum",              "24",     "mg/dL",   "17 - 43",     "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "hemoglobin": 13.8,
            "hematocrit": 41,
            # WBC reported as 8.5 ×10^3/uL (modern convention).
            "wbc": 8.5,
            # Platelets in raw /uL with multi-digit thousand-sep is unambiguous.
            "platelets": 245000,
            "rbc": 4.8,
            "glucose_fasting": 98,
            "total_cholesterol": 208,
            "triglycerides": 162,
            "ldl": 138,
            "hdl": 42,
            "alt": 32,
            "ast": 28,
            "creatinine": 1.12,
            "bun": 24,
        }
    }
    return "thousand_seps_and_comma_decimal", story, expected


def sample_multipage_long():
    """3-page report with biomarkers spread across pages — exercises the
    multi-page chunker that joins pymupdf form-feeds back into chunks."""
    s = _styles()
    story = [
        Paragraph("LABORATORIUM KLINIK PRAMITA", s["title"]),
        Paragraph("Cabang Yogyakarta — Multi-Page Comprehensive Panel", s["subtitle"]),
        Spacer(1, 6),
        _patient_block("Dewi Lestari", 29, "Perempuan", "PR-26050822", "06-05-2026", "dr. Tono S."),
        Spacer(1, 8),
        Paragraph("PROFIL HEMATOLOGI (HALAMAN 1/3)", s["section"]),
        _result_table(
            [
                ["Hemoglobin",     "13.2",  "g/dL",       "12 - 16",       "Normal"],
                ["Hematokrit",     "39",    "%",          "36 - 46",       "Normal"],
                ["Leukosit",       "6.5",   "10^3/uL",    "4.5 - 11.0",    "Normal"],
                ["Trombosit",      "295",   "10^3/uL",    "150 - 450",     "Normal"],
                ["Eritrosit",      "4.6",   "10^6/uL",    "4.5 - 5.9",     "Normal"],
                ["MCV",            "85",    "fL",         "80 - 100",      "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Paragraph("Catatan: Hasil hematologi dalam batas normal.", s["small"]),
        PageBreak(),

        Paragraph("PROFIL KIMIA DARAH (HALAMAN 2/3)", s["section"]),
        _result_table(
            [
                ["Glukosa Puasa",       "92",    "mg/dL",   "70 - 99",     "Normal"],
                ["HbA1c",               "5.4",   "%",       "< 5.7",       "Normal"],
                ["Kolesterol Total",    "188",   "mg/dL",   "< 200",       "Normal"],
                ["LDL",                 "115",   "mg/dL",   "< 100",       "Tinggi"],
                ["HDL",                 "55",    "mg/dL",   "> 40",        "Normal"],
                ["Trigliserida",        "108",   "mg/dL",   "< 150",       "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Paragraph("Catatan: Pertimbangkan diet rendah kolesterol.", s["small"]),
        PageBreak(),

        Paragraph("FUNGSI HATI, GINJAL & TIROID (HALAMAN 3/3)", s["section"]),
        _result_table(
            [
                ["SGPT",                "22",    "U/L",     "< 35",        "Normal"],
                ["SGOT",                "20",    "U/L",     "< 35",        "Normal"],
                ["Bilirubin Total",     "0.8",   "mg/dL",   "< 1.2",       "Normal"],
                ["Kreatinin",           "0.78",  "mg/dL",   "0.6 - 1.1",   "Normal"],
                ["Ureum",               "18",    "mg/dL",   "17 - 43",     "Normal"],
                ["Asam Urat",           "4.2",   "mg/dL",   "2.4 - 5.7",   "Normal"],
                ["TSH",                 "1.8",   "mIU/L",   "0.4 - 4.0",   "Normal"],
                ["Natrium",             "139",   "mmol/L",  "135 - 145",   "Normal"],
                ["Kalium",              "4.1",   "mmol/L",  "3.5 - 5.0",   "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Spacer(1, 8),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "hemoglobin": 13.2,
            "hematocrit": 39,
            "wbc": 6.5,
            "platelets": 295,
            "rbc": 4.6,
            "mcv": 85,
            "glucose_fasting": 92,
            "hba1c": 5.4,
            "total_cholesterol": 188,
            "ldl": 115,
            "hdl": 55,
            "triglycerides": 108,
            "alt": 22,
            "ast": 20,
            "bilirubin_total": 0.8,
            "creatinine": 0.78,
            "bun": 18,
            "uric_acid": 4.2,
            "tsh": 1.8,
            "sodium": 139,
            "potassium": 4.1,
        }
    }
    return "pramita_multipage", story, expected


def sample_english_baseline():
    """Pure English Quest/LabCorp-style report. Regression baseline — must
    stay close to the existing English happy path."""
    s = _styles()
    story = [
        Paragraph("QUEST DIAGNOSTICS — COMPREHENSIVE METABOLIC PANEL", s["title"]),
        Paragraph("Patient: J. Doe — Sample collected 05/06/2026", s["subtitle"]),
        Spacer(1, 8),
        Paragraph("RESULTS", s["section"]),
        _result_table(
            [
                ["Glucose, Fasting",  "112",   "mg/dL",  "70-99",         "H"],
                ["Total Cholesterol", "232",   "mg/dL",  "<200",          "H"],
                ["LDL Cholesterol",   "165",   "mg/dL",  "<100",          "H"],
                ["HDL Cholesterol",   "38",    "mg/dL",  ">40",           "L"],
                ["Triglycerides",     "215",   "mg/dL",  "<150",          "H"],
                ["ALT (SGPT)",        "48",    "U/L",    "<40",           "H"],
                ["AST (SGOT)",        "35",    "U/L",    "<40",           ""],
                ["Creatinine",        "1.0",   "mg/dL",  "0.7-1.3",       ""],
                ["Hemoglobin",        "14.5",  "g/dL",   "13.5-17.5",     ""],
                ["WBC",               "7.0",   "10^3/uL","4.5-11",        ""],
                ["Platelets",         "250",   "10^3/uL","150-450",       ""],
                ["BP",                "138/86","mmHg",   "<120/80",       "H"],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "glucose_fasting": 112,
            "total_cholesterol": 232,
            "ldl": 165,
            "hdl": 38,
            "triglycerides": 215,
            "alt": 48,
            "ast": 35,
            "creatinine": 1.0,
            "hemoglobin": 14.5,
            "wbc": 7.0,
            "platelets": 250,
            "bp_systolic": 138,
            "bp_diastolic": 86,
        }
    }
    return "english_quest_style", story, expected


def sample_prodia_multitier_ranges():
    """Prodia-style with multi-tier explanatory reference ranges — the real
    Medifarma format we saw, where the printed range column lists multiple
    tiers ("70-99 : Normal", "100-125 : Berisiko", ">=126 : Diabetes")."""
    s = _styles()
    story = [
        Paragraph("LABORATORIUM KLINIK PRODIA", s["title"]),
        Paragraph("HASIL PEMERIKSAAN LABORATORIUM", s["title"]),
        Spacer(1, 6),
        _patient_block("Pasien Demo", 48, "Laki-laki", "PR-26050908", "08-05-2026", "dr. Demo"),
        Spacer(1, 8),
        Paragraph("HEMATOLOGI", s["section"]),
        _result_table(
            [
                ["Hemoglobin",   "13.5",  "g/dL",       "13.0 - 17.0",   "Normal"],
                ["Hematokrit",   "41",    "%",          "40 - 50",       "Normal"],
                ["Leukosit",     "8.2",   "10^3/uL",    "4.5 - 11.0",    "Normal"],
                ["Trombosit",    "260",   "10^3/uL",    "150 - 450",     "Normal"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Status"],
        ),
        Paragraph("KARBOHIDRAT (Multi-tier reference)", s["section"]),
        # The crucial "Normal" tier first, then risk-tier text underneath that
        # the column scanner must NOT use as the active range.
        _result_table(
            [
                ["Glukosa Puasa", "115", "mg/dL", "70 - 99 : Normal", "[H]"],
                ["", "", "", "100 - 125 : Berisiko Diabetes", ""],
                ["", "", "", ">=126 : Diabetes mellitus", ""],
                ["HbA1c", "5.9", "%", "<5.7 : Normal", "[H]"],
                ["", "", "", "5.7-6.4 : Prediabetes", ""],
                ["", "", "", ">=6.5 : Diabetes", ""],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("LEMAK DARAH", s["section"]),
        _result_table(
            [
                ["Kolesterol Total", "220", "mg/dL", "< 200", "[H]"],
                ["HDL",             "48",  "mg/dL", ">= 40", ""],
                ["LDL",             "140", "mg/dL", "< 100", "[H]"],
                ["Trigliserida",    "165", "mg/dL", "< 150", "[H]"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("FUNGSI HATI", s["section"]),
        _result_table(
            [
                ["SGOT (AST)", "28", "U/L", "<= 35", ""],
                ["SGPT (ALT)", "42", "U/L", "<= 35", "[H]"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("FUNGSI GINJAL", s["section"]),
        _result_table(
            [
                ["Ureum",      "32",  "mg/dL",            "13 - 43",   ""],
                ["Kreatinin",  "1.0", "mg/dL",            "0.7 - 1.3", ""],
                ["eGFR",       "85",  "mL/min/1.73 m^2",  ">= 90 : Normal", ""],
                ["Asam Urat",  "5.5", "mg/dL",            "2.6 - 7.0", ""],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "hemoglobin": 13.5,
            "hematocrit": 41,
            "wbc": 8.2,
            "platelets": 260,
            "glucose_fasting": 115,
            "hba1c": 5.9,
            "total_cholesterol": 220,
            "hdl": 48,
            "ldl": 140,
            "triglycerides": 165,
            "ast": 28,
            "alt": 42,
            "bun": 32,
            "creatinine": 1.0,
            "egfr": 85,
            "uric_acid": 5.5,
        }
    }
    return "prodia_multitier_ranges", story, expected


def sample_si_units_international():
    """International SI-units style (Indian/EU lab) — every value in SI
    (mmol/L for cholesterol, µmol/L for creatinine, g/L for hemoglobin) with
    the unit string PRESENT (so we don't rely on inference) and printed
    ranges in SI."""
    s = _styles()
    story = [
        Paragraph("INTERNATIONAL CLINICAL LAB", s["title"]),
        Paragraph("Health Profile — SI Units", s["subtitle"]),
        Spacer(1, 8),
        _patient_block("Demo Patient", 41, "female", "INT-26050911", "09-05-2026", "dr. Demo"),
        Spacer(1, 8),
        Paragraph("Lipid Profile", s["section"]),
        _result_table(
            [
                ["Total Cholesterol", "5.50", "mmol/L", "< 5.17",      "H"],
                ["LDL Cholesterol",   "3.40", "mmol/L", "< 2.59",      "H"],
                ["HDL Cholesterol",   "1.50", "mmol/L", "> 1.03",      ""],
                ["Triglycerides",     "1.20", "mmol/L", "< 1.71",      ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Paragraph("Glucose / Diabetes", s["section"]),
        _result_table(
            [
                ["Fasting Glucose", "5.10", "mmol/L", "3.9 - 5.5", ""],
                ["HbA1c",           "5.6",  "%",      "< 5.7",     ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Paragraph("Kidney Function", s["section"]),
        _result_table(
            [
                ["Creatinine", "65",  "umol/L",  "45 - 84",      ""],
                ["Urea",       "4.8", "mmol/L",  "3.5 - 7.2",    ""],
                ["Uric Acid",  "320", "umol/L",  "143 - 339",    ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Paragraph("Liver Function", s["section"]),
        _result_table(
            [
                ["AST",            "22",   "U/L",     "0 - 32",   ""],
                ["ALT",            "18",   "U/L",     "0 - 33",   ""],
                ["Bilirubin Total","12.5", "umol/L",  "0 - 15",   ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Paragraph("Hematology", s["section"]),
        _result_table(
            [
                ["Hemoglobin", "138", "g/L",      "112 - 154",   ""],
                ["Platelets",  "285", "10^9/L",   "150 - 400",   ""],
                ["WBC",        "6.5", "10^9/L",   "3.0 - 10.0",  ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Paragraph("Thyroid", s["section"]),
        _result_table(
            [
                ["TSH",     "2.10",  "mIU/L",  "0.27 - 4.2",  ""],
                ["Free T4", "13.5",  "pmol/L", "11.5 - 19.6", ""],
            ],
            ["Test", "Result", "Units", "Reference", "Flag"],
        ),
        Spacer(1, 12),
        _disclaimer(),
    ]
    # All expected values are CANONICAL units after conversion.
    expected = {
        "biomarkers": {
            "total_cholesterol": 212.7,    # 5.50 * 38.67
            "ldl": 131.5,                  # 3.40 * 38.67
            "hdl": 58.0,                   # 1.50 * 38.67
            "triglycerides": 106.3,        # 1.20 * 88.57
            "glucose_fasting": 91.9,       # 5.10 * 18.0182
            "hba1c": 5.6,
            "creatinine": 0.74,            # 65 / 88.4
            "bun": 13.5,                   # 4.8 mmol/L urea → BUN mg/dL
            "uric_acid": 5.38,             # 320 / 59.48
            "ast": 22,
            "alt": 18,
            "bilirubin_total": 0.73,       # 12.5 / 17.1
            "hemoglobin": 13.8,            # 138 / 10
            "platelets": 285,              # 10^9/L → 10^3/uL same
            "wbc": 6.5,
            "tsh": 2.1,
            "free_t4": 1.05,               # 13.5 * 0.07764
        }
    }
    return "si_units_international", story, expected


def sample_kimia_farma_with_h_l_flags():
    """Kimia Farma-style report where abnormal values get a textual flag in
    the rightmost column (H, L, *) — exercises the inline flag handling AND
    the value-vs-flag separation."""
    s = _styles()
    story = [
        Paragraph("KIMIA FARMA DIAGNOSTIKA", s["title"]),
        Paragraph("Hasil Pemeriksaan Laboratorium", s["title"]),
        Spacer(1, 6),
        _patient_block("Demo KF", 55, "Laki-laki", "KF-26051012", "10-05-2026", "dr. Demo"),
        Spacer(1, 8),
        Paragraph("HEMATOLOGI", s["section"]),
        _result_table(
            [
                ["Hemoglobin",   "11.5", "g/dL",      "13.0 - 17.0",  "L"],
                ["Hematokrit",   "35",   "%",         "40 - 50",      "L"],
                ["Eritrosit",    "4.0",  "10^6/uL",   "4.5 - 5.9",    "L"],
                ["Leukosit",     "9.0",  "10^3/uL",   "4.5 - 11.0",   ""],
                ["Trombosit",    "330",  "10^3/uL",   "150 - 450",    ""],
                ["MCV",          "80",   "fL",        "80 - 100",     ""],
                ["LED (ESR)",    "35",   "mm/jam",    "< 20",         "H"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("KIMIA DARAH", s["section"]),
        _result_table(
            [
                ["Glukosa Puasa",      "108",  "mg/dL",   "70 - 99",     "H"],
                ["HbA1c",              "5.6",  "%",       "< 5.7",       ""],
                ["Kolesterol Total",   "245",  "mg/dL",   "< 200",       "H"],
                ["LDL",                "168",  "mg/dL",   "< 100",       "H"],
                ["HDL",                "38",   "mg/dL",   "> 40",        "L"],
                ["Trigliserida",       "260",  "mg/dL",   "< 150",       "H"],
                ["SGOT",               "32",   "U/L",     "< 35",        ""],
                ["SGPT",               "48",   "U/L",     "< 35",        "H"],
                ["Ureum",              "22",   "mg/dL",   "13 - 43",     ""],
                ["Kreatinin",          "0.95", "mg/dL",   "0.7 - 1.3",   ""],
                ["Asam Urat",          "8.2",  "mg/dL",   "2.6 - 7.0",   "H"],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("ELEKTROLIT", s["section"]),
        _result_table(
            [
                ["Natrium",   "138", "mmol/L", "136 - 145", ""],
                ["Kalium",    "4.0", "mmol/L", "3.5 - 5.1", ""],
                ["Klorida",   "102", "mmol/L", "98 - 107",  ""],
            ],
            ["Pemeriksaan", "Hasil", "Satuan", "Nilai Rujukan", "Flag"],
        ),
        Paragraph("Vital signs: Tekanan Darah 145/95 mmHg", s["small"]),
        Spacer(1, 12),
        _disclaimer(),
    ]
    expected = {
        "biomarkers": {
            "hemoglobin": 11.5,
            "hematocrit": 35,
            "rbc": 4.0,
            "wbc": 9.0,
            "platelets": 330,
            "mcv": 80,
            "esr": 35,
            "glucose_fasting": 108,
            "hba1c": 5.6,
            "total_cholesterol": 245,
            "ldl": 168,
            "hdl": 38,
            "triglycerides": 260,
            "ast": 32,
            "alt": 48,
            "bun": 22,
            "creatinine": 0.95,
            "uric_acid": 8.2,
            "sodium": 138,
            "potassium": 4.0,
            "chloride": 102,
            "bp_systolic": 145,
            "bp_diastolic": 95,
        }
    }
    return "kimia_farma_with_h_l_flags", story, expected


def main():
    SAMPLES_DIR.mkdir(parents=True, exist_ok=True)
    EXPECTED_DIR.mkdir(parents=True, exist_ok=True)

    generators = [
        sample_prodia_typical,
        sample_kimia_farma_bilingual,
        sample_comma_decimals_and_thousand_seps,
        sample_multipage_long,
        sample_english_baseline,
        sample_prodia_multitier_ranges,
        sample_si_units_international,
        sample_kimia_farma_with_h_l_flags,
    ]
    for gen in generators:
        name, story, expected = gen()
        pdf_path = SAMPLES_DIR / f"{name}.pdf"
        doc = SimpleDocTemplate(
            str(pdf_path),
            pagesize=A4,
            leftMargin=18*mm, rightMargin=18*mm,
            topMargin=15*mm, bottomMargin=15*mm,
            title=name,
        )
        doc.build(story)
        expected_path = EXPECTED_DIR / f"{name}.json"
        expected_path.write_text(json.dumps(expected, indent=2))
        print(f"+ {pdf_path.name} ({pdf_path.stat().st_size // 1024} KB)  expected={len(expected['biomarkers'])} biomarkers")


if __name__ == "__main__":
    main()
