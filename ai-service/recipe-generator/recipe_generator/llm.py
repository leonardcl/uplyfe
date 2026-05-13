"""Thin Ollama client — generate text + JSON.

Replaces the hardcoded `requests.post` to a remote IP in FixMakan with an
httpx call that respects `OLLAMA_BASE_URL` (defaults to localhost).
"""
from __future__ import annotations

import json
from typing import Optional

import httpx

from recipe_generator.config import get_settings


class LLMUnavailableError(RuntimeError):
    """Ollama unreachable or returned an unrecoverable error."""


def generate(prompt: str, *, model: Optional[str] = None, format_json: bool = False) -> str:
    settings = get_settings()
    payload: dict = {
        "model": model or settings.llm_model,
        "prompt": prompt,
        "stream": False,
        # Disable "thinking" so the model spends its budget on the actual
        # response, not hidden reasoning we'd discard.
        "think": False,
    }
    if format_json:
        payload["format"] = "json"
    try:
        with httpx.Client(timeout=settings.llm_timeout_seconds) as c:
            r = c.post(
                f"{settings.ollama_base_url.rstrip('/')}/api/generate",
                json=payload,
            )
            r.raise_for_status()
            body = r.json()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(f"Ollama generate failed: {e}") from e
    return (body.get("response") or "").strip()


def generate_json(prompt: str, *, model: Optional[str] = None) -> dict:
    """Generate with `format=json` and parse. Robust to the model wrapping
    its JSON in markdown fences or trailing prose — falls back to scraping
    the first `{...}` block."""
    raw = generate(prompt, model=model, format_json=True)
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        start, end = raw.find("{"), raw.rfind("}")
        if start == -1 or end == -1:
            raise
        return json.loads(raw[start : end + 1])


def is_available() -> bool:
    settings = get_settings()
    try:
        with httpx.Client(timeout=3.0) as c:
            r = c.get(f"{settings.ollama_base_url.rstrip('/')}/api/tags")
            return r.status_code == 200
    except Exception:
        return False
