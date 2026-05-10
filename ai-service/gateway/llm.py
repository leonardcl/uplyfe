"""Thin Ollama client shared by gateway-internal modules (exercise, recipe).

Health-checkup-extraction has its own dedicated Ollama client; we don't reuse
it here so the gateway stays decoupled from that module's internals.
"""
from __future__ import annotations

import json
from typing import Any

import httpx

from config import get_settings


class OllamaError(RuntimeError):
    pass


def generate_json(prompt: str, *, system: str | None = None, temperature: float = 0.4) -> dict[str, Any]:
    """Call Ollama and return the parsed JSON object the model produced.

    Uses Ollama's `format=json` mode so the model is forced to emit JSON.
    """
    settings = get_settings()
    payload: dict[str, Any] = {
        "model": settings.ollama_model,
        "prompt": prompt,
        "stream": False,
        "format": "json",
        "options": {"temperature": temperature},
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

    try:
        return json.loads(raw)
    except json.JSONDecodeError as e:
        raise OllamaError(f"Ollama did not return valid JSON: {raw[:300]}") from e


def is_available() -> bool:
    settings = get_settings()
    url = f"{settings.ollama_host.rstrip('/')}/api/tags"
    try:
        with httpx.Client(timeout=2.0) as client:
            resp = client.get(url)
            return resp.status_code == 200
    except httpx.HTTPError:
        return False
