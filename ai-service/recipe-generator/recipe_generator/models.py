"""Pydantic schemas for the public API.

Output shape mirrors the existing recipe.blade.php frontend's expectations
(see `_archived/Food-public-dir/data/example.json`): each day has
breakfast / lunch / dinner / snack with a rich `meal` object the UI can
render directly.
"""
from __future__ import annotations

from typing import Literal, Optional

from pydantic import BaseModel, Field


# --- Request -----------------------------------------------------------


class MealPlanRequest(BaseModel):
    """The same fields the existing gateway stub accepts, kept for
    backward compatibility, plus optional free-text controls."""
    target_calories: int = Field(2000, ge=800, le=5000)
    servings: int = Field(1, ge=1, le=10)
    diet: Literal[
        "none", "vegetarian", "vegan", "pescatarian",
        "halal", "kosher", "keto", "low_carb",
    ] = "none"
    allergies: list[str] = Field(default_factory=list)
    cuisine_preferences: list[str] = Field(default_factory=list)
    notes: Optional[str] = None
    query: Optional[str] = Field(
        default=None,
        description="Free-text retrieval query. Defaults to a generic "
                    "'balanced healthy meals' if not provided.",
    )
    days: int = Field(1, ge=1, le=7, description="Generate 1 day or up to a full week.")


# --- Response (shared) -------------------------------------------------


class MealTag(BaseModel):
    text: str
    color: str = "green"


class MealIngredient(BaseModel):
    name: str
    quantity: Optional[str] = None


class Meal(BaseModel):
    title: str
    subtitle: str = ""                   # "Breakfast" / "Lunch" / …
    badge: str = "Balanced Meal"
    calories: str = "0"
    protein: str = "0"
    carbs: str = "0"
    description: str = ""
    benefits: list[str] = Field(default_factory=list)
    tags: list[MealTag] = Field(default_factory=list)
    ingredients: list[MealIngredient] = Field(default_factory=list)
    instructions: list[str] = Field(default_factory=list)
    tips: str = ""


class DayPlan(BaseModel):
    title: str = ""
    breakfast: Optional[Meal] = None
    lunch: Optional[Meal] = None
    dinner: Optional[Meal] = None
    snack: Optional[Meal] = None


# --- Top-level response ------------------------------------------------


class MealPlanResponse(BaseModel):
    """Either a single day (when `days == 1`) or a `monday..sunday` map."""
    summary: str = ""
    plan: dict[str, DayPlan] = Field(
        default_factory=dict,
        description=(
            "Keyed by day name ('monday'…'sunday') OR by 'day_1'…'day_N' for "
            "fractional weeks. The frontend uses the keys directly."
        ),
    )
    sources_used: int = 0
    disclaimer: str = (
        "Recipes are AI-generated suggestions, not medical or nutritional advice. "
        "Always check ingredients against your own allergies and dietary needs, and "
        "consult a registered dietitian for personalised guidance."
    )
