"""Split long PDF text into manageable chunks.

Why: the LLM extractor truncates input at a fixed character limit, so a
30-page hospital report would silently lose values that fall past the cutoff.
Splitting on page boundaries (form-feed `\\f`, which PyMuPDF emits between
pages) preserves the natural structure and lets each page be processed in
isolation.

Pure function, no I/O, no LLM. Empty/whitespace chunks are dropped so
downstream code never has to special-case them.
"""
from __future__ import annotations

import re


PAGE_DELIMITER = "\f"


def chunk_text_by_page(text: str, max_chunk_chars: int = 24000) -> list[str]:
    """Split `text` into chunks, each <= `max_chunk_chars`.

    Strategy:
      1. If the input contains form-feed (`\\f`), split on it — that's PyMuPDF's
         page break. Empty/whitespace pages are dropped.
      2. If any single page is larger than the limit, paragraph-split it further.
      3. If the input has no form-feed and is small enough, return [text] (the
         common case for short snippets and manual JSON paths).
    """
    if not text or not text.strip():
        return []

    if PAGE_DELIMITER in text:
        pages = [p.strip() for p in text.split(PAGE_DELIMITER)]
        pages = [p for p in pages if p]
    else:
        pages = [text.strip()]

    out: list[str] = []
    for page in pages:
        if len(page) <= max_chunk_chars:
            out.append(page)
        else:
            out.extend(_split_by_paragraph(page, max_chunk_chars))
    return out


def _split_by_paragraph(text: str, max_chars: int) -> list[str]:
    """Greedy paragraph-aware splitter for very long single pages."""
    paragraphs = re.split(r"\n\s*\n", text)
    chunks: list[str] = []
    buf = ""
    for p in paragraphs:
        p = p.strip()
        if not p:
            continue
        # If a single paragraph is longer than max_chars, hard-split on lines.
        if len(p) > max_chars:
            for line in p.splitlines():
                if len(buf) + len(line) + 1 <= max_chars:
                    buf = (buf + "\n" + line) if buf else line
                else:
                    if buf:
                        chunks.append(buf)
                    buf = line
            continue
        if len(buf) + len(p) + 2 <= max_chars:
            buf = (buf + "\n\n" + p) if buf else p
        else:
            if buf:
                chunks.append(buf)
            buf = p
    if buf:
        chunks.append(buf)
    return chunks
