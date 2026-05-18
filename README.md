# Uplyfe — Your Personal Health Stack, Running Entirely on Your Device

## Motivation

In Indonesia — and across Southeast Asia — getting a health checkup is a stressful experience
before you even see the results. There is a quiet dread that comes with it. And then the
printout arrives: a multi-page PDF filled with dozens of lab values, reference ranges in tiny
type, findings flagged with cryptic abbreviations like `[H]` or `↑`. Most people fold it up,
feel a vague anxiety about the numbers they don't recognize, and file it away. Not because
they don't care. Because they genuinely don't know what to do with it.

We felt this personally. We watched family members leave clinics like Prodia and Kimia Farma
with results in hand and no real understanding of what those results meant for how they should
eat, move, or live. The checkup happened. The understanding never did.

The problem isn't access to testing. It's access to understanding — and to a concrete next
step.

A second problem compounds the first: Indonesian lab reports are genuinely hard to parse,
even programmatically. Values are formatted with thousand-dot separators (`245.000` platelets,
not a quarter million cholesterol). Decimal commas appear next to decimal points in the same
document. Biomarker labels switch between Indonesian (`Glukosa Puasa`, `SGPT`, `Eritrosit`)
and English depending on the lab. A general-purpose chatbot — even a capable one — will
hallucinate reference ranges, confuse units, and occasionally suggest medications it has no
business recommending.

We wanted to build something that actually closes the loop: upload your lab report, get a
plain-language explanation grounded in real clinical guidelines, and walk away with a concrete
plan — your exercise routine, your meal plan — all calibrated to what your body is telling
you right now. And we wanted none of your health data to leave your machine.

## Solution Approach

Uplyfe is a full-stack health companion built around four AI modules, all powered locally by
Gemma via Ollama. A FastAPI gateway fronts every module; Laravel calls the gateway and routes
to whichever module the user's action requires. Everything runs on-device: Ollama, ChromaDB,
Tesseract for OCR. Health data never touches an external API.

```
Browser → Laravel (PHP)
              │  HTTP + X-API-Key
              ▼
         AI Gateway (FastAPI)
              ├── /health-checkup/*  →  multilingual lab-report pipeline
              ├── /exercise/*        →  3-stage RAG workout planner
              ├── /recipe/*          →  RAG meal planner
              └── /chat              →  context-aware wellness assistant
```

**Health Checkup Extraction** — Upload a PDF or photo of a lab report; the system extracts,
normalizes, interprets, and explains every biomarker it finds. The critical design decision
was to make Gemma the *last* layer, not the first. A deterministic rules engine — citing ADA
2024 for glucose, AHA/ACC 2018 for lipids, KDIGO 2024 for kidney function, AASLD for liver
markers — decides severity. Gemma only re-states those pre-decided findings in plain language,
grounded by passages retrieved from a curated clinical knowledge base (MedlinePlus, CDC, NIH
ODS, ATA, USDA DGA). If Gemma hallucinates a diagnosis, a post-generation safety validator
rewrites it before the user ever sees it.

**Exercise Routine Generator** — A 3-stage RAG pipeline that builds personalized weekly
workout plans grounded in real data. Stage 1 retrieves ACSM/NSCA textbook context and asks
Gemma to flag exercise types to avoid given the user's health profile. Stage 2 drafts a
day-by-day weekly structure (focus, duration, equipment). Stage 3 selects actual exercises
from a 1,300+ exercise catalog indexed in ChromaDB — Gemma never invents exercises, it only
picks from what was retrieved and assembles them into a structured plan. The result is a safe,
grounded routine rather than a generic list hallucinated from training data.

**Recipe Generator** — RAG-based daily and weekly meal planning over a 47-recipe indexed
corpus. A user's dietary profile (allergies, calorie target, cuisine preferences) drives the
retrieval query; Gemma shapes each retrieved recipe into structured output: title, calories,
badge, benefits, ingredients, step-by-step instructions, tip. The LLM never invents recipes —
it formats and adds short narrative. If Ollama is unreachable, a metadata-only fallback
ensures the frontend never returns empty.

**Chat** — A context-aware wellness assistant that ties the other three modules together.
Gemma reads a `USER CONTEXT` block containing the user's profile, their latest health checkup
biomarkers, today's meal plan, and their saved workout. It answers in natural language and,
crucially, returns a structured `intent` JSON alongside every reply that tells Laravel exactly
what to do next: show a recipe card, show the workout, regenerate the weekly meal plan,
regenerate the exercise routine, save a dietary exclusion. The chat is the connective tissue
— a single conversation where the user can say "I can't eat fish" and the system immediately
persists that preference and queues a background meal plan regeneration.

## Development Process

### Multilingual Extraction Was Harder Than It Looked

Our initial approach — pass the raw PDF text to Gemma and ask it to extract the biomarker
values — failed in exactly the ways we feared. Gemma would occasionally invent plausible-
sounding values, misread thousand separators, or silently skip biomarkers it didn't recognize.
LLM-first extraction is fundamentally unsuited for this task because there is no tolerating
errors in health data.

We scrapped it entirely and made regex the primary path. A bilingual alias table maps
Indonesian labels (`Asam Urat`, `Trigliserida`, `Leukosit`, `Trombosit`, `SGPT`) to canonical
keys alongside their English counterparts. A locale-aware number parser handles thousand-dot
separators, comma decimals, and mixed conventions in the same document. Bracketed flags
(`[H]`, `[L]`, `↑`, `↓`, `*`) that appear between a value and its unit are stripped before
parsing. The LLM extractor only runs as a fallback when regex finds nothing on a page — a
clean escape hatch, not a first-line guess.

The payoff: 100% extraction recall across 82 biomarker values on the synthetic test suite —
Prodia-style, Kimia Farma bilingual, Pramita multi-page, English baseline, and the
pathological thousand-separator layout — zero wrong values, without Gemma touching a single
number.

### The Locale Number Problem Almost Broke Everything

This is the detail we underestimated most. Indonesian medical reports use the European
convention: periods as thousand separators, commas as decimal points. `8.500/µL` means 8,500
white blood cells — not 8.5. `13,8 g/dL` is 13.8 hemoglobin — not 138. And within the same
bilingual document you sometimes find values formatted the American way on the English headers.

The naive approach — split on the first non-digit character — produced catastrophic misreads.
The parser we ended up with applies a plausibility heuristic: if the integer part exceeds a
biomarker-specific ceiling, treat the separator as a thousands marker; if the value would be
implausibly small, treat it as a decimal. Units like `juta/µL` (literally "million per
microliter") serve as a cross-check. This logic is tested against every synthetic layout.

### Making the LLM Safe for Medical Context

The safety constraint was non-negotiable: this tool must never tell a user they have a
disease or recommend a medication. But instructing a model to be careful in a system prompt
is not a safety layer — it is a polite request.

We built defense in depth instead. The rules engine emits findings with a severity label and
a source citation (e.g., "borderline — ADA 2024 IFG threshold 100–125 mg/dL"). Gemma
receives the pre-decided severity and re-states it in plain language. After generation, a
safety validator runs regex over the output: phrases like "you have diabetes", "this confirms",
or "take metformin" are rewritten or blocked outright, with an explicit marker so the user
knows something was filtered. Fourteen escalation triggers — fasting glucose ≥ 400 mg/dL,
eGFR < 30, Hgb < 8 g/dL, BP ≥ 180/120, among others — always surface a "When to See a
Doctor" section regardless of what Gemma wrote. The system runs usefully in `--no-llm` mode,
producing a fully structured deterministic report. That is also how all 110 tests run in CI —
no model required, no flakiness.

### Hallucination Prevention in Exercise Planning

The exercise generator had its own version of the same problem. A model asked to "build a
workout plan for someone with a previous shoulder injury and a home gym" will produce something
that sounds reasonable and is largely fabricated — exercises that don't exist, rep schemes
from nowhere, warnings that contradict each other.

The 3-stage pipeline was our answer. Stage 1 retrieves the relevant contraindication context
from ACSM/NSCA passages before any plan is generated. Stage 3 is a *selection* step, not a
generation step: Gemma receives a retrieved list from the 1,300-entry exercise catalog and
picks from it. The model assembles; it does not invent. The generated plan is grounded in
indexed exercises matched to the user's available equipment and body-part focus.

### Intent Detection: Two Layers Are Better Than One

The chat module returns a structured `intent` JSON alongside every natural-language reply.
Gemma fills it — `wants_recipe`, `wants_workout`, `regenerate_menu`, `regenerate_workout`,
`dietary_change`, `cuisine_request`, `target_day`, and more. But Gemma is a generative model
and occasionally misfires on intent: it conflates "I want Indonesian food" (show a recipe)
with "regenerate my meal plan" (expensive background job), or it silently sets
`wants_recipe: true` alongside `regenerate_menu: true` when those two must never coexist.

So we added a second, cheaper layer: a deterministic regex classifier in PHP that runs before
the LLM call. It catches the unambiguous cases — "what's my dinner tonight", "show me my
workout", "give me a recipe for lunch" — and applies hard rules that override whatever Gemma
returns. False positives are far worse than false negatives here (accidentally triggering a
plan regeneration is disruptive), so both layers are deliberately conservative.

The same philosophy applies to dietary intent. When the user says "I can't eat fish", a
rule-based PHP detector extracts the food exclusion *before* the LLM answers, so the
assistant can acknowledge a change the user just made in the same reply. It guards against
the obvious edge case — "Why no workout today?" must not match the `no <food>` pattern —
by checking whether the message is a question before running the looser patterns.

### Keeping Chat Grounded in Real User Data

The worst version of an AI wellness assistant says things like "since your cholesterol is
elevated, you should..." and then cites a number it invented. The chat module avoids this by
injecting a `USER CONTEXT` block — containing the user's latest biomarker values from their
most recent checkup, today's meal plan slots, and their saved workout — directly into the
system prompt at call time. Gemma is instructed to quote values verbatim from that block or
say "I don't have that detail" if the fact isn't there. Temperature is set to 0.15 — lower
than the default — to push the model toward quoting context rather than improvising. The
anti-hallucination rules are explicit: never invent biomarker values, dates, recipe names, or
workout names. If a card was shown in the previous turn, quote its title verbatim from the
history, not a paraphrase.

## What's Next

Uplyfe is not finished, and we know it. The backend for all four AI modules is production-
ready — the extraction pipeline, the exercise planner, the recipe generator, the chat. What
we are still building is the full frontend experience that ties it together: upload UI, report
history with trend charts across multiple checkups, and wearable ingestion so that steps,
sleep, and heart rate can feed into the same picture as lab values.

But the hard parts are done. Parsing Indonesian lab formats that no one else handles. Making
an LLM safe enough to touch medical data. Preventing hallucination in exercise planning by
making the model select instead of invent. Building a chat layer that knows when to hand off
to a background job and when to just answer the question.

We started with a feeling we knew firsthand — that sinking moment of leaving a clinic with
numbers you don't understand — and we built toward the thing we wished had existed: a
companion that reads the report with you, grounds every claim in a real guideline, and tells
you what to do next. Not a chatbot. Not a symptom checker. A closed loop from lab result to
daily plan, running entirely on your device, with your data staying yours.
