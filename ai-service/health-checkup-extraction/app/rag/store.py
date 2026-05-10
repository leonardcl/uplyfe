"""ChromaDB-backed knowledge store with sentence-transformer embeddings.

Why Chroma? Easy local persistence, no server. The embedder
(sentence-transformers/all-MiniLM-L6-v2) is small enough to run on CPU.

Documents are stored with metadata: {topic, source, title}. Retrieval is
filtered by topic when possible — this is what makes "topic-based retrieval"
fast and useful, instead of one big query bag.
"""
from __future__ import annotations

import hashlib
import re
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, Optional

from app.config import get_settings


@dataclass
class Passage:
    text: str
    topic: str
    source: str
    title: str
    score: float = 0.0


def _hash_id(text: str) -> str:
    return hashlib.sha1(text.encode("utf-8")).hexdigest()[:16]


def _chunk(text: str, max_chars: int = 1200) -> list[str]:
    """Greedy paragraph-aware chunker.

    Splits on blank lines, then merges paragraphs up to ~1200 chars per chunk.
    """
    paragraphs = re.split(r"\n\s*\n", text.strip())
    chunks: list[str] = []
    buf = ""
    for p in paragraphs:
        p = p.strip()
        if not p:
            continue
        if len(buf) + len(p) + 2 <= max_chars:
            buf = (buf + "\n\n" + p) if buf else p
        else:
            if buf:
                chunks.append(buf)
            buf = p
    if buf:
        chunks.append(buf)
    return chunks


class KnowledgeStore:
    """Thin wrapper around a Chroma persistent collection."""

    COLLECTION = "health-checkup-knowledge"

    def __init__(self):
        self.settings = get_settings()
        self._client = None
        self._collection = None
        self._embedder = None

    # --- lazy init so import is cheap ---

    def _ensure(self):
        if self._collection is not None:
            return
        import chromadb
        from chromadb.utils import embedding_functions

        Path(self.settings.chroma_dir).mkdir(parents=True, exist_ok=True)
        self._client = chromadb.PersistentClient(path=self.settings.chroma_dir)
        # Prefer sentence-transformers when available (better quality, more RAM).
        # Fall back to ChromaDB's built-in ONNX embedder, which is smaller and ships
        # with chromadb itself — no torch needed.
        try:
            from chromadb.utils.embedding_functions import SentenceTransformerEmbeddingFunction

            self._embedder = SentenceTransformerEmbeddingFunction(
                model_name=self.settings.embedding_model
            )
        except Exception:
            self._embedder = embedding_functions.DefaultEmbeddingFunction()
        self._collection = self._client.get_or_create_collection(
            name=self.COLLECTION,
            embedding_function=self._embedder,
            metadata={"hnsw:space": "cosine"},
        )

    # --- public API ---

    def add_document(self, text: str, *, topic: str, source: str, title: str) -> int:
        self._ensure()
        chunks = _chunk(text)
        if not chunks:
            return 0
        ids = [f"{topic}-{_hash_id(title + str(i) + chunks[i][:64])}" for i in range(len(chunks))]
        metadatas = [{"topic": topic, "source": source, "title": title}] * len(chunks)
        self._collection.upsert(ids=ids, documents=chunks, metadatas=metadatas)
        return len(chunks)

    def add_documents(self, items: Iterable[tuple[str, str, str, str]]) -> int:
        """items: iterable of (text, topic, source, title)."""
        n = 0
        for text, topic, source, title in items:
            n += self.add_document(text, topic=topic, source=source, title=title)
        return n

    def query(self, text: str, *, topic: Optional[str] = None, k: int = 4) -> list[Passage]:
        self._ensure()
        where = {"topic": topic} if topic else None
        try:
            r = self._collection.query(query_texts=[text], n_results=k, where=where)
        except Exception:
            return []
        passages: list[Passage] = []
        docs = (r.get("documents") or [[]])[0]
        metas = (r.get("metadatas") or [[]])[0]
        dists = (r.get("distances") or [[]])[0] if "distances" in r else [0.0] * len(docs)
        for d, m, dist in zip(docs, metas, dists):
            passages.append(
                Passage(
                    text=d,
                    topic=(m or {}).get("topic", ""),
                    source=(m or {}).get("source", ""),
                    title=(m or {}).get("title", ""),
                    score=float(1.0 - dist) if dist is not None else 0.0,
                )
            )
        return passages

    def count(self) -> int:
        self._ensure()
        return self._collection.count()
