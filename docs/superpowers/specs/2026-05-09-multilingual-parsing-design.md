# Multilingual Lab Report Parsing — Design

**Date:** 2026-05-09
**Scope:** `ai-service/health-checkup-extraction/`
**Approach:** #2 (Surgical fixes + multilingual extraction layer) from the brainstorm

## Goal

Make the health-checkup pipeline correctly parse Indonesian lab reports (Prodia, Kimia Farma, Pramita), bilingual ID/EN reports, and long multi-page PDFs that today get silently truncated. Improve a few quality bugs found along the way.

## Out of scope

- Cold-start handling for `gemma4:26b` (model is fixed; first-call latency is its own problem)
- Streaming responses (frontend change)
- Persisting reports in Laravel (separate design)
- Tuning against real-world report corpus (Approach 3, follow-up project)

## Architecture changes

The pipeline gains two new stages between input and the existing extractors:

```
PDF/image/text input
    ↓
[NEW] language_detect       ← keyword heuristic → "id" | "en" | "mixed"
    ↓
[NEW] page_chunker          ← splits long PDFs by page; pass-through for short text
    ↓
regex_extract_panel         ← multilingual aliases now
    ↓
extract_blood_pressure      ← unchanged
    ↓
(if regex empty) llm_extractor   ← language-aware, runs per-chunk
    ↓
(unchanged) to_canonical → validate → rules → rag → llm → safety
```

Both new stages are deterministic, LLM-free, and individually testable.

## Components

### `app/parsers/language.py` (new)

```
detect_language(text: str) -> Literal["id", "en", "mixed"]
```

- Counts hits of distinctive Indonesian medical terms (`glukosa`, `kreatinin`, `trombosit`, `leukosit`, `trigliserida`, `asam urat`, `kolesterol`, `eritrosit`) plus common stopwords (`dan`, `hasil`, `nilai`, `rujukan`)
- Counts English equivalents
- Returns `"id"` if id_count ≥ 2× en_count, `"en"` if reverse, `"mixed"` otherwise (including the all-zero case)
- Pure function, no side effects

### `app/parsers/regex_extractor.py` (modified)

- Split `_ALIASES` into `_ALIASES_EN` (existing list, unchanged) and `_ALIASES_ID` (new)
- Public function gains `language` parameter:

```
regex_extract_panel(text: str, language: Literal["id","en","mixed","auto"] = "auto") -> list[LabValue]
```

- `auto` and `mixed` use both alias tables; `id` and `en` use one
- Add a flag-stripping step that removes trailing `[H]`, `[L]`, `H`, `L`, `Tinggi`, `Rendah`, `*`, `↑`, `↓` tokens before value parsing so they don't corrupt the numeric value
- Indonesian aliases (high-confidence terms only):
  - `glucose_fasting`: `glukosa\s*puasa`, `gula\s*darah\s*puasa`, `\bGDP\b`
  - `glucose_random`: `gula\s*darah\s*sewaktu`, `\bGDS\b`
  - `total_cholesterol`: `kolesterol\s*total`
  - `triglycerides`: `trigliserida`
  - `alt`: `\bSGPT\b` *(already present in EN list — keep both)*
  - `ast`: `\bSGOT\b` *(already present)*
  - `creatinine`: `kreatinin`
  - `bun`: `ureum`
  - `uric_acid`: `asam\s*urat`
  - `hemoglobin`: `hemoglobin`, `\bHb\b` *(already present)*
  - `hematocrit`: `hematokrit`
  - `rbc`: `eritrosit`
  - `wbc`: `leukosit`
  - `platelets`: `trombosit`
  - `sodium`: `natrium`
  - `potassium`: `kalium`
  - `chloride`: `klorida`
  - `calcium`: `kalsium`

### `app/parsers/page_chunker.py` (new)

```
chunk_text_by_page(text: str, max_chunk_chars: int = 24000) -> list[str]
```

- If input contains form-feed `\f`, split on it (PyMuPDF emits one per page)
- Otherwise, if input ≤ `max_chunk_chars`, return `[text]`
- Otherwise, split by paragraph boundaries to get chunks ≤ `max_chunk_chars`
- Empty/whitespace-only chunks are dropped

### `app/parsers/pdf_parser.py` (modified)

- `_extract_pymupdf` joins pages with `\f` instead of `\n` so the chunker can use page boundaries
- `_extract_pdfplumber` mirrors that

### `app/parsers/ocr_parser.py` (modified)

New helper `_preprocess_image(img: PIL.Image) -> PIL.Image`:
- Convert to grayscale
- `ImageOps.autocontrast` for low-contrast scans
- `ImageOps.invert` if median brightness < 128 (rare, dark scans)

Heuristic: apply preprocessing only when the image is grayscale-ish or low-variance. Pass `--psm 4` to Tesseract (assume single column of variable-size text — closer to a lab report's layout than the default PSM 3).

### `app/parsers/llm_extractor.py` (modified)

- New parameter `language: Literal["id","en","mixed"] = "mixed"`
- Inject a one-line hint into the prompt: `Report language: {language}. Indonesian biomarker terms map to the same English keys.` when not English
- Bump per-call text limit from 8000 → 24000 chars

### `app/pipeline/orchestrator.py` (modified)

`_coerce_panel` becomes:
1. Get raw text (PDF / OCR / passed-in)
2. `lang = detect_language(text)`
3. `chunks = chunk_text_by_page(text)`
4. For each chunk: `regex_extract_panel(chunk, language=lang)` + `extract_blood_pressure(chunk)`
5. Merge results with `_merge_lab_values(values_per_chunk)` — last-write-wins per biomarker
6. If merged is empty → fall back to LLM extractor on the full text (or the longest single chunk)
7. Return `LabPanel(age=0, sex="unknown", values=...)` with merged values

Also fix `_bulletize`'s line picker:
- Reject lines that end with `:`, `—`, `OR`, `AND`, or look like sentence fragments
- Prefer lines with a verb-shaped pattern (≥ one word ending in `-s` `-ed` `-ing` or matching common imperative starters: `aim`, `limit`, `replace`, `build`, `add`, `reduce`, `consider`, `seek`, `discuss`, `take`)
- Keep length floor at 30 chars but raise floor to 40 once a sentence-shaped candidate is found

## Data flow example

A 30-page Indonesian Prodia PDF with 18 biomarkers spread across 3 pages:

1. PyMuPDF extracts text, joined by `\f` → ~120 KB
2. `detect_language` sees `glukosa`, `kolesterol`, `trigliserida` → returns `"id"`
3. `chunk_text_by_page` splits on `\f` → 30 chunks, mostly small
4. Per chunk: regex with ID aliases → 18 LabValues found across pages 4, 5, 6
5. Merged into one LabPanel
6. Pipeline continues unchanged

## Error handling

- Language detect ambiguous → `"mixed"` (uses both alias tables; safe default)
- Single chunk fails → log warning, continue with remaining chunks
- LLM extractor unreachable → only deterministic regex output is returned
- OCR preprocessing exception → fall back to raw image
- `_merge_lab_values` on conflicting values for same biomarker → last write wins, with a `conv_warning` recorded

No new failure mode raises out of the pipeline. All paths still produce a `FinalReport`.

## Testing

All deterministic — runs without LLM, in CI.

| Test file | What it covers |
|---|---|
| `tests/parsers/test_language.py` | id-only / en-only / mixed / empty / single-word inputs |
| `tests/parsers/test_regex_indonesian.py` | Each ID alias finds its biomarker, value, and unit |
| `tests/parsers/test_regex_bilingual.py` | Bilingual line gets matched once, not duplicated |
| `tests/parsers/test_regex_flags.py` | `[H]`, `H`, `Tinggi`, `*`, `↑` are stripped from value |
| `tests/parsers/test_page_chunker.py` | Form-feed splitting, size cutoff, empty input, no-form-feed long input |
| `tests/parsers/test_orchestrator_indonesian.py` | End-to-end with synthetic ID PDF text → correct findings |
| `tests/parsers/test_bulletize.py` | New picker rejects truncated fragments like "For substantial health benefits, adults should do EITHER:" |

Synthetic fixture: `tests/fixtures/prodia_style.txt` based on klikdokter / alodokter Indonesian biomarker vocabulary.

## Backwards compatibility

- `regex_extract_panel(text)` without the `language` arg defaults to `"auto"` — current callers unaffected
- `_chunker` is a new pre-step; for inputs < 24KB without `\f`, returns `[text]` — extractor sees identical input as today
- All existing English tests must still pass

## Risk register

| Risk | Mitigation |
|---|---|
| Indonesian regex accidentally matches English words (e.g. `kalium` ≠ "calcium" by spelling, but generic prefixes can collide) | Aliases use word boundaries (`\b...\b`), tested with deliberately ambiguous fixtures |
| Form-feed split produces zero-length chunks | Drop empties in `chunk_text_by_page` |
| OCR preprocessing makes some images *worse* | Apply only behind a heuristic; on failure, fall back to raw image |
| `last-write-wins` merge silently drops a real value | Log a `conv_warning` so it surfaces in `validation_issues` |

## Done criteria

- All new + existing tests pass (`make test` from `health-checkup-extraction/`)
- Synthetic Indonesian Prodia-style fixture extracts ≥ 12 of 14 expected biomarkers
- Synthetic bilingual fixture extracts each biomarker exactly once
- Existing English `manual_input.json` end-to-end run still produces the same overall_severity, abnormal count, and same set of biomarkers as before this change
- Gateway restart shows `knowledge_chunks` still 90 (no regression)
