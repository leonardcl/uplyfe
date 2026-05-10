"""Convenience helpers that sit alongside the mounted health-checkup app.

These do not duplicate functionality — they just make the existing /manual
endpoint easier to test and integrate with from the frontend:

    GET /health-checkup/sample   — run the bundled sample lab through the AI
                                   pipeline and return the full FinalReport JSON.
                                   Optional ?use_llm=false to skip Ollama (fast).
    GET /health-checkup/schema   — JSON Schema of the FinalReport shape, so the
                                   frontend can be built/typed against a single
                                   contract.
"""
from __future__ import annotations

import importlib
import json
import shutil
from pathlib import Path

from fastapi import APIRouter, HTTPException, Query

from config import get_settings
from llm import is_available as ollama_is_available

router = APIRouter()


def _load_sample_panel() -> dict:
    settings = get_settings()
    sample = settings.health_checkup_abs_path / "data" / "samples" / "manual_input.json"
    if not sample.exists():
        raise HTTPException(
            status_code=500,
            detail=f"Sample input not found at {sample}. Check HEALTH_CHECKUP_PATH in .env.",
        )
    return json.loads(sample.read_text())


@router.get("/sample", summary="Run the bundled sample lab through the AI (returns FinalReport JSON)")
def sample(
    use_llm: bool = Query(default=True, description="Set to false for a fast deterministic-only pass."),
    use_rag: bool = Query(default=True),
) -> dict:
    """Drive the existing pipeline end-to-end with the bundled sample input.

    Imports are local because they require the health-checkup project to be on
    sys.path, which only happens after main.py has done its mount setup.
    """
    try:
        from app.models import LabPanel  # type: ignore[import-not-found]
        from app.pipeline import PipelineOptions, run_pipeline  # type: ignore[import-not-found]
    except Exception as e:
        raise HTTPException(
            status_code=503,
            detail=f"health-checkup-extraction not importable: {e}",
        ) from e

    panel_dict = _load_sample_panel()
    panel = LabPanel(**panel_dict)
    report = run_pipeline(
        panel=panel,
        options=PipelineOptions(use_llm=use_llm, use_rag=use_rag),
    )
    # Pydantic v2 — round-trip via model_dump(mode="json") so datetimes etc. are
    # serialised to plain JSON-friendly types.
    return report.model_dump(mode="json")


@router.get("/schema", summary="JSON Schema of the FinalReport response shape")
def schema() -> dict:
    try:
        from app.models import FinalReport  # type: ignore[import-not-found]
    except Exception as e:
        raise HTTPException(
            status_code=503,
            detail=f"health-checkup-extraction not importable: {e}",
        ) from e
    return FinalReport.model_json_schema()


@router.get("/sample-input", summary="Inspect the lab input that /sample uses")
def sample_input() -> dict:
    return _load_sample_panel()


@router.get("/probe", summary="Confirm every upload-path dependency is available")
def probe() -> dict:
    """Quick install check. Hit this before troubleshooting an upload failure.

    Returns a flat dict of `{component: ok|reason}` so the frontend / dev can
    see at a glance what's missing.
    """

    def _import_check(modname: str) -> str:
        try:
            importlib.import_module(modname)
            return "ok"
        except Exception as e:
            return f"missing: {type(e).__name__}: {e}"

    settings = get_settings()
    hc_path = settings.health_checkup_abs_path
    sample = hc_path / "data" / "samples" / "manual_input.json"
    chroma_dir = hc_path / "data" / "chroma"

    return {
        "ollama_reachable": ollama_is_available(),
        "ollama_model_configured": settings.ollama_model,
        "health_checkup_path_exists": hc_path.exists(),
        "sample_input_exists": sample.exists(),
        "rag_index_seeded": chroma_dir.exists() and any(chroma_dir.iterdir()) if chroma_dir.exists() else False,
        "deps": {
            "fitz_pymupdf": _import_check("fitz"),
            "pdfplumber": _import_check("pdfplumber"),
            "pytesseract": _import_check("pytesseract"),
            "PIL": _import_check("PIL"),
            "chromadb": _import_check("chromadb"),
            "sentence_transformers": _import_check("sentence_transformers"),
            "tenacity": _import_check("tenacity"),
        },
        "system_binaries": {
            "tesseract_in_path": shutil.which("tesseract") is not None,
        },
    }
