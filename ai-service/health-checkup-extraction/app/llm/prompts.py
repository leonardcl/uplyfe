"""Prompt templates.

Design rules:
  * The LLM never decides thresholds. It receives the deterministic findings
    and only re-states them in plain language.
  * Every prompt explicitly forbids diagnostic phrasing ("you have X") and
    medication advice. The safety validator then checks the output against
    that promise.
  * Keep prompts short and unambiguous; gemma-class models follow direct,
    structured prompts well at low temperature.
  * Findings are passed in as a JSON-shaped block so the model is *forced*
    to copy values verbatim instead of summarizing or making up substitutes.
  * The synthesis prompt ends with a sentinel ("END_OF_REPORT") used as an
    Ollama stop token to make the loop class of bug impossible.
"""

SYSTEM_PROMPT = """\
You are a careful, conservative health-report assistant.
You DO NOT diagnose disease.
You DO NOT recommend medications.
You ONLY explain lab findings in plain language.
If a finding is escalated as urgent, say so plainly and recommend professional consultation.
Use phrasing like 'this result is in the X range' or 'this pattern is associated with Y' —
never 'you have X'. Do not contradict, override, or invent thresholds beyond what is given.
"""

EXPLAIN_FINDING_PROMPT = """\
A deterministic rule produced this finding for a person aged {age}, sex {sex}:

  Biomarker: {biomarker}
  Value: {value} {unit}
  Severity: {severity}
  Label: {label}
  Source: {source}
  Rule rationale: {rationale}

Background passages from a curated knowledge base (use only what's relevant):
{context}

Write 2–4 short sentences explaining what this finding means for the person in plain language.
Forbidden:
  * Do not diagnose ('you have X').
  * Do not name medications.
  * Do not contradict the severity or label above.
  * Do not propose new thresholds.
Required:
  * Use the given label and severity verbatim.
  * If escalate=True, end with: "This warrants prompt professional evaluation."
"""

REPORT_SYNTHESIS_PROMPT = """\
PERSON
  age: {age}
  sex: {sex}

DETERMINISTIC FINDINGS — these are the facts. Use these values verbatim.
Do NOT introduce different numbers. Do NOT round or restate them.
{findings_json}

CROSS-BIOMARKER PATTERNS
{patterns_block}

KNOWLEDGE PASSAGES (use only what is relevant; reference by [source]):
{context}

YOUR TASK
Write a structured report for this person, in plain language, with EXACTLY these
sections in this order. Each section appears at most once.

1. Overall Health Summary — 3 to 5 sentences. Use only the findings above.
   START by mentioning what is HEALTHY first (anything with severity=normal),
   THEN explain what is borderline or abnormal. Patients deserve to see what's
   working before what isn't. Use the actual values from the findings list.
2. What's Going Well — 2 to 4 short bullets naming the biomarker groups
   (kidney function, liver enzymes, thyroid, etc.) where ALL findings were
   normal. No citation required for this section since it just mirrors the
   deterministic findings.
3. Diet Suggestions — 3 to 5 short bullets. EACH bullet MUST end with a citation
   in the form `[source: <Source name from a passage above>]`. Bullets without
   a citation will be DROPPED from the report.
4. Exercise Suggestions — 3 to 5 short bullets. SAME citation rule as Diet.
5. What to Recheck — 3 to 5 short bullets.
6. When to See a Doctor — only items with severity=critical or escalate=true.
   If none, write a single line: "No urgent items in this panel."

HARD RULES — these are enforced by post-processing; violations cause text to
be silently stripped:
  * Use ONLY values from "DETERMINISTIC FINDINGS" above. Do NOT invent or
    substitute numbers. Any sentence with a clinical-unit number that isn't
    in the findings or knowledge passages will be DROPPED.
  * Every Diet and Exercise bullet must end with `[source: <name>]` — uncited
    bullets get DROPPED.
  * Never say "you have X". Never recommend or name medications.
  * Use the exact severities and labels from the findings list.
  * If a section has nothing to write, output exactly: "No items in this category." once.
  * After section 5, stop immediately. Do not repeat sections. Do not write a new "Key
    Findings" or "Summary" block.
  * End your output with this exact line, by itself, on its own line:
    END_OF_REPORT
"""

SAFETY_REVIEW_PROMPT = """\
Read the draft report below and rewrite ONLY where it violates these rules:
  * No diagnostic phrasing ("you have X", "this means you have Y", "you suffer from").
  * No medication recommendations or names.
  * Urgent findings must be presented as such.
Return the corrected report only — do not add commentary.

Draft:
{draft}
"""
