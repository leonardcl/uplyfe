# Safety policy

This system is a **report assistant**, not a clinician. Everything below is enforced in code; safety is not an instruction in a prompt.

## Hard rules (enforced in `app/safety/validator.py`)

1. **No diagnoses.** Phrases like "you have diabetes", "the patient has heart disease", "you suffer from..." are rewritten to neutral phrasing such as "this result is in the range associated with...".
2. **No medication recommendations.** Medication names and prescription-style phrasing are blocked. The output substitutes an explicit "[medication advice removed — please consult a clinician]" marker so the user sees that something was filtered.
3. **No diagnostic confirmation.** Phrases like "this confirms...", "this proves...", "definitively diagnoses..." are softened to "this is consistent with...".
4. **Disclaimer is always appended.** If the LLM's output already contains it, we don't duplicate it; otherwise the validator inserts it.
5. **Escalations are surfaced separately.** Findings with `escalate=True` or `severity=CRITICAL` produce a "When to See a Doctor" section that lists each one with the line "Seek prompt professional evaluation."

## Escalation triggers (deterministic, in the rules engine)

| Trigger | Source |
|---|---|
| Fasting glucose ≥ 400 mg/dL | ADA 2024 |
| HbA1c ≥ 10 % | ADA 2024 |
| Random glucose ≥ 400 mg/dL | ADA 2024 |
| Triglycerides ≥ 1000 mg/dL (pancreatitis risk) | NCEP / AACC |
| ALT or AST ≥ 400 U/L (≥10× ULN) | AASLD |
| Total bilirubin > 5 mg/dL | AACC |
| eGFR < 30 mL/min/1.73m² | KDIGO 2024 |
| Hemoglobin < 8 g/dL | MedlinePlus / AACC |
| WBC < 2 or > 30 ×10³/µL | MedlinePlus / AACC |
| Platelets < 50 or > 1000 ×10³/µL | MedlinePlus / AACC |
| Sodium < 120 or > 160 mmol/L | AACC |
| Potassium < 2.5 or > 6.5 mmol/L | AACC |
| Calcium < 7 or > 13 mg/dL | AACC |
| BP ≥ 180 / ≥ 120 mmHg (hypertensive crisis) | ACC/AHA 2017 |

## What the LLM is allowed to do

- Re-state a deterministic finding in plain language.
- Use the supplied label and severity verbatim.
- Reference a passage from the curated knowledge base.
- Suggest a physician consultation.

## What the LLM is **not** allowed to do

- Decide thresholds.
- Override or contradict the rules engine.
- Recommend medications.
- Diagnose disease.
- Promise that a normal result rules out disease.

## Defense in depth

The LLM has a `system` prompt that tells it the rules. Then the rules are also enforced post-hoc in `validate_text(...)`. If the LLM ignores its instructions, the safety layer catches it. If the safety layer is bypassed (e.g., in `--no-llm` mode), the deterministic report is the *only* output and contains no LLM-generated prose at all.

## Reference-range caveat

Reference ranges vary by lab, age, sex, hydration, time of day, fasting state, and recent illness. The rules in this repo use widely accepted screening cutoffs from major guidelines. Lab-specific reference ranges from the user's report (when supplied via `reference_low`/`reference_high`) are preserved on the LabValue and shown to the user, but rule severities use the codified guideline cutoffs to keep evaluation consistent.

## Privacy

This project does not transmit lab data to any external service unless the user explicitly configures one (e.g., a non-local LLM). Default deployment is fully local: Ollama, ChromaDB, Tesseract.
