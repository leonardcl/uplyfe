"""HTTP endpoints.

Routes:
    POST /manual         — body is a LabPanel JSON; runs the full pipeline.
    POST /upload         — multipart file; supports .pdf / image; runs the full pipeline.
    GET  /healthz        — quick liveness + LLM/store status.
"""
from __future__ import annotations

import shutil
import tempfile
from pathlib import Path

from fastapi import APIRouter, File, Form, HTTPException, UploadFile
from pydantic import BaseModel

from app.config import get_settings
from app.llm import OllamaClient
from app.models import FinalReport, LabPanel
from app.pipeline import PipelineOptions, run_pipeline
from app.rag import KnowledgeStore


router = APIRouter()


class ManualRequest(BaseModel):
    panel: LabPanel
    use_llm: bool = True
    use_rag: bool = True


@router.post("/manual", response_model=FinalReport)
async def manual(req: ManualRequest) -> FinalReport:
    return run_pipeline(
        panel=req.panel,
        options=PipelineOptions(use_llm=req.use_llm, use_rag=req.use_rag),
    )


@router.post("/upload", response_model=FinalReport)
async def upload(
    file: UploadFile = File(...),
    use_llm: bool = Form(default=True),
    use_rag: bool = Form(default=True),
) -> FinalReport:
    suffix = Path(file.filename or "").suffix.lower()
    if suffix not in {".pdf", ".png", ".jpg", ".jpeg", ".tif", ".tiff"}:
        raise HTTPException(status_code=415, detail=f"Unsupported file type: {suffix}")

    with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as tmp:
        shutil.copyfileobj(file.file, tmp)
        tmp_path = Path(tmp.name)
    try:
        opts = PipelineOptions(use_llm=use_llm, use_rag=use_rag)
        if suffix == ".pdf":
            return run_pipeline(pdf_path=tmp_path, options=opts)
        return run_pipeline(image_path=tmp_path, options=opts)
    finally:
        tmp_path.unlink(missing_ok=True)


@router.get("/healthz")
async def healthz() -> dict:
    settings = get_settings()
    llm_ok = OllamaClient(settings).is_available()
    try:
        store = KnowledgeStore()
        n_chunks = store.count()
    except Exception:
        n_chunks = -1
    return {
        "status": "ok",
        "llm_available": llm_ok,
        "ollama_model": settings.ollama_model,
        "knowledge_chunks": n_chunks,
    }
