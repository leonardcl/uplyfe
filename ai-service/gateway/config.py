from functools import lru_cache
from pathlib import Path

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", extra="ignore")

    gateway_host: str = "0.0.0.0"
    gateway_port: int = 8000

    ai_service_key: str = "change-me-please"

    # OpenRouter — used by the chat module
    openrouter_api_key: str = ""
    openrouter_model: str = "google/gemma-2-27b-it"

    # Ollama — still used by exercise / recipe / health-checkup modules
    ollama_host: str = "http://localhost:11434"
    ollama_model: str = "gemma2:9b"
    ollama_timeout_seconds: int = 120

    health_checkup_path: str = "../health-checkup-extraction"
    exercise_routine_path: str = "../exercise-routine-generator"

    @property
    def health_checkup_abs_path(self) -> Path:
        p = Path(self.health_checkup_path)
        if not p.is_absolute():
            p = (Path(__file__).parent / p).resolve()
        return p

    @property
    def exercise_routine_abs_path(self) -> Path:
        p = Path(self.exercise_routine_path)
        if not p.is_absolute():
            p = (Path(__file__).parent / p).resolve()
        return p


@lru_cache
def get_settings() -> Settings:
    return Settings()
