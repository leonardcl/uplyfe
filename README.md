# Uplyfe — Your Personal AI Health Companion

> The checkup tells you the numbers. Uplyfe tells you what to do with them.

We watched family members leave clinics like Prodia and Kimia Farma with lab results they didn't understand. The checkup happened. The understanding never did. Uplyfe is what we built so that never happens again.

Upload a lab report — PDF or photo, in English, Indonesian, or Korean — and get a plain-language explanation grounded in real clinical guidelines, a personalized meal plan, and a tailored workout routine.

## How It Works

The LLM is the **last layer**, not the first. A deterministic rules engine (ADA 2024, AHA/ACC 2018, KDIGO 2024) decides severity before Gemma is ever invoked. Gemma only re-states what the rules engine already decided. A safety validator rewrites any hallucinated diagnosis before the user sees it.

- **Health Checkup** — Regex-first extraction with two-layer hallucination filtering. 100% recall across 82 biomarker values on the synthetic test suite.
- **Exercise Planner** — 3-stage RAG pipeline over a 1,300+ exercise catalog. Gemma selects; it does not invent.
- **Meal Planner** — RAG-based daily and weekly meal planning over an indexed recipe corpus.
- **Chat** — Context-aware assistant that reads the user's biomarkers, meal plan, and workout. Returns structured intent JSON so the UI always knows what to do next.

## Stack

Laravel 10 · FastAPI · Gemma 4 26B (Ollama or OpenRouter) · ChromaDB · Tesseract OCR · SQLite
