"""LLM client for the exercise generator.

Routes to Ollama (local) or OpenRouter (cloud) based on LLM_PROVIDER in .env.
  LLM_PROVIDER=ollama      → local Ollama
  LLM_PROVIDER=openrouter  → OpenRouter cloud

Embeddings always use local Ollama regardless of LLM_PROVIDER (ChromaDB
vector search requires a stable local embedding model).
"""
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
    """Embeddings always run locally via Ollama (ChromaDB vector search)."""
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
    if settings.llm_provider == "openrouter":
        return _generate_openrouter(prompt, model=model, format_json=format_json, settings=settings)
    return _generate_ollama(prompt, model=model, format_json=format_json, settings=settings)


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
    raise LLMUnavailableError(f"LLM did not return valid JSON: {raw[:300]}")


def is_available() -> bool:
    settings = get_settings()
    if settings.llm_provider == "openrouter":
        try:
            with httpx.Client(timeout=5.0) as c:
                r = c.get("https://openrouter.ai/api/v1/models",
                          headers={"Authorization": f"Bearer {settings.openrouter_api_key}"})
                return r.status_code == 200
        except Exception:
            return False
    # Ollama
    try:
        with httpx.Client(timeout=3.0) as c:
            r = c.get(f"{settings.ollama_base_url.rstrip('/')}/api/tags")
            return r.status_code == 200
    except Exception:
        return False


# ---------- Ollama ----------

def _generate_ollama(prompt: str, *, model, format_json: bool, settings) -> str:
    model_id = model or settings.llm_model
    url = f"{settings.ollama_base_url.rstrip('/')}/api/generate"
    payload = {
        "model": model_id,
        "prompt": prompt,
        "stream": False,
        "options": {"temperature": 0.5, "num_predict": 4096},
    }
    if format_json:
        payload["format"] = "json"

    try:
        with httpx.Client(timeout=settings.llm_timeout_seconds) as c:
            r = c.post(url, json=payload)
            r.raise_for_status()
            return (r.json().get("response") or "").strip()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(f"Ollama generate failed: {e}") from e


# ---------- OpenRouter ----------

def _generate_openrouter(prompt: str, *, model, format_json: bool, settings) -> str:
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
            return (r.json().get("choices", [{}])[0].get("message", {}).get("content") or "").strip()
    except (httpx.HTTPError, ValueError) as e:
        raise LLMUnavailableError(f"OpenRouter generate failed: {e}") from e
