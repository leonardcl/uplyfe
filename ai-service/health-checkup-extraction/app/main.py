"""FastAPI entrypoint.

Run with:  uvicorn app.main:app --reload
"""
from fastapi import FastAPI

from app.api.routes import router


app = FastAPI(
    title="Health Checkup Extraction",
    version="0.1.0",
    description=(
        "Safety-first health-check report assistant. "
        "Extracts → normalizes → flags → retrieves knowledge → explains → safety-validates. "
        "Does NOT diagnose disease and does NOT recommend medications."
    ),
)
app.include_router(router)
