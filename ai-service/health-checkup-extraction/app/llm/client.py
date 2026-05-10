"""Local-first LLM client targeting Ollama.

Why Ollama? The user's spec asks for a local model (gemma family). Ollama is
the simplest stable local-LLM runner; pull the model once, then HTTP it.

The client is intentionally minimal:
  * .generate(prompt) → str
  * format_json=True nudges the model to emit JSON when supported (Ollama exposes
    a "format": "json" field that constrains decoding).
  * Never raises on connect failure during normal use — instead we raise
    LLMUnavailableError so callers can fall back to deterministic-only mode.
"""
from __future__ import annotations

from typing import Optional

import httpx
from tenacity import retry, retry_if_exception_type, stop_after_attempt, wait_exponential

from app.config import Settings


class LLMUnavailableError(RuntimeError):
    """The LLM is unreachable or returned an unrecoverable error."""


class OllamaClient:
    def __init__(self, settings: Settings):
        self.base_url = settings.ollama_base_url.rstrip("/")
        self.model = settings.ollama_model
        self.timeout = settings.llm_timeout_seconds
        self.temperature = settings.llm_temperature

    def is_available(self) -> bool:
        try:
            with httpx.Client(timeout=3.0) as c:
                r = c.get(f"{self.base_url}/api/tags")
                return r.status_code == 200
        except Exception:
            return False

    @retry(
        retry=retry_if_exception_type((httpx.ConnectError, httpx.ReadTimeout)),
        wait=wait_exponential(multiplier=0.5, min=0.5, max=4),
        stop=stop_after_attempt(3),
        reraise=True,
    )
    def _post(self, payload: dict) -> dict:
        with httpx.Client(timeout=self.timeout) as c:
            r = c.post(f"{self.base_url}/api/generate", json=payload)
            r.raise_for_status()
            return r.json()

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
        """Generate text from Ollama with anti-loop defaults.

        Why these defaults:
          * `repeat_penalty=1.18` discourages the bullet/section loop that gemma-class
            models fall into without it.
          * `repeat_last_n=256` extends the look-back window so it catches paragraph-
            level repetition, not just token-level.
          * `num_ctx=4096` is enough headroom for findings + retrieved context + output
            without trashing throughput on a 26B-class model.
          * A `stop` token list lets the caller hard-cut on a sentinel. The synthesis
            prompt uses `END_OF_REPORT`.
        """
        options: dict = {
            "temperature": self.temperature,
            "repeat_penalty": repeat_penalty,
            "repeat_last_n": repeat_last_n,
            "num_ctx": num_ctx,
        }
        if max_tokens:
            options["num_predict"] = max_tokens
        if stop:
            options["stop"] = stop

        payload: dict = {
            "model": self.model,
            "prompt": prompt,
            "stream": False,
            # Disable reasoning models' hidden thinking phase. This pipeline never
            # needs the model to "decide" — it only re-states deterministic findings.
            # Without this, thinking models burn the num_predict budget on hidden
            # reasoning tokens and emit an empty `response` field.
            "think": False,
            "options": options,
        }
        if system:
            payload["system"] = system
        if format_json:
            payload["format"] = "json"

        try:
            data = self._post(payload)
        except (httpx.ConnectError, httpx.ReadTimeout, httpx.HTTPStatusError) as e:
            raise LLMUnavailableError(
                f"Ollama unreachable at {self.base_url} for model {self.model!r}: {e}"
            ) from e

        return (data.get("response") or "").strip()
