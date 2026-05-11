"""Thin Ollama client.

Replaces the raw `requests.post` calls in `exercise7.py` with an httpx
client that respects the configured base URL and timeout. The original
code pointed at a remote IP (`192.168.111.132`) — here everything goes
through `OLLAMA_BASE_URL`, defaulting to localhost.
"""
from __future__ import annotations

import json
from typing import Optional

import httpx

from exercise_routine_generator.config import get_settings


class LLMUnavailableError(RuntimeError):
    """Ollama is unreachable or returned an unrecoverable error."""


def _client() -> httpx.Client:
    settings = get_settings()
    return httpx.Client(timeout=settings.llm_timeout_seconds)


def embed(text: str) -> list[float]:
    settings = get_settings()
    try:
        with _client() as c:
            r = c.post(
                f"{settings.ollama_base_url.rstrip('/')}/api/embeddings",
                json={"model": settings.embed_model, "prompt": text},
            )
            r.raise_for_status()
            body = r.json()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(
            f"Ollama embed failed (model={settings.embed_model}): {e}"
        ) from e
    if "embedding" not in body:
        raise LLMUnavailableError(f"Embed response missing 'embedding': {body}")
    return body["embedding"]


def generate(prompt: str, *, model: Optional[str] = None, format_json: bool = False) -> str:
    settings = get_settings()
    chosen = model or settings.llm_model
    payload: dict = {
        "model": chosen,
        "prompt": prompt,
        "stream": False,
        # Disable Ollama "thinking" so the model spends its budget on the
        # actual response, not hidden reasoning that we'd discard.
        "think": False,
    }
    if format_json:
        payload["format"] = "json"
    try:
        with _client() as c:
            r = c.post(
                f"{settings.ollama_base_url.rstrip('/')}/api/generate",
                json=payload,
            )
            r.raise_for_status()
            body = r.json()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(
            f"Ollama generate failed (model={chosen}): {e}"
        ) from e
    return (body.get("response") or "").strip()


def generate_small(prompt: str) -> str:
    """Use the small model when one is configured (faster + cheaper); falls
    back to the default model when the small slot points at the same name."""
    settings = get_settings()
    return generate(prompt, model=settings.llm_model_small)


def generate_json(prompt: str, *, model: Optional[str] = None) -> dict:
    """Call `generate` with `format=json` and parse the result. Robust to
    the model occasionally wrapping the JSON in markdown fences."""
    raw = generate(prompt, model=model, format_json=True)
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        # Scrape the first {...} block as a fallback.
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
