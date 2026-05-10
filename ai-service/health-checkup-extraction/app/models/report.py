"""Final report shape — what callers consume."""
from __future__ import annotations

from datetime import datetime

from pydantic import BaseModel, Field

from .biomarkers import Biomarker
from .findings import Finding, Severity
from .lab import LabPanel, ValidationIssue


# Human-readable biomarker labels used in markdown rendering.
DISPLAY_NAME: dict[Biomarker, str] = {
    Biomarker.GLUCOSE_FASTING: "Glucose, fasting",
    Biomarker.GLUCOSE_RANDOM: "Glucose, random",
    Biomarker.GLUCOSE_POSTPRANDIAL: "Glucose, 2-hr postprandial",
    Biomarker.HBA1C: "HbA1c",
    Biomarker.TOTAL_CHOLESTEROL: "Total cholesterol",
    Biomarker.LDL: "LDL cholesterol",
    Biomarker.HDL: "HDL cholesterol",
    Biomarker.TRIGLYCERIDES: "Triglycerides",
    Biomarker.NON_HDL: "Non-HDL cholesterol",
    Biomarker.ALT: "ALT",
    Biomarker.AST: "AST",
    Biomarker.ALP: "Alkaline phosphatase",
    Biomarker.GGT: "GGT",
    Biomarker.BILIRUBIN_TOTAL: "Bilirubin, total",
    Biomarker.BILIRUBIN_DIRECT: "Bilirubin, direct",
    Biomarker.ALBUMIN: "Albumin",
    Biomarker.CREATININE: "Creatinine",
    Biomarker.BUN: "BUN",
    Biomarker.EGFR: "eGFR",
    Biomarker.URIC_ACID: "Uric acid",
    Biomarker.HEMOGLOBIN: "Hemoglobin",
    Biomarker.HEMATOCRIT: "Hematocrit",
    Biomarker.RBC: "RBC",
    Biomarker.WBC: "WBC",
    Biomarker.PLATELETS: "Platelets",
    Biomarker.MCV: "MCV",
    Biomarker.TSH: "TSH",
    Biomarker.FREE_T4: "Free T4",
    Biomarker.FREE_T3: "Free T3",
    Biomarker.CRP: "CRP",
    Biomarker.ESR: "ESR",
    Biomarker.SODIUM: "Sodium",
    Biomarker.POTASSIUM: "Potassium",
    Biomarker.CHLORIDE: "Chloride",
    Biomarker.CALCIUM: "Calcium",
    Biomarker.VITAMIN_D_25OH: "25-OH Vitamin D",
    Biomarker.VITAMIN_B12: "Vitamin B12",
    Biomarker.BMI: "BMI",
    Biomarker.WAIST_CM: "Waist",
    Biomarker.BP_SYSTOLIC: "Blood pressure",
    Biomarker.BP_DIASTOLIC: "Blood pressure (diastolic)",
}


def _fmt_value(v: float) -> str:
    """Drop trailing .0 on whole-number floats; keep small decimals readable."""
    if v == int(v):
        return str(int(v))
    return f"{v:g}"


def _short_finding_line(f: Finding) -> str:
    name = DISPLAY_NAME.get(f.biomarker, f.biomarker.value)
    return (
        f"- **{name}** — {_fmt_value(f.value)} {f.unit} "
        f"— _{f.severity.value}_ — {f.label}. _{f.source}_"
    )


class ReportSection(BaseModel):
    title: str
    body: str


class KeyInsight(BaseModel):
    """One headline biomarker the UI shows in a hero card.

    Three are typically computed (cholesterol, blood sugar, vitamin D) — only
    those that are actually present in the panel are emitted. Pure
    deterministic; the LLM does not pick these.
    """
    key: str                          # canonical key: "cholesterol" | "blood_sugar" | "vitamin_d"
    label: str                        # human-readable card title, e.g. "Cholesterol Levels"
    biomarker: Biomarker              # the actual biomarker the value came from
    value: float
    unit: str
    status: str                       # "optimal" | "normal" | "borderline" | "high" | "low" | "critical"
    summary: str                      # one short sentence the card subtitle uses


class FinalReport(BaseModel):
    generated_at: datetime = Field(default_factory=datetime.utcnow)
    panel: LabPanel
    overall_severity: Severity
    summary: str
    key_insights: list[KeyInsight] = []         # cholesterol / blood sugar / vitamin D for hero cards
    abnormal_findings: list[Finding]            # biomarker findings only — never patterns
    critical_findings: list[Finding]
    pattern_findings: list[Finding] = []        # cross-biomarker patterns, full detail
    pattern_notes: list[str] = []               # back-compat: just the labels
    diet_advice: list[str] = []
    exercise_advice: list[str] = []
    recheck_advice: list[str] = []
    when_to_see_doctor: list[str] = []
    validation_issues: list[ValidationIssue] = []
    sections: list[ReportSection] = []          # LLM-rendered prose sections
    disclaimer: str = ""
    sources: list[str] = []                     # de-duped citation list

    def to_markdown(self) -> str:
        """Render the report as plain markdown.

        Important: the disclaimer is rendered exactly once, at the end. Pattern
        findings are rendered in their own section, not mixed into Key Abnormal
        Results.
        """
        out: list[str] = []
        out.append(f"# Health Checkup Report")
        out.append(f"_Generated {self.generated_at.isoformat(timespec='seconds')} UTC_\n")

        out.append("## Overall Health Summary")
        out.append(f"**Overall severity:** {self.overall_severity.value}\n")
        out.append(self.summary + "\n")

        if self.critical_findings:
            out.append("## ⚠️ Critical — please act on these")
            for f in self.critical_findings:
                out.append(_short_finding_line(f))
            out.append("")

        if self.abnormal_findings:
            out.append("## Key Abnormal Results")
            for f in self.abnormal_findings:
                out.append(_short_finding_line(f))
            out.append("")

        if self.pattern_findings:
            out.append("## Possible Health Patterns")
            for p in self.pattern_findings:
                out.append(f"- **{p.label}** — {p.rationale} _{p.source}_")
            out.append("")
        elif self.pattern_notes:
            out.append("## Possible Health Patterns")
            for note in self.pattern_notes:
                out.append(f"- {note}")
            out.append("")

        if self.diet_advice:
            out.append("## Diet Suggestions")
            for d in self.diet_advice:
                out.append(f"- {d}")
            out.append("")

        if self.exercise_advice:
            out.append("## Exercise Suggestions")
            for e in self.exercise_advice:
                out.append(f"- {e}")
            out.append("")

        if self.recheck_advice:
            out.append("## What to Recheck")
            for r in self.recheck_advice:
                out.append(f"- {r}")
            out.append("")

        if self.when_to_see_doctor:
            out.append("## When to See a Doctor")
            for w in self.when_to_see_doctor:
                out.append(f"- {w}")
            out.append("")

        for s in self.sections:
            out.append(f"## {s.title}")
            out.append(s.body + "\n")

        if self.validation_issues:
            out.append("## Data Quality Notes")
            for i in self.validation_issues:
                out.append(f"- ({i.kind}) {i.message}")
            out.append("")

        if self.sources:
            out.append("## Sources Cited")
            for s in self.sources:
                out.append(f"- {s}")
            out.append("")

        if self.disclaimer:
            out.append("---")
            out.append(self.disclaimer)

        return "\n".join(out)
