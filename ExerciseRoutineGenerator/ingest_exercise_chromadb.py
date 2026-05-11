import json
import requests
from tqdm import tqdm
import chromadb
from chromadb.config import Settings

# =========================
# CONFIG
# =========================
JSON_PATH = "/home/alvin/exercises-dataset/data/exercises.json"
COLLECTION_NAME = "exercise_data1"
CHROMA_DB_PATH = "/home/alvin/testqdrant/.chromadb"   # persistence directory
OLLAMA_URL = "http://localhost:11434/api/embeddings"
EMBED_MODEL = "embeddinggemma"
BATCH_SIZE = 32

# =========================
# LOAD JSON
# =========================
with open(JSON_PATH, "r", encoding="utf-8") as f:
    data = json.load(f)

# =========================
# CLEAN + TRANSFORM
# =========================
def clean_entry(entry):
    return {
        "id": entry["id"],
        "name": entry["name"],
        "category": entry.get("category"),
        "body_part": entry.get("body_part"),
        "equipment": entry.get("equipment"),
        "instructions": entry["instructions"]["en"],
        "muscle_group": entry.get("muscle_group"),
        "target": entry.get("target"),
    }

def build_text(entry):
    return f"""
Exercise ID: {entry['id']}
Exercise: {entry['name']}
Category: {entry['category']}
Body Part: {entry['body_part']}
Equipment: {entry['equipment']}
Target: {entry['target']}
Muscles: {entry['muscle_group']}
Instructions: {entry['instructions']}
""".strip()

cleaned = [clean_entry(e) for e in data]

# =========================
# OLLAMA EMBEDDING
# =========================
def embed(text):
    response = requests.post(
        OLLAMA_URL,
        json={"model": EMBED_MODEL, "prompt": text}
    )
    return response.json()["embedding"]

# =========================
# CHROMADB SETUP (PERSISTENT)
# =========================
client = chromadb.PersistentClient(
    path=CHROMA_DB_PATH
)

# -------------------------
# DELETE EXISTING COLLECTION
# -------------------------
try:
    client.delete_collection(COLLECTION_NAME)
    print(f"Deleted existing collection: {COLLECTION_NAME}")

except Exception:
    print("Collection does not exist yet.")

# -------------------------
# CREATE FRESH COLLECTION
# -------------------------
collection = client.get_or_create_collection(
    name=COLLECTION_NAME
)

# =========================
# BATCH INSERT
# =========================
ids = []
documents = []
metadatas = []
embeddings = []

for i, entry in enumerate(tqdm(cleaned)):
    text = build_text(entry)
    vector = embed(text)

    ids.append(str(entry["id"]))
    documents.append(text)
    embeddings.append(vector)

    metadatas.append({
        "exercise_id": entry["id"],
        "name": entry["name"],
        "category": entry["category"],
        "body_part": entry["body_part"],
        "target": entry["target"],
        "equipment": entry["equipment"],
        "muscle_group": entry["muscle_group"]
    })

    # Insert batch
    if len(ids) >= BATCH_SIZE:
        collection.add(
            ids=ids,
            documents=documents,
            metadatas=metadatas,
            embeddings=embeddings
        )

        ids, documents, metadatas, embeddings = [], [], [], []

# Insert remaining
if ids:
    collection.add(
        ids=ids,
        documents=documents,
        metadatas=metadatas,
        embeddings=embeddings
    )

print("Done inserting data into persistent ChromaDB.")