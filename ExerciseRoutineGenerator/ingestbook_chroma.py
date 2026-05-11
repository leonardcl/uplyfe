import os
import re
import requests
from tqdm import tqdm
import pymupdf
import chromadb

# =========================
# CONFIG
# =========================
PDF_PATH = "/home/alvin/testqdrant/ACSM's Guidelines for Exercise Testing and Prescription -- Cemal Ozemek & Amanda Bonikowske & Jeffrey Christle & Paul.pdf"
#PDF_PATH = "/home/alvin/testqdrant/Essentials of Strength Training and Conditioning 4th Edition -- NSCA -National Strength & Conditioning Association -- 4, 2015.pdf"
COLLECTION_NAME = "pdf_books"
CHROMA_DB_PATH = "/home/alvin/testqdrant/.chromadb"   # persistent storage

OLLAMA_EMBED_URL = "http://localhost:11434/api/embeddings"
OLLAMA_CHAT_URL = "http://192.168.111.132:11434/api/generate"

EMBED_MODEL = "embeddinggemma"
LLM_MODEL = "gemma4:e2b"

CHUNK_SIZE = 1200
CHUNK_OVERLAP = 200
BATCH_SIZE = 32
TOP_K = 8

# =========================
# OLLAMA
# =========================
def embed(text):
    response = requests.post(
        OLLAMA_EMBED_URL,
        json={"model": EMBED_MODEL, "prompt": text}
    )

    body = response.json()

    if response.status_code != 200:
        raise RuntimeError(f"Embedding API error: {body}")

    if "embedding" not in body:
        raise RuntimeError(f"Invalid embedding response: {body}")

    return body["embedding"]

def generate(prompt):
    r = requests.post(
        OLLAMA_CHAT_URL,
        json={"model": LLM_MODEL, "prompt": prompt, "stream": False}
    )
    r.raise_for_status()
    return r.json()["response"]

# =========================
# CHROMADB SETUP
# =========================
client = chromadb.PersistentClient(path=CHROMA_DB_PATH)

collection = client.get_or_create_collection(
    name=COLLECTION_NAME
)

# =========================
# PDF EXTRACTION
# =========================
def extract_pdf(pdf_path):
    doc = pymupdf.open(pdf_path)
    pages = []
    for i, page in enumerate(doc):
        text = page.get_text("text")
        pages.append({
            "page": i + 1,
            "text": text
        })
    return pages

# =========================
# CLEANING
# =========================
def clean_text(text):
    text = re.sub(r"\n(?=[a-z])", " ", text)
    text = re.sub(r"\s+", " ", text)
    return text.strip()

# =========================
# CHUNKING
# =========================
def chunk_text(text, chunk_size=CHUNK_SIZE, overlap=CHUNK_OVERLAP):
    chunks = []
    start = 0
    n = len(text)

    while start < n:
        end = start + chunk_size
        chunk = text[start:end]
        chunks.append(chunk)
        start += chunk_size - overlap

    return chunks

# =========================
# BUILD RECORDS
# =========================
def build_records(pages, source_name):
    records = []
    uid = 0

    for p in pages:
        cleaned = clean_text(p["text"])
        chunks = chunk_text(cleaned)

        for ch in chunks:
            records.append({
                "id": str(uid),   # Chroma requires string IDs
                "text": ch,
                "page": p["page"],
                "source": source_name
            })
            uid += 1

    return records

# =========================
# INGEST
# =========================
def ingest_pdf(pdf_path):
    pages = extract_pdf(pdf_path)
    records = build_records(pages, os.path.basename(pdf_path))

    ids = []
    documents = []
    metadatas = []
    embeddings = []

    for r in tqdm(records, desc="Embedding + Inserting"):
        vec = embed(r["text"])

        ids.append(r["id"])
        documents.append(r["text"])
        embeddings.append(vec)

        metadatas.append({
            "page": r["page"],
            "source": r["source"]
        })

        if len(ids) >= BATCH_SIZE:
            collection.upsert(   # use upsert to avoid duplicate issues
                ids=ids,
                documents=documents,
                metadatas=metadatas,
                embeddings=embeddings
            )
            ids, documents, metadatas, embeddings = [], [], [], []

    if ids:
        collection.upsert(
            ids=ids,
            documents=documents,
            metadatas=metadatas,
            embeddings=embeddings
        )

    print("PDF ingestion complete (ChromaDB persistent).")

# =========================
# RETRIEVAL
# =========================
def retrieve(query):
    qv = embed(query)

    res = collection.query(
        query_embeddings=[qv],
        n_results=TOP_K
    )

    contexts = []

    docs = res.get("documents", [[]])[0]
    metas = res.get("metadatas", [[]])[0]

    for doc, meta in zip(docs, metas):
        page = meta["page"]
        contexts.append(f"[Page {page}]\n{doc}")

    return contexts

# =========================
# RAG
# =========================
def rag_query(query):
    ctxs = retrieve(query)
    context_block = "\n\n---\n\n".join(ctxs)

    prompt = f"""
You are answering questions from a book.

Use ONLY the provided context.
If unsure, say you don't know.
Cite page numbers when relevant.

Context:
{context_block}

Question:
{query}

Answer:
""".strip()

    return generate(prompt)

# =========================
# CLI
# =========================
def chat():
    print("\nPDF RAG ready (ChromaDB). Type 'exit' to quit.\n")

    while True:
        q = input("You: ")
        if q.lower() in ["exit", "quit"]:
            break

        ans = rag_query(q)
        print("\nAnswer:\n")
        print(ans)
        print("\n" + "="*60 + "\n")

# =========================
# MAIN
# =========================
if __name__ == "__main__":
    ingest_pdf(PDF_PATH)
    #chat()