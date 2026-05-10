"""Tests for the locale-aware number parser.

Indonesian and US conventions for separators are inverted (Indonesian: period =
thousand-sep, comma = decimal; US: comma = thousand-sep, period = decimal).
The parser disambiguates by inspecting the digit count after the LAST
separator: exactly 3 → thousand-sep, otherwise → decimal.
"""
from __future__ import annotations

import pytest

from app.parsers.regex_extractor import _parse_number


@pytest.mark.parametrize("raw, expected", [
    ("110", 110.0),
    ("5.4", 5.4),
    ("13,8", 13.8),
    ("5,40", 5.4),
    ("1.05", 1.05),
    ("0.78", 0.78),
])
def test_simple_numbers_unchanged(raw, expected):
    assert _parse_number(raw) == expected


@pytest.mark.parametrize("raw, expected", [
    ("8.500", 8500.0),
    ("245.000", 245000.0),
    ("8,500", 8500.0),
    ("245,000", 245000.0),
    ("1.234.567", 1234567.0),
    ("1,234,567", 1234567.0),
])
def test_thousand_separators(raw, expected):
    assert _parse_number(raw) == expected


@pytest.mark.parametrize("raw, expected", [
    ("1,234.56", 1234.56),    # US: thousand , then decimal .
    ("1.234,56", 1234.56),    # ID/EU: thousand . then decimal ,
    ("12.345.67", 12345.67),  # multi sep, last is decimal (2-digit trailing)
])
def test_mixed_thousand_and_decimal(raw, expected):
    assert _parse_number(raw) == expected


def test_negative_values():
    assert _parse_number("-5.4") == -5.4
    assert _parse_number("-1,234.56") == -1234.56


def test_whitespace_tolerance():
    assert _parse_number("  110  ") == 110.0
    assert _parse_number(" 8.500 ") == 8500.0


def test_empty_raises():
    with pytest.raises(ValueError):
        _parse_number("")
    with pytest.raises(ValueError):
        _parse_number("   ")


def test_three_decimal_places_treated_as_thousand_sep():
    """Pragmatic: lab values almost never have 3 decimal places, so
    `1.500` is decoded as 1500 (thousand-sep) rather than 1.5 (decimal).
    Documenting this behavior explicitly."""
    # This trade-off would be wrong for `1.500 mg/dL creatinine` — but real
    # creatinine values are reported to 2 decimals, never 3.
    assert _parse_number("1.500") == 1500.0
