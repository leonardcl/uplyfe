"""Ingestion CLI.

Examples:
    python -m app.cli.ingest seed
    python -m app.cli.ingest add path/to/notes.md --topic lipids --source "AHA 2018"
    python -m app.cli.ingest add path/to/guideline.pdf --topic kidney --source "KDIGO 2024"
    python -m app.cli.ingest from-manifest data/sources.json
    python -m app.cli.ingest stats
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Optional

import typer
from rich.console import Console
from rich.table import Table

from app.parsers import extract_text_from_pdf
from app.rag import KnowledgeStore


app = typer.Typer(help="Ingest knowledge into the RAG vector store.")
console = Console()


SEED_DIR = Path(__file__).resolve().parent.parent / "rag" / "seed"


@app.command()
def seed():
    """Ingest the curated starter pack from app/rag/seed/*.md."""
    store = KnowledgeStore()
    total = 0
    for md in sorted(SEED_DIR.glob("*.md")):
        topic = md.stem
        text = md.read_text(encoding="utf-8")
        title = topic.replace("_", " ").title()
        added = store.add_document(text, topic=topic, source=f"seed/{md.name}", title=title)
        console.print(f"[green]+[/green] {md.name} → topic={topic} chunks={added}")
        total += added
    console.print(f"\n[bold]Done.[/bold] {total} chunks indexed; collection now has {store.count()} chunks.")


@app.command()
def add(
    path: Path = typer.Argument(..., exists=True, readable=True),
    topic: str = typer.Option(..., "--topic", "-t", help="Topic key, e.g. lipids, kidney, diet."),
    source: str = typer.Option("user-added", "--source", "-s"),
    title: Optional[str] = typer.Option(None, "--title"),
):
    """Add one document (markdown / text / PDF) to the store."""
    store = KnowledgeStore()
    title = title or path.stem.replace("_", " ").title()
    if path.suffix.lower() == ".pdf":
        text = extract_text_from_pdf(path)
    else:
        text = path.read_text(encoding="utf-8")
    added = store.add_document(text, topic=topic, source=source, title=title)
    console.print(f"[green]+[/green] {path.name} → topic={topic} chunks={added}")


@app.command("from-manifest")
def from_manifest(
    manifest: Path = typer.Argument(..., exists=True, readable=True, help="JSON manifest file."),
    base_dir: Optional[Path] = typer.Option(None, "--base-dir", help="Resolve relative paths against this dir."),
    dry_run: bool = typer.Option(False, "--dry-run", help="List what would be ingested without indexing."),
):
    """Ingest a batch of local files described in a JSON manifest.

    Manifest schema (one item per document):
        [
          {
            "path": "knowledge/ada-2024-summary.pdf",
            "topic": "glucose",
            "source": "ADA Standards of Care 2024",
            "title": "ADA Standards of Care in Diabetes — 2024",
            "url": "https://diabetesjournals.org/care/issue/47/Supplement_1",
            "license": "Author/publisher restrictions apply — local-use only.",
            "last_updated": "2024-01-01",
            "jurisdiction": "US"
          },
          ...
        ]

    Only `path`, `topic`, `source`, `title` are required. The rest is
    metadata for your own auditing.
    """
    items = json.loads(manifest.read_text(encoding="utf-8"))
    if not isinstance(items, list):
        console.print("[red]Manifest must be a JSON array of objects.[/red]")
        raise typer.Exit(2)

    # Resolve relative paths against, in order of preference:
    #   1. --base-dir if the user passed one;
    #   2. the current working directory (the typical case — user runs the
    #      command from the project root and the manifest's `path` fields are
    #      relative to that root, e.g. "knowledge/medlineplus/cbc.md");
    #   3. the manifest's own directory (last-resort fallback for layouts where
    #      knowledge files sit alongside the manifest).
    if base_dir is not None:
        base = base_dir
    elif (Path.cwd() / items[0].get("path", "")).exists():
        base = Path.cwd()
    elif (manifest.parent / items[0].get("path", "")).exists():
        base = manifest.parent
    else:
        base = Path.cwd()

    table = Table(title="Manifest items")
    table.add_column("Topic"); table.add_column("Source"); table.add_column("File"); table.add_column("Status")

    store = None if dry_run else KnowledgeStore()
    total_chunks = 0
    for item in items:
        try:
            rel = item["path"]
            topic = item["topic"]
            source = item["source"]
            title = item.get("title") or Path(rel).stem
        except KeyError as e:
            table.add_row("?", "?", str(item), f"[red]missing key {e}[/red]")
            continue
        path = (base / rel).resolve()
        if not path.exists():
            table.add_row(topic, source, rel, "[yellow]missing on disk[/yellow]")
            continue
        if dry_run:
            table.add_row(topic, source, rel, "[dim]would ingest[/dim]")
            continue

        try:
            if path.suffix.lower() == ".pdf":
                text = extract_text_from_pdf(path)
            else:
                text = path.read_text(encoding="utf-8")
        except Exception as e:
            table.add_row(topic, source, rel, f"[red]read error: {e}[/red]")
            continue

        chunks = store.add_document(text, topic=topic, source=source, title=title)
        total_chunks += chunks
        table.add_row(topic, source, rel, f"[green]+{chunks} chunks[/green]")

    console.print(table)
    if not dry_run:
        console.print(f"[bold]Done.[/bold] +{total_chunks} chunks. Collection size: {store.count()}")


@app.command()
def stats():
    """Show how many chunks are in the store."""
    store = KnowledgeStore()
    console.print(f"Collection size: [bold]{store.count()}[/bold] chunks")


if __name__ == "__main__":
    app()
