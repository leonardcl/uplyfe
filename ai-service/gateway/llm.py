"""LLM client for the Uplyfe AI gateway.

Routes to Ollama (local) or OpenRouter (cloud) based on LLM_PROVIDER in .env.
  LLM_PROVIDER=ollama      → local Ollama /api/generate
  LLM_PROVIDER=openrouter  → OpenRouter chat completions
"""
from __future__ import annotations

import json
import re
from typing import Any

import httpx

from config import get_settings


class OllamaError(RuntimeError):
    pass


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
    settings = get_settings()
    if settings.llm_provider == "openrouter":
        return _generate_json_openrouter(prompt, system=system, temperature=temperature,
                                         model=model, num_predict=num_predict, settings=settings)
    return _generate_json_ollama(prompt, system=system, temperature=temperature,
                                 model=model, num_predict=num_predict, settings=settings)


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
    url = f"{settings.ollama_host.rstrip('/')}/api/tags"
    try:
        with httpx.Client(timeout=2.0) as c:
            return c.get(url).status_code == 200
    except httpx.HTTPError:
        return False


# ---------- Ollama ----------

def _generate_json_ollama(prompt, *, system, temperature, model, num_predict, settings) -> dict:
    model_id = model or settings.ollama_model
    url = f"{settings.ollama_host.rstrip('/')}/api/generate"
    payload: dict[str, Any] = {
        "model": model_id,
        "prompt": prompt,
        "stream": False,
        "format": "json",
        "options": {"temperature": temperature, "num_predict": num_predict},
    }
    if system:
        payload["system"] = system

    try:
        with httpx.Client(timeout=settings.ollama_timeout_seconds) as c:
            resp = c.post(url, json=payload)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPError as e:
        raise LLMError(f"Ollama request failed: {e}") from e

    raw = data.get("response", "").strip()
    if not raw:
        raise LLMError("Ollama returned empty response.")
    return _parse_json(raw)


# ---------- OpenRouter ----------

def _generate_json_openrouter(prompt, *, system, temperature, model, num_predict, settings) -> dict:
    model_id = model or settings.openrouter_model
    messages: list[dict] = []
    if system:
        messages.append({"role": "system", "content": system})
    messages.append({"role": "user", "content":
                     prompt + "\n\nIMPORTANT: Reply with valid JSON only. No markdown, no prose outside the JSON."})

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
        with httpx.Client(timeout=120) as c:
            resp = c.post(_OPENROUTER_URL, json=payload, headers=headers)
            resp.raise_for_status()
            data = resp.json()
    except httpx.HTTPError as e:
        raise LLMError(f"OpenRouter request failed: {e}") from e

    raw = (data.get("choices", [{}])[0].get("message", {}).get("content") or "").strip()
    if not raw:
        raise LLMError("OpenRouter returned empty content.")
    return _parse_json(raw)


# ---------- shared ----------

def _parse_json(raw: str) -> dict:
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
    raise LLMError(f"LLM did not return valid JSON: {raw[:300]}")
