"""Ingestion CLI — reworked from the original two scripts so paths are
env-driven and the same command works on any machine.

Examples:

    # Build the exercise collection from the curated dataset (~10 min):
    python -m app.cli.ingest exercises path/to/exercises.json

    # Default: looks for rag_sources/exercises-dataset/data/exercises.json
    python -m app.cli.ingest exercises

    # Index a textbook PDF into the pdf_books collection (~30-90 min):
    python -m app.cli.ingest pdf "rag_sources/ACSM Guidelines.pdf"

    # Quick stats:
    python -m app.cli.ingest stats
"""
from __future__ import annotations

import os
import re
from pathlib import Path
from typing import Optional

import typer
from rich.console import Console
from rich.progress import Progress

from exercise_routine_generator.chroma_store import _client, exercise_count, pdf_count
from exercise_routine_generator.config import PROJECT_ROOT, get_settings
from exercise_routine_generator.llm import embed


app = typer.Typer(help="Ingest data into the exercise + pdf ChromaDB collections.")
console = Console()


# --- helpers ---


def _build_exercise_text(entry: dict) -> str:
    return (
        f"Exercise ID: {entry.get('id')}\n"
        f"Exercise: {entry.get('name')}\n"
        f"Category: {entry.get('category')}\n"
        f"Body Part: {entry.get('body_part')}\n"
        f"Equipment: {entry.get('equipment')}\n"
        f"Target: {entry.get('target')}\n"
        f"Muscles: {entry.get('muscle_group')}\n"
        f"Instructions: {entry.get('instructions')}"
    ).strip()


def _flatten_instructions(entry: dict) -> str:
    """Dataset stores instructions as {en: "...", tr: "..."}. Prefer en."""
    inst = entry.get("instructions")
    if isinstance(inst, dict):
        return inst.get("en") or next(iter(inst.values()), "")
    return inst or ""


# --- commands ---


@app.command()
def exercises(
    json_path: Optional[Path] = typer.Argument(
        None,
        help="Path to exercises.json (defaults to rag_sources/exercises-dataset/data/exercises.json).",
    ),
    batch_size: int = typer.Option(32, "--batch-size"),
    reset: bool = typer.Option(
        False, "--reset", help="Delete the existing collection before reindexing.",
    ),
):
    """Embed every entry in exercises.json into the `exercises` collection."""
    import json as _json

    settings = get_settings()
    if json_path is None:
        json_path = (
            PROJECT_ROOT
            / "rag_sources"
            / "exercises-dataset"
            / "data"
            / "exercises.json"
        )
    if not json_path.exists():
        console.print(f"[red]✗ exercises file not found:[/red] {json_path}")
        raise typer.Exit(1)

    with open(json_path, "r", encoding="utf-8") as f:
        data = _json.load(f)
    console.print(f"Loaded {len(data)} exercises from {json_path.name}")

    client = _client()
    if reset:
        try:
            client.delete_collection(settings.exercise_collection)
            console.print(f"[yellow]Reset[/yellow] collection {settings.exercise_collection!r}")
        except Exception:
            pass
    collection = client.get_or_create_collection(name=settings.exercise_collection)

    ids: list[str] = []
    documents: list[str] = []
    embeddings: list[list[float]] = []
    metadatas: list[dict] = []

    with Progress() as progress:
        task = progress.add_task("Embedding…", total=len(data))
        for entry in data:
            cleaned = {
                "id": entry.get("id"),
                "name": entry.get("name"),
                "category": entry.get("category"),
                "body_part": entry.get("body_part"),
                "equipment": entry.get("equipment"),
                "instructions": _flatten_instructions(entry),
                "muscle_group": entry.get("muscle_group"),
                "target": entry.get("target"),
            }
            text = _build_exercise_text(cleaned)
            vec = embed(text)
            ids.append(str(cleaned["id"]))
            documents.append(text)
            embeddings.append(vec)
            metadatas.append({
                "exercise_id": cleaned["id"],
                "name": cleaned["name"],
                "category": cleaned["category"],
                "body_part": cleaned["body_part"],
                "target": cleaned["target"],
                "equipment": cleaned["equipment"],
                "muscle_group": cleaned["muscle_group"],
            })
            if len(ids) >= batch_size:
                collection.upsert(
                    ids=ids, documents=documents, metadatas=metadatas, embeddings=embeddings,
                )
                ids, documents, metadatas, embeddings = [], [], [], []
            progress.advance(task)

    if ids:
        collection.upsert(
            ids=ids, documents=documents, metadatas=metadatas, embeddings=embeddings,
        )

    console.print(f"[bold green]Done.[/bold green] Collection size: {collection.count()}")


@app.command()
def pdf(
    pdf_path: Path = typer.Argument(..., exists=True, readable=True),
    chunk_size: int = typer.Option(1200, "--chunk-size"),
    chunk_overlap: int = typer.Option(200, "--chunk-overlap"),
    batch_size: int = typer.Option(32, "--batch-size"),
):
    """Index a textbook PDF (ACSM / NSCA) into the `pdf_books` collection."""
    import pymupdf

    settings = get_settings()
    collection = _client().get_or_create_collection(name=settings.pdf_collection)
    source_name = pdf_path.name

    console.print(f"Extracting text from {source_name}…")
    doc = pymupdf.open(pdf_path)
    pages: list[tuple[int, str]] = []
    for i, page in enumerate(doc):
        pages.append((i + 1, page.get_text("text")))
    doc.close()
    console.print(f"  {len(pages)} pages")

    def _clean(text: str) -> str:
        text = re.sub(r"\n(?=[a-z])", " ", text)
        return re.sub(r"\s+", " ", text).strip()

    def _chunks(text: str):
        i = 0
        n = len(text)
        step = chunk_size - chunk_overlap
        while i < n:
            yield text[i : i + chunk_size]
            i += step

    # Pre-build records so we know total.
    records = []
    uid = 0
    for page_no, raw in pages:
        for ch in _chunks(_clean(raw)):
            records.append({"id": f"{source_name}:{uid}", "text": ch, "page": page_no})
            uid += 1
    console.print(f"  {len(records)} chunks; embedding…")

    ids: list[str] = []
    documents: list[str] = []
    embeddings: list[list[float]] = []
    metadatas: list[dict] = []

    with Progress() as progress:
        task = progress.add_task("Embedding…", total=len(records))
        for r in records:
            vec = embed(r["text"])
            ids.append(r["id"])
            documents.append(r["text"])
            embeddings.append(vec)
            metadatas.append({"page": r["page"], "source": source_name})
            if len(ids) >= batch_size:
                collection.upsert(
                    ids=ids, documents=documents, metadatas=metadatas, embeddings=embeddings,
                )
                ids, documents, metadatas, embeddings = [], [], [], []
            progress.advance(task)

    if ids:
        collection.upsert(
            ids=ids, documents=documents, metadatas=metadatas, embeddings=embeddings,
        )

    console.print(f"[bold green]Done.[/bold green] Collection size: {collection.count()}")


@app.command()
def stats():
    """Show how many chunks live in each collection."""
    console.print(f"exercises: [bold]{exercise_count()}[/bold] entries")
    console.print(f"pdf_books: [bold]{pdf_count()}[/bold] chunks")


if __name__ == "__main__":
    app()
