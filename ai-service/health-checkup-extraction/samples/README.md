# Samples & Evaluation

This folder is the testing ground for the multilingual extraction pipeline. It
holds three kinds of lab-report PDFs plus their ground-truth manifests, and
the evaluation CLI uses them to measure extraction recall.

## Folder layout

| Folder | What's in it | Tracked by git? |
|---|---|---|
| `synthetic/` | High-fidelity Indonesian/English lab reports built by `tools/generate_synthetic.py` | ❌ PDFs ignored — regenerable |
| `online/` | PDFs scraped from public sources | ❌ ignored (treat as private) |
| `private/` | Real lab PDFs (yours, family, friends, anonymized) | ❌ ignored |
| `expected/` | Ground-truth biomarker manifests (`<name>.json`) | ✅ tracked |

The PDFs themselves never leave your machine. The `expected/` JSONs are the
contract that says "for this PDF, the pipeline should extract these
biomarkers with these values."

## Quick start

```bash
# From the health-checkup-extraction root:
. .venv/bin/activate

# 1. Generate the synthetic suite (idempotent)
python tools/generate_synthetic.py

# 2. Run the eval — deterministic, no LLM needed
python -m app.cli.eval
```

Expected output (with the shipped synthetic samples):

```
                Sample-set recall summary

  Sample                              Found   Expected   Recall   Wrong values
 ───────────────────────────────────────────────────────────────────────────
  english_quest_style.pdf                13         13     100%              0
  kimia_farma_bilingual.pdf              16         16     100%              0
  pramita_multipage.pdf                  21         21     100%              0
  prodia_typical.pdf                     18         18     100%              0
  thousand_seps_and_comma_decimal…       14         14     100%              0
  OVERALL                                82         82     100%              0
```

## Adding a real lab PDF

1. **Drop the PDF** into `samples/private/`. Filename matters — the
   evaluation looks for an `expected/<filename-without-extension>.json`
   companion file. Use a descriptive name: `prodia_jakarta_2024_anon.pdf`,
   not `IMG_4823.pdf`.

2. **Write the ground truth** at `samples/expected/<name>.json`. Format:

   ```json
   {
     "biomarkers": {
       "glucose_fasting": 110,
       "total_cholesterol": 230,
       "ldl": 160,
       "hdl": 35,
       "triglycerides": 210,
       "alt": 55,
       "creatinine": 1.0,
       "uric_acid": 6.8,
       "hemoglobin": 14.5,
       "platelets": 250,
       "bp_systolic": 132,
       "bp_diastolic": 84
     }
   }
   ```

   Use canonical biomarker keys (see [`app/models/biomarkers.py`](../app/models/biomarkers.py))
   and **canonical units** (the pipeline normalizes incoming units, so e.g. a
   triglyceride reported as `2.4 mmol/L` should appear in expected as `213 mg/dL`
   after conversion — or just write it in the report's unit and use a wider
   `--tolerance`).

3. **Run the eval** scoped to that folder:

   ```bash
   python -m app.cli.eval --only private
   ```

4. **Read the per-sample table**. Each biomarker shows one of:

   - `ok` — extracted with the expected value (within tolerance)
   - `MISSED` — biomarker key not in the extracted set
   - `VALUE OFF` — extracted but value differs from expected

5. **Investigate misses**:
   - `MISSED` usually means the biomarker label or unit isn't in the regex
     alias / unit pattern. Add it to [`app/parsers/regex_extractor.py`](../app/parsers/regex_extractor.py).
   - `VALUE OFF` usually means the number parser is choosing wrong (decimal
     vs thousand-sep) or a flag token is bleeding into the value.

6. **Add a test** to `tests/test_regex_indonesian.py` (or similar) to lock
   the fix in before re-running the eval.

## Generating new synthetic samples

`tools/generate_synthetic.py` is a small ReportLab-based generator that
emits PDFs mimicking real Indonesian lab chains. To add a new layout:

1. Add a new function `sample_<name>()` that returns
   `(name: str, story: list, expected: dict)`.
2. Append it to the `generators` list in `main()`.
3. Run `python tools/generate_synthetic.py` — the new `samples/synthetic/<name>.pdf`
   and `samples/expected/<name>.json` are written together, so the eval
   picks them up automatically.

Useful variations to add:

- A scanned/photo-of-paper layout (low contrast, slight rotation) to stress OCR
- A urinalysis report (string values like "negatif", "1+ trace")
- A diabetic monitoring panel with multi-month glucose history
- A pediatric reference-range layout

## What "recall" means here

- **Recall** = `(extracted ∧ correct value) / expected`
- **Wrong values** = extracted, but value differs from expected by > tolerance
- The eval intentionally runs **without** the LLM and **without** RAG — it
  measures the deterministic layers (parsers + regex + number parser + units),
  which are the part we can validate against ground truth.

A recall of 100% on synthetic data is necessary but not sufficient. The
synthetic generator and the regex aliases share assumptions, so you should
weight real-sample (private/) results much more heavily.

## Tolerance

Defaults to **2%** relative tolerance for value comparison. Lower it
(`--tolerance 0.005`) for stricter matching, raise it (`--tolerance 0.05`)
when the report's unit differs from canonical and you accept the conversion
rounding.

## Showing extras

By default the eval only reports on biomarkers in the `expected/` manifest.
If the pipeline is *finding more* than you expect, pass `--show-unexpected`
to see them — useful when investigating false positives.

```bash
python -m app.cli.eval --show-unexpected
```
