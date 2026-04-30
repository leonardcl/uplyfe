<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Screen</title>
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
        <aside
            class="hidden md:flex w-64 lg:w-72 bg-card border-r border-border flex-shrink-0 flex-col transition-all duration-300">
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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                    Health Checkup
                </a>

                <a href="/recipe"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:apple" class="text-lg"></iconify-icon>
                    Nutrition & Recipes
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
                    <iconify-icon icon="lucide:dumbbell" class="text-lg"></iconify-icon>
                    Exercise Routine
                </a>

                <a href="/chat"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:bot" class="text-lg"></iconify-icon>
                    AI Assistant
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
            <!-- Topbar -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Exercise Routine</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="bg-card border border-border px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-muted transition-colors flex items-center gap-2">
                        <iconify-icon icon="lucide:calendar"></iconify-icon>
                        Weekly View
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                <div class="max-w-5xl mx-auto space-y-8">

                    <!-- Preferences & Status -->
                    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div
                            class="lg:col-span-2 bg-card rounded-3xl border border-border p-6 sm:p-8 shadow-sm flex flex-col justify-center">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-heading font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:activity" class="text-primary"></iconify-icon>
                                    Activity Profile
                                </h2>
                                <button
                                    class="text-sm font-medium text-primary hover:text-tertiary transition-colors flex items-center gap-1">
                                    <iconify-icon icon="lucide:pencil" class="text-xs"></iconify-icon> Edit
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Fitness Level</p>
                                    <p class="font-bold text-sm">Intermediate</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Goal</p>
                                    <p class="font-bold text-sm">Cardio Health</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Available Days</p>
                                    <p class="font-bold text-sm">4 Days/Week</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Equipment</p>
                                    <p class="font-bold text-sm">Dumbbells, Mat</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gradient-to-br from-primary/10 to-background rounded-3xl border border-primary/20 p-6 shadow-sm flex flex-col justify-center">
                            <div class="flex items-start gap-3 mb-4">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground flex-shrink-0">
                                    <iconify-icon icon="lucide:stethoscope" class="text-lg"></iconify-icon>
                                </div>
                                <div>
                                    <p class="text-sm font-bold mb-1">Medical Alignment</p>
                                    <p class="text-xs text-muted-foreground leading-relaxed">AI has prioritized
                                        low-impact cardio to support healthy cholesterol levels while avoiding joint
                                        strain.</p>
                                </div>
                            </div>
                            <button
                                class="w-full bg-primary text-primary-foreground py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <iconify-icon icon="lucide:sparkles"></iconify-icon>
                                Generate New Routine
                            </button>
                        </div>
                    </section>

                    <!-- Today's Workout -->
                    <section>
                        <h2 class="text-2xl font-heading font-bold mb-6">Today's Workout: Full Body Flow</h2>

                        <div class="bg-card rounded-3xl border border-border overflow-hidden shadow-sm">
                            <div class="p-6 sm:p-8 flex flex-col md:flex-row items-center gap-8 border-b border-border">
                                <div
                                    class="w-full md:w-1/3 aspect-video bg-muted rounded-2xl relative overflow-hidden flex items-center justify-center group cursor-pointer">
                                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors">
                                    </div>
                                    <div
                                        class="w-14 h-14 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-primary shadow-lg group-hover:scale-110 transition-transform">
                                        <iconify-icon icon="lucide:play" class="text-2xl ml-1"></iconify-icon>
                                    </div>
                                </div>

                                <div class="flex-1 w-full">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-primary/10 text-primary text-xs font-bold">Low
                                            Impact</span>
                                        <span
                                            class="px-2.5 py-1 rounded-md bg-muted text-muted-foreground text-xs font-bold">45
                                            Mins</span>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Cardio & Mobility Flow</h3>
                                    <p class="text-sm text-muted-foreground mb-6">A balanced routine designed to elevate
                                        heart rate safely while improving joint mobility. Perfect for your current
                                        health markers.</p>

                                    <div class="flex gap-4">
                                        <button
                                            class="flex-1 bg-primary text-primary-foreground py-3 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                            Start Workout
                                        </button>
                                        <button
                                            class="w-12 h-12 rounded-xl border border-border bg-background flex items-center justify-center text-muted-foreground hover:text-foreground transition-colors">
                                            <iconify-icon icon="lucide:share-2"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-background p-6 sm:px-8">
                                <h4 class="font-bold text-sm mb-4 uppercase tracking-wider text-muted-foreground">
                                    Exercises (4)</h4>

                                <div class="space-y-3">
                                    <!-- Exercise 1 -->
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                1</div>
                                            <div>
                                                <p class="font-bold text-sm">Dynamic Stretching</p>
                                                <p class="text-xs text-muted-foreground">Warm up</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold">5 mins</div>
                                    </div>

                                    <!-- Exercise 2 -->
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                2</div>
                                            <div>
                                                <p class="font-bold text-sm">Dumbbell Goblet Squats</p>
                                                <p class="text-xs text-muted-foreground">3 sets x 12 reps</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold">10 mins</div>
                                    </div>

                                    <!-- Exercise 3 -->
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                3</div>
                                            <div>
                                                <p class="font-bold text-sm">Plank Variations</p>
                                                <p class="text-xs text-muted-foreground">3 sets x 45 secs</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold">8 mins</div>
                                    </div>

                                    <!-- Exercise 4 -->
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                4</div>
                                            <div>
                                                <p class="font-bold text-sm">Low-Intensity Steady State (LISS)</p>
                                                <p class="text-xs text-muted-foreground">Brisk walking or cycling</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold">22 mins</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </main>
    </div>
</body>

</html>