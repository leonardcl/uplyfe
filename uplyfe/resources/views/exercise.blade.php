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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                    Health Checkup
                </a>

                <a href="/recipe"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:apple" class="text-lg"></iconify-icon>
                    Nutrition & Recipes
                </a>

                <a href="/exercise"
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

            <div class="p-4 border-t border-border">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                        class="w-10 h-10 rounded-full border border-border">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">Sarah Jenkins</p>
                        <p class="text-xs text-muted-foreground truncate">Free Plan</p>
                    </div>
                    <a href="/profile"
                        class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted transition-colors inline-flex items-center justify-center">
                        <iconify-icon icon="lucide:settings" class="text-lg"></iconify-icon>
                    </a>
                </div>
            </div>
        </aside>
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"
            onclick="toggleSidebar(false)"></div>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
            <!-- Topbar -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Exercise Routine</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button id="weekly-view-btn" onclick="toggleWeeklyView()"
                        class="bg-card border border-border px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-muted transition-colors flex items-center gap-2">
                        <iconify-icon icon="lucide:calendar"></iconify-icon>
                        Weekly View
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                <div class="max-w-5xl mx-auto space-y-8">
                    <div id="weekly-view-panel" class="hidden space-y-6">
                        <div class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-2xl font-heading font-bold">Weekly Workout Overview</h2>
                                    <p class="text-sm text-muted-foreground">
                                        Switch to weekly mode to review your planned sessions across the entire week.
                                    </p>
                                </div>
                                <span class="px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold">Weekly summary</span>
                            </div>

                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Monday</p>
                                    <h3 class="font-bold mb-2">Cardio Blast</h3>
                                    <p class="text-sm text-muted-foreground">30 mins interval training</p>
                                </article>
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Tuesday</p>
                                    <h3 class="font-bold mb-2">Strength Focus</h3>
                                    <p class="text-sm text-muted-foreground">Lower body resistance training</p>
                                </article>
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Wednesday</p>
                                    <h3 class="font-bold mb-2">Rest & Recovery</h3>
                                    <p class="text-sm text-muted-foreground">Mobility and stretching routine</p>
                                </article>
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Thursday</p>
                                    <h3 class="font-bold mb-2">Full Body Flow</h3>
                                    <p class="text-sm text-muted-foreground">Balanced cardio and strength</p>
                                </article>
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Friday</p>
                                    <h3 class="font-bold mb-2">Core & Stability</h3>
                                    <p class="text-sm text-muted-foreground">Core sequences and balance work</p>
                                </article>
                                <article class="bg-background rounded-3xl border border-border p-5">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Saturday</p>
                                    <h3 class="font-bold mb-2">Active Recovery</h3>
                                    <p class="text-sm text-muted-foreground">Light walk or yoga session</p>
                                </article>
                            </div>
                        </div>
                    </div>
                    <div id="daily-view-content" class="space-y-8">

                    <!-- Preferences & Status -->
                    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div
                            class="lg:col-span-2 bg-card rounded-3xl border border-border p-6 sm:p-8 shadow-sm flex flex-col justify-center">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-heading font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:activity" class="text-primary"></iconify-icon>
                                    Activity Profile
                                </h2>
                                <button onclick="openEditActivityModal()"
                                    class="text-sm font-medium text-primary hover:text-tertiary transition-colors flex items-center gap-1">
                                    <iconify-icon icon="lucide:pencil" class="text-xs"></iconify-icon> Edit
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Body Weight</p>
                                    <p class="font-bold text-sm" id="profile-weight">68 kg</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Height</p>
                                    <p class="font-bold text-sm" id="profile-height">165 cm</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Age</p>
                                    <p class="font-bold text-sm" id="profile-age">28</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Exercise Preference</p>
                                    <p class="font-bold text-sm" id="profile-exercise-pref">Cardio & Strength</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Time Available</p>
                                    <p class="font-bold text-sm" id="profile-time">45-60 mins</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Available Days</p>
                                    <p class="font-bold text-sm" id="profile-days">4 days/week</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Equipment</p>
                                    <p class="font-bold text-sm" id="profile-equipment">Dumbbells, Mat</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Fitness Goals</p>
                                    <p class="font-bold text-sm" id="profile-goals">Weight Loss</p>
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

        <div id="edit-activity-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeEditActivityModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm sm:max-w-2xl md:max-w-4xl mx-2 sm:mx-4 h-[95vh] sm:h-[90vh] flex flex-col">
                <div class="bg-card rounded-3xl border border-border shadow-xl flex-1 flex flex-col overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-border flex-shrink-0">
                        <div>
                            <h3 class="text-xl font-heading font-bold">Edit Activity Profile</h3>
                            <p class="text-sm text-muted-foreground">Update your fitness profile to get personalized exercise recommendations.</p>
                        </div>
                        <button onclick="closeEditActivityModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="space-y-6">
                            <!-- Physical Information -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Body Weight (kg)</label>
                                    <input id="profile-weight-input" type="number" step="0.1" value="68"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Height (cm)</label>
                                    <input id="profile-height-input" type="number" value="165"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Age</label>
                                <input id="profile-age-input" type="number" value="28"
                                    class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                            </div>

                            <!-- Exercise Preferences -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Exercise Preference</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="exercise-preference" value="cardio" class="text-primary">
                                        <span class="text-sm">Cardio</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="exercise-preference" value="strength" class="text-primary">
                                        <span class="text-sm">Strength</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="radio" name="exercise-preference" value="cardio-strength" checked class="text-primary">
                                        <span class="text-sm">Cardio & Strength</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="exercise-preference" value="flexibility" class="text-primary">
                                        <span class="text-sm">Flexibility</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Time Available -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Time Available per Session</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="time-available" value="15-30" class="text-primary">
                                        <span class="text-sm">15-30 mins</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="radio" name="time-available" value="45-60" checked class="text-primary">
                                        <span class="text-sm">45-60 mins</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="time-available" value="60-90" class="text-primary">
                                        <span class="text-sm">60-90 mins</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="time-available" value="90+" class="text-primary">
                                        <span class="text-sm">90+ mins</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Available Days -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Available Days per Week</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="available-days" value="2" class="text-primary">
                                        <span class="text-sm">2 days</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="available-days" value="3" class="text-primary">
                                        <span class="text-sm">3 days</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="radio" name="available-days" value="4" checked class="text-primary">
                                        <span class="text-sm">4 days</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="available-days" value="5-6" class="text-primary">
                                        <span class="text-sm">5-6 days</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Equipment Available -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Equipment Available</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="checkbox" name="equipment" value="dumbbells" checked class="text-primary">
                                        <span class="text-sm">Dumbbells</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="checkbox" name="equipment" value="mat" checked class="text-primary">
                                        <span class="text-sm">Mat</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="equipment" value="resistance-bands" class="text-primary">
                                        <span class="text-sm">Resistance Bands</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="equipment" value="treadmill" class="text-primary">
                                        <span class="text-sm">Treadmill</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="equipment" value="bike" class="text-primary">
                                        <span class="text-sm">Exercise Bike</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="equipment" value="none" class="text-primary">
                                        <span class="text-sm">Bodyweight Only</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Fitness Goals -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Fitness Goals</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                        <input type="radio" name="fitness-goals" value="weight-loss" checked class="text-primary">
                                        <span class="text-sm">Weight Loss</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="fitness-goals" value="muscle-gain" class="text-primary">
                                        <span class="text-sm">Muscle Gain</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="fitness-goals" value="endurance" class="text-primary">
                                        <span class="text-sm">Improve Endurance</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="fitness-goals" value="flexibility" class="text-primary">
                                        <span class="text-sm">Increase Flexibility</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="fitness-goals" value="general-health" class="text-primary">
                                        <span class="text-sm">General Health</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="radio" name="fitness-goals" value="sports-performance" class="text-primary">
                                        <span class="text-sm">Sports Performance</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Body Part Focus -->
                            <div>
                                <label class="block text-sm font-medium mb-3">Body Part Focus (Optional)</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="upper-body" class="text-primary">
                                        <span class="text-sm">Upper Body</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="lower-body" class="text-primary">
                                        <span class="text-sm">Lower Body</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="core" class="text-primary">
                                        <span class="text-sm">Core</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="full-body" class="text-primary">
                                        <span class="text-sm">Full Body</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="back" class="text-primary">
                                        <span class="text-sm">Back</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                        <input type="checkbox" name="body-focus" value="shoulders" class="text-primary">
                                        <span class="text-sm">Shoulders</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 p-6 border-t border-border">
                        <button onclick="closeEditActivityModal()"
                            class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                            Cancel
                        </button>
                        <button onclick="saveActivityProfile()"
                            class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                            Save Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleWeeklyView() {
            const weeklyPanel = document.getElementById('weekly-view-panel');
            const dailyContent = document.getElementById('daily-view-content');
            const button = document.getElementById('weekly-view-btn');
            const isWeekly = weeklyPanel.classList.contains('hidden');

            if (isWeekly) {
                weeklyPanel.classList.remove('hidden');
                dailyContent.classList.add('hidden');
                button.innerHTML = `<iconify-icon icon="lucide:calendar" class=""></iconify-icon> Daily View`;
            } else {
                weeklyPanel.classList.add('hidden');
                dailyContent.classList.remove('hidden');
                button.innerHTML = `<iconify-icon icon="lucide:calendar" class=""></iconify-icon> Weekly View`;
            }
        }

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
        document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());

        function openEditActivityModal() {
            // Load current profile data into the modal
            loadProfileData();
            document.getElementById('edit-activity-modal').classList.remove('hidden');
            // Initialize form styling
            initializeFormStyling();
        }

        function closeEditActivityModal() {
            document.getElementById('edit-activity-modal').classList.add('hidden');
        }

        function initializeFormStyling() {
            // Handle radio button groups
            const radioGroups = ['exercise-preference', 'time-available', 'available-days', 'fitness-goals'];
            radioGroups.forEach(groupName => {
                const radios = document.querySelectorAll(`input[name="${groupName}"]`);
                radios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        // Remove styling from all labels in this group
                        document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                            r.closest('label').classList.remove('border-primary', 'bg-primary/10');
                            r.closest('label').classList.add('border-border', 'bg-background');
                        });
                        // Add styling to selected label
                        if (this.checked) {
                            this.closest('label').classList.remove('border-border', 'bg-background');
                            this.closest('label').classList.add('border-primary', 'bg-primary/10');
                        }
                    });
                });
            });

            // Handle checkbox groups
            const checkboxGroups = ['equipment', 'body-focus'];
            checkboxGroups.forEach(groupName => {
                const checkboxes = document.querySelectorAll(`input[name="${groupName}"]`);
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            this.closest('label').classList.remove('border-border', 'bg-background');
                            this.closest('label').classList.add('border-primary', 'bg-primary/10');
                        } else {
                            this.closest('label').classList.remove('border-primary', 'bg-primary/10');
                            this.closest('label').classList.add('border-border', 'bg-background');
                        }
                    });
                });
            });
        }

        function loadProfileData() {
            // Load data from profile display elements into modal inputs
            document.getElementById('profile-weight-input').value = document.getElementById('profile-weight').textContent.replace(' kg', '');
            document.getElementById('profile-height-input').value = document.getElementById('profile-height').textContent.replace(' cm', '');
            document.getElementById('profile-age-input').value = document.getElementById('profile-age').textContent;

            // Set radio buttons based on current values
            const exercisePref = document.getElementById('profile-exercise-pref').textContent.toLowerCase().replace(' & ', '-');
            const exerciseRadio = document.querySelector(`input[name="exercise-preference"][value="${exercisePref}"]`);
            if (exerciseRadio) exerciseRadio.checked = true;

            const timeAvailable = document.getElementById('profile-time').textContent.toLowerCase().replace(' mins', '').replace('-', '-');
            const timeRadio = document.querySelector(`input[name="time-available"][value="${timeAvailable}"]`);
            if (timeRadio) timeRadio.checked = true;

            const availableDays = document.getElementById('profile-days').textContent.split(' ')[0];
            const daysRadio = document.querySelector(`input[name="available-days"][value="${availableDays}"]`);
            if (daysRadio) daysRadio.checked = true;

            const fitnessGoals = document.getElementById('profile-goals').textContent.toLowerCase().replace(' ', '-');
            const goalsRadio = document.querySelector(`input[name="fitness-goals"][value="${fitnessGoals}"]`);
            if (goalsRadio) goalsRadio.checked = true;

            // Set equipment checkboxes
            const equipment = document.getElementById('profile-equipment').textContent.toLowerCase().split(', ');
            document.querySelectorAll('input[name="equipment"]').forEach(cb => {
                cb.checked = equipment.includes(cb.value);
            });

            // Apply initial styling after setting checked states
            setTimeout(() => {
                applyInitialStyling();
            }, 10);
        }

        function applyInitialStyling() {
            // Apply styling to checked radio buttons
            const radioGroups = ['exercise-preference', 'time-available', 'available-days', 'fitness-goals'];
            radioGroups.forEach(groupName => {
                const checkedRadio = document.querySelector(`input[name="${groupName}"]:checked`);
                if (checkedRadio) {
                    checkedRadio.closest('label').classList.remove('border-border', 'bg-background');
                    checkedRadio.closest('label').classList.add('border-primary', 'bg-primary/10');
                }
            });

            // Apply styling to checked checkboxes
            const checkboxGroups = ['equipment', 'body-focus'];
            checkboxGroups.forEach(groupName => {
                document.querySelectorAll(`input[name="${groupName}"]:checked`).forEach(cb => {
                    cb.closest('label').classList.remove('border-border', 'bg-background');
                    cb.closest('label').classList.add('border-primary', 'bg-primary/10');
                });
            });
        }

        function saveActivityProfile() {
            // Collect form data
            const profileData = {
                body_weight: document.getElementById('profile-weight-input').value + ' kg',
                height: document.getElementById('profile-height-input').value + ' cm',
                age: document.getElementById('profile-age-input').value,
                exercise_preference: document.querySelector('input[name="exercise-preference"]:checked').value.replace('-', ' & ').replace(/\b\w/g, l => l.toUpperCase()),
                time_available: document.querySelector('input[name="time-available"]:checked').value + ' mins',
                available_days: document.querySelector('input[name="available-days"]:checked').value + ' days/week',
                equipment_available: Array.from(document.querySelectorAll('input[name="equipment"]:checked')).map(cb => cb.value.replace(/\b\w/g, l => l.toUpperCase())).join(', '),
                fitness_goals: document.querySelector('input[name="fitness-goals"]:checked').value.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                body_part_focus: Array.from(document.querySelectorAll('input[name="body-focus"]:checked')).map(cb => cb.value.replace(/\b\w/g, l => l.toUpperCase())).join(', ') || 'None specified'
            };

            // Update the profile display
            document.getElementById('profile-weight').textContent = profileData.body_weight;
            document.getElementById('profile-height').textContent = profileData.height;
            document.getElementById('profile-age').textContent = profileData.age;
            document.getElementById('profile-exercise-pref').textContent = profileData.exercise_preference;
            document.getElementById('profile-time').textContent = profileData.time_available;
            document.getElementById('profile-days').textContent = profileData.available_days;
            document.getElementById('profile-equipment').textContent = profileData.equipment_available;
            document.getElementById('profile-goals').textContent = profileData.fitness_goals;

            console.log('Activity profile saved', profileData);
            alert('Activity profile updated successfully!');
            closeEditActivityModal();
        }
    </script>
</body>

</html>