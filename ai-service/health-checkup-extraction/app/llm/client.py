"""LLM client for health-checkup — uses OpenRouter for generation.

Keeps the same OllamaClient class interface so all callers remain unchanged.
"""
from __future__ import annotations

import json
import re
from typing import Optional

import httpx

from app.config import Settings

_OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions"


class LLMUnavailableError(RuntimeError):
    """The LLM is unreachable or returned an unrecoverable error."""


class OllamaClient:
    """Drop-in replacement — same public interface, now backed by OpenRouter."""

    def __init__(self, settings: Settings):
        self.api_key = settings.openrouter_api_key
        self.model = settings.openrouter_model
        self.timeout = settings.llm_timeout_seconds
        self.temperature = settings.llm_temperature

    def is_available(self) -> bool:
        try:
            with httpx.Client(timeout=5.0) as c:
                r = c.get(
                    "https://openrouter.ai/api/v1/models",
                    headers={"Authorization": f"Bearer {self.api_key}"},
                )
                return r.status_code == 200
        except Exception:
            return False

    def generate(
        self,
        prompt: str,
        *,
        system: Optional[str] = None,
        format_json: bool = False,
        max_tokens: Optional[int] = None,
        stop: Optional[list[str]] = None,
        repeat_penalty: float = 1.18,
        repeat_last_n: int = 256,
        num_ctx: int = 4096,
    ) -> str:
        messages = []
        if system:
            messages.append({"role": "system", "content": system})

        content = prompt
        if format_json:
            content += "\n\nIMPORTANT: Reply with valid JSON only. No markdown, no prose outside the JSON."
        if stop:
            content += f"\n\nStop generating when you reach: {', '.join(stop)}"
        messages.append({"role": "user", "content": content})

        payload: dict = {
            "model": self.model,
            "messages": messages,
            "temperature": self.temperature,
            "max_tokens": max_tokens or 4096,
        }

        headers = {
            "Authorization": f"Bearer {self.api_key}",
            "Content-Type": "application/json",
            "HTTP-Referer": "https://uplyfe.app",
            "X-Title": "Uplyfe AI",
        }

        try:
            with httpx.Client(timeout=self.timeout) as c:
                r = c.post(_OPENROUTER_URL, json=payload, headers=headers)
                r.raise_for_status()
                body = r.json()
        except (httpx.HTTPError, ValueError) as e:
            raise LLMUnavailableError(f"OpenRouter request failed: {e}") from e

        raw = (body.get("choices", [{}])[0].get("message", {}).get("content") or "").strip()
        if not raw:
            raise LLMUnavailableError("OpenRouter returned empty content.")

        # If a stop token was requested, truncate at it.
        if stop:
            for tok in stop:
                idx = raw.find(tok)
                if idx != -1:
                    raw = raw[:idx]

        return raw.strip()
