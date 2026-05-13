"""Sentence-transformer embedding helper.

Preserves FixMakan's choice of `all-MiniLM-L6-v2` (small, fast, runs on CPU)
rather than the heavier Ollama embedding model. The model is lazy-loaded
once and reused for the lifetime of the process.
"""
from __future__ import annotations

from functools import lru_cache

from recipe_generator.config import get_settings


@lru_cache(maxsize=1)
def _model():
    # Imported lazily so test runners that mock embeddings don't pay the
    # ~300 MB sentence-transformers load cost.
    from sentence_transformers import SentenceTransformer

    settings = get_settings()
    return SentenceTransformer(settings.embed_model)


def embed(text: str) -> list[float]:
    return _model().encode([text]).tolist()[0]


def embed_batch(texts: list[str]) -> list[list[float]]:
    return _model().encode(texts).tolist()
