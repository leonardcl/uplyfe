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

                <a href="/health-check"
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
                        <p class="text-xs text-muted-foreground truncate">Free Plan</p>
                    </div>
                    <button
                        class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:settings" class="text-lg"></iconify-icon>
                    </button>
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
                    <button onclick="history.back()"
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:arrow-left" class="text-xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Full Health Analysis</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:download" class="text-xl"></iconify-icon>
                    </button>
                    <button
                        class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                        <iconify-icon icon="lucide:share" class="text-base"></iconify-icon>
                        Share Report
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-6xl mx-auto space-y-8">

                    <!-- Report Header -->
                    <section class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div>
                                <h2 class="text-2xl font-heading font-bold mb-2">Blood Work Analysis - October 24, 2023</h2>
                                <p class="text-muted-foreground text-sm mb-4">
                                    Comprehensive analysis of your latest blood work report with AI-powered insights and recommendations.
                                </p>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:file-type-pdf" class="text-red-500"></iconify-icon>
                                        Blood_Work_2023.pdf
                                    </span>
                                    <span class="text-muted-foreground">•</span>
                                    <span>2.4 MB</span>
                                    <span class="text-muted-foreground">•</span>
                                    <span class="flex items-center gap-1">
                                        <iconify-icon icon="lucide:check-circle-2" class="text-tertiary"></iconify-icon>
                                        Analyzed
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-center">
                                    <div class="text-3xl font-heading font-extrabold text-tertiary">92%</div>
                                    <div class="text-xs text-muted-foreground">Overall Health Score</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Detailed Biomarkers -->
                    <section>
                        <h2 class="text-xl font-heading font-bold mb-6 flex items-center gap-2">
                            <iconify-icon icon="lucide:activity" class="text-primary"></iconify-icon>
                            Detailed Biomarkers
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Cholesterol Panel -->
                            <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                                <h3 class="font-bold mb-4 flex items-center gap-2">
                                    <iconify-icon icon="lucide:heart" class="text-blue-500"></iconify-icon>
                                    Cholesterol Panel
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Total Cholesterol</span>
                                        <span class="font-semibold">175 mg/dL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-blue-500 w-[70%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Optimal range: <125-200 mg/dL</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">HDL (Good)</span>
                                        <span class="font-semibold">55 mg/dL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-[85%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Optimal: >40 mg/dL</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">LDL (Bad)</span>
                                        <span class="font-semibold">95 mg/dL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-yellow-500 w-[60%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Optimal: <100 mg/dL</p>
                                </div>
                            </div>

                            <!-- Blood Sugar -->
                            <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                                <h3 class="font-bold mb-4 flex items-center gap-2">
                                    <iconify-icon icon="lucide:droplet" class="text-red-500"></iconify-icon>
                                    Blood Sugar
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Fasting Glucose</span>
                                        <span class="font-semibold">92 mg/dL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-[80%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Normal: 70-99 mg/dL</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">HbA1c</span>
                                        <span class="font-semibold">5.2%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-[75%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Normal: <5.7%</p>
                                </div>
                            </div>

                            <!-- Vitamins & Minerals -->
                            <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                                <h3 class="font-bold mb-4 flex items-center gap-2">
                                    <iconify-icon icon="lucide:sun" class="text-yellow-500"></iconify-icon>
                                    Vitamins & Minerals
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Vitamin D</span>
                                        <span class="font-semibold">22 ng/mL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-yellow-500 w-[40%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Optimal: 30-50 ng/mL</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Vitamin B12</span>
                                        <span class="font-semibold">450 pg/mL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-[90%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Normal: 200-900 pg/mL</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">Iron</span>
                                        <span class="font-semibold">85 mcg/dL</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-muted overflow-hidden">
                                        <div class="h-full bg-tertiary w-[85%] rounded-full"></div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Normal: 60-170 mcg/dL</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- AI Recommendations -->
                    <section class="bg-gradient-to-r from-primary/5 to-tertiary/5 rounded-3xl border border-primary/20 p-8">
                        <h2 class="text-xl font-heading font-bold mb-6 flex items-center gap-2">
                            <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                            AI-Powered Recommendations
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-card rounded-2xl p-6 border border-border">
                                <h3 class="font-bold mb-4 text-tertiary">Immediate Actions</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3">
                                        <iconify-icon icon="lucide:sun" class="text-yellow-500 mt-0.5"></iconify-icon>
                                        <div>
                                            <p class="font-medium">Increase Vitamin D Intake</p>
                                            <p class="text-sm text-muted-foreground">Consider 2000 IU daily supplement and 15-20 min sun exposure.</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <iconify-icon icon="lucide:apple" class="text-tertiary mt-0.5"></iconify-icon>
                                        <div>
                                            <p class="font-medium">Maintain Current Diet</p>
                                            <p class="text-sm text-muted-foreground">Your blood sugar levels are excellent. Continue balanced nutrition.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="bg-card rounded-2xl p-6 border border-border">
                                <h3 class="font-bold mb-4 text-blue-600">Long-term Monitoring</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-start gap-3">
                                        <iconify-icon icon="lucide:activity" class="text-blue-500 mt-0.5"></iconify-icon>
                                        <div>
                                            <p class="font-medium">Cholesterol Check</p>
                                            <p class="text-sm text-muted-foreground">Schedule next lipid panel in 6 months.</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <iconify-icon icon="lucide:calendar" class="text-purple-500 mt-0.5"></iconify-icon>
                                        <div>
                                            <p class="font-medium">Regular Monitoring</p>
                                            <p class="text-sm text-muted-foreground">Continue quarterly blood work to track Vitamin D levels.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Trends Chart -->
                    <section class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <h2 class="text-xl font-heading font-bold mb-6 flex items-center gap-2">
                            <iconify-icon icon="lucide:trending-up" class="text-primary"></iconify-icon>
                            Health Trends (Last 12 Months)
                        </h2>
                        <div class="h-80">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </section>

                </div>
            </div>
        </main>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());
        });

        // Trends Chart
        const ctx = document.getElementById('trendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Oct 2022', 'Jan 2023', 'Apr 2023', 'Jul 2023', 'Oct 2023'],
                datasets: [{
                    label: 'Cholesterol (mg/dL)',
                    data: [185, 180, 178, 175, 175],
                    borderColor: '#3b82f6',
                    backgroundColor: '#3b82f640',
                    tension: 0.4
                }, {
                    label: 'Blood Sugar (mg/dL)',
                    data: [98, 95, 94, 93, 92],
                    borderColor: '#10b981',
                    backgroundColor: '#10b98140',
                    tension: 0.4
                }, {
                    label: 'Vitamin D (ng/mL)',
                    data: [18, 20, 21, 22, 22],
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b40',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    </script>
</body>

</html>