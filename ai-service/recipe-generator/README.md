# Recipe Generator

A RAG-based daily / weekly meal-plan generator. Drops onto an existing
collection of recipe JSON, indexes them in ChromaDB, then assembles
balanced day plans with structured per-meal output (title, calories,
ingredients, steps, tips).

Replaces the older standalone `FixMakan.py` script — same idea, but
packaged, env-driven, and mountable into the Uplyfe gateway.

## Quick start

```bash
cd ai-service/recipe-generator

# 1. Drop your recipe JSON files into recipes/
#    See recipes/README.md for the accepted formats.

# 2. Make sure Ollama is running locally:
ollama list   # should show gemma4:26b (or whatever model you configure)

# 3. Install and ingest:
python -m venv .venv && source .venv/bin/activate
make install
cp .env.example .env
make ingest          # reads recipes/*.json and embeds into ChromaDB

# 4. Stats:
make stats
# recipes: 47 entries

# 5. Run standalone (or it'll be mounted by the gateway automatically):
make dev
# Then POST to http://localhost:8003/daily-menu
```

## How it integrates with the rest of the project

```
browser → Laravel → AI gateway (port 8001)
                  → this app (mounted at /recipe)
                  → ChromaDB (recipes collection)
                  → Ollama (LLM shapes each meal's output JSON)
```

The Uplyfe gateway (`ai-service/gateway/main.py`) imports
`recipe_generator.main:app` and mounts it at `/recipe`. Laravel's
`/api/ai/recipe/daily-menu` forwards through there.

## API

### `POST /daily-menu`
Single-day plan. Same request shape as the old stub:
```json
{
  "target_calories": 2000,
  "servings": 1,
  "diet": "vegetarian",
  "allergies": ["peanuts"],
  "cuisine_preferences": ["mediterranean"],
  "notes": "I prefer one-pan meals",
  "query": "high-fibre balanced day"
}
```

### `POST /weekly-menu`
Same body, generates seven days keyed by weekday name (`monday`…`sunday`).

### `POST /generate`
Generic — pass `days: 1..7` in the request body.

### `GET /healthz`
Shows whether Ollama is reachable, how many recipes are indexed, and the
configured paths.

## Repo layout

```
recipe_generator/
├── main.py              FastAPI entrypoint
├── config.py            env-driven settings
├── llm.py               Ollama generate + JSON parser
├── embeddings.py        sentence-transformers helper (all-MiniLM-L6-v2)
├── chroma_store.py      shared ChromaDB client + retrieval
├── models.py            Pydantic schemas (Meal, DayPlan, MealPlanResponse)
├── service.py           the multi-stage pipeline
├── api/routes.py        HTTP routes
└── cli/ingest.py        CLI for ingesting recipe JSON files
recipes/                 ← drop your recipe .json files here (gitignored)
data/chroma/             ChromaDB persistent store (gitignored)
```

## Why the LLM step

After retrieval, we ask the LLM to **shape** each picked recipe into the
rich `Meal` JSON the UI expects (title, calories, badge, benefits, tags,
clean ingredient list, step-by-step instructions, tip). The LLM never
invents recipes — it only formats and adds short narrative. If the LLM
is unreachable, the system still returns the recipe via a metadata-only
fallback, so the frontend never sees an empty result.
