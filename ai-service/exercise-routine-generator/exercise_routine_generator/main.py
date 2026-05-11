"""FastAPI entrypoint. Run standalone with:

    uvicorn app.main:app --reload --port 8002

Or mount as a sub-app from the gateway:

    from exercise_routine_generator.main import app as exercise_app
    main_app.mount("/exercise", exercise_app)
"""
from fastapi import FastAPI

from exercise_routine_generator.api.routes import router


app = FastAPI(
    title="Exercise Routine Generator",
    version="0.1.0",
    description=(
        "3-stage RAG pipeline that builds weekly workout plans grounded in "
        "a curated exercise dataset and (optionally) exercise-science textbooks. "
        "Each plan cites exercise IDs from the dataset; the LLM never invents "
        "exercises."
    ),
)
app.include_router(router)
