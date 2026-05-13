from __future__ import annotations

from llm import generate_json

from .schemas import ChatRequest, ChatResponse

SYSTEM_PROMPT = (
    "You are Uplyfe AI, a friendly evidence-based wellness assistant for habits, "
    "nutrition, and exercise. Be concise (2-4 short paragraphs max). Be specific "
    "and practical. Never diagnose or prescribe — refer to a clinician for serious "
    "medical questions. Always respond with a single JSON object of shape "
    '{"reply": "<your answer>"}.'
)


def _format_history(req: ChatRequest) -> str:
    if not req.history:
        return ""
    lines = []
    for turn in req.history:
        prefix = "User" if turn.role == "user" else "Uplyfe"
        lines.append(f"{prefix}: {turn.content}")
    return "\n".join(lines) + "\n"


def _build_system(req: ChatRequest) -> str:
    """Compose the system prompt. When the Laravel side has provided
    `user_context` (profile + latest health checkup summary), prepend it so
    the model can ground its reply in the user's actual data instead of
    answering in the abstract."""
    if not req.user_context:
        return SYSTEM_PROMPT
    return (
        SYSTEM_PROMPT
        + "\n\n--- USER CONTEXT (read-only; ground your reply in this data) ---\n"
        + req.user_context.strip()
        + "\n--- END USER CONTEXT ---\n"
        + "When the user asks about their health, latest checkup, biomarkers, "
        "diet, weight, or fitness, refer to the USER CONTEXT above explicitly "
        "and quote relevant values. If the requested data isn't in the context, "
        "say so plainly instead of inventing numbers."
    )


def chat(req: ChatRequest) -> ChatResponse:
    prompt = f"{_format_history(req)}User: {req.message}\nUplyfe:"
    data = generate_json(prompt, system=_build_system(req), temperature=0.3)
    reply = str(data.get("reply", "")).strip()
    if not reply:
        reply = "Sorry, I didn't catch that. Could you rephrase?"
    return ChatResponse(reply=reply)
