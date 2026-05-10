# Health Checkup Extraction

A safety-first **health-check report assistant** that turns a lab report (PDF, scan, or manual JSON) into a structured, explained, evidence-anchored summary — without ever pretending to diagnose.

> This tool does **not** diagnose disease and does **not** prescribe medication. It explains lab results in plain language, flags values that fall outside reference ranges, and points the user to a clinician when warranted. Every threshold is deterministic and cited. The LLM only explains; it never decides.

---

## Pipeline

```
PDF / image / manual JSON
        ↓  parsers (PyMuPDF, pdfplumber, Tesseract w/ preprocessing, LLM fallback)
Raw text + tables (pages joined by form-feed)
        ↓  language.detect_language          ← "id" | "en" | "mixed"
        ↓  page_chunker.chunk_text_by_page   ← split long PDFs by page
Per-chunk extraction loop:
        ↓  regex_extract_panel(language)     ← multilingual aliases + locale-aware numbers
        ↓  extract_blood_pressure
Merged Lab values  ────────────────┐
   (LLM extractor as fallback if regex finds nothing)
        ↓  normalize.units        │
Canonical-unit lab panel          │
        ↓  normalize.validator    │ unit / range / missing / duplicate checks
Validated lab panel               │
        ↓  rules.engine           │ deterministic, source-cited thresholds
Findings (per biomarker)          │
        ↓  rag.retrieve           │ topic-based retrieval (one query per cluster)
Findings + supporting passages    │
        ↓  llm.client (Ollama)    │ explain in plain language
Draft report                      │
        ↓  safety.validator       │ block diagnostic / prescriptive phrasing
Final report  (summary, risks, diet, exercise, next steps, disclaimer)
```

## Features

- **Comprehensive biomarker coverage** — glucose, HbA1c, full lipid panel, liver (AST/ALT/ALP/GGT/bilirubin), kidney (creatinine + eGFR via CKD-EPI 2021), uric acid, full CBC (Hgb, RBC, WBC, platelets, MCV), thyroid (TSH, free T4, free T3), inflammation (CRP, ESR), electrolytes (Na, K, Cl, Ca), vitamin D, B12, BMI, waist, blood pressure.
- **Multilingual extraction** — Indonesian (Prodia, Kimia Farma, Pramita-style) and English reports out of the box, plus bilingual layouts where labels appear in both languages. Language is auto-detected; aliases include `Glukosa Puasa`, `SGPT`, `Trigliserida`, `Eritrosit`, `Leukosit`, `Trombosit`, `Asam Urat`, `Tekanan Darah`, etc.
- **Locale-aware number parsing** — handles thousand separators (`8.500/uL`, `245.000`), comma decimals (`13,8 g/dL`), and mixed conventions (`1,234.56`). Indonesian units like `juta/uL` and `mm/jam` are recognized.
- **Multi-page PDF support** — pages are joined by form-feed and the extractor runs per-page so values that fall past the first page aren't silently truncated.
- **Bracketed flag stripping** — `[H]`, `[L]`, `↑`, `↓`, `*` between value and unit don't break parsing.
- **Deterministic rules engine** — every threshold cites its source (ADA, AHA/ACC, KDIGO, NIH, CDC, MedlinePlus, AACC).
- **Multi-format ingest** — digital PDFs (PyMuPDF / pdfplumber), scanned images (Tesseract with grayscale + autocontrast preprocessing, PSM 4), and a Pydantic-validated manual JSON path.
- **RAG with curated starter pack** — markdown notes per biomarker family + diet + exercise + safety guidelines, indexed in ChromaDB. Topic-based retrieval (one query per finding cluster, not one query for everything).
- **Ingestion CLI** — add your own clinical references over time (markdown, plain text, PDF).
- **Local LLM via Ollama** — configurable model (default `gemma4:26b`). The LLM only explains findings; it never picks thresholds.
- **Safety validator** — post-generation pass that blocks diagnostic and prescriptive phrasing, escalates emergencies, and always appends a disclaimer.
- **Runs without an LLM** — disable the LLM and the deterministic layers still produce a useful structured report (great for tests and CI).
- **Sample-driven evaluation** — `python -m app.cli.eval` runs the full pipeline against `samples/*/*.pdf` and reports per-biomarker recall vs ground-truth `samples/expected/<name>.json`. See [samples/README.md](samples/README.md).

## Quick start

### 1. Prerequisites

- Python 3.10+
- (Optional) [Ollama](https://ollama.com) for local LLM
- (Optional) Tesseract for OCR: `brew install tesseract` on macOS

### 2. Install

```bash
cd coding/health-checkup-extraction
python -m venv .venv
source .venv/bin/activate
pip install -e ".[dev]"
cp .env.example .env
```

### 3. (Optional) Pull a local model

```bash
ollama pull gemma4:26b   # default; or set OLLAMA_MODEL in .env to whatever you have
```

### 4. Seed the knowledge base

```bash
python -m app.cli.ingest seed
```

This loads `app/rag/seed/*.md` into ChromaDB at `data/chroma/`.

### 5. Run a demo

```bash
# CLI — uses the sample manual input, no LLM required
python -m app.cli.demo --input data/samples/manual_input.json --no-llm

# CLI — full pipeline with Ollama
python -m app.cli.demo --input data/samples/manual_input.json

# API
uvicorn app.main:app --reload
# then POST a JSON panel to http://localhost:8000/manual
```

## Adding more knowledge

```bash
# Markdown / text
python -m app.cli.ingest add path/to/notes.md --topic lipids

# PDF (textbook chapter, guideline doc, etc.)
python -m app.cli.ingest add path/to/guideline.pdf --topic kidney
```

## Evaluation against real and synthetic samples

The pipeline ships with a sample-driven evaluation harness so you can measure
extraction recall on real lab reports and quickly catch regressions.

```bash
# Generate the synthetic sample suite (Prodia/KF/Pramita styles, English baseline,
# tricky thousand-separator + comma-decimal layouts)
python tools/generate_synthetic.py

# Run the evaluation across every sample folder
python -m app.cli.eval

# Limit to one folder
python -m app.cli.eval --only synthetic
python -m app.cli.eval --only private

# Tighter / looser tolerance for value-match (default 2%)
python -m app.cli.eval --tolerance 0.05
```

Drop your own real PDFs in `samples/private/` (gitignored) with a matching
`samples/expected/<name>.json` ground-truth file. See
[samples/README.md](samples/README.md) for the per-sample workflow.

## Repo layout

```
app/
├── main.py                 FastAPI entrypoint
├── config.py               settings (env-driven; chroma_dir is anchored to PROJECT_ROOT)
├── models/                 Pydantic schemas (LabPanel, Finding, FinalReport)
├── parsers/
│   ├── pdf_parser          PyMuPDF + pdfplumber, pages joined by form-feed
│   ├── ocr_parser          Tesseract w/ grayscale + autocontrast + PSM 4
│   ├── language            id/en/mixed detector (keyword counts, no ML)
│   ├── page_chunker        split text on form-feed for per-page extraction
│   ├── regex_extractor     EN + ID alias tables, locale-aware number parser
│   └── llm_extractor       JSON fallback (language-hinted, 24KB cap)
├── normalize/              units, validator
├── rules/                  one module per biomarker family + engine.py
├── rag/                    store, ingest, retrieve, seed/*.md
├── llm/                    Ollama client + prompt templates
├── safety/                 post-generation safety validator
├── pipeline/               end-to-end orchestrator (lang-aware, multi-page)
├── api/                    FastAPI routes
└── cli/                    demo, ingest, eval CLIs
samples/
├── synthetic/              generated PDFs (gitignored; reproducible)
├── online/                 publicly-sourced PDFs (gitignored)
├── private/                your real PDFs (gitignored)
└── expected/<name>.json    ground-truth biomarker values (committed)
tools/
└── generate_synthetic.py   reportlab-based synthetic PDF builder
data/
├── samples/manual_input.json
└── chroma/                 (created on first ingest)
tests/                      pytest, deterministic layers (110 tests)
docs/                       ARCHITECTURE.md, SAFETY.md, COVERAGE.md
```

## Safety policy

See [docs/SAFETY.md](docs/SAFETY.md). Short version:

- Never produces a diagnosis. Says "in the X range" instead.
- Never recommends or names medication.
- Always recommends professional consultation for emergencies (very high glucose, BP crisis, severe anemia, severely abnormal liver/kidney markers).
- Always appends a non-removable disclaimer.

## Coverage and sources

See [docs/COVERAGE.md](docs/COVERAGE.md) for the full biomarker → rule → source table.

## License

For personal / research use. Not a medical device. Not a substitute for clinical judgment.
