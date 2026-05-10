# Health Checkup — Frontend Integration

Everything the AI returns is JSON. This doc shows the shape and how to consume it from the Uplyfe Laravel/Blade frontend.

## Endpoints (from the frontend's point of view)

All paths below are on the **Laravel** side. Laravel forwards to the Python gateway internally, so the frontend never talks to port 8000 directly.

| Method | Path                                | Auth     | Purpose                                                    |
| ------ | ----------------------------------- | -------- | ---------------------------------------------------------- |
| GET    | `/api/ai/health`                    | none     | Liveness — gateway up? Ollama up?                          |
| GET    | `/api/ai/health-checkup/sample`     | none     | Run AI against bundled sample lab (great for frontend dev) |
| GET    | `/api/ai/health-checkup/schema`     | none     | Full JSON Schema of the response                           |
| POST   | `/api/ai/health-checkup/manual`     | sanctum  | Analyze a user-submitted lab panel (JSON)                  |
| POST   | `/api/ai/health-checkup/upload`     | sanctum  | Upload a lab PDF / image, get analysis back                |

## Quick test

```bash
# Confirm the gateway is up
curl http://localhost/api/ai/health

# Get a real AI-generated report for the bundled sample lab
curl http://localhost/api/ai/health-checkup/sample | jq
```

The first call to `/sample` will be slow (Ollama loads `gemma4:26b` into memory). Subsequent calls are fast. Pass `?use_llm=false` for an instant deterministic-only report.

## Response shape (`FinalReport`)

```jsonc
{
  "generated_at": "2026-05-09T07:12:33Z",
  "panel": { /* echoed input lab panel */ },
  "overall_severity": "abnormal",          // "normal" | "borderline" | "abnormal" | "critical"
  "summary": "Several lipid markers are above target and vitamin D is low...",

  "abnormal_findings": [
    {
      "biomarker": "ldl",
      "value": 160,
      "unit": "mg/dL",
      "severity": "abnormal",
      "label": "High LDL",
      "rationale": "LDL ≥ 160 mg/dL is in the high range...",
      "source": "AHA/ACC 2018 Cholesterol Guideline",
      "escalate": false,
      "related_topics": ["lipids"]
    }
    // ...
  ],

  "critical_findings": [],                 // same Finding shape, urgent items
  "pattern_findings": [],                  // cross-biomarker patterns (e.g. "metabolic syndrome")
  "pattern_notes": [],                     // back-compat: pattern labels only

  "diet_advice":      ["...", "..."],
  "exercise_advice":  ["...", "..."],
  "recheck_advice":   ["...", "..."],
  "when_to_see_doctor": ["...", "..."],

  "validation_issues": [],                 // {kind, message} — data-quality notes

  "sections": [
    { "title": "What this means", "body": "Plain-English LLM-rendered prose..." }
  ],

  "disclaimer": "This report is for general information only ...",
  "sources": [
    "ADA Standards of Care 2024",
    "AHA/ACC 2018 Cholesterol Guideline"
  ]
}
```

### Severity colors (suggested mapping)

| Severity      | Suggested color  | Used by       |
| ------------- | ---------------- | ------------- |
| `normal`      | green            | findings      |
| `borderline`  | yellow / amber   | findings      |
| `abnormal`    | orange           | findings      |
| `critical`    | red              | findings      |

## Example: render in a Blade page

Drop this fetch into `healthcheck.php` (or a new `healthcheck.blade.php`). It pulls the sample, then renders summary + findings.

```html
<div id="hc-loading">Generating report…</div>
<div id="hc-report" class="hidden"></div>

<script>
  (async () => {
    const res = await fetch('/api/ai/health-checkup/sample', {
      headers: { 'Accept': 'application/json' },
    });
    if (!res.ok) {
      document.getElementById('hc-loading').textContent =
        'Could not load report (HTTP ' + res.status + ')';
      return;
    }
    const r = await res.json();

    const sevColor = {
      normal: 'text-green-600',
      borderline: 'text-amber-600',
      abnormal: 'text-orange-600',
      critical: 'text-red-600',
    };

    const findings = (r.abnormal_findings || [])
      .map(f => `
        <li class="py-2 border-b">
          <strong>${f.biomarker.replace(/_/g, ' ')}</strong>
          ${f.value} ${f.unit}
          <span class="${sevColor[f.severity] || ''}">— ${f.severity}</span>
          <div class="text-sm text-muted-foreground">${f.rationale}</div>
        </li>`).join('');

    document.getElementById('hc-report').innerHTML = `
      <h1 class="text-2xl font-bold">Your Health Checkup</h1>
      <p class="${sevColor[r.overall_severity]} font-semibold mt-1">
        Overall: ${r.overall_severity}
      </p>
      <p class="mt-3">${r.summary}</p>

      <h2 class="mt-6 text-lg font-semibold">Findings</h2>
      <ul>${findings || '<li>No abnormal findings.</li>'}</ul>

      <h2 class="mt-6 text-lg font-semibold">Diet</h2>
      <ul class="list-disc pl-5">
        ${(r.diet_advice || []).map(d => `<li>${d}</li>`).join('')}
      </ul>

      <h2 class="mt-6 text-lg font-semibold">Exercise</h2>
      <ul class="list-disc pl-5">
        ${(r.exercise_advice || []).map(e => `<li>${e}</li>`).join('')}
      </ul>

      <p class="mt-8 text-xs text-muted-foreground">${r.disclaimer}</p>
    `;
    document.getElementById('hc-loading').classList.add('hidden');
    document.getElementById('hc-report').classList.remove('hidden');
  })();
</script>
```

## Switching to real user input

Replace the GET to `/sample` with a POST to `/manual` carrying the user's values:

```js
const res = await fetch('/api/ai/health-checkup/manual', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-XSRF-TOKEN': csrfToken,         // sanctum
  },
  credentials: 'include',
  body: JSON.stringify({
    panel: {
      age: 26, sex: 'male', height_cm: 175, weight_kg: 82,
      values: [
        { biomarker: 'glucose_fasting', value: 110, unit: 'mg/dL' },
        { biomarker: 'ldl',             value: 160, unit: 'mg/dL' },
        // ...
      ],
    },
    use_llm: true,
    use_rag: true,
  }),
});
const report = await res.json();
```

Or for a PDF upload:

```js
const fd = new FormData();
fd.append('file', fileInput.files[0]);
fd.append('use_llm', 'true');
fd.append('use_rag', 'true');

const res = await fetch('/api/ai/health-checkup/upload', {
  method: 'POST',
  body: fd,
  credentials: 'include',
  headers: { 'X-XSRF-TOKEN': csrfToken },
});
const report = await res.json();
```

The response shape is identical in all three cases (`/sample`, `/manual`, `/upload`).

## Available biomarkers (input side)

`glucose_fasting`, `glucose_random`, `glucose_postprandial`, `hba1c`, `total_cholesterol`, `ldl`, `hdl`, `triglycerides`, `non_hdl`, `alt`, `ast`, `alp`, `ggt`, `bilirubin_total`, `bilirubin_direct`, `albumin`, `creatinine`, `bun`, `egfr`, `uric_acid`, `hemoglobin`, `hematocrit`, `rbc`, `wbc`, `platelets`, `mcv`, `tsh`, `free_t4`, `free_t3`, `crp`, `esr`, `sodium`, `potassium`, `chloride`, `calcium`, `vitamin_d_25oh`, `vitamin_b12`, `bmi`, `waist_cm`, `bp_systolic`, `bp_diastolic`.
