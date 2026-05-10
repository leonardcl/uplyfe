# Architecture

## Layered design

```
┌──────────────────────────────────────────────────────────────────┐
│  Inputs                                                          │
│   PDF (digital)   PDF (scan)   Image   Manual JSON   Pasted text │
└──────────┬─────────┬───────────┬─────────────┬───────────────────┘
           │         │           │             │
        PyMuPDF / pdfplumber  Tesseract / Paddle           │
           │         │           │             │
           └─────────┴───────────┴─────────────┘
                            │
                Regex extractor (label aliases)
                            │
                LLM extractor (fallback only)
                            │
                       LabPanel  (Pydantic-validated)
                            │
                Unit normalizer  →  canonical units
                            │
                  Validator  → ValidationIssue[]
                            │
                Rules engine  →  FindingCluster[] + pattern Findings
                            │
       Topic-based RAG retrieval  →  passages per cluster
                            │
        LLM synthesis (Ollama)  →  draft prose section
                            │
            Safety validator  →  rewritten / blocked / escalated
                            │
                       FinalReport  (markdown / JSON)
```

## Determinism boundaries

The single most important property: **the LLM never picks thresholds**.

- The rules engine is the only place severities are decided.
- Findings carry their own source citations.
- The LLM only re-states findings in plain language.
- The safety validator runs after the LLM and is enforced regardless of what
  the LLM did. Diagnostic and prescriptive phrasing is rewritten or blocked
  by regex, not by asking the LLM nicely.

This is what lets the system run usefully in `--no-llm` mode for CI and tests.

## Module responsibilities

| Module | Reads | Writes |
|---|---|---|
| `app.parsers` | bytes / text | `LabValue[]` |
| `app.normalize.units` | `LabPanel` | canonical `LabPanel`, conversion warnings |
| `app.normalize.validator` | canonical `LabPanel` + warnings | `ValidationIssue[]` |
| `app.rules.*` | canonical `LabPanel` | `Finding[]` |
| `app.rules.engine` | family modules | `FindingCluster[]` + pattern `Finding[]` |
| `app.rag.store` | seed/markdown | ChromaDB |
| `app.rag.retrieve` | clusters + store | `dict[topic, Passage[]]` |
| `app.llm.client` | prompt, system | text |
| `app.safety.validator` | LLM text + findings | sanitized text + escalations |
| `app.pipeline.orchestrator` | inputs | `FinalReport` |

## Why these technology choices

- **PyMuPDF + pdfplumber**: digital lab PDFs almost always have a layered text
  layer. PyMuPDF is fast and robust; pdfplumber recovers tables when PyMuPDF
  loses them.
- **Tesseract**: shipping a heavy OCR (PaddleOCR) by default is hostile to
  install. PaddleOCR is opt-in via `engine="paddle"`.
- **ChromaDB + sentence-transformers/all-MiniLM-L6-v2**: simple persistent
  local vector store, no external service.
- **Ollama**: matches the user's preference for a local LLM; the client is
  intentionally small so it can be replaced with another provider later.
- **Pydantic v2 everywhere**: every layer accepts and emits validated types.
- **Typer + Rich**: ergonomic CLI without a frontend yet.

## Future work

- React/Next.js dashboard (upload UI + chart cards + chat-with-report).
- Postgres-backed user / report persistence (the Pydantic models already make
  the table shapes obvious).
- ICD-10 / SNOMED tagging of findings for downstream filtering.
- Trend tracking over multiple panels (delta against last visit).
- Wearable ingestion (sleep, steps, HR) into a side knowledge layer.
