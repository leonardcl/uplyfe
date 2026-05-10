# Uplyfe AI Gateway

Single FastAPI service that fronts every AI module in the Uplyfe project. Laravel calls this gateway; the gateway routes to the appropriate module.

```
Laravel (PHP)
    │ HTTP + X-API-Key
    ▼
Gateway (this app, port 8000)
    ├── /health-checkup/*   →  ../health-checkup-extraction (mounted)
    ├── /exercise/*         →  modules/exercise (Ollama-backed stub)
    ├── /recipe/*           →  modules/recipe   (Ollama-backed stub)
    └── /healthz            →  liveness probe (no auth)
```

## Quick start

```bash
cd ai-service/gateway

# 1. install (creates .venv, installs gateway + the existing
#    health-checkup-extraction project as an editable dep so its routes
#    can be mounted)
make install

# 2. configure
cp .env.example .env
# then edit AI_SERVICE_KEY to a real value (must match Laravel's .env)

# 3. (optional, for real LLM output) make sure Ollama is running and pull a model
ollama pull gemma4:26b

# 4. run
make dev
```

The interactive API docs are at <http://localhost:8000/docs>.

## Auth

Every route except `/healthz` requires an `X-API-Key` header. The same value goes in Laravel's `.env` as `AI_SERVICE_KEY`.

```bash
curl -H "X-API-Key: your-key" \
     -H "Content-Type: application/json" \
     -d '{"goal":"build muscle","level":"beginner","days_per_week":3}' \
     http://localhost:8000/exercise/generate
```

## Modules

| Path prefix         | Source                                | Status     |
| ------------------- | ------------------------------------- | ---------- |
| `/health-checkup`   | `../health-checkup-extraction/app`    | Production |
| `/exercise`         | `modules/exercise`                    | Stub (MVP) |
| `/recipe`           | `modules/recipe`                      | Stub (MVP) |

When an MVP module grows large enough, promote it to a sibling top-level project alongside `health-checkup-extraction/`.
