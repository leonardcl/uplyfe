"""LLM client for health-checkup.

Routes to Ollama (local) or OpenRouter (cloud) based on LLM_PROVIDER in .env.
  LLM_PROVIDER=ollama      → local Ollama /api/generate
  LLM_PROVIDER=openrouter  → OpenRouter chat completions

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
    """Unified LLM client — routes to Ollama or OpenRouter based on settings.llm_provider."""

    def __init__(self, settings: Settings):
        self.settings = settings
        self.provider = settings.llm_provider
        self.timeout = settings.llm_timeout_seconds
        self.temperature = settings.llm_temperature

    def is_available(self) -> bool:
        if self.provider == "openrouter":
            try:
                with httpx.Client(timeout=5.0) as c:
                    r = c.get("https://openrouter.ai/api/v1/models",
                              headers={"Authorization": f"Bearer {self.settings.openrouter_api_key}"})
                    return r.status_code == 200
            except Exception:
                return False
        # Ollama
        try:
            url = f"{self.settings.ollama_base_url.rstrip('/')}/api/tags"
            with httpx.Client(timeout=2.0) as c:
                return c.get(url).status_code == 200
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
        if self.provider == "openrouter":
            return self._generate_openrouter(
                prompt, system=system, format_json=format_json,
                max_tokens=max_tokens, stop=stop,
            )
        return self._generate_ollama(
            prompt, system=system, format_json=format_json,
            max_tokens=max_tokens, stop=stop,
            repeat_penalty=repeat_penalty, repeat_last_n=repeat_last_n, num_ctx=num_ctx,
        )

    # ---------- Ollama ----------

    def _generate_ollama(
        self, prompt: str, *, system, format_json, max_tokens, stop,
        repeat_penalty, repeat_last_n, num_ctx,
    ) -> str:
        url = f"{self.settings.ollama_base_url.rstrip('/')}/api/generate"
        payload: dict = {
            "model": self.settings.ollama_model,
            "prompt": prompt,
            "stream": False,
            "options": {
                "temperature": self.temperature,
                "num_predict": max_tokens or 1500,
                "repeat_penalty": repeat_penalty,
                "repeat_last_n": repeat_last_n,
                "num_ctx": num_ctx,
            },
        }
        if system:
            payload["system"] = system
        if format_json:
            payload["format"] = "json"
        if stop:
            payload["options"]["stop"] = stop

        try:
            with httpx.Client(timeout=self.timeout) as c:
                r = c.post(url, json=payload)
                r.raise_for_status()
                raw = (r.json().get("response") or "").strip()
        except (httpx.HTTPError, ValueError) as e:
            raise LLMUnavailableError(f"Ollama request failed: {e}") from e

        if not raw:
            raise LLMUnavailableError("Ollama returned empty response.")

        if stop:
            for tok in stop:
                idx = raw.find(tok)
                if idx != -1:
                    raw = raw[:idx]

        return raw.strip()

    # ---------- OpenRouter ----------

    def _generate_openrouter(
        self, prompt: str, *, system, format_json, max_tokens, stop,
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
            "model": self.settings.openrouter_model,
            "messages": messages,
            "temperature": self.temperature,
            "max_tokens": max_tokens or 4096,
        }
        headers = {
            "Authorization": f"Bearer {self.settings.openrouter_api_key}",
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

        if stop:
            for tok in stop:
                idx = raw.find(tok)
                if idx != -1:
                    raw = raw[:idx]

        return raw.strip()
