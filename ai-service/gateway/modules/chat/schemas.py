from __future__ import annotations

from typing import Literal

from pydantic import BaseModel, Field


class ChatTurn(BaseModel):
    role: Literal["user", "assistant"]
    content: str = Field(min_length=1, max_length=4000)


class ChatRequest(BaseModel):
    message: str = Field(min_length=1, max_length=4000)
    history: list[ChatTurn] = Field(default_factory=list, max_length=20)
    # Optional plain-text block summarizing what the assistant should
    # know about the user (profile, latest health checkup, etc.). Kept
    # as opaque text so the gateway doesn't need to track every field
    # the Laravel side might add.
    user_context: str | None = Field(default=None, max_length=6000)


class ChatResponse(BaseModel):
    reply: str
