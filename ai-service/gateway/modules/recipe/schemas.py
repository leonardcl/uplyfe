from typing import Literal

from pydantic import BaseModel, Field


class RecipeRequest(BaseModel):
    target_calories: int = Field(2000, ge=1000, le=4000)
    diet: Literal[
        "none", "vegetarian", "vegan", "pescatarian", "halal", "kosher", "keto", "low_carb"
    ] = "none"
    allergies: list[str] = Field(default_factory=list, description="e.g. ['peanuts', 'shellfish']")
    cuisine_preferences: list[str] = Field(default_factory=list, description="e.g. ['indonesian', 'japanese']")
    servings: int = Field(1, ge=1, le=10)
    notes: str | None = Field(default=None, description="Any extra context (e.g. 'no pork', 'budget-friendly').")


class Ingredient(BaseModel):
    name: str
    quantity: str  # "200g" / "1 cup" / "2 cloves"


class Meal(BaseModel):
    name: str  # "breakfast" / "lunch" / "dinner" / "snack"
    title: str
    estimated_calories: int
    prep_minutes: int | None = None
    ingredients: list[Ingredient]
    steps: list[str]
    notes: str | None = None


class DailyMenu(BaseModel):
    summary: str
    total_estimated_calories: int
    meals: list[Meal]
    grocery_list: list[Ingredient] = Field(default_factory=list)
    disclaimer: str = (
        "Recipes are AI-generated suggestions, not medical or nutritional advice. "
        "Always check ingredients against your own allergies and dietary needs, and consult a registered "
        "dietitian for personalised guidance."
    )
