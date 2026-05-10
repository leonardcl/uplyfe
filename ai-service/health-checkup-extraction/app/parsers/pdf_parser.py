"""PDF text extraction.

Tries PyMuPDF first (fast, robust on digital PDFs and most lab reports), then
falls back to pdfplumber (better at preserving table structure on some
templates). Raises a clear error if both yield empty text — the caller should
fall through to OCR.
"""
from __future__ import annotations

from pathlib import Path


PAGE_DELIMITER = "\f"  # form-feed; downstream chunker splits on this


def _extract_pymupdf(path: Path) -> str:
    try:
        import fitz  # PyMuPDF
    except ImportError as e:  # pragma: no cover
        raise RuntimeError("PyMuPDF (`fitz`) not installed.") from e
    parts: list[str] = []
    with fitz.open(path) as doc:
        for page in doc:
            parts.append(page.get_text("text"))
    # Pages joined with form-feed so the page-chunker can split cleanly.
    return PAGE_DELIMITER.join(parts).strip()


def _extract_pdfplumber(path: Path) -> str:
    try:
        import pdfplumber
    except ImportError as e:  # pragma: no cover
        raise RuntimeError("pdfplumber not installed.") from e
    pages_text: list[str] = []
    with pdfplumber.open(path) as pdf:
        for page in pdf.pages:
            page_parts: list[str] = []
            t = page.extract_text() or ""
            page_parts.append(t)
            # Tables often hold the lab values; render them as TSV-ish text.
            for tbl in page.extract_tables() or []:
                for row in tbl:
                    page_parts.append("\t".join((c or "").strip() for c in row))
            pages_text.append("\n".join(page_parts))
    return PAGE_DELIMITER.join(pages_text).strip()


class EmptyPDFTextError(RuntimeError):
    """Both PDF text extractors returned no usable text — likely a scan."""


def extract_text_from_pdf(path: str | Path) -> str:
    p = Path(path)
    if not p.exists():
        raise FileNotFoundError(p)

    try:
        text = _extract_pymupdf(p)
    except Exception:
        text = ""
    if len(text) < 40:
        try:
            text = _extract_pdfplumber(p)
        except Exception:
            text = text  # keep what we have

    if len(text) < 40:
        raise EmptyPDFTextError(
            f"Could not extract usable text from {p.name}. "
            "The PDF is likely a scan — fall back to OCR."
        )
    return text
