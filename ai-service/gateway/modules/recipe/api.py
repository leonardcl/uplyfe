from fastapi import APIRouter, HTTPException

from llm import OllamaError

from .schemas import DailyMenu, RecipeRequest
from .service import build_menu

router = APIRouter()


@router.post("/daily-menu", response_model=DailyMenu, summary="Generate a one-day menu")
def daily_menu(req: RecipeRequest) -> DailyMenu:
    try:
        return build_menu(req)
    except OllamaError as e:
        raise HTTPException(status_code=503, detail=f"LLM unavailable: {e}") from e
    except ValueError as e:
        raise HTTPException(status_code=502, detail=f"LLM returned invalid menu: {e}") from e
