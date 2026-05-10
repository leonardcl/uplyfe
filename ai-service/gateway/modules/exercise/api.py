from fastapi import APIRouter, HTTPException

from llm import OllamaError

from .schemas import ExercisePlan, ExerciseRequest
from .service import build_plan

router = APIRouter()


@router.post("/generate", response_model=ExercisePlan, summary="Generate a weekly workout plan")
def generate(req: ExerciseRequest) -> ExercisePlan:
    try:
        return build_plan(req)
    except OllamaError as e:
        raise HTTPException(status_code=503, detail=f"LLM unavailable: {e}") from e
    except ValueError as e:
        raise HTTPException(status_code=502, detail=f"LLM returned invalid plan: {e}") from e
