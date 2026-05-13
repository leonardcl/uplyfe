"""FastAPI entrypoint for the recipe generator.

Mountable into the Uplyfe gateway at `/recipe` (see gateway/main.py).
Or run standalone:

    uvicorn recipe_generator.main:app --reload --port 8003
"""
from __future__ import annotations

from fastapi import FastAPI

from recipe_generator.api.routes import router


app = FastAPI(
    title="Recipe Generator",
    version="0.1.0",
    description=(
        "RAG-based daily / weekly meal-plan generator. Searches a curated "
        "recipe collection in ChromaDB and asks the LLM to shape each meal "
        "into the rich JSON the UI renders."
    ),
)
app.include_router(router)
