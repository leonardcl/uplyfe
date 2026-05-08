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
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                        class="w-10 h-10 rounded-full border border-border">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">Sarah Jenkins</p>
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

                                <div class="border-2 border-dashed border-border rounded-2xl p-8 flex flex-col items-center justify-center bg-background/50 hover:bg-muted/50 hover:border-primary/50 transition-all cursor-pointer group/drop"
                                    id="drop-zone">
                                    <input type="file" id="file-input" accept=".pdf,.jpg,.png" class="hidden">
                                    <div
                                        class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4 group-hover/drop:scale-110 transition-transform duration-300">
                                        <iconify-icon icon="lucide:file-up" class="text-3xl"></iconify-icon>
                                    </div>
                                    <p class="font-medium text-sm mb-1">Drag & drop your file here</p>
                                    <p class="text-xs text-muted-foreground mb-4">PDF, JPG, PNG up to 10MB</p>
                                    <button
                                        class="bg-card border border-border px-5 py-2 rounded-full text-sm font-semibold shadow-sm hover:bg-muted transition-colors"
                                        id="browse-btn">
                                        Browse Files
                                    </button>
                                </div>
                            </div>

                            <!-- Analysis Status Card (Simulating processing) -->
                            <div
                                class="w-full md:w-80 bg-background rounded-2xl p-6 border border-border shadow-sm flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-bold text-sm">Last Upload Status</h3>
                                    <span
                                        class="px-2 py-1 rounded-md bg-tertiary/20 text-tertiary text-xs font-bold">Analyzed</span>
                                </div>

                                <div class="flex items-center gap-4 p-3 rounded-xl bg-card border border-border">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                                        <iconify-icon icon="lucide:file-type-pdf" class="text-xl"></iconify-icon>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate">Blood_Work_2023.pdf</p>
                                        <p class="text-xs text-muted-foreground">Oct 24, 2023 • 2.4 MB</p>
                                    </div>
                                    <iconify-icon icon="lucide:check-circle-2"
                                        class="text-tertiary text-xl"></iconify-icon>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-xs font-medium">
                                        <span class="text-muted-foreground">AI Translation</span>
                                        <span class="text-tertiary">100%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-full rounded-full"></div>
                                    </div>
                                </div>

                                <button
                                    class="w-full py-2 bg-primary/10 text-primary-foreground bg-primary rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors">
                                    <a href="/full-analysis"
                                        class="block w-full h-full flex items-center justify-center">View Full
                                        Analysis</a>
                                </button>
                            </div>
                        </div>
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Insight Card 1 -->
                            <div
                                class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <iconify-icon icon="lucide:activity" class="text-lg"></iconify-icon>
                                    </div>
                                    <span
                                        class="px-2 py-1 rounded-md bg-blue-100 text-blue-700 text-xs font-bold">Optimal</span>
                                </div>
                                <h3 class="font-bold mb-1">Cholesterol Levels</h3>
                                <p class="text-2xl font-heading font-extrabold mb-1">175 <span
                                        class="text-sm font-medium text-muted-foreground">mg/dL</span></p>
                                <p class="text-xs text-muted-foreground mb-4">Total cholesterol is within healthy range.
                                    HDL is excellent.</p>
                                <div
                                    class="mt-auto pt-4 border-t border-border flex items-center gap-2 text-xs font-medium text-muted-foreground">
                                    <iconify-icon icon="lucide:arrow-down-right" class="text-tertiary"></iconify-icon>
                                    <span class="text-tertiary">Down 5%</span> from last checkup
                                </div>
                            </div>

                            <!-- Insight Card 2 -->
                            <div
                                class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                        <iconify-icon icon="lucide:sun" class="text-lg"></iconify-icon>
                                    </div>
                                    <span
                                        class="px-2 py-1 rounded-md bg-yellow-100 text-yellow-700 text-xs font-bold">Attention</span>
                                </div>
                                <h3 class="font-bold mb-1">Vitamin D</h3>
                                <p class="text-2xl font-heading font-extrabold mb-1">22 <span
                                        class="text-sm font-medium text-muted-foreground">ng/mL</span></p>
                                <p class="text-xs text-muted-foreground mb-4">Levels are slightly low. AI recommends
                                    increased sun exposure and dietary supplements.</p>
                                <div
                                    class="mt-auto pt-4 border-t border-border flex items-center gap-2 text-xs font-medium text-muted-foreground">
                                    <iconify-icon icon="lucide:minus" class="text-yellow-600"></iconify-icon>
                                    <span class="text-yellow-600">Unchanged</span> from last checkup
                                </div>
                            </div>

                            <!-- Insight Card 3 -->
                            <div
                                class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                        <iconify-icon icon="lucide:droplet" class="text-lg"></iconify-icon>
                                    </div>
                                    <span
                                        class="px-2 py-1 rounded-md bg-tertiary/20 text-tertiary text-xs font-bold">Improved</span>
                                </div>
                                <h3 class="font-bold mb-1">Blood Sugar (Fasting)</h3>
                                <p class="text-2xl font-heading font-extrabold mb-1">92 <span
                                        class="text-sm font-medium text-muted-foreground">mg/dL</span></p>
                                <p class="text-xs text-muted-foreground mb-4">Perfectly within the normal range. Great
                                    job maintaining a balanced diet.</p>
                                <div
                                    class="mt-auto pt-4 border-t border-border flex items-center gap-2 text-xs font-medium text-muted-foreground">
                                    <iconify-icon icon="lucide:arrow-down-right" class="text-tertiary"></iconify-icon>
                                    <span class="text-tertiary">Down 12%</span> from last checkup
                                </div>
                            </div>
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
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const browseBtn = document.getElementById('browse-btn');

        // Browse button click
        browseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.click();
        });

        // Drop zone click
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop events
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

        function handleFile(file) {
            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a PDF, JPG, or PNG file.');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('File size must be less than 10MB.');
                return;
            }

            // Here you would typically upload the file to the server
            console.log('File selected:', file.name);
            alert(`File "${file.name}" selected successfully! Upload functionality would be implemented here.`);
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