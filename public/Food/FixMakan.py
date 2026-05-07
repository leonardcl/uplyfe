import chromadb
import json
import requests
import os
import random
from pathlib import Path
from sentence_transformers import SentenceTransformer

# ========================
# CONFIG
# ========================
BASE_DIR = Path(__file__).resolve().parents[1]
CHROMA_PATH = BASE_DIR / "Data" / "chroma"
OUTPUT_PATH = BASE_DIR / "Data" / "Processed" / "meal_planv2.json"

OLLAMA_MODEL = os.getenv("OLLAMA_MODEL", "gemma4:26b")

client = chromadb.PersistentClient(path=str(CHROMA_PATH))
collection = client.get_or_create_collection("recipes")

model = SentenceTransformer("all-MiniLM-L6-v2")


# ========================
# LLM CALL
# ========================
def call_gemma(prompt):
    response = requests.post(
        "http://192.168.111.132:11434/api/generate",
        json={
            "model": OLLAMA_MODEL,
            "prompt": prompt,
            "stream": False
        }
    )
    response.raise_for_status()
    return response.json()["response"]


# ========================
# SEARCH RAG
# ========================
def search(query, k=50):
    q_emb = model.encode([query]).tolist()

    results = collection.query(
        query_embeddings=q_emb,
        n_results=k,
        include=["documents", "metadatas"],
    )

    docs = results["documents"][0]
    metas = results["metadatas"][0]

    recipes = []
    for d, m in zip(docs, metas):
        r = dict(m)
        r["document"] = d
        recipes.append(r)

    return recipes


# ========================
# PICK RANDOM (ANTI REPEAT)
# ========================
def pick(recipes, used):
    available = [r for r in recipes if r["name"] not in used]
    return random.choice(available if available else recipes)

def build_recipe_full(recipe):
    return f"""
Recipe Name: {recipe['name']}

Ingredients:
{recipe.get('ingredients', recipe.get('document', ''))}

Instructions:
{recipe.get('instructions', recipe.get('document', ''))}
"""

# ========================
# BUILD UI MEAL (IMPORTANT PART)
# ========================
def build_ui_meal(recipe, meal_type):
    full_recipe = build_recipe_full(recipe)

    prompt = f"""
You are a nutrition AI.

Convert this recipe into structured JSON.

RECIPE:
{full_recipe}

MEAL TYPE: {meal_type}

RETURN JSON ONLY:

{{
  "title": "{recipe['name']}",
  "subtitle": "{meal_type.title()}",
  "badge": "Balanced Meal",
  "calories": "{recipe.get('calories_per_serving', 0)}",
  "protein": "{recipe.get('protein_per_serving', 0)}",
  "carbs": "{recipe.get('carbs_per_serving', 0)}",
  "description": "Short explanation",
  "benefits": ["Healthy", "Balanced"],
  "tags": [{{"text":"Healthy","color":"green"}}],
  "ingredients": [],
  "instructions": [],
  "tips": "Simple cooking tip"
}}

RULES:
- Extract REAL ingredients
- Extract REAL instructions
- NO placeholders
- JSON ONLY
"""

    raw = call_gemma(prompt)

    print("\n=== RAW ===\n", raw)  # debug

    parsed = safe_parse_json(raw)

    if parsed:
        return parsed

    # fallback kalau gagal
    return {
        "title": recipe["name"],
        "subtitle": meal_type.title(),
        "badge": "Simple",
        "calories": str(recipe.get("calories_per_serving", 400)),
        "protein": str(recipe.get("protein_per_serving", 20)),
        "carbs": str(recipe.get("carbs_per_serving", 40)),
        "description": "Simple generated meal",
        "benefits": ["Basic nutrition"],
        "tags": [{"text": "Basic", "color": "gray"}],
        "ingredients": recipe.get("document", "").split("\n")[:5],
        "instructions": ["Cook based on recipe"],
        "tips": "Keep it simple"
    }



def safe_parse_json(text):
    try:
        return json.loads(text)
    except:
        start = text.find("{")
        end = text.rfind("}") + 1
        if start != -1 and end != -1:
            try:
                return json.loads(text[start:end])
            except:
                return None
        return None




# ========================
# BUILD FULL DAY
# ========================
def build_day(recipes, used):
    meals = ["breakfast", "lunch", "dinner"]

    day = {}

    for meal in meals:
        recipe = pick(recipes, used)
        used.add(recipe["name"])

        day[meal] = build_ui_meal(recipe, meal)

    # 🔥 pakai dinner sebagai title
    day["title"] = day["dinner"]["title"]

    return day

# ========================
# BUILD FULL WEEK
# ========================
def generate_week(recipes):
    days = ["monday","tuesday","wednesday","thursday","friday","saturday","sunday"]

    plan = {}
    used = set()

    for d in days:
        plan[d] = build_day(recipes, used)

    return plan


# ========================
# SAVE JSON
# ========================
def save(plan):
    OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)

    with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
        json.dump(plan, f, indent=2)

    print(f"\n✅ Saved to {OUTPUT_PATH}")


# ========================
# MAIN
# ========================
if __name__ == "__main__":
    while True:
        query = input("\nYou: ")

        if query.lower() in ["exit", "quit"]:
            break

        recipes = search(query, k=50)

        plan = generate_week(recipes)

        save(plan)