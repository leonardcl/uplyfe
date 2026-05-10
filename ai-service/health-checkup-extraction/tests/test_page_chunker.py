from __future__ import annotations

from app.parsers.page_chunker import chunk_text_by_page


def test_empty_input_returns_empty_list():
    assert chunk_text_by_page("") == []
    assert chunk_text_by_page("   \n\n  ") == []


def test_short_text_returned_unchanged():
    text = "Glucose 110 mg/dL\nCholesterol 230 mg/dL"
    assert chunk_text_by_page(text) == [text]


def test_form_feed_splits_into_pages():
    text = "page1 line\nmore\f\fpage2 line\f"
    chunks = chunk_text_by_page(text)
    assert len(chunks) == 2
    assert chunks[0].startswith("page1")
    assert chunks[1].startswith("page2")


def test_form_feed_drops_empty_pages():
    text = "real page\f\f\f"
    chunks = chunk_text_by_page(text)
    assert chunks == ["real page"]


def test_long_single_page_is_paragraph_split():
    p1 = "Para one " * 200    # ~1800 chars
    p2 = "Para two " * 200
    p3 = "Para three " * 200
    text = "\n\n".join([p1, p2, p3])  # ~5500 chars
    chunks = chunk_text_by_page(text, max_chunk_chars=2500)
    assert len(chunks) >= 2
    for c in chunks:
        assert len(c) <= 2500


def test_long_paragraph_hard_split_on_lines():
    text = "\n".join(["x" * 100 for _ in range(50)])  # 50 lines, ~5050 chars total, but as one paragraph
    chunks = chunk_text_by_page(text, max_chunk_chars=600)
    for c in chunks:
        assert len(c) <= 600


def test_under_limit_with_form_feed_still_splits():
    """Even small documents get split on form-feed when present, because page
    structure is meaningful for downstream extraction."""
    text = "abc\fdef"
    chunks = chunk_text_by_page(text, max_chunk_chars=10000)
    assert chunks == ["abc", "def"]
