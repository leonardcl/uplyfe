# Drop your recipe JSON files here

The ingest CLI reads every `.json` file in this folder and indexes the
recipes into the local ChromaDB store so the meal-plan generator can
retrieve them.

## Quickest path

1. Drop one or more `.json` files into this folder.
2. From the package root, run:

   ```bash
   cd ../  # → ai-service/recipe-generator/
   make ingest
   ```

3. Verify:
   ```bash
   make stats
   # recipes: 47 entries
   ```

Re-running `make ingest` is safe — recipes are **upserted** by a hash of
`source_file + name`, so the same recipe in the same file won't duplicate.

## Accepted JSON shapes

The ingester is intentionally permissive. Pick whichever is easiest for
your data source.

### A. Array of recipes (most common)
```json
[
  {
    "name": "Avocado Toast",
    "cuisine": "Modern",
    "calories_per_serving": 380,
    "protein_per_serving": 14,
    "carbs_per_serving": 35,
    "diet_tags": ["vegetarian", "high-fibre"],
    "ingredients": [
      "2 slices whole-grain bread",
      "1 ripe avocado",
      "1 tsp lemon juice",
      "Salt and pepper to taste"
    ],
    "instructions": [
      "Toast the bread until golden.",
      "Mash avocado with lemon juice, salt, and pepper.",
      "Spread avocado on toast and serve immediately."
    ]
  },
  {
    "name": "Nasi Goreng Ayam",
    ...
  }
]
```

### B. Wrapper object
```json
{
  "recipes": [
    { "name": "...", "ingredients": [...], "instructions": [...] }
  ]
}
```

### C. Single recipe
```json
{ "name": "...", "ingredients": [...], "instructions": [...] }
```

## Required fields

| Field | Required? | Notes |
|---|---|---|
| `name` | ✅ yes | Falls back to `title`, `recipe_name` |
| `ingredients` | ✅ yes | List of strings, or one comma/newline-separated string |
| `instructions` | ✅ yes | Falls back to `directions`, `steps`, `method` |

If any of those three are missing, the recipe is **skipped** with a count
in the ingest summary.

## Optional metadata (keeps retrieval smart)

| Field | Used for |
|---|---|
| `cuisine` | Cuisine filter / retrieval signal |
| `calories_per_serving` | Calorie-target matching, surfaced in the meal card |
| `protein_per_serving` | Macro display |
| `carbs_per_serving` | Macro display |
| `diet_tags` | Diet matching (`vegetarian`, `vegan`, `halal`, …) |

The system also accepts loose synonyms: `calories`/`kcal`,
`protein_g`/`protein`, `carbs_g`/`carbohydrates`. See
[`cli/ingest.py`](../recipe_generator/cli/ingest.py) for the full alias map.

## Where to find recipe datasets

If you don't have a dataset yet, here are some MIT/CC-licensed options:

- **[Food.com (~500k recipes)](https://www.kaggle.com/datasets/shuyangli94/food-com-recipes-and-user-interactions)** — large and varied
- **[Recipe1M+](http://im2recipe.csail.mit.edu/)** — academic, ~1M recipes
- **[RecipeNLG](https://recipenlg.cs.put.poznan.pl/)** — generation-friendly format
- **Indonesian local**: scrape from `resepkoki.id`, `sajiansedap.grid.id`, etc. (respect their terms)

## What's in this folder right now?

Nothing tracked — only this README. The `.json` files you add are
**gitignored** in `.gitignore` so you don't accidentally upload large
datasets to the repo.
