# Knowledge sourcing — what to feed the RAG layer

The seed pack in `app/rag/seed/*.md` is intentionally a *starter* — it's enough to demo the pipeline, not enough to give a clinician-grade explanation. To make the RAG layer genuinely useful, you replace and extend it with curated, cited, jurisdiction-appropriate medical knowledge.

This doc tells you what to add, where to get it, how to format it, and what the operational hygiene looks like.

## Mental model

The retriever is sized for **interpretation passages, not raw guidelines**. Each passage should answer one question well: "what does an HbA1c of 6.2% mean?", "what does NCEP say about LDL goals?", "why does AST/ALT >2 matter?". Big PDFs are fine — the chunker will split them — but the *content* needs to be explanation-shaped, not exhaustive policy text. If a chunk reads like a footnote it will retrieve poorly.

## Source tiers

### Tier 1 — public-domain or open-license, redistributable

These are the safest sources to ship and the most-cited in the seed pack. Most are US-government works and are public domain.

| Source | Topics | License | Best for |
|---|---|---|---|
| **MedlinePlus** (NLM/NIH) | every lab topic, patient education | Public domain | Plain-language explanations of every common lab test |
| **CDC** | exercise, BP, diet, screening | Public domain | Practical recommendations, behavior change |
| **NIH Office of Dietary Supplements** | vitamins, minerals | Public domain | Authoritative vitamin/B12/D fact sheets |
| **USPSTF** | screening recommendations | Public domain | Evidence-graded screening guidance |
| **2020–2025 Dietary Guidelines for Americans** | diet | Public domain | The default US diet reference |
| **HHS Physical Activity Guidelines** (2nd ed., 2018) | exercise | Public domain | Adult and special-population activity targets |
| **WHO Fact Sheets** | diet, NCDs | CC BY-NC-SA 3.0 IGO | International framing; required attribution |
| **KDIGO Guidelines** | kidney | Open access, attribution required | eGFR, CKD staging, AKI |

### Tier 2 — open-access scholarly, attribution required

Free to read and cite, but check redistribution terms before bundling into a shipped artifact.

- **AHA / ACC clinical guidelines** (Circulation, JACC) — published open-access for clinical guidelines.
- **AASLD practice guidelines** — usually free to read.
- **American Thyroid Association** — guidelines published in *Thyroid*, often open access.
- **GINA / GOLD** (asthma, COPD) — free reports for personal use.
- **Choosing Wisely** — clinician-facing recommendations, free to use.

### Tier 3 — copyrighted reference works (you need a license)

These are the gold standard, and worth the cost if you intend to deploy this seriously.

- **Tietz Textbook of Clinical Chemistry and Molecular Diagnostics** — *the* clinical chemistry reference for assay specifics, interferences, reference intervals.
- **Harrison's Principles of Internal Medicine** — comprehensive disease background.
- **Conn's Current Therapy** — practical clinical decision trees.
- **ACSM's Guidelines for Exercise Testing and Prescription** — exercise prescription detail beyond CDC.
- **Krause's Food & the Nutrition Care Process** — detailed dietetic guidance.
- **UpToDate** — search-style clinical reference (subscription).

You should **not** ship copyrighted text in a public artifact. Ingest into a private store you run for yourself or your organization, and keep the manifest's `license` field honest so future contributors know.

### Tier 4 — peer-reviewed primary literature

When a guideline says "X is associated with Y", you sometimes want the underlying paper. Useful sources:

- **PubMed Central (PMC)** — free full text for many studies.
- **bioRxiv / medRxiv** — preprints; flag as such in metadata.
- **Cochrane Reviews** — systematic reviews; abstracts free.

Primary literature is best for *patterns* and *uncertainty* — passages like "evidence is mixed on X" — rather than cutoffs.

## Topic coverage targets

For each rule family in the engine, aim for at least 2–3 high-quality passages per topic. The retriever will pick the most relevant chunk per query.

| Rule family | Minimum passages | Recommended sources |
|---|---|---|
| glucose | 4 | ADA Standards of Care, MedlinePlus, NIDDK |
| lipids | 4 | NCEP ATP III, AHA/ACC 2018, MedlinePlus |
| liver | 3 | AASLD 2017, MedlinePlus, AACC |
| kidney | 4 | KDIGO 2024, NIDDK, MedlinePlus |
| cbc | 3 | MedlinePlus, AACC, ASH education |
| thyroid | 3 | ATA guidelines, MedlinePlus, ATA pregnancy guideline |
| inflammation | 2 | AHA hs-CRP statement, MedlinePlus |
| electrolytes | 3 | MedlinePlus, AACC |
| vitamins | 3 | NIH ODS (D, B12, folate) |
| anthropometric | 3 | CDC BMI, NCEP waist, ACC/AHA 2017 BP |
| diet | 5 | DGA 2020-2025, AHA, ADA nutrition therapy, WHO |
| exercise | 4 | HHS PAG 2nd ed., CDC, ACSM |
| safety | 4 | MedlinePlus emergency symptoms, CDC, ACC/AHA crisis criteria |

## Preparation workflow

### 1. Decide jurisdiction

Cutoffs differ across regions. Asian-population BMI thresholds, Japanese hyperuricemia targets, European diabetes screening intervals — all real differences. Tag every document with `jurisdiction` in the manifest. The retrieval layer doesn't filter on jurisdiction yet, but having it as metadata lets you add a filter later, and lets you audit what made it into a given report.

### 2. Get the source content

- Download the official PDF or HTML from the canonical URL.
- For HTML, save a clean `*.md` with just the body text (not the nav/footer) — easiest with `pandoc` or a quick script. Strip tracking links and ad units.
- For PDFs, leave them as PDF and let the ingest CLI extract.
- Keep the original file in `knowledge/` (gitignored if it's not redistributable).

```
knowledge/
├── medlineplus/
│   ├── glucose-blood-test.md
│   ├── hba1c.md
│   └── cbc.md
├── ada/
│   └── standards-of-care-2024-summary.md
├── aha/
│   └── 2018-cholesterol-guideline-summary.md
├── kdigo/
│   └── ckd-2024-summary.md
├── nih-ods/
│   ├── vitamin-d-fact-sheet.md
│   └── vitamin-b12-fact-sheet.md
└── usda/
    └── dietary-guidelines-2020-2025.pdf
```

### 3. Clean before you index

The chunker is paragraph-aware (~1200 chars per chunk), but garbage in still ranks. Quick wins:

- Remove navigation, footers, copyright boilerplate, and reference lists if they aren't useful for explanation.
- Collapse tables that won't render as text.
- Remove cross-reference numbers like `[1]`, `[citation needed]`.
- For PDFs, do a quick `pdftotext` sanity pass and discard pages that are figures-only.

For consistency across sources, the seed pack uses a markdown skeleton:

```markdown
# <Topic> — interpretation notes

Source: <Source name and date>.

## <Subsection>

<2–6 sentences in plain language.>
- <bullet of cutoffs / categories>

## Practical context

<Caveats: lab variation, confounders, when to repeat, when to escalate.>
```

If you adapt third-party text, **paraphrase rather than verbatim quote** for any source where you don't have a redistribution right. Keep the citation in the manifest's `source` field so the rule engine can render it.

### 4. Write a manifest

Drop a JSON manifest like `data/sources.example.json` (an example ships in this repo). Each entry needs at least:

```json
{
  "path": "relative/path.md",
  "topic": "lipids",
  "source": "AHA/ACC 2018 Cholesterol Guideline",
  "title": "AHA/ACC 2018 — Summary"
}
```

Optional fields used for auditing:

- `url` — canonical source URL.
- `license` — your judgment of redistribution rights.
- `last_updated` — guideline publication date.
- `jurisdiction` — `US`, `EU`, `International`, etc.

### 5. Ingest

```bash
# Sanity pass: lists items + flags missing files, no indexing.
python -m app.cli.ingest from-manifest data/sources.json --dry-run

# Real ingest.
python -m app.cli.ingest from-manifest data/sources.json

# Single ad-hoc add.
python -m app.cli.ingest add knowledge/aasld/alt-elevation-2017.md \
    --topic liver --source "AASLD 2017"

# Inspect.
python -m app.cli.ingest stats
```

### 6. Sanity-check retrieval

Spot-check that the retriever returns the right passage for each topic. The simplest check is to run the demo against a panel that triggers a single finding and confirm the retrieved passage matches:

```bash
python -m app.cli.demo --input data/samples/manual_input.json --no-llm
```

Look at the **Sources Cited** section in the report — that's your audit trail of what got into a finding.

## Operational hygiene

### Versioning

Re-index when a guideline updates. ADA Standards of Care updates yearly; KDIGO every few years; the AHA/ACC cholesterol guideline last updated in 2018 (with annual ACC/AHA updates).

The simplest pattern is to version your manifest (`sources-2024-q1.json`, `sources-2024-q3.json`) and rebuild the Chroma collection from scratch. Chroma upserts make incremental updates cheap, but a full rebuild gives you a clean diff.

### Update cadence

| Source | Recommended re-check |
|---|---|
| ADA Standards of Care | Yearly (Jan) |
| AHA/ACC cholesterol & BP | Every 1–2 years |
| KDIGO | Every 2–3 years |
| MedlinePlus / CDC pages | Yearly |
| NIH ODS fact sheets | Yearly |
| USDA/HHS Dietary Guidelines | Every 5 years |

### Demographic coverage gaps

The current seed pack assumes non-pregnant adults. Real-world deployment needs separate notes for:

- **Pregnancy** — TSH trimester ranges, gestational diabetes thresholds, hemoglobin floor by trimester.
- **Pediatrics** — wildly different reference ranges; consider blocking unless you have pediatric-specific rules.
- **Older adults** — sarcopenia confounds creatinine-based eGFR and BMI interpretation.
- **South Asian / East Asian / South-East Asian populations** — lower BMI cutoffs (overweight ≥23, obesity ≥27.5), lower waist cutoffs.
- **Black populations** — historical race-coefficient debates around eGFR are part of why CKD-EPI 2021 dropped race; use that equation.

The simplest path is to add `population` as a metadata key and add a retrieval filter. If you want population-specific rules in the engine, do that explicitly — never let the LLM choose between cutoffs.

### License auditing

When you `from-manifest`, the manifest stays on disk. That's your license audit trail. If you ever ship the Chroma database, you should be able to point at the manifest and say "every chunk in here corresponds to one of these sources, and we are within license terms".

The store API does not currently strip a document on demand. If you need takedown support, the simplest remedy is to delete the chunk IDs that share a `source` field from Chroma directly:

```python
from app.rag import KnowledgeStore
store = KnowledgeStore()
store._ensure()
store._collection.delete(where={"source": "AHA/ACC 2018 Cholesterol Guideline"})
```

(That's a power-user escape hatch; we'll wrap it in a CLI when there's demand.)

### Don't ingest

- Patient-identifiable data of any kind.
- Drug-prescribing references — the system explicitly does not recommend medications, and putting a prescribing reference into the RAG store invites the LLM to reach for it. Keep the corpus to *interpretation* and *lifestyle*.
- Forum posts, blogs, or non-evidence-based content. The retriever has no quality filter; if it's in the store, it can be cited.

## Quick-start recipe

1. `mkdir -p knowledge && cd knowledge`
2. Download MedlinePlus pages for: glucose, hba1c, cbc, electrolytes, lipid panel, kidney function, liver tests. Save each as a clean `*.md`.
3. Download the **NIH ODS** Vitamin D and B12 fact sheets. Save as markdown.
4. Download the **2020-2025 Dietary Guidelines for Americans** PDF.
5. Download the **HHS Physical Activity Guidelines (2nd ed.)** PDF.
6. Copy `data/sources.example.json` to `data/sources.json` and edit paths.
7. `python -m app.cli.ingest from-manifest data/sources.json --dry-run`
8. Fix any "missing on disk" rows.
9. `python -m app.cli.ingest from-manifest data/sources.json`
10. `python -m app.cli.demo --input data/samples/manual_input.json` — confirm the report's "Sources Cited" section now references your real sources.

That's the minimum viable knowledge base. From there you layer in ADA, AHA/ACC, KDIGO, AASLD, and ATA summaries to make the explanations match the cutoffs the rules engine is already using.
