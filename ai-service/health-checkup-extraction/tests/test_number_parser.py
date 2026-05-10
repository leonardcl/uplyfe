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
    # Multi-digit integer part with 3-digit trailing → thousand-sep.
    ("245.000", 245000.0),
    ("245,000", 245000.0),
    ("12.345", 12345.0),
    ("1.234.567", 1234567.0),
    ("1,234,567", 1234567.0),
])
def test_thousand_separators_multi_digit_integer(raw, expected):
    assert _parse_number(raw) == expected


@pytest.mark.parametrize("raw, expected", [
    # Single-digit integer + 3-digit trailing → 3-decimal-place value, NOT
    # thousand-separator. Real world: TSH 4.200 mIU/L, hematocrit 0.400, etc.
    ("0.400", 0.4),
    ("4.200", 4.2),
    ("8.500", 8.5),
    ("9.999", 9.999),
])
def test_single_digit_int_with_3_decimal_is_decimal(raw, expected):
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
    # Single-digit int + 3-digit trailing → decimal interpretation, not
    # thousand-sep. "8.500" reads as 8.5 (3 decimal places).
    assert _parse_number(" 8.500 ") == 8.5
    # Multi-digit int + 3-digit trailing remains thousand-sep.
    assert _parse_number(" 245.000 ") == 245000.0


def test_empty_raises():
    with pytest.raises(ValueError):
        _parse_number("")
    with pytest.raises(ValueError):
        _parse_number("   ")


def test_single_digit_int_with_3_decimal_real_world_cases():
    """Single-digit-int + 3-decimal cases that appear in real lab reports —
    must be parsed as decimals, not thousand-separators. These were producing
    "TSH 4200" and "Hematocrit 400%" in the live system before this fix."""
    # TSH 3-decimal precision (mIU/L typical 0.1–10):
    assert _parse_number("4.200") == 4.2
    assert _parse_number("0.275") == 0.275
    # Hematocrit fraction (0.0–0.6):
    assert _parse_number("0.400") == 0.4
    assert _parse_number("0.338") == 0.338
