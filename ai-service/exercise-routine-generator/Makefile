.PHONY: install ingest-exercises ingest-pdf stats dev clean

install:
	pip install -e ".[dev]"

ingest-exercises:
	python -m exercise_routine_generator.cli.ingest exercises

ingest-pdf:
	@echo "Usage: make ingest-pdf FILE='rag_sources/<book>.pdf'"
	@test -n "$(FILE)" && python -m exercise_routine_generator.cli.ingest pdf "$(FILE)"

stats:
	python -m exercise_routine_generator.cli.ingest stats

dev:
	uvicorn exercise_routine_generator.main:app --reload --host 0.0.0.0 --port 8002

clean:
	rm -rf .venv .pytest_cache __pycache__ */__pycache__ */*/__pycache__
