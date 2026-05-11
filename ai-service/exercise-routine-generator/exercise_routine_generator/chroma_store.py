"""ChromaDB-backed retrieval for two collections.

  * `exercises`  — every entry from the curated exercise dataset, one chunk
                   per exercise, embedded by Ollama's `embeddinggemma`.
  * `pdf_books`  — chunks from exercise-science textbooks (ACSM / NSCA).
                   Optional — assessment still works when this collection
                   is empty (the LLM produces a generic assessment).

We share a single PersistentClient pointed at `settings.chroma_dir` so both
collections live in the same on-disk store.
"""
from __future__ import annotations

from dataclasses import dataclass
from functools import lru_cache
from pathlib import Path
from typing import Optional

from exercise_routine_generator.config import get_settings
from exercise_routine_generator.llm import embed


@dataclass
class Passage:
    text: str
    metadata: dict
    score: float = 0.0


@lru_cache(maxsize=1)
def _client():
    import chromadb

    settings = get_settings()
    Path(settings.chroma_dir).mkdir(parents=True, exist_ok=True)
    return chromadb.PersistentClient(path=settings.chroma_dir)


def _collection(name: str):
    return _client().get_or_create_collection(name=name)


def exercise_collection():
    return _collection(get_settings().exercise_collection)


def pdf_collection():
    return _collection(get_settings().pdf_collection)


def retrieve_exercises(query: str, k: Optional[int] = None) -> list[Passage]:
    """Vector search over the exercise dataset; returns the top-k entries."""
    settings = get_settings()
    n = k or settings.top_k_exercise
    qv = embed(query)
    res = exercise_collection().query(query_embeddings=[qv], n_results=n)
    docs = (res.get("documents") or [[]])[0]
    metas = (res.get("metadatas") or [[]])[0]
    dists = (res.get("distances") or [[]])[0] if "distances" in res else [0.0] * len(docs)
    out: list[Passage] = []
    for d, m, dist in zip(docs, metas, dists):
        out.append(Passage(text=d, metadata=m or {}, score=float(1.0 - dist) if dist is not None else 0.0))
    return out


def retrieve_pdf(query: str, k: Optional[int] = None) -> list[Passage]:
    """Vector search over the textbook PDF chunks. Returns an empty list
    when the collection has never been ingested (caller treats this as
    'no expert notes available')."""
    settings = get_settings()
    n = k or settings.top_k_pdf
    try:
        coll = pdf_collection()
        if coll.count() == 0:
            return []
        qv = embed(query)
        res = coll.query(query_embeddings=[qv], n_results=n)
    except Exception:
        return []
    docs = (res.get("documents") or [[]])[0]
    metas = (res.get("metadatas") or [[]])[0]
    return [Passage(text=d, metadata=m or {}) for d, m in zip(docs, metas)]


def exercise_count() -> int:
    try:
        return exercise_collection().count()
    except Exception:
        return 0


def pdf_count() -> int:
    try:
        return pdf_collection().count()
    except Exception:
        return 0
