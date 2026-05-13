"""ChromaDB-backed `recipes` collection.

One collection, one chunk per recipe. Metadata holds every queryable
field (name, calories, diet tags, cuisine, …) so the rules-engine-style
filtering can run after vector search.
"""
from __future__ import annotations

from dataclasses import dataclass
from functools import lru_cache
from pathlib import Path
from typing import Optional

from recipe_generator.config import get_settings
from recipe_generator.embeddings import embed


@dataclass
class RecipeMatch:
    document: str
    metadata: dict
    score: float = 0.0


@lru_cache(maxsize=1)
def _client():
    import chromadb

    settings = get_settings()
    Path(settings.chroma_dir).mkdir(parents=True, exist_ok=True)
    return chromadb.PersistentClient(path=settings.chroma_dir)


def _collection():
    return _client().get_or_create_collection(get_settings().recipes_collection)


def recipe_count() -> int:
    try:
        return _collection().count()
    except Exception:
        return 0


def search(query: str, k: Optional[int] = None) -> list[RecipeMatch]:
    settings = get_settings()
    n = k or settings.top_k_recipes
    qv = embed(query)
    res = _collection().query(query_embeddings=[qv], n_results=n)
    docs = (res.get("documents") or [[]])[0]
    metas = (res.get("metadatas") or [[]])[0]
    dists = (res.get("distances") or [[]])[0] if "distances" in res else [0.0] * len(docs)
    out: list[RecipeMatch] = []
    for d, m, dist in zip(docs, metas, dists):
        out.append(RecipeMatch(
            document=d,
            metadata=m or {},
            score=float(1.0 - dist) if dist is not None else 0.0,
        ))
    return out


def fetch_curated(limit: int = 100) -> list[RecipeMatch]:
    """Pull every recipe whose metadata has `curated == true`. Used to
    guarantee the small curated set always lands in the candidate pool
    even when vector search would drown them in the 200k bulk corpus."""
    try:
        res = _collection().get(where={"curated": True}, limit=limit)
    except Exception:
        return []
    docs = res.get("documents") or []
    metas = res.get("metadatas") or []
    out: list[RecipeMatch] = []
    for d, m in zip(docs, metas):
        out.append(RecipeMatch(document=d, metadata=m or {}, score=1.0))
    return out
