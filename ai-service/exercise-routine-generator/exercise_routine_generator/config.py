"""Environment-driven settings.

All hardcoded paths and IPs from the original CLI script (`exercise7.py`)
are replaced with env-overridable settings that resolve relative to the
project root, so the package works the same on any machine.
"""
from __future__ import annotations

from functools import lru_cache
from pathlib import Path

from pydantic import Field, field_validator
from pydantic_settings import BaseSettings, SettingsConfigDict


PROJECT_ROOT = Path(__file__).resolve().parent.parent


class Settings(BaseSettings):
    model_config = SettingsConfigDict(
        env_file=str(PROJECT_ROOT / ".env"),
        env_file_encoding="utf-8",
        extra="ignore",
    )

    # LLM provider — "ollama" (local) or "openrouter" (cloud)
    llm_provider: str = Field(default="ollama")

    # OpenRouter
    openrouter_api_key: str = Field(default="")
    openrouter_model: str = Field(default="google/gemma-4-26b-a4b-it")

    # Ollama
    ollama_base_url: str = Field(default="http://localhost:11434")
    llm_model: str = Field(default="gemma4:26b")
    llm_model_small: str = Field(default="gemma4:26b")
    embed_model: str = Field(default="embeddinggemma")
    llm_timeout_seconds: float = Field(default=180.0)

    # ChromaDB
    chroma_dir: str = Field(default=str(PROJECT_ROOT / "data" / "chroma"))
    exercise_collection: str = Field(default="exercises")
    pdf_collection: str = Field(default="pdf_books")

    # Retrieval
    top_k_exercise: int = Field(default=20)
    top_k_pdf: int = Field(default=5)

    # API (for standalone uvicorn use)
    api_host: str = Field(default="0.0.0.0")
    api_port: int = Field(default=8002)

    @field_validator("chroma_dir")
    @classmethod
    def _anchor_chroma_dir(cls, v: str) -> str:
        # Relative paths resolve against PROJECT_ROOT so callers running from
        # a different cwd (e.g. the gateway) still find the same store.
        p = Path(v)
        if not p.is_absolute():
            p = (PROJECT_ROOT / p).resolve()
        return str(p)


@lru_cache(maxsize=1)
def get_settings() -> Settings:
    return Settings()
