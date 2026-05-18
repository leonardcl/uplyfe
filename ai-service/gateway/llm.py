"""LLM client for the Uplyfe AI gateway.

`generate_json` — used by the chat module — calls OpenRouter (cloud).
`is_available`  — checks Ollama, still used by exercise / recipe / health-checkup.
"""
from __future__ import annotations

import json
import re
from typing import Any

import httpx

from config import get_settings


class OllamaError(RuntimeError):
    pass


# Alias so callers that catch OllamaError still work.
LLMError = OllamaError

_OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions"


def generate_json(
    prompt: str,
    *,
    system: str | None = None,
    temperature: float = 0.4,
    model: str | None = None,
    num_predict: int = 4096,
) -> dict[str, Any]:
    """Call OpenRouter and return the parsed JSON object the model produced."""
    settings = get_settings()
    model_id = model or settings.openrouter_model

    messages: list[dict] = []
    if system:
        messages.append({"role": "system", "content": system})
    messages.append({"role": "user", "content": prompt})

    # Append a hard JSON reminder to the last user message so models that
    # don't support response_format still stay on-track.
    if messages and messages[-1]["role"] == "user":
        messages[-1]["content"] += "\n\nIMPORTANT: Reply with valid JSON only. No markdown, no prose outside the JSON."

    payload: dict[str, Any] = {
        "model": model_id,
        "messages": messages,
        "temperature": temperature,
        "max_tokens": num_predict,
    }

    headers = {
        "Authorization": f"Bearer {settings.openrouter_api_key}",
        "Content-Type": "application/json",
        "HTTP-Referer": "https://uplyfe.app",
        "X-Title": "Uplyfe AI",
    }

    try:
        with httpx.Client(timeout=120) as client:
            resp = client.post(_OPENROUTER_URL, json=payload, headers=headers)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPError as e:
        raise LLMError(f"OpenRouter request failed: {e}") from e

    raw = (
        data.get("choices", [{}])[0]
        .get("message", {})
        .get("content", "")
        .strip()
    )
    if not raw:
        raise LLMError("OpenRouter returned empty content.")

    # --- Attempt 1: parse as-is ---
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        pass

    # --- Attempt 2: extract first {...} block (model may wrap in markdown) ---
    match = re.search(r"\{.*\}", raw, re.DOTALL)
    if match:
        try:
            return json.loads(match.group())
        except json.JSONDecodeError:
            pass

    raise LLMError(f"OpenRouter did not return valid JSON: {raw[:300]}")


def is_available() -> bool:
    """Check if the local Ollama instance is reachable (used by exercise/recipe/health-checkup)."""
    settings = get_settings()
    url = f"{settings.ollama_host.rstrip('/')}/api/tags"
    try:
        with httpx.Client(timeout=2.0) as client:
            resp = client.get(url)
            return resp.status_code == 200
    except httpx.HTTPError:
        return False
