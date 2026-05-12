<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Uplyfe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&family=Fira+Code:wght@300..700&family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
    <style type="text/tailwindcss">
        @import "tailwindcss";
      @theme inline {
        --color-background: var(--background);
        --color-foreground: var(--foreground);
        --color-primary: var(--primary);
        --color-primary-foreground: var(--primary-foreground);
        --color-secondary: var(--secondary);
        --color-secondary-foreground: var(--secondary-foreground);
        --color-tertiary: var(--tertiary);
        --color-muted: var(--muted);
        --color-muted-foreground: var(--muted-foreground);
        --color-accent: var(--accent);
        --color-destructive: var(--destructive);
        --color-card: var(--card);
        --color-card-foreground: var(--card-foreground);
        --color-border: var(--border);
        --color-input: var(--input);
        --color-ring: var(--ring);
        --radius-sm: calc(var(--radius) - 4px);
        --radius-md: calc(var(--radius) - 2px);
        --radius-lg: var(--radius);
        --font-family-sans: var(--font-sans);
        --font-family-heading: var(--font-heading);
      }
      :root { --border: #e2e8f0;
        --accent: #f1f5f9;
        --input: #e2e8f0;
        --card-foreground: #0f172a;
        --ring: #90ee90;
        --muted-foreground: #64748b;
        --primary-foreground: #0f172a;
        --tertiary: #10b981;
        --foreground: #0f172a;
        --muted: #f1f5f9;
        --background: #f8fafc;
        --secondary-foreground: #0f172a;
        --font-heading: Inter, sans-serif;
        --card: #ffffff;
        --font-sans: Inter, sans-serif;
        --primary: #90ee90;
        --destructive: #ef4444;
        --radius: 1rem;
        --secondary: #e2e8f0; }
    </style>
</head>

<body>
    <div class="min-h-screen w-full bg-background flex flex-col md:flex-row relative font-sans text-foreground">

        <!-- Sidebar Navigation -->
        <aside id="mobile-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 -translate-x-full md:relative md:translate-x-0 md:flex bg-card border-r border-border flex-shrink-0 flex-col transition-transform duration-300 shadow-xl md:shadow-none">
            <div class="h-16 flex items-center px-6 border-b border-border">
                <div class="flex items-center gap-2 cursor-pointer group">
                    <div
                        class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-sm">
                        <iconify-icon icon="lucide:leaf" class="text-lg"></iconify-icon>
                    </div>
                    <span class="text-xl font-heading font-bold tracking-tight">Uplyfe</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
                <p class="px-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Main Menu</p>

                <a href="/"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:layout-dashboard" class="text-lg"></iconify-icon>
                    Dashboard
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
                    <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                    Health Checkup
                </a>

                <a href="/recipe"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:apple" class="text-lg"></iconify-icon>
                    Nutrition & Recipes
                </a>

                <a href="/exercise"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:dumbbell" class="text-lg"></iconify-icon>
                    Exercise Routine
                </a>

                <a href="/chat"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:bot" class="text-lg"></iconify-icon>
                    AI Assistant
                </a>
            </div>

            <div class="p-4 border-t border-border">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="{{ $user->profile_photo ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="User"
                        class="w-10 h-10 rounded-full border border-border">
                    <div class="flex-1 min-w-0">
                        <p id="sidebar-user-name" class="text-sm font-bold truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                    </div>
                    <a href="/profile"
                        class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted transition-colors inline-flex items-center justify-center">
                        <iconify-icon icon="lucide:settings" class="text-lg"></iconify-icon>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"
            onclick="toggleSidebar(false)"></div>
            
        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Health Checkup</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:bell" class="text-xl"></iconify-icon>
                        <span
                            class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-destructive border border-card"></span>
                    </button>
                    <button
                        class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all flex items-center gap-2"
                        onclick="openNewReportModal()">
                        <iconify-icon icon="lucide:upload-cloud" class="text-base"></iconify-icon>
                        New Report
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-5xl mx-auto space-y-8">

                    <!-- Upload Section -->
                    <section
                        class="bg-card rounded-3xl border border-border p-8 shadow-sm relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>

                        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1">
                                <h2 class="text-2xl font-heading font-bold mb-2">Upload Medical Report</h2>
                                <p class="text-muted-foreground text-sm mb-6 max-w-md leading-relaxed">
                                    Upload your latest blood work or health checkup PDF. Our AI will analyze the data,
                                    translate complex terms, and update your health profile.
                                </p>

                                <div class="relative border-2 border-dashed border-border rounded-2xl p-8 bg-background/50 hover:bg-muted/50 hover:border-primary/50 transition-all group/drop"
                                    id="drop-zone">
                                    <!-- The file input itself fills the drop-zone, transparent.
                                         Clicking ANYWHERE in the box opens the picker — no JS,
                                         no label-for indirection. -->
                                    <input type="file" id="file-input" accept=".pdf,.jpg,.png"
                                        style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:10;">
                                    <div id="drop-zone-idle" class="flex flex-col items-center pointer-events-none relative">
                                        <div
                                            class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 group-hover/drop:scale-110 transition-transform duration-300">
                                            <iconify-icon icon="lucide:file-up" class="text-3xl"></iconify-icon>
                                        </div>
                                        <p class="font-medium text-sm mb-1">Drag &amp; drop your file here</p>
                                        <p class="text-xs text-muted-foreground mb-4">PDF, JPG, PNG up to 10MB</p>
                                        <span
                                            class="bg-card border border-border px-5 py-2 rounded-full text-sm font-semibold shadow-sm">
                                            Browse Files
                                        </span>
                                    </div>
                                    <div id="drop-zone-busy" class="hidden flex-col items-center text-center pointer-events-none relative">
                                        <div
                                            class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                                            <iconify-icon icon="lucide:loader-2" class="text-3xl animate-spin"></iconify-icon>
                                        </div>
                                        <p class="font-medium text-sm mb-1" id="hc-status-title">Uploading…</p>
                                        <p class="text-xs text-muted-foreground" id="hc-status-detail">Sending file to AI gateway</p>
                                    </div>
                                </div>
                                <div id="hc-error" class="hidden mt-4 p-4 rounded-xl border border-red-300 bg-red-50 text-sm text-red-700"></div>
                            </div>

                            <!-- Last upload card. Loads from /api/health-reports on page render
                                 and updates after every fresh upload. Hidden until we have real data. -->
                            <div id="hc-last-upload-card"
                                class="w-full md:w-80 bg-background rounded-2xl p-6 border border-border shadow-sm flex flex-col gap-4 hidden">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-bold text-sm">Last Upload Status</h3>
                                    <span id="hc-last-overall-badge"
                                        class="px-2 py-1 rounded-md text-xs font-bold"></span>
                                </div>

                                <div class="flex items-center gap-4 p-3 rounded-xl bg-card border border-border">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                                        <iconify-icon icon="lucide:file-type-pdf" class="text-xl"></iconify-icon>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="hc-last-filename" class="text-sm font-semibold truncate"></p>
                                        <p id="hc-last-meta" class="text-xs text-muted-foreground"></p>
                                    </div>
                                    <iconify-icon icon="lucide:check-circle-2"
                                        class="text-tertiary text-xl"></iconify-icon>
                                </div>

                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between"><span class="text-muted-foreground">Biomarkers extracted</span><span id="hc-last-biocount" class="font-semibold"></span></div>
                                    <div class="flex justify-between"><span class="text-muted-foreground">Outside reference range</span><span id="hc-last-abncount" class="font-semibold"></span></div>
                                </div>

                                <button id="hc-last-view-btn" type="button"
                                    class="w-full py-2 bg-primary text-primary-foreground rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors">
                                    View this report
                                </button>
                            </div>

                            <!-- Empty-state card when there are no past uploads. -->
                            <div id="hc-no-history-card"
                                class="w-full md:w-80 bg-background rounded-2xl p-6 border border-dashed border-border text-center text-sm text-muted-foreground flex items-center justify-center min-h-[150px]">
                                Your past uploads will appear here once you analyze your first lab report.
                            </div>
                        </div>
                    </section>

                    <!-- Live AI Result (populated by handleFile) -->
                    <section id="hc-results" class="hidden bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                            <h2 class="text-xl font-heading font-bold flex items-center gap-2">
                                <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                                Your AI Health Report
                            </h2>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <span id="hc-file-name"></span>
                                <span id="hc-overall-badge" class="px-2 py-1 rounded-md text-xs font-bold"></span>
                            </div>
                        </div>

                        <p id="hc-summary" class="text-sm leading-relaxed mb-6"></p>

                        <div id="hc-critical-block" class="hidden mb-6 p-4 rounded-xl border border-red-300 bg-red-50">
                            <h3 class="font-bold text-sm text-red-800 mb-2 flex items-center gap-2">
                                <iconify-icon icon="lucide:alert-triangle"></iconify-icon>
                                Critical — please consult a clinician
                            </h3>
                            <ul id="hc-critical" class="space-y-1 text-sm text-red-800"></ul>
                        </div>

                        <!-- "What's going well" — biomarker groups whose findings all came back normal. -->
                        <div id="hc-healthy-block" class="hidden mb-6 p-4 rounded-xl border border-tertiary/30 bg-tertiary/5">
                            <h3 class="font-bold text-sm mb-2 flex items-center gap-2 text-tertiary">
                                <iconify-icon icon="lucide:check-circle-2"></iconify-icon>
                                What's going well
                            </h3>
                            <ul id="hc-healthy" class="list-disc pl-5 space-y-1 text-sm"></ul>
                        </div>

                        <div id="hc-abnormal-block" class="hidden mb-6">
                            <h3 class="font-bold text-sm mb-2">Findings outside reference ranges</h3>
                            <ul id="hc-abnormal" class="divide-y divide-border border border-border rounded-xl overflow-hidden"></ul>
                        </div>

                        <!-- Complete table of every biomarker the system extracted, with status colour coding. -->
                        <details id="hc-allvalues-block" class="hidden mb-6 group/all border border-border rounded-xl">
                            <summary class="cursor-pointer p-4 flex items-center gap-2 text-sm font-bold hover:bg-muted/40 select-none">
                                <iconify-icon icon="lucide:list" class="text-muted-foreground"></iconify-icon>
                                All extracted values
                                <span class="ml-auto text-xs font-normal text-muted-foreground" id="hc-allvalues-count"></span>
                                <iconify-icon icon="lucide:chevron-down" class="ml-1 transition-transform group-open/all:rotate-180"></iconify-icon>
                            </summary>
                            <div class="overflow-x-auto border-t border-border">
                                <table class="w-full text-sm">
                                    <thead class="bg-muted/40 text-xs text-muted-foreground">
                                        <tr>
                                            <th class="text-left px-4 py-2 font-semibold">Biomarker</th>
                                            <th class="text-right px-4 py-2 font-semibold">Value</th>
                                            <th class="text-left px-4 py-2 font-semibold">Reference</th>
                                            <th class="text-left px-4 py-2 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="hc-allvalues"></tbody>
                                </table>
                            </div>
                        </details>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div id="hc-diet-block" class="hidden">
                                <h3 class="font-bold text-sm mb-2 flex items-center gap-2">
                                    <iconify-icon icon="lucide:salad" class="text-tertiary"></iconify-icon>
                                    Diet
                                </h3>
                                <ul id="hc-diet" class="list-disc pl-5 space-y-1 text-sm"></ul>
                            </div>
                            <div id="hc-exercise-block" class="hidden">
                                <h3 class="font-bold text-sm mb-2 flex items-center gap-2">
                                    <iconify-icon icon="lucide:dumbbell" class="text-tertiary"></iconify-icon>
                                    Exercise
                                </h3>
                                <ul id="hc-exercise" class="list-disc pl-5 space-y-1 text-sm"></ul>
                            </div>
                        </div>

                        <div id="hc-doctor-block" class="hidden mb-6">
                            <h3 class="font-bold text-sm mb-2">When to see a doctor</h3>
                            <ul id="hc-doctor" class="list-disc pl-5 space-y-1 text-sm"></ul>
                        </div>

                        <div id="hc-recheck-block" class="hidden mb-6">
                            <h3 class="font-bold text-sm mb-2">What to recheck</h3>
                            <ul id="hc-recheck" class="list-disc pl-5 space-y-1 text-sm"></ul>
                        </div>

                        <p id="hc-disclaimer" class="text-xs text-muted-foreground border-t border-border pt-4"></p>
                    </section>

                    <!-- AI Insights Section -->
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-heading font-bold flex items-center gap-2">
                                <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                                Key Health Insights
                            </h2>
                            <select
                                class="bg-card border border-border text-sm rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-primary shadow-sm">
                                <option>Latest Report (Oct 24)</option>
                                <option>Previous (Jun 12)</option>
                            </select>
                        </div>

                        <!-- Hero cards. Populated from report.key_insights when an upload completes;
                             stay hidden until then so we never show stale mock numbers. -->
                        <div id="hc-insights-grid" class="grid grid-cols-1 md:grid-cols-3 gap-6 hidden"></div>
                        <div id="hc-insights-empty" class="bg-card rounded-2xl p-6 border border-dashed border-border shadow-sm text-center text-sm text-muted-foreground">
                            Upload a lab report to see your top three health insights here — cholesterol, blood sugar, and vitamin D.
                        </div>

                        <!-- Summary Column -->
                        <div class="mt-6">
                            <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary/15 flex items-center justify-center text-primary-foreground">
                                        <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold mb-2">Summary</h3>
                                        <p class="text-sm text-muted-foreground leading-relaxed">
                                            Overall health trend is positive. Cholesterol and fasting blood sugar are in a healthy range,
                                            while Vitamin D still needs improvement. Continue your current diet and exercise routine,
                                            and add daily sunlight exposure or Vitamin D-rich foods to close the gap.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Action Plan Generator -->
                    <section
                        class="bg-gradient-to-br from-primary/10 to-background rounded-3xl border border-primary/20 p-8 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <h3 class="text-xl font-heading font-bold mb-2">Update Your Health Plan</h3>
                            <p class="text-sm text-muted-foreground max-w-lg">
                                Based on your latest report, our AI can regenerate your nutrition and exercise routines
                                to perfectly match your current health status.
                            </p>
                        </div>
                        <button
                            class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all whitespace-nowrap flex items-center gap-2"
                            onclick="openNewPlanModal()">
                            <iconify-icon icon="lucide:refresh-cw" class="text-lg"></iconify-icon>
                            Generate New Plan
                        </button>
                    </section>
                </div>
            </div>
        </main>

        <!-- New Report Modal -->
        <div id="new-report-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeNewReportModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Create New Report</h3>
                        <button onclick="closeNewReportModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Report Type</label>
                            <select
                                class="w-full bg-background border border-border text-sm rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                                <option>Blood Work Analysis</option>
                                <option>General Health Check</option>
                                <option>Cardiac Assessment</option>
                                <option>Metabolic Panel</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Upload Files</label>
                            <div class="border-2 border-dashed border-border rounded-xl p-6 text-center bg-background/50 hover:bg-muted/50 transition-colors cursor-pointer"
                                onclick="document.getElementById('modal-file-input').click()">
                                <iconify-icon icon="lucide:file-up" class="text-2xl text-primary mb-2"></iconify-icon>
                                <p class="text-sm font-medium">Click to upload files</p>
                                <p class="text-xs text-muted-foreground">PDF, JPG, PNG up to 10MB</p>
                            </div>
                            <input type="file" id="modal-file-input" multiple accept=".pdf,.jpg,.png" class="hidden">
                        </div>

                        <div class="flex gap-3">
                            <button onclick="closeNewReportModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="submitNewReport()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                                Create Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate New Plan Modal -->
        <div id="new-plan-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeNewPlanModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Generate New Health Plan</h3>
                        <button onclick="closeNewPlanModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-primary/10 rounded-2xl p-4 border border-primary/20">
                            <div class="flex items-center gap-3 mb-2">
                                <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                                <span class="font-semibold text-sm">AI Analysis Complete</span>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Based on your latest blood work, we'll create personalized nutrition and exercise
                                recommendations.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-card border border-border rounded-xl p-4 text-center">
                                <iconify-icon icon="lucide:apple" class="text-2xl text-tertiary mb-2"></iconify-icon>
                                <h4 class="font-semibold text-sm mb-1">Nutrition Plan</h4>
                                <p class="text-xs text-muted-foreground">7-day meal plan</p>
                            </div>
                            <div class="bg-card border border-border rounded-xl p-4 text-center">
                                <iconify-icon icon="lucide:dumbbell" class="text-2xl text-blue-500 mb-2"></iconify-icon>
                                <h4 class="font-semibold text-sm mb-1">Exercise Routine</h4>
                                <p class="text-xs text-muted-foreground">Custom workout plan</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Plan Preferences</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" checked class="rounded border-border">
                                    <span class="text-sm">Include dietary restrictions</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" checked class="rounded border-border">
                                    <span class="text-sm">Consider current fitness level</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" class="rounded border-border">
                                    <span class="text-sm">Focus on weight management</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button onclick="closeNewPlanModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="generateNewPlan()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                                <iconify-icon icon="lucide:refresh-cw" class="text-base"></iconify-icon>
                                Generate Plan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle functionality
        function toggleSidebar(open) {
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileBackdrop = document.getElementById('mobile-sidebar-backdrop');
            const isOpen = mobileSidebar.classList.contains('translate-x-0');
            const shouldOpen = typeof open === 'boolean' ? open : !isOpen;

            if (shouldOpen) {
                mobileSidebar.classList.remove('-translate-x-full');
                mobileSidebar.classList.add('translate-x-0');
                mobileBackdrop.classList.remove('hidden');
            } else {
                mobileSidebar.classList.remove('translate-x-0');
                mobileSidebar.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());
        });

        // File upload functionality
        // The drop-zone is a <label for="file-input"> so clicking it natively
        // opens the file picker — no JS click handler needed. We only attach
        // listeners for: file selection, and drag-and-drop.
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');

        // File input change
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop events (label elements support these the same as divs).
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                handleFile(file);
            }
        }

        const HC_UPLOAD_URL = '/api/ai/health-checkup/upload';
        const HC_HISTORY_URL = '/api/health-reports';

        // --- Last-upload card state (replaces the old hardcoded mock) ---

        function fmtDateAgo(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            const now = Date.now();
            const days = Math.floor((now - d.getTime()) / (1000 * 60 * 60 * 24));
            if (days < 1) return 'today';
            if (days === 1) return 'yesterday';
            if (days < 30) return `${days} days ago`;
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function showLastUploadCard(report) {
            // report shape: { id, original_filename, original_size_bytes,
            //   overall_severity, biomarker_count, abnormal_count,
            //   critical_count, summary, created_at }
            const card = document.getElementById('hc-last-upload-card');
            const empty = document.getElementById('hc-no-history-card');
            if (!report) {
                card.classList.add('hidden');
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');
            card.classList.remove('hidden');

            document.getElementById('hc-last-filename').textContent = report.original_filename || '—';
            const sizeKb = report.original_size_bytes ? (report.original_size_bytes / 1024).toFixed(0) + ' KB' : '';
            const ago = fmtDateAgo(report.created_at);
            document.getElementById('hc-last-meta').textContent = [ago, sizeKb].filter(Boolean).join(' • ');

            const overall = report.overall_severity || 'normal';
            const badge = document.getElementById('hc-last-overall-badge');
            badge.textContent = overall;
            badge.className = 'px-2 py-1 rounded-md text-xs font-bold ' + severityClasses(overall);

            document.getElementById('hc-last-biocount').textContent = report.biomarker_count ?? 0;
            document.getElementById('hc-last-abncount').textContent =
                (report.abnormal_count ?? 0) + (report.critical_count ? ` (+${report.critical_count} critical)` : '');

            const btn = document.getElementById('hc-last-view-btn');
            btn.onclick = async () => {
                // Reload the full report and re-render the result section.
                try {
                    const r = await fetch(`${HC_HISTORY_URL}/${report.id}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    const data = await r.json();
                    renderReport(
                        { name: report.original_filename || 'report', size: report.original_size_bytes || 0 },
                        data,
                    );
                } catch (e) {
                    showError('Could not load past report: ' + (e?.message || e));
                }
            };
        }

        async function loadLatestReportFromHistory() {
            try {
                const r = await fetch(HC_HISTORY_URL, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) {
                    showLastUploadCard(null);
                    return;
                }
                const data = await r.json();
                showLastUploadCard((data.reports || [])[0] || null);
            } catch (e) {
                showLastUploadCard(null);
            }
        }

        // Run once on page load
        loadLatestReportFromHistory();

        const $idle = () => document.getElementById('drop-zone-idle');
        const $busy = () => document.getElementById('drop-zone-busy');
        const $err  = () => document.getElementById('hc-error');
        const $res  = () => document.getElementById('hc-results');

        function setBusy(title, detail) {
            $idle().classList.add('hidden');
            $busy().classList.remove('hidden');
            $busy().classList.add('flex');
            document.getElementById('hc-status-title').textContent = title;
            document.getElementById('hc-status-detail').textContent = detail;
            $err().classList.add('hidden');
        }

        function setIdle() {
            $busy().classList.add('hidden');
            $busy().classList.remove('flex');
            $idle().classList.remove('hidden');
        }

        function showError(msg) {
            setIdle();
            $err().textContent = msg;
            $err().classList.remove('hidden');
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        function severityClasses(sev) {
            return {
                normal:     'bg-tertiary/20 text-tertiary',
                borderline: 'bg-amber-100 text-amber-700',
                abnormal:   'bg-orange-100 text-orange-700',
                critical:   'bg-red-100 text-red-700',
            }[sev] || 'bg-muted text-muted-foreground';
        }

        function renderFindingLi(f) {
            const name = escapeHtml(String(f.biomarker || '').replace(/_/g, ' '));
            const sev = escapeHtml(f.severity);
            return `
                <li class="p-3 flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <span class="font-semibold capitalize text-sm">${name}</span>
                        <span class="text-xs">
                            <span class="font-mono">${escapeHtml(f.value)} ${escapeHtml(f.unit)}</span>
                            <span class="ml-2 px-2 py-0.5 rounded ${severityClasses(f.severity)} font-bold">${sev}</span>
                        </span>
                    </div>
                    <div class="text-xs text-muted-foreground">${escapeHtml(f.label)} — ${escapeHtml(f.rationale)}</div>
                    <div class="text-[11px] text-muted-foreground italic">${escapeHtml(f.source)}</div>
                </li>`;
        }

        function fillList(elId, blockId, items, lineFn) {
            const el = document.getElementById(elId);
            const block = document.getElementById(blockId);
            if (!items || items.length === 0) {
                el.innerHTML = '';
                block.classList.add('hidden');
                return;
            }
            el.innerHTML = items.map(lineFn).join('');
            block.classList.remove('hidden');
        }

        // Map a key_insights[].status to the visual style of the badge + icon
        // colour ring on the hero cards. The keys mirror what the backend emits.
        const INSIGHT_STATUS_STYLE = {
            optimal:    { badge: 'bg-tertiary/20 text-tertiary',     ring: 'bg-tertiary/15 text-tertiary',     iconCls: 'text-tertiary',     label: 'Optimal' },
            normal:     { badge: 'bg-tertiary/20 text-tertiary',     ring: 'bg-tertiary/15 text-tertiary',     iconCls: 'text-tertiary',     label: 'Normal' },
            borderline: { badge: 'bg-amber-100 text-amber-700',      ring: 'bg-amber-100 text-amber-600',      iconCls: 'text-amber-600',    label: 'Borderline' },
            high:       { badge: 'bg-orange-100 text-orange-700',    ring: 'bg-orange-100 text-orange-600',    iconCls: 'text-orange-600',   label: 'High' },
            low:        { badge: 'bg-yellow-100 text-yellow-700',    ring: 'bg-yellow-100 text-yellow-600',    iconCls: 'text-yellow-600',   label: 'Low' },
            critical:   { badge: 'bg-red-100 text-red-700',          ring: 'bg-red-100 text-red-600',          iconCls: 'text-red-600',      label: 'Critical' },
        };

        const INSIGHT_ICON = {
            cholesterol: 'lucide:activity',
            blood_sugar: 'lucide:droplet',
            vitamin_d:   'lucide:sun',
        };

        function formatInsightValue(v) {
            // Drop trailing .0 for whole numbers, keep one decimal otherwise.
            if (v === null || v === undefined) return '';
            return Number.isInteger(v) ? String(v) : (Math.round(v * 100) / 100).toString();
        }

        function renderInsightCard(insight) {
            const style = INSIGHT_STATUS_STYLE[insight.status] || INSIGHT_STATUS_STYLE.normal;
            const icon = INSIGHT_ICON[insight.key] || 'lucide:activity';
            return `
              <div class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                <div class="flex items-center justify-between mb-4">
                  <div class="w-10 h-10 rounded-full ${style.ring} flex items-center justify-center">
                    <iconify-icon icon="${icon}" class="text-lg ${style.iconCls}"></iconify-icon>
                  </div>
                  <span class="px-2 py-1 rounded-md ${style.badge} text-xs font-bold">${escapeHtml(style.label)}</span>
                </div>
                <h3 class="font-bold mb-1">${escapeHtml(insight.label)}</h3>
                <p class="text-2xl font-heading font-extrabold mb-1">
                  ${escapeHtml(formatInsightValue(insight.value))}
                  <span class="text-sm font-medium text-muted-foreground">${escapeHtml(insight.unit)}</span>
                </p>
                <p class="text-xs text-muted-foreground mb-4">${escapeHtml(insight.summary)}</p>
              </div>`;
        }

        function renderInsightsGrid(insights) {
            const grid = document.getElementById('hc-insights-grid');
            const empty = document.getElementById('hc-insights-empty');
            if (!insights || insights.length === 0) {
                grid.innerHTML = '';
                grid.classList.add('hidden');
                empty.classList.remove('hidden');
                return;
            }
            grid.innerHTML = insights.map(renderInsightCard).join('');
            grid.classList.remove('hidden');
            empty.classList.add('hidden');
        }

        // "What's going well" — render the deterministic healthy_topics list
        // (biomarker groups whose findings all came back within range).
        function renderHealthyBlock(topics) {
            const block = document.getElementById('hc-healthy-block');
            const list = document.getElementById('hc-healthy');
            if (!topics || topics.length === 0) {
                list.innerHTML = '';
                block.classList.add('hidden');
                return;
            }
            list.innerHTML = topics.map(t => `<li>${escapeHtml(t)} — within reference range</li>`).join('');
            block.classList.remove('hidden');
        }

        // Status pill for a single biomarker row in the all-values table.
        function valueStatus(value, refLow, refHigh) {
            if (refLow == null && refHigh == null) {
                return { label: '—', cls: 'bg-muted text-muted-foreground' };
            }
            if (refLow != null && value < refLow) {
                return { label: 'low', cls: 'bg-yellow-100 text-yellow-700' };
            }
            if (refHigh != null && value > refHigh) {
                return { label: 'high', cls: 'bg-orange-100 text-orange-700' };
            }
            return { label: 'normal', cls: 'bg-tertiary/20 text-tertiary' };
        }

        function renderAllValuesTable(values) {
            const block = document.getElementById('hc-allvalues-block');
            const tbody = document.getElementById('hc-allvalues');
            const count = document.getElementById('hc-allvalues-count');
            if (!values || values.length === 0) {
                tbody.innerHTML = '';
                block.classList.add('hidden');
                return;
            }
            count.textContent = `${values.length} biomarkers`;
            tbody.innerHTML = values.map(v => {
                const status = valueStatus(v.value, v.reference_low, v.reference_high);
                const refStr = (v.reference_low != null || v.reference_high != null)
                    ? `${v.reference_low ?? '—'} – ${v.reference_high ?? '—'} ${escapeHtml(v.unit || '')}`
                    : '—';
                const orig = (v.original_value != null)
                    ? `<div class="text-[11px] text-muted-foreground italic">was ${escapeHtml(v.original_value)} ${escapeHtml(v.original_unit || '(no unit)')}</div>`
                    : '';
                const name = String(v.biomarker || '').replace(/_/g, ' ');
                return `
                    <tr class="border-t border-border">
                      <td class="px-4 py-2 align-top">
                        <div class="font-medium capitalize">${escapeHtml(name)}</div>
                        ${orig}
                      </td>
                      <td class="px-4 py-2 text-right font-mono whitespace-nowrap">
                        ${escapeHtml(v.value)} ${escapeHtml(v.unit || '')}
                      </td>
                      <td class="px-4 py-2 text-xs text-muted-foreground whitespace-nowrap">${refStr}</td>
                      <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs font-bold ${status.cls}">${status.label}</span></td>
                    </tr>`;
            }).join('');
            block.classList.remove('hidden');
        }

        function renderReport(file, report) {
            document.getElementById('hc-file-name').textContent = file.name + ' • ' + (file.size / 1024).toFixed(0) + ' KB';

            const overall = report.overall_severity || 'normal';
            const badge = document.getElementById('hc-overall-badge');
            badge.textContent = 'Overall: ' + overall;
            badge.className = 'px-2 py-1 rounded-md text-xs font-bold ' + severityClasses(overall);

            document.getElementById('hc-summary').textContent = report.summary || '';

            fillList('hc-critical', 'hc-critical-block', report.critical_findings || [],
                f => `<li>• ${escapeHtml(String(f.biomarker).replace(/_/g, ' '))} — ${escapeHtml(f.label)} (${escapeHtml(f.value)} ${escapeHtml(f.unit)})</li>`);

            fillList('hc-abnormal', 'hc-abnormal-block', report.abnormal_findings || [], renderFindingLi);

            fillList('hc-diet', 'hc-diet-block', report.diet_advice || [],
                d => `<li>${escapeHtml(d)}</li>`);
            fillList('hc-exercise', 'hc-exercise-block', report.exercise_advice || [],
                e => `<li>${escapeHtml(e)}</li>`);
            fillList('hc-doctor', 'hc-doctor-block', report.when_to_see_doctor || [],
                w => `<li>${escapeHtml(w)}</li>`);
            fillList('hc-recheck', 'hc-recheck-block', report.recheck_advice || [],
                r => `<li>${escapeHtml(r)}</li>`);

            renderInsightsGrid(report.key_insights || []);
            renderHealthyBlock(report.healthy_topics || []);
            renderAllValuesTable(report.panel?.values || []);

            document.getElementById('hc-disclaimer').textContent = report.disclaimer || '';

            $res().classList.remove('hidden');
            $res().scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        async function handleFile(file) {
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showError('Please select a PDF, JPG, or PNG file.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                showError('File size must be less than 10 MB.');
                return;
            }

            const fd = new FormData();
            fd.append('file', file);
            fd.append('use_llm', '1');
            fd.append('use_rag', '1');

            setBusy('Uploading…', `Sending ${file.name} to the AI gateway`);

            // Switch the status copy after a short delay so users know
            // the LLM step is the slow part (10–60s typical).
            const slowMsg = setTimeout(() => {
                document.getElementById('hc-status-title').textContent = 'Analyzing…';
                document.getElementById('hc-status-detail').textContent =
                    'AI is reading the report. This can take 30–90 seconds.';
            }, 1500);

            try {
                const res = await fetch(HC_UPLOAD_URL, {
                    method: 'POST',
                    body: fd,
                    headers: { 'Accept': 'application/json' },
                });
                clearTimeout(slowMsg);

                const text = await res.text();
                let data;
                try { data = text ? JSON.parse(text) : {}; } catch { data = { error: text }; }

                if (!res.ok) {
                    const detail = data.error || data.message || ('HTTP ' + res.status);
                    showError('Analysis failed: ' + detail);
                    return;
                }

                setIdle();
                renderReport(file, data);
                // Refresh the last-upload card now that the report has been
                // persisted server-side.
                loadLatestReportFromHistory();
            } catch (e) {
                clearTimeout(slowMsg);
                showError('Network error: ' + (e && e.message ? e.message : e));
            }
        }

        // Modal functions
        function openNewReportModal() {
            document.getElementById('new-report-modal').classList.remove('hidden');
        }

        function closeNewReportModal() {
            document.getElementById('new-report-modal').classList.add('hidden');
        }

        function submitNewReport() {
            // Here you would submit the form data
            alert('New report creation functionality would be implemented here.');
            closeNewReportModal();
        }

        function openNewPlanModal() {
            document.getElementById('new-plan-modal').classList.remove('hidden');
        }

        function closeNewPlanModal() {
            document.getElementById('new-plan-modal').classList.add('hidden');
        }

        function generateNewPlan() {
            // Here you would generate the new plan
            alert('New plan generation functionality would be implemented here.');
            closeNewPlanModal();
        }
    </script>
</body>

</html>