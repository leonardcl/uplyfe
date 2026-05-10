.PHONY: install test lint demo demo-no-llm api seed clean

install:
	pip install -e ".[dev]"

test:
	pytest

lint:
	ruff check app tests

seed:
	python -m app.cli.ingest seed

demo:
	python -m app.cli.demo --input data/samples/manual_input.json

demo-no-llm:
	python -m app.cli.demo --input data/samples/manual_input.json --no-llm

api:
	uvicorn app.main:app --reload

clean:
	rm -rf .pytest_cache .ruff_cache .mypy_cache __pycache__ */__pycache__ */*/__pycache__
