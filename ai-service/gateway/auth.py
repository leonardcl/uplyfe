from starlette.middleware.base import BaseHTTPMiddleware
from starlette.requests import Request
from starlette.responses import JSONResponse

from config import get_settings

PUBLIC_PATHS = {"/healthz", "/docs", "/redoc", "/openapi.json"}


class ApiKeyMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        path = request.url.path
        if path in PUBLIC_PATHS or path.startswith("/docs") or path.startswith("/static"):
            return await call_next(request)

        expected = get_settings().ai_service_key
        provided = request.headers.get("x-api-key")
        if not provided or provided != expected:
            return JSONResponse(
                status_code=401,
                content={"detail": "Missing or invalid X-API-Key header."},
            )
        return await call_next(request)
