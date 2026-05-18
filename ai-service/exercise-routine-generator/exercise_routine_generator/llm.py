"""LLM client for the exercise generator — OpenRouter for text, Ollama for embeddings."""
from __future__ import annotations

import json
import re
from typing import Optional

import httpx

from exercise_routine_generator.config import get_settings

_OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions"


class LLMUnavailableError(RuntimeError):
    """LLM unreachable or returned an unrecoverable error."""


def _client() -> httpx.Client:
    settings = get_settings()
    return httpx.Client(timeout=settings.llm_timeout_seconds)


def embed(text: str) -> list[float]:
    """Embeddings still run locally via Ollama (ChromaDB vector search)."""
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
        raise LLMUnavailableError(f"Ollama embed failed: {e}") from e
    if "embedding" not in body:
        raise LLMUnavailableError(f"Embed response missing 'embedding': {body}")
    return body["embedding"]


def generate(prompt: str, *, model: Optional[str] = None, format_json: bool = False) -> str:
    settings = get_settings()
    model_id = model or settings.openrouter_model

    content = prompt
    if format_json:
        content += "\n\nIMPORTANT: Reply with valid JSON only. No markdown, no prose outside the JSON."

    payload = {
        "model": model_id,
        "messages": [{"role": "user", "content": content}],
        "temperature": 0.5,
        "max_tokens": 4096,
    }
    headers = {
        "Authorization": f"Bearer {settings.openrouter_api_key}",
        "Content-Type": "application/json",
        "HTTP-Referer": "https://uplyfe.app",
        "X-Title": "Uplyfe AI",
    }
    try:
        with httpx.Client(timeout=120) as c:
            r = c.post(_OPENROUTER_URL, json=payload, headers=headers)
            r.raise_for_status()
            body = r.json()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(f"OpenRouter generate failed: {e}") from e
    return (body.get("choices", [{}])[0].get("message", {}).get("content") or "").strip()


def generate_small(prompt: str) -> str:
    return generate(prompt)


def generate_json(prompt: str, *, model: Optional[str] = None) -> dict:
    raw = generate(prompt, model=model, format_json=True)
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        pass
    match = re.search(r"\{.*\}", raw, re.DOTALL)
    if match:
        try:
            return json.loads(match.group())
        except json.JSONDecodeError:
            pass
    raise LLMUnavailableError(f"OpenRouter did not return valid JSON: {raw[:300]}")


def is_available() -> bool:
    settings = get_settings()
    try:
        with httpx.Client(timeout=5.0) as c:
            r = c.get(
                "https://openrouter.ai/api/v1/models",
                headers={"Authorization": f"Bearer {settings.openrouter_api_key}"},
            )
            return r.status_code == 200
    except Exception:
        return False
