# Exercise Routine Generator

A 3-stage RAG pipeline that builds **weekly workout plans grounded in real data**:

1. **Health assessment** — retrieves textbook context (ACSM / NSCA) and asks the LLM to flag exercise types to avoid.
2. **Weekly structure** — drafts a day-by-day outline (focus, duration, equipment).
3. **Exercise selection** — retrieves matching exercises from a 1,300+ exercise dataset and asks the LLM to assemble the final plan. **The LLM never invents exercises** — it can only select from the retrieved catalog.

## Quick start

```bash
cd ai-service/exercise-routine-generator
python -m venv .venv && source .venv/bin/activate
pip install -e ".[dev]"
cp .env.example .env

# 1. Make sure Ollama has the models we need:
ollama pull embeddinggemma          # ~620 MB, for vector embeddings
ollama pull gemma4:26b              # the generator model (likely already present)

# 2. Ingest the exercise dataset (~10 min on a laptop):
python -m exercise_routine_generator.cli.ingest exercises

# 3. Optional — ingest textbook PDFs for richer assessment (~30-90 min):
python -m exercise_routine_generator.cli.ingest pdf "rag_sources/ACSM's Guidelines for Exercise Testing and Prescription -- ...pdf"

# 4. Stats:
python -m exercise_routine_generator.cli.ingest stats

# 5. Run standalone:
uvicorn exercise_routine_generator.main:app --reload --port 8002
# Then POST a profile to http://localhost:8002/generate
```

## How it integrates with the rest of the project

The Uplyfe gateway (`ai-service/gateway/`) mounts this package's FastAPI
app at `/exercise/*`. Laravel's `/api/ai/exercise/generate` forwards
requests through the gateway, so the chain is:

```
browser → Laravel → AI gateway (port 8001) → this app (mounted at /exercise)
                                            → Ollama (embed + generate)
                                            → ChromaDB (exercise + textbook collections)
```

## API

### `POST /generate`

Request body:
```json
{
  "profile": {
    "body_weight": "70 kg",
    "height": "170 cm",
    "age": "30",
    "sex": "male",
    "fitness_goals": "build muscle",
    "exercise_preference": "strength",
    "time_available": "45 minutes",
    "available_days": "Mon, Wed, Fri, Sat",
    "equipment_available": "home gym with dumbbells and a bench",
    "body_part_focus": "full body",
    "limitations": "previous shoulder injury"
  },
  "query": "Create a weekly exercise routine"
}
```

Response: `WeeklyPlan` JSON with stage outputs, the canonical equipment
list, and the structured `weekly_workout_plan` array.

### `GET /healthz`

Reports ChromaDB collection sizes and whether Ollama is reachable.

## Repo layout

```
app/
├── main.py             FastAPI entrypoint (mountable)
├── config.py           env-driven settings (anchors paths to PROJECT_ROOT)
├── llm.py              Ollama embed + generate wrappers
├── chroma_store.py     shared ChromaDB client + retrieval helpers
├── equipment.py        free-text → canonical equipment labels (LLM-backed)
├── service.py          the 3-stage pipeline
├── models.py           Pydantic request / response schemas
├── api/routes.py       HTTP routes
└── cli/ingest.py       CLI for building the ChromaDB collections
rag_sources/            (local, gitignored where appropriate)
data/chroma/            persistent vector store (gitignored)
```

## Origin

This package replaces the standalone CLI script `exercise7.py` (and its two
ingest scripts). The original code worked but baked in absolute paths and
remote IPs; this version is env-driven, packaged, and HTTP-callable.
