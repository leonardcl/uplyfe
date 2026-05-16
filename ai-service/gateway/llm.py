"""Thin Ollama client shared by gateway-internal modules (exercise, recipe).

Health-checkup-extraction has its own dedicated Ollama client; we don't reuse
it here so the gateway stays decoupled from that module's internals.
"""
from __future__ import annotations

import json
import re
from typing import Any

import httpx

from config import get_settings


class OllamaError(RuntimeError):
    pass


def generate_json(
    prompt: str,
    *,
    system: str | None = None,
    temperature: float = 0.4,
    model: str | None = None,
    num_predict: int = 2048,
) -> dict[str, Any]:
    """Call Ollama and return the parsed JSON object the model produced.

    Uses Ollama's `format=json` mode so the model is forced to emit JSON.
    Pass `model` to override the configured default (e.g. a smaller, faster
    model for chat).
    """
    settings = get_settings()
    payload: dict[str, Any] = {
        "model": model or settings.ollama_model,
        "prompt": prompt,
        "stream": False,
        "format": "json",
        # gemma4:26b advertises the "thinking" capability — without disabling
        # it, the model burns its num_predict budget on hidden reasoning
        # tokens, leaving an empty or truncated `response` field.
        "think": False,
        "options": {
            "temperature": temperature,
            # num_predict caps generation length; gemma-family models can
            # wander into long loops that hit our HTTP timeout.
            "num_predict": num_predict,
            # Defensive against token-level degeneration on longer outputs.
            "repeat_penalty": 1.15,
            "top_p": 0.9,
            "top_k": 40,
        },
    }
    if system:
        payload["system"] = system

    url = f"{settings.ollama_host.rstrip('/')}/api/generate"
    try:
        with httpx.Client(timeout=settings.ollama_timeout_seconds) as client:
            resp = client.post(url, json=payload)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPError as e:
        raise OllamaError(f"Ollama request failed: {e}") from e

    raw = data.get("response", "").strip()
    if not raw:
        raise OllamaError("Ollama returned an empty response.")

    # --- Attempt 1: parse as-is ---
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        pass

    # --- Attempt 2: extract the first {...} block the model may have buried
    # inside markdown fences, preamble text, or trailing junk. ---
    json_match = re.search(r'\{.*\}', raw, re.DOTALL)
    if json_match:
        try:
            return json.loads(json_match.group())
        except json.JSONDecodeError:
            pass

    raise OllamaError(f"Ollama did not return valid JSON: {raw[:300]}")


def is_available() -> bool:
    settings = get_settings()
    url = f"{settings.ollama_host.rstrip('/')}/api/tags"
    try:
        with httpx.Client(timeout=2.0) as client:
            resp = client.get(url)
            return resp.status_code == 200
    except httpx.HTTPError:
        return False
