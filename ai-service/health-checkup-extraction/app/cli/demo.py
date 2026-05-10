"""Demo CLI.

Examples:
    python -m app.cli.demo --input data/samples/manual_input.json
    python -m app.cli.demo --input data/samples/manual_input.json --no-llm
    python -m app.cli.demo --pdf path/to/report.pdf
    python -m app.cli.demo --image path/to/scan.png
"""
from __future__ import annotations

import json
from pathlib import Path
from typing import Optional

import typer
from rich.console import Console
from rich.markdown import Markdown
from rich.panel import Panel

from app.models import LabPanel
from app.pipeline import PipelineOptions, run_pipeline


app = typer.Typer(help="Run the health-checkup pipeline end-to-end.")
console = Console()


@app.command()
def main(
    input: Optional[Path] = typer.Option(None, "--input", "-i", help="JSON LabPanel."),
    pdf: Optional[Path] = typer.Option(None, "--pdf", help="PDF lab report."),
    image: Optional[Path] = typer.Option(None, "--image", help="Image scan."),
    no_llm: bool = typer.Option(False, "--no-llm", help="Skip the LLM step entirely."),
    no_rag: bool = typer.Option(False, "--no-rag", help="Skip retrieval (use deterministic advice only)."),
    save: Optional[Path] = typer.Option(None, "--save", help="Write the report markdown here."),
    json_out: bool = typer.Option(False, "--json", help="Print the report as JSON."),
):
    if sum(x is not None for x in (input, pdf, image)) != 1:
        console.print("[red]Provide exactly one of --input / --pdf / --image.[/red]")
        raise typer.Exit(2)

    options = PipelineOptions(use_llm=not no_llm, use_rag=not no_rag)

    if input:
        panel = LabPanel.model_validate(json.loads(input.read_text(encoding="utf-8")))
        report = run_pipeline(panel=panel, options=options)
    elif pdf:
        report = run_pipeline(pdf_path=pdf, options=options)
    else:
        report = run_pipeline(image_path=image, options=options)

    if json_out:
        console.print_json(data=json.loads(report.model_dump_json()))
    else:
        md = report.to_markdown()
        console.print(Panel.fit(Markdown(md), title="Health Checkup Report", border_style="cyan"))

    if save:
        save.write_text(report.to_markdown(), encoding="utf-8")
        console.print(f"\n[green]Saved →[/green] {save}")


if __name__ == "__main__":
    app()
