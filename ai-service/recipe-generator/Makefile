.PHONY: install ingest stats dev clean

install:
	pip install -e ".[dev]"

# Ingest every .json file in ./recipes/ into the ChromaDB collection.
# Add new files there and re-run this command — already-ingested recipes
# are upserted (no duplicates).
ingest:
	python -m recipe_generator.cli.ingest run

stats:
	python -m recipe_generator.cli.ingest stats

dev:
	uvicorn recipe_generator.main:app --reload --host 0.0.0.0 --port 8003

clean:
	rm -rf .venv .pytest_cache __pycache__ */__pycache__ */*/__pycache__
