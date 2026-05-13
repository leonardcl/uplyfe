"""CLI for ingesting recipe JSON files into ChromaDB.

The user drops `.json` files into `recipes/` and runs:

    python -m recipe_generator.cli.ingest run

Accepted JSON shapes (the ingester is intentionally permissive):

  1. Top-level array of recipe objects:
        [ { "name": "...", "ingredients": [...], "instructions": [...] }, ... ]

  2. Top-level object with a "recipes" array:
        { "recipes": [ {...}, {...} ] }

  3. Single recipe object:
        { "name": "...", "ingredients": [...], "instructions": [...] }

Each recipe is expected to have AT LEAST a name + ingredients + instructions.
Other fields (cuisine, calories_per_serving, protein_per_serving, diet tags)
are kept as metadata so retrieval can filter on them later.
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Iterable

import typer
from rich.console import Console
from rich.progress import Progress

from recipe_generator.chroma_store import _client, recipe_count
from recipe_generator.config import get_settings
from recipe_generator.embeddings import embed_batch


app = typer.Typer(help="Ingest recipe JSON files into the recipes collection.")
console = Console()


# --- Shape normalization ------------------------------------------------


# Field-name aliases — the user's JSON may use any of these spellings.
_NAME_FIELDS = ["name", "title", "recipe_name", "Name"]
_INGREDIENT_FIELDS = ["ingredients", "ingredient_list", "ings"]
_INSTRUCTION_FIELDS = ["instructions", "directions", "steps", "method"]
_CALORIES_FIELDS = ["calories_per_serving", "calories", "kcal"]
_PROTEIN_FIELDS = ["protein_per_serving", "protein", "protein_g"]
_CARBS_FIELDS = ["carbs_per_serving", "carbs", "carbohydrates", "carbs_g"]
_CUISINE_FIELDS = ["cuisine", "cuisine_type", "origin"]
_DIET_FIELDS = ["diet", "diet_tags", "tags"]
_MEAL_TYPE_FIELDS = ["meal_type", "meal", "course", "slot"]
_CURATED_FIELDS = ["curated", "is_curated"]


def _first(entry: dict, fields: list[str], default=None):
    for f in fields:
        if f in entry and entry[f] not in (None, ""):
            return entry[f]
    return default


def _to_string_list(v) -> list[str]:
    if v is None:
        return []
    if isinstance(v, list):
        return [str(item).strip() for item in v if str(item).strip()]
    # Comma-separated, newline-separated, or single string.
    raw = str(v)
    parts: list[str]
    if "\n" in raw:
        parts = raw.split("\n")
    elif "," in raw and len(raw) > 80:
        parts = raw.split(",")
    else:
        parts = [raw]
    return [p.strip("-• ").strip() for p in parts if p.strip()]


def _normalize(entry: dict) -> dict:
    """Map an arbitrary recipe dict to a canonical shape."""
    name = _first(entry, _NAME_FIELDS, default="").strip() or None
    ingredients = _to_string_list(_first(entry, _INGREDIENT_FIELDS))
    instructions = _to_string_list(_first(entry, _INSTRUCTION_FIELDS))
    if not name or not ingredients:
        return {}  # skip incomplete entries
    return {
        "name": name,
        "ingredients": ingredients,
        "instructions": instructions,
        "cuisine": str(_first(entry, _CUISINE_FIELDS, default="")) or None,
        "calories_per_serving": _first(entry, _CALORIES_FIELDS),
        "protein_per_serving": _first(entry, _PROTEIN_FIELDS),
        "carbs_per_serving": _first(entry, _CARBS_FIELDS),
        "diet_tags": _to_string_list(_first(entry, _DIET_FIELDS)),
        "meal_type": str(_first(entry, _MEAL_TYPE_FIELDS, default="")).lower() or None,
        "curated": bool(_first(entry, _CURATED_FIELDS, default=False)),
        "description": str(entry.get("description") or "").strip() or None,
    }


def _iter_recipes_from_file(path: Path) -> Iterable[dict]:
    """Yield raw recipe dicts from a JSON file.

    Handles three top-level shapes (array, {recipes: [...]}, single object)
    AND prefers streaming with `ijson` when the file is large, so 600 MB+
    inputs don't have to fit in memory.
    """
    # For big files, prefer streaming via ijson if available — keeps memory
    # bounded regardless of input size.
    size_mb = path.stat().st_size / (1024 * 1024)
    if size_mb > 50:
        try:
            import ijson  # type: ignore[import-not-found]
        except ImportError:
            ijson = None
        if ijson is not None:
            with open(path, "rb") as f:
                # Try array-at-root first (most common shape for RAG datasets).
                try:
                    for entry in ijson.items(f, "item"):
                        if isinstance(entry, dict):
                            yield entry
                    return
                except ijson.JSONError:
                    pass
                # Fall back to {"recipes": [...]} wrapper.
                f.seek(0)
                try:
                    for entry in ijson.items(f, "recipes.item"):
                        if isinstance(entry, dict):
                            yield entry
                    return
                except ijson.JSONError:
                    pass
    # Small file or no ijson — load it all into memory.
    with open(path, "r", encoding="utf-8") as f:
        data = json.load(f)
    if isinstance(data, list):
        for entry in data:
            if isinstance(entry, dict):
                yield entry
    elif isinstance(data, dict):
        if "recipes" in data and isinstance(data["recipes"], list):
            for entry in data["recipes"]:
                if isinstance(entry, dict):
                    yield entry
        else:
            # Single recipe object.
            yield data


def _is_prebuilt_rag_shape(entry: dict) -> bool:
    """Detect the pre-built RAG format used by some recipe datasets:

        { "id": "...", "document": "Recipe: ...\\nIngredients: ...",
          "metadata": { "name": "...", "calories": ..., ... } }

    `document` is the text to embed; `metadata` holds scalar fields. We
    skip the normalize step entirely for these.
    """
    return (
        isinstance(entry.get("document"), str)
        and isinstance(entry.get("metadata"), dict)
        and entry["metadata"].get("name")
    )


def _build_text(r: dict) -> str:
    """The text we embed + return as the document."""
    parts = [f"Recipe: {r['name']}"]
    if r.get("meal_type"):
        # Embedded in the document so vector search ranks this recipe
        # higher for queries that mention the meal slot.
        parts.append(f"Meal type: {r['meal_type']}")
    if r.get("cuisine"):
        parts.append(f"Cuisine: {r['cuisine']}")
    if r.get("description"):
        parts.append(r["description"])
    if r.get("calories_per_serving"):
        parts.append(f"Calories: {r['calories_per_serving']}")
    if r.get("protein_per_serving"):
        parts.append(f"Protein: {r['protein_per_serving']}g")
    if r.get("carbs_per_serving"):
        parts.append(f"Carbs: {r['carbs_per_serving']}g")
    if r.get("diet_tags"):
        parts.append(f"Diet: {', '.join(r['diet_tags'])}")
    parts.append("\nIngredients:")
    parts.extend(f"- {i}" for i in r["ingredients"])
    parts.append("\nInstructions:")
    parts.extend(f"{idx + 1}. {s}" for idx, s in enumerate(r["instructions"]))
    return "\n".join(parts)


def _stable_id(name: str, source_file: str) -> str:
    """Generate a stable id from the name + source file so re-ingesting
    the same file upserts rather than duplicates."""
    import hashlib

    return hashlib.sha1(f"{source_file}:{name}".encode("utf-8")).hexdigest()[:24]


def _metadata_for_chroma(r: dict, source_file: str) -> dict:
    """ChromaDB metadata values must be str/int/float/bool. Coerce."""
    def _scalar(v):
        if v is None:
            return None
        if isinstance(v, (str, int, float, bool)):
            return v
        if isinstance(v, list):
            return ", ".join(str(item) for item in v)
        return str(v)

    return {
        "name": r["name"],
        "source": source_file,
        "cuisine": _scalar(r.get("cuisine")) or "",
        "calories_per_serving": _scalar(r.get("calories_per_serving")) or "",
        "protein_per_serving": _scalar(r.get("protein_per_serving")) or "",
        "carbs_per_serving": _scalar(r.get("carbs_per_serving")) or "",
        "diet_tags": _scalar(r.get("diet_tags")) or "",
        "meal_type": _scalar(r.get("meal_type")) or "",
        "curated": bool(r.get("curated", False)),
        "description": _scalar(r.get("description")) or "",
        "ingredients": ", ".join(r["ingredients"])[:1500],
    }


# --- Commands -----------------------------------------------------------


@app.command()
def run(
    recipes_dir: Path = typer.Option(
        None, "--dir", "-d",
        help="Override RECIPES_DIR. Defaults to the package's recipes/ folder.",
    ),
    batch_size: int = typer.Option(128, "--batch-size"),
    limit: int = typer.Option(
        0, "--limit",
        help="Cap the total number of recipes ingested (0 = all). Useful for quick smoke tests.",
    ),
    reset: bool = typer.Option(
        False, "--reset",
        help="Delete the existing collection before reindexing.",
    ),
):
    """Read every .json file in recipes/ and ingest into ChromaDB.

    Streams entries one-by-one so multi-hundred-megabyte files don't blow
    up memory. Supports two source shapes:
      * normalized: {name, ingredients, instructions, ...}
      * pre-built RAG: {id, document, metadata}
    """
    settings = get_settings()
    folder = Path(recipes_dir) if recipes_dir else Path(settings.recipes_dir)
    if not folder.exists():
        console.print(f"[red]✗[/red] Recipes folder not found: {folder}")
        raise typer.Exit(1)

    files = sorted(folder.glob("*.json"))
    if not files:
        console.print(f"[yellow]⚠[/yellow]  No .json files found in {folder}")
        console.print("    Drop your recipe files there and run this command again.")
        raise typer.Exit(0)

    console.print(f"Found {len(files)} JSON file(s) in {folder.name}/")
    for p in files:
        size_mb = p.stat().st_size / (1024 * 1024)
        console.print(f"  {p.name}  ({size_mb:.1f} MB)")
    if limit:
        console.print(f"[yellow]--limit {limit}[/yellow] — stopping after that many recipes")

    client = _client()
    if reset:
        try:
            client.delete_collection(settings.recipes_collection)
            console.print(f"[yellow]Reset[/yellow] collection {settings.recipes_collection!r}")
        except Exception:
            pass
    collection = client.get_or_create_collection(name=settings.recipes_collection)

    # Stream + batch: never hold more than `batch_size` records in memory.
    pending_ids: list[str] = []
    pending_docs: list[str] = []
    pending_meta: list[dict] = []
    pending_texts_to_embed: list[str] = []  # what gets fed to the embedder

    skipped = 0
    total_seen = 0
    total_ingested = 0
    prebuilt_used = False

    def flush():
        nonlocal pending_ids, pending_docs, pending_meta, pending_texts_to_embed, total_ingested
        if not pending_ids:
            return
        vectors = embed_batch(pending_texts_to_embed)
        collection.upsert(
            ids=pending_ids,
            documents=pending_docs,
            metadatas=pending_meta,
            embeddings=vectors,
        )
        total_ingested += len(pending_ids)
        pending_ids = []
        pending_docs = []
        pending_meta = []
        pending_texts_to_embed = []

    with Progress() as progress:
        # We don't know the total up-front for streamed files; show indeterminate.
        task = progress.add_task("Ingesting…", total=None)

        for path in files:
            try:
                source_iter = _iter_recipes_from_file(path)
            except (json.JSONDecodeError, OSError) as e:
                console.print(f"[red]  ✗ {path.name}[/red] — parse error: {e}")
                continue

            for entry in source_iter:
                total_seen += 1
                if limit and total_ingested + len(pending_ids) >= limit:
                    break

                if _is_prebuilt_rag_shape(entry):
                    # Pre-built RAG shape: use as-is.
                    prebuilt_used = True
                    meta = dict(entry["metadata"])
                    name = str(meta.get("name", "")).strip()
                    if not name:
                        skipped += 1
                        continue
                    rec_id = str(entry.get("id") or _stable_id(name, path.name))
                    document = str(entry["document"])
                    # Stamp source so we can trace later.
                    meta.setdefault("source", path.name)
                    # ChromaDB metadata must be scalar — coerce.
                    safe_meta = {
                        k: (v if isinstance(v, (str, int, float, bool)) else str(v))
                        for k, v in meta.items()
                    }
                    pending_ids.append(rec_id)
                    pending_docs.append(document)
                    pending_meta.append(safe_meta)
                    pending_texts_to_embed.append(document)
                else:
                    # Normalized shape: parse fields and synthesize document text.
                    normalized = _normalize(entry)
                    if not normalized:
                        skipped += 1
                        continue
                    text = _build_text(normalized)
                    pending_ids.append(_stable_id(normalized["name"], path.name))
                    pending_docs.append(text)
                    pending_meta.append(_metadata_for_chroma(normalized, path.name))
                    pending_texts_to_embed.append(text)

                if len(pending_ids) >= batch_size:
                    flush()
                    progress.update(task, completed=total_ingested,
                                    description=f"Ingested {total_ingested:,}…")

            if limit and total_ingested + len(pending_ids) >= limit:
                break

        flush()
        progress.update(task, completed=total_ingested,
                        description=f"Ingested {total_ingested:,}")

    if not total_ingested:
        console.print(f"[red]✗ Nothing to ingest.[/red] Saw {total_seen} entries, skipped {skipped}.")
        raise typer.Exit(1)

    shape = "pre-built RAG" if prebuilt_used else "normalized"
    console.print(
        f"\n[bold green]Done.[/bold green] Ingested {total_ingested:,} recipe(s) "
        f"(source shape: {shape}). Collection now has {collection.count():,} recipe(s) total. "
        f"(Saw {total_seen:,}, skipped {skipped:,} incomplete.)"
    )


@app.command()
def stats():
    """Show how many recipes are currently in the collection."""
    console.print(f"recipes: [bold]{recipe_count()}[/bold] entries")
    settings = get_settings()
    console.print(f"chroma_dir: {settings.chroma_dir}")
    console.print(f"recipes_dir: {settings.recipes_dir}")


if __name__ == "__main__":
    app()
