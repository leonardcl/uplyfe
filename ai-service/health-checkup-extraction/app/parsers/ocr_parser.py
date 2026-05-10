"""OCR for scanned reports.

Uses pytesseract by default. Pass `engine="paddle"` to opt in to PaddleOCR if
the user has it installed (we don't ship it as a hard dependency because it
pulls a much heavier model).

For tesseract we apply light, dependency-free preprocessing (grayscale +
autocontrast, optional invert for unusually dark scans) and `--psm 4` which
treats the input as a single column of variable-size text — closer to a lab
report's layout than the default PSM 3.
"""
from __future__ import annotations

from pathlib import Path
from typing import Literal


Engine = Literal["tesseract", "paddle"]


def extract_text_from_image(path: str | Path, *, engine: Engine = "tesseract") -> str:
    p = Path(path)
    if not p.exists():
        raise FileNotFoundError(p)

    if engine == "tesseract":
        return _tesseract(p)
    if engine == "paddle":
        return _paddle(p)
    raise ValueError(f"Unknown OCR engine: {engine}")


def _preprocess_image(img):
    """Light, dependency-free preprocessing: grayscale + autocontrast.

    Returns the (possibly enhanced) image. On any error, returns the original
    unchanged — we never fail OCR just because preprocessing didn't work.
    """
    try:
        from PIL import ImageOps, ImageStat

        gray = img.convert("L")
        stat = ImageStat.Stat(gray)
        # autocontrast to recover from low-contrast scans
        out = ImageOps.autocontrast(gray, cutoff=2)
        # If the median brightness is very dark, the page may be inverted
        # (light text on dark background). Flip it.
        if stat.median and stat.median[0] < 90:
            out = ImageOps.invert(out)
        return out
    except Exception:
        return img


def _tesseract(path: Path) -> str:
    import pytesseract
    from PIL import Image

    img = Image.open(path)
    img = _preprocess_image(img)
    # PSM 4: assume a single column of text of variable sizes.
    return pytesseract.image_to_string(img, config="--psm 4").strip()


def _paddle(path: Path) -> str:  # pragma: no cover — optional
    try:
        from paddleocr import PaddleOCR
    except ImportError as e:
        raise RuntimeError(
            "PaddleOCR is not installed. `pip install paddleocr paddlepaddle`."
        ) from e
    ocr = PaddleOCR(use_angle_cls=True, lang="en", show_log=False)
    result = ocr.ocr(str(path), cls=True)
    parts: list[str] = []
    for page in result or []:
        for line in page or []:
            try:
                parts.append(line[1][0])
            except Exception:
                continue
    return "\n".join(parts).strip()
