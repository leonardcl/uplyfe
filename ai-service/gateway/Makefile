.PHONY: install dev run test lint clean

install:
	python -m venv .venv
	. .venv/bin/activate && pip install -U pip
	. .venv/bin/activate && pip install -e ".[dev]"
	# Install the existing health-checkup-extraction project into this same venv
	# so the gateway can import its FastAPI app + pull in chromadb / pymupdf /
	# pdfplumber / pytesseract / sentence-transformers / tenacity transitively.
	. .venv/bin/activate && pip install -e ../health-checkup-extraction
	@echo
	@echo "Done. Activate with:  source .venv/bin/activate"
	@echo "Then copy env:        cp .env.example .env"

dev:
	. .venv/bin/activate && uvicorn main:app --reload --host 0.0.0.0 --port 8001

run:
	. .venv/bin/activate && uvicorn main:app --host 0.0.0.0 --port 8001

test:
	. .venv/bin/activate && pytest

lint:
	. .venv/bin/activate && ruff check .

clean:
	rm -rf .venv .pytest_cache *.egg-info build dist
	find . -name __pycache__ -type d -exec rm -rf {} +
