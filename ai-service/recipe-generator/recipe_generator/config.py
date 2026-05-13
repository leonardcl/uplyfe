"""Environment-driven settings for the recipe generator.

Mirrors the pattern used by exercise-routine-generator and
health-checkup-extraction: all paths and URLs are env-overridable, and any
relative path resolves against the package root so the system works
regardless of the calling cwd.
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

    # LLM (Ollama)
    ollama_base_url: str = Field(default="http://localhost:11434")
    llm_model: str = Field(default="gemma4:26b")
    llm_timeout_seconds: float = Field(default=180.0)

    # Embeddings — sentence-transformers, runs in-process.
    embed_model: str = Field(default="sentence-transformers/all-MiniLM-L6-v2")

    # Vector store
    chroma_dir: str = Field(default=str(PROJECT_ROOT / "data" / "chroma"))
    recipes_collection: str = Field(default="recipes")

    # Source data — JSON files dropped here by the user.
    recipes_dir: str = Field(default=str(PROJECT_ROOT / "recipes"))

    # Retrieval
    top_k_recipes: int = Field(default=50)

    # API host / port (standalone uvicorn usage)
    api_host: str = Field(default="0.0.0.0")
    api_port: int = Field(default=8003)

    @field_validator("chroma_dir", "recipes_dir")
    @classmethod
    def _anchor_paths(cls, v: str) -> str:
        p = Path(v)
        if not p.is_absolute():
            p = (PROJECT_ROOT / p).resolve()
        return str(p)


@lru_cache(maxsize=1)
def get_settings() -> Settings:
    return Settings()
