"""Evaluation CLI — runs the pipeline against sample PDFs and reports recall.

Reads each PDF in `samples/synthetic/`, `samples/online/`, `samples/private/`,
matches it to its `samples/expected/<name>.json` ground truth, and reports:
  * which biomarkers were extracted, missed, or got the wrong value
  * per-sample recall and overall recall
  * a side-by-side table per sample for visual inspection

Run:
    python -m app.cli.eval run                    # all samples
    python -m app.cli.eval run --only synthetic   # one folder only
    python -m app.cli.eval run --tolerance 0.05   # 5% relative tolerance
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Optional

import typer
from rich.console import Console
from rich.table import Table
from rich import box

from app.parsers import extract_text_from_pdf
from app.parsers.pdf_parser import EmptyPDFTextError
from app.pipeline import run_pipeline, PipelineOptions


app = typer.Typer(help="Evaluate extraction recall against sample PDFs.")
console = Console()


PROJECT_ROOT = Path(__file__).resolve().parent.parent.parent
SAMPLES_ROOT = PROJECT_ROOT / "samples"
EXPECTED_DIR = SAMPLES_ROOT / "expected"


def _find_sample_pdfs(only: Optional[str]) -> list[Path]:
    folders = ["synthetic", "online", "private"] if only is None else [only]
    out: list[Path] = []
    for f in folders:
        d = SAMPLES_ROOT / f
        if d.exists():
            out.extend(sorted(d.glob("*.pdf")))
    return out


def _load_expected(pdf: Path) -> Optional[dict]:
    expected_file = EXPECTED_DIR / f"{pdf.stem}.json"
    if not expected_file.exists():
        return None
    return json.loads(expected_file.read_text(encoding="utf-8"))


def _values_close(actual: float, expected: float, tolerance: float) -> bool:
    if expected == 0:
        return abs(actual) <= tolerance
    return abs(actual - expected) / abs(expected) <= tolerance


@app.command()
def run(
    only: Optional[str] = typer.Option(None, "--only", help="One of: synthetic, online, private"),
    tolerance: float = typer.Option(0.02, "--tolerance", "-t", help="Relative tolerance for value match (default 2%)"),
    show_unexpected: bool = typer.Option(False, "--show-unexpected", help="Print biomarkers extracted that were not in expected"),
):
    """Run the deterministic pipeline against all sample PDFs and report recall."""
    pdfs = _find_sample_pdfs(only)
    if not pdfs:
        console.print(f"[yellow]No PDFs found in samples/{only or '*'}/")
        raise typer.Exit(1)

    summary = Table(title="Sample-set recall summary", box=box.SIMPLE)
    summary.add_column("Sample", style="bold")
    summary.add_column("Found", justify="right")
    summary.add_column("Expected", justify="right")
    summary.add_column("Recall", justify="right")
    summary.add_column("Wrong values", justify="right", style="yellow")

    total_found = 0
    total_expected = 0
    total_wrong = 0

    for pdf in pdfs:
        expected = _load_expected(pdf)
        if expected is None:
            console.print(f"[dim]skip[/dim] {pdf.name} — no expected.json")
            continue

        try:
            text = extract_text_from_pdf(pdf)
        except EmptyPDFTextError:
            console.print(f"[red]✗[/red] {pdf.name} — PDF has no extractable text")
            continue

        report = run_pipeline(
            raw_text=text,
            options=PipelineOptions(use_llm=False, use_rag=False),
        )
        actual_by_b = {v.biomarker.value: v for v in report.panel.values}

        per_sample = Table(title=pdf.name, box=box.SIMPLE_HEAD, show_header=True)
        per_sample.add_column("Biomarker", style="bold")
        per_sample.add_column("Expected", justify="right")
        per_sample.add_column("Got", justify="right")
        per_sample.add_column("Status")

        found = 0
        wrong = 0
        for b_key, expected_value in expected["biomarkers"].items():
            actual = actual_by_b.get(b_key)
            if actual is None:
                per_sample.add_row(b_key, str(expected_value), "—", "[red]MISSED[/red]")
            else:
                ok = _values_close(actual.value, float(expected_value), tolerance)
                if ok:
                    found += 1
                    per_sample.add_row(b_key, str(expected_value), f"{actual.value} {actual.unit}", "[green]ok[/green]")
                else:
                    wrong += 1
                    per_sample.add_row(b_key, str(expected_value), f"{actual.value} {actual.unit}", "[yellow]VALUE OFF[/yellow]")

        if show_unexpected:
            for k in actual_by_b:
                if k not in expected["biomarkers"]:
                    v = actual_by_b[k]
                    per_sample.add_row(k, "—", f"{v.value} {v.unit}", "[blue]extra[/blue]")

        console.print(per_sample)
        n = len(expected["biomarkers"])
        total_found += found
        total_expected += n
        total_wrong += wrong
        recall = found / n if n else 0
        recall_color = "green" if recall >= 0.9 else "yellow" if recall >= 0.6 else "red"
        summary.add_row(
            pdf.name, str(found), str(n),
            f"[{recall_color}]{recall:.0%}[/{recall_color}]",
            str(wrong) if wrong else "0",
        )

    overall = total_found / total_expected if total_expected else 0
    overall_color = "green" if overall >= 0.9 else "yellow" if overall >= 0.6 else "red"
    summary.add_row(
        "[bold]OVERALL[/bold]", str(total_found), str(total_expected),
        f"[bold {overall_color}]{overall:.0%}[/bold {overall_color}]",
        str(total_wrong) if total_wrong else "0",
    )
    console.print()
    console.print(summary)


if __name__ == "__main__":
    app()
