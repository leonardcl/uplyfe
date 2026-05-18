"""OCR for scanned reports.

Uses pytesseract by default. Auto-detects Korean/Indonesian/English so the
right Tesseract language pack is used — critical for non-Latin scripts.

Pass `engine="paddle"` to opt in to PaddleOCR if installed.

For tesseract we apply light preprocessing (grayscale + autocontrast,
optional invert for dark scans) and `--psm 4` which treats the input as a
single column of variable-size text — closer to a lab report's layout.
"""
from __future__ import annotations

import re
from pathlib import Path
from typing import Literal


Engine = Literal["tesseract", "paddle"]


def _detect_script(img) -> str:
    """Quick pixel-level heuristic: run a tiny Tesseract pass with OSD (orientation
    and script detection) to decide which language pack to use.

    Returns one of: "kor", "ind", "eng"  (Tesseract lang codes).
    Falls back to "eng+ind" on any error.
    """
    try:
        import pytesseract
        osd = pytesseract.image_to_osd(img, output_type=pytesseract.Output.DICT)
        script = (osd.get("script") or "").lower()
        if "hangul" in script or "korean" in script:
            return "kor+eng"
    except Exception:
        pass

    # Fallback: run a fast eng pass and count Korean Unicode codepoints vs
    # Latin — if there are Hangul chars we know it's Korean.
    try:
        import pytesseract
        quick = pytesseract.image_to_string(img, lang="eng", config="--psm 11")
        hangul = len(re.findall(r'[가-힣ᄀ-ᇿ㄰-㆏]', quick))
        if hangul > 3:
            return "kor+eng"
    except Exception:
        pass

    return "eng+ind"


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
    """Grayscale + autocontrast; invert if the page is unusually dark."""
    try:
        from PIL import ImageOps, ImageStat

        gray = img.convert("L")
        stat = ImageStat.Stat(gray)
        out = ImageOps.autocontrast(gray, cutoff=2)
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

    # Auto-detect script so we pick the right language pack.
    lang = _detect_script(img)

    # PSM 4: single column of variable-size text (best for lab report layout).
    text = pytesseract.image_to_string(img, lang=lang, config="--psm 4").strip()
    return text


def _paddle(path: Path) -> str:  # pragma: no cover — optional
    try:
        from paddleocr import PaddleOCR
    except ImportError as e:
        raise RuntimeError(
            "PaddleOCR is not installed. `pip install paddleocr paddlepaddle`."
        ) from e
    ocr = PaddleOCR(use_angle_cls=True, lang="korean", show_log=False)
    result = ocr.ocr(str(path), cls=True)
    parts: list[str] = []
    for page in result or []:
        for line in page or []:
            try:
                parts.append(line[1][0])
            except Exception:
                continue
    return "\n".join(parts).strip()
