"""Input lab panel + per-value record + validation issue type."""
from __future__ import annotations

from datetime import date
from typing import Literal, Optional

from pydantic import BaseModel, Field, model_validator

from .biomarkers import Biomarker


Sex = Literal["male", "female", "other", "unknown"]


class LabValue(BaseModel):
    """A single biomarker reading."""

    biomarker: Biomarker
    value: float
    unit: str
    reference_low: Optional[float] = None
    reference_high: Optional[float] = None
    note: Optional[str] = None
    # `original_*` is preserved when the normalizer converts units, so we never
    # silently lose the report's stated value.
    original_value: Optional[float] = None
    original_unit: Optional[str] = None


class LabPanel(BaseModel):
    """The validated input to the pipeline.

    Either upload a PDF/image (parsers will produce this) or POST it directly.
    """

    age: int = Field(ge=0, le=130)
    sex: Sex = "unknown"
    height_cm: Optional[float] = Field(default=None, ge=30, le=260)
    weight_kg: Optional[float] = Field(default=None, ge=2, le=400)
    waist_cm: Optional[float] = Field(default=None, ge=30, le=300)
    collection_date: Optional[date] = None
    fasting: Optional[bool] = None
    pregnant: Optional[bool] = None
    smoker: Optional[bool] = None

    values: list[LabValue] = Field(default_factory=list)

    # --- helpers ---
    def get(self, biomarker: Biomarker) -> Optional[LabValue]:
        for v in self.values:
            if v.biomarker == biomarker:
                return v
        return None

    def value_of(self, biomarker: Biomarker) -> Optional[float]:
        v = self.get(biomarker)
        return v.value if v else None

    @model_validator(mode="after")
    def _no_duplicate_biomarkers(self) -> "LabPanel":
        seen: set[Biomarker] = set()
        for v in self.values:
            if v.biomarker in seen:
                raise ValueError(
                    f"Duplicate biomarker in panel: {v.biomarker.value}. "
                    "If both fasting and random glucose were measured, use the distinct biomarker keys."
                )
            seen.add(v.biomarker)
        return self


class ValidationIssue(BaseModel):
    """Anything the validator wants to surface before rules run.

    Severity here is *data quality* severity, not clinical severity.
    """

    biomarker: Optional[Biomarker] = None
    kind: Literal["unit", "range", "missing", "duplicate", "implausible", "info"]
    message: str
