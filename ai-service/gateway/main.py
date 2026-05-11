"""Uplyfe AI Gateway — FastAPI entrypoint.

Run with:
    uvicorn main:app --reload --port 8000
"""
from __future__ import annotations

import sys
from pathlib import Path

from fastapi import FastAPI

from auth import ApiKeyMiddleware
from config import get_settings
from llm import is_available as ollama_is_available
from modules.health_checkup_helpers.api import router as health_checkup_helpers_router
from modules.recipe.api import router as recipe_router


def _build_app() -> FastAPI:
    settings = get_settings()
    app = FastAPI(
        title="Uplyfe AI Gateway",
        version="0.1.0",
        description=(
            "Single entrypoint for all Uplyfe AI modules. "
            "Routes /health-checkup/* to the existing health-checkup-extraction app, "
            "and /exercise/* and /recipe/* to gateway-internal modules."
        ),
    )

    app.add_middleware(ApiKeyMiddleware)

    # --- gateway-owned modules ---
    app.include_router(recipe_router, prefix="/recipe", tags=["recipe"])

    # Helpers that live alongside the mounted health-checkup app.
    # MUST be included BEFORE the mount, otherwise the mount swallows every
    # /health-checkup/* path and these helpers become unreachable.
    app.include_router(
        health_checkup_helpers_router,
        prefix="/health-checkup",
        tags=["health-checkup"],
    )

    # --- mount the existing health-checkup-extraction FastAPI app under /health-checkup ---
    # We add its directory to sys.path and import its `app` object. This keeps the
    # health-checkup project completely unmodified. Health-checkup's inner package
    # is still named `app`, so we import it FIRST (before exercise-routine, which
    # uses a distinct `exercise_routine_generator` package name and so doesn't
    # collide).
    hc_path = settings.health_checkup_abs_path
    if hc_path.exists():
        sys.path.insert(0, str(hc_path))
        try:
            from app.main import app as health_checkup_app  # type: ignore[import-not-found]
            app.mount("/health-checkup", health_checkup_app)
        except Exception as e:  # pragma: no cover — surfaced in /healthz
            app.state.health_checkup_error = str(e)
    else:
        app.state.health_checkup_error = f"path not found: {hc_path}"

    # --- mount the exercise-routine-generator FastAPI app under /exercise ---
    # Replaces the old in-gateway stub. The package is `pip install -e`'d into
    # this venv with a distinct module name so it can't collide with the
    # health-checkup mount above.
    try:
        from exercise_routine_generator.main import app as exercise_app
        app.mount("/exercise", exercise_app)
    except Exception as e:  # pragma: no cover — surfaced in /healthz
        app.state.exercise_routine_error = str(e)

    # --- liveness ---
    @app.get("/healthz", tags=["meta"])
    def healthz() -> dict:
        return {
            "status": "ok",
            "ollama_available": ollama_is_available(),
            "ollama_model": settings.ollama_model,
            "health_checkup_mounted": not hasattr(app.state, "health_checkup_error"),
            "health_checkup_error": getattr(app.state, "health_checkup_error", None),
            "exercise_routine_mounted": not hasattr(app.state, "exercise_routine_error"),
            "exercise_routine_error": getattr(app.state, "exercise_routine_error", None),
            "modules": ["health-checkup", "exercise", "recipe"],
        }

    return app


app = _build_app()
