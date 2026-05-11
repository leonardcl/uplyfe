"""HTTP routes.

  POST /generate    — build a weekly routine for one user profile.
  GET  /healthz     — liveness + chroma collection sizes + Ollama check.
"""
from __future__ import annotations

from fastapi import APIRouter, HTTPException

from exercise_routine_generator.chroma_store import exercise_count, pdf_count
from exercise_routine_generator.config import get_settings
from exercise_routine_generator.llm import LLMUnavailableError, is_available as ollama_is_available
from exercise_routine_generator.models import GenerateRequest, WeeklyPlan
from exercise_routine_generator.service import generate_routine


router = APIRouter()


@router.post("/generate", response_model=WeeklyPlan)
def generate(req: GenerateRequest) -> WeeklyPlan:
    try:
        return generate_routine(req)
    except LLMUnavailableError as e:
        raise HTTPException(status_code=503, detail=f"LLM unavailable: {e}") from e
    except ValueError as e:
        raise HTTPException(status_code=502, detail=f"LLM returned invalid plan: {e}") from e


@router.get("/healthz")
def healthz() -> dict:
    settings = get_settings()
    return {
        "status": "ok",
        "ollama_available": ollama_is_available(),
        "llm_model": settings.llm_model,
        "embed_model": settings.embed_model,
        "exercise_count": exercise_count(),
        "pdf_count": pdf_count(),
    }
