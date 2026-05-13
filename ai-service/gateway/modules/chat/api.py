from fastapi import APIRouter, HTTPException

from llm import OllamaError

from .schemas import ChatRequest, ChatResponse
from .service import chat as run_chat

router = APIRouter()


@router.post("", response_model=ChatResponse, summary="Free-form chat with Uplyfe AI")
def chat(req: ChatRequest) -> ChatResponse:
    try:
        return run_chat(req)
    except OllamaError as e:
        raise HTTPException(status_code=503, detail=f"LLM unavailable: {e}") from e
