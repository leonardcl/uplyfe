"""HTTP routes for the recipe generator.

  POST /daily-menu    — single-day plan (default).
  POST /weekly-menu   — 7-day plan.
  POST /generate      — generic; respects request.days (1-7).
  GET  /healthz       — liveness + collection size + LLM check.
"""
from __future__ import annotations

from fastapi import APIRouter, HTTPException

from recipe_generator.chroma_store import recipe_count
from recipe_generator.config import get_settings
from recipe_generator.llm import LLMUnavailableError, is_available as ollama_is_available
from recipe_generator.models import MealPlanRequest, MealPlanResponse
from recipe_generator.service import build_meal_plan


router = APIRouter()


@router.post("/daily-menu", response_model=MealPlanResponse)
def daily_menu(req: MealPlanRequest) -> MealPlanResponse:
    req = req.model_copy(update={"days": 1})
    return _safe_build(req)


@router.post("/weekly-menu", response_model=MealPlanResponse)
def weekly_menu(req: MealPlanRequest) -> MealPlanResponse:
    req = req.model_copy(update={"days": 7})
    return _safe_build(req)


@router.post("/generate", response_model=MealPlanResponse)
def generate(req: MealPlanRequest) -> MealPlanResponse:
    return _safe_build(req)


@router.get("/healthz")
def healthz() -> dict:
    settings = get_settings()
    return {
        "status": "ok",
        "ollama_available": ollama_is_available(),
        "llm_model": settings.llm_model,
        "embed_model": settings.embed_model,
        "recipes_dir": settings.recipes_dir,
        "recipe_count": recipe_count(),
    }


def _safe_build(req: MealPlanRequest) -> MealPlanResponse:
    try:
        return build_meal_plan(req)
    except RuntimeError as e:
        # Empty collection / no recipes ingested.
        raise HTTPException(status_code=503, detail=str(e)) from e
    except LLMUnavailableError as e:
        raise HTTPException(status_code=503, detail=f"LLM unavailable: {e}") from e
    except ValueError as e:
        raise HTTPException(status_code=502, detail=f"LLM returned invalid plan: {e}") from e
