"""Environment-driven settings."""
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

    # OpenRouter
    openrouter_api_key: str = Field(default="")
    openrouter_model: str = Field(default="google/gemma-4-26b-a4b-it")

    # Ollama (kept for backward compat, no longer used for generation)
    ollama_base_url: str = Field(default="http://localhost:11434")
    ollama_model: str = Field(default="gemma4:26b")
    llm_timeout_seconds: float = Field(default=120.0)
    llm_temperature: float = Field(default=0.2)

    # Vector store
    chroma_dir: str = Field(default=str(PROJECT_ROOT / "data" / "chroma"))
    embedding_model: str = Field(default="sentence-transformers/all-MiniLM-L6-v2")

    @field_validator("chroma_dir")
    @classmethod
    def _anchor_chroma_dir(cls, v: str) -> str:
        # Relative paths in .env (e.g. "./data/chroma") would otherwise resolve
        # against process cwd — which differs between the seed CLI and the
        # gateway, leading to silently divergent stores.
        p = Path(v)
        if not p.is_absolute():
            p = (PROJECT_ROOT / p).resolve()
        return str(p)

    # API
    api_host: str = Field(default="0.0.0.0")
    api_port: int = Field(default=8000)

    # Safety
    always_include_disclaimer: bool = Field(default=True)
    emergency_thresholds_enabled: bool = Field(default=True)

    # Logging
    log_level: str = Field(default="INFO")


@lru_cache(maxsize=1)
def get_settings() -> Settings:
    return Settings()
