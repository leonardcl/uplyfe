<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                                <article data-workout-day="0" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Monday</p>
                                    <h3 class="font-bold mb-2">Cardio Blast</h3>
                                    <p class="text-sm text-muted-foreground">30 mins interval training</p>
                                </article>
                                <article data-workout-day="1" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Tuesday</p>
                                    <h3 class="font-bold mb-2">Strength Focus</h3>
                                    <p class="text-sm text-muted-foreground">Lower body resistance training</p>
                                </article>
                                <article data-workout-day="2" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Wednesday</p>
                                    <h3 class="font-bold mb-2">Rest & Recovery</h3>
                                    <p class="text-sm text-muted-foreground">Mobility and stretching routine</p>
                                </article>
                                <article data-workout-day="3" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Thursday</p>
                                    <h3 class="font-bold mb-2">Full Body Flow</h3>
                                    <p class="text-sm text-muted-foreground">Balanced cardio and strength</p>
                                </article>
                                <article data-workout-day="4" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Friday</p>
                                    <h3 class="font-bold mb-2">Core & Stability</h3>
                                    <p class="text-sm text-muted-foreground">Core sequences and balance work</p>
                                </article>
                                <article data-workout-day="5" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Saturday</p>
                                    <h3 class="font-bold mb-2">Active Recovery</h3>
                                    <p class="text-sm text-muted-foreground">Light walk or yoga session</p>
                                </article>
                                <article data-workout-day="6" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Sunday</p>
                                    <h3 class="font-bold mb-2">Weekly Reset</h3>
                                    <p class="text-sm text-muted-foreground">Full body mobility and conditioning</p>
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
                                    <p class="font-bold text-sm" id="profile-weight">
                                        {{ session('user')->weight ? session('user')->weight . ' kg' : 'Not set' }}
                                    </p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Height</p>
                                    <p class="font-bold text-sm" id="profile-height">
                                        {{ session('user')->height ? session('user')->height . ' cm' : 'Not set' }}
                                    </p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Age</p>
                                    <p class="font-bold text-sm" id="profile-age">
                                        {{ session('user')->age ?? 'Not set' }}
                                    </p>
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
                            <button id="generate-routine-btn" onclick="generateNewRoutine()"
                                class="w-full bg-primary text-primary-foreground py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <iconify-icon icon="lucide:sparkles"></iconify-icon>
                                Generate New Routine
                            </button>
                        </div>
                    </section>

                    <!-- Today's Workout -->
                    <section>
                        <div class="flex items-center justify-between gap-4 mb-6">
                            <h2 id="workout-heading" class="text-2xl font-heading font-bold">Today's Workout: Full Body Flow</h2>
                            <div class="flex items-center gap-2">
                                <button id="workout-prev-day-btn" type="button" class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors" aria-label="Previous day workout">
                                    <iconify-icon icon="lucide:chevron-left"></iconify-icon>
                                </button>
                                <button id="workout-next-day-btn" type="button" class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors" aria-label="Next day workout">
                                    <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <div class="bg-card rounded-3xl border border-border overflow-hidden shadow-sm">
                            <div class="p-6 sm:p-8 flex flex-col md:flex-row items-center gap-8 border-b border-border">
                                <div
                                    class="w-[180px] h-[180px] bg-muted rounded-2xl relative overflow-hidden flex items-center justify-center group cursor-pointer">
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
                                        <span id="workout-duration-badge"
                                            class="px-2.5 py-1 rounded-md bg-muted text-muted-foreground text-xs font-bold">45
                                            Mins</span>
                                    </div>
                                    <h3 id="workout-title" class="text-xl font-bold mb-2">Cardio & Mobility Flow</h3>
                                    <p id="workout-description" class="text-sm text-muted-foreground mb-6">A balanced routine designed to elevate
                                        heart rate safely while improving joint mobility. Perfect for your current
                                        health markers.</p>

                                    <div class="flex gap-4">
                                        <button
                                            class="flex-1 bg-primary text-primary-foreground py-3 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                            Start Workout
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-background p-6 sm:px-8">
                                <h4 id="workout-exercises-title" class="font-bold text-sm mb-4 uppercase tracking-wider text-muted-foreground">
                                    Exercises (4)</h4>

                                <div id="workout-exercise-list" class="space-y-3">
                                    <!-- Exercise 1 -->
                                    <div
                                        class="flex items-start justify-between gap-3 p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                1</div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm">Dynamic Stretching</p>
                                                <p class="text-xs text-muted-foreground">Warm up</p>
                                                <p class="text-xs text-muted-foreground leading-relaxed break-words">This exercise is a great way to warm up your muscles and get your heart rate up.</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold whitespace-nowrap shrink-0">5 mins</div>
                                    </div>

                                    <!-- Exercise 2 -->
                                    <div
                                        class="flex items-start justify-between gap-3 p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                2</div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm">Dumbbell Goblet Squats</p>
                                                <p class="text-xs text-muted-foreground">3 sets x 12 reps</p>
                                                <p class="text-xs text-muted-foreground leading-relaxed break-words">This exercise is a great way to work your legs and glutes. It is a compound exercise that works multiple muscle groups at once.</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold whitespace-nowrap shrink-0">10 mins</div>
                                    </div>

                                    <!-- Exercise 3 -->
                                    <div
                                        class="flex items-start justify-between gap-3 p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                3</div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm">Plank Variations</p>
                                                <p class="text-xs text-muted-foreground">3 sets x 45 secs</p>
                                                <p class="text-xs text-muted-foreground leading-relaxed break-words">This exercise is a great way to work your core and improve your balance and stability.</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold whitespace-nowrap shrink-0">8 mins</div>
                                    </div>

                                    <!-- Exercise 4 -->
                                    <div
                                        class="flex items-start justify-between gap-3 p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            <div
                                                class="w-10 h-10 shrink-0 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">
                                                4</div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm">Low-Intensity Steady State (LISS)</p>
                                                <p class="text-xs text-muted-foreground">Brisk walking or cycling</p>
                                                <p class="text-xs text-muted-foreground leading-relaxed break-words">This exercise is a great way to improve your cardiovascular health and endurance.</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold whitespace-nowrap shrink-0">22 mins</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </main>

        <div id="exercise-detail-modal" class="fixed inset-0 z-50 hidden">
            <div id="exercise-detail-backdrop" class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
            <div class="absolute top-1/2 left-1/2 w-full max-w-sm sm:max-w-2xl md:max-w-3xl mx-2 sm:mx-4 max-h-[92vh] -translate-x-1/2 -translate-y-1/2">
                <div class="bg-card rounded-3xl border border-border shadow-xl overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-border">
                        <div>
                            <p id="exercise-modal-subtitle" class="text-xs uppercase tracking-wider text-muted-foreground mb-1">Exercise Detail</p>
                            <h3 id="exercise-modal-title" class="text-xl font-heading font-bold">Exercise Name</h3>
                        </div>
                        <button id="exercise-modal-close-btn" type="button" class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-4 lg:gap-5 items-start">
                            <div class="rounded-2xl border border-border bg-muted/60 p-4 sm:p-5">
                                <div class="flex items-center justify-center rounded-xl border border-border/60 bg-background min-h-[220px]">
                                    <img id="exercise-modal-image" src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80" alt="Exercise preview" class="w-[180px] h-[180px] rounded-xl object-cover shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3">
                                <div class="rounded-xl border border-border bg-background p-4">
                                    <p class="text-xs text-muted-foreground mb-1">Duration</p>
                                    <p id="exercise-modal-duration" class="font-semibold text-sm">-</p>
                                </div>
                                <div class="rounded-xl border border-border bg-background p-4">
                                    <p class="text-xs text-muted-foreground mb-1">Body Part</p>
                                    <p id="exercise-modal-body-part" class="font-semibold text-sm">-</p>
                                </div>
                                <div class="rounded-xl border border-border bg-background p-4">
                                    <p class="text-xs text-muted-foreground mb-1">Equipment</p>
                                    <p id="exercise-modal-equipment" class="font-semibold text-sm">-</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold mb-2 uppercase tracking-wider text-muted-foreground">Description</h4>
                            <p id="exercise-modal-instructions" class="text-sm text-muted-foreground leading-relaxed"></p>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold mb-2 uppercase tracking-wider text-muted-foreground">How To Perform</h4>
                            <ol id="exercise-modal-steps" class="space-y-2"></ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        function switchToDailyView() {
            const weeklyPanel = document.getElementById('weekly-view-panel');
            const dailyContent = document.getElementById('daily-view-content');
            const button = document.getElementById('weekly-view-btn');
            weeklyPanel.classList.add('hidden');
            dailyContent.classList.remove('hidden');
            button.innerHTML = `<iconify-icon icon="lucide:calendar" class=""></iconify-icon> Weekly View`;
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

        // `let` (not `const`) so the AI-generated plan can replace this in-place.
        // The initial value below is the static placeholder shown before the user
        // clicks Generate.
        let weeklyWorkoutPlans = [
            {
                dayLabel: "Today's Workout",
                heading: "Full Body Flow",
                title: "Cardio & Mobility Flow",
                duration: "45 Mins",
                description: "A balanced routine designed to elevate heart rate safely while improving joint mobility. Perfect for your current health markers.",
                exercises: [
                    { name: "Dynamic Stretching", detail: "Warm up", description: "This exercise is a great way to warm up your muscles and get your heart rate up.", duration: "5 mins" },
                    { name: "Dumbbell Goblet Squats", detail: "3 sets x 12 reps", description: "This exercise is a great way to work your legs and glutes. It is a compound exercise that works multiple muscle groups at once.", duration: "10 mins" },
                    { name: "Plank Variations", detail: "3 sets x 45 secs", description: "This exercise is a great way to work your core and improve your balance and stability.", duration: "8 mins" },
                    { name: "Low-Intensity Steady State (LISS)", detail: "Brisk walking or cycling", description: "This exercise is a great way to improve your cardiovascular health and endurance.", duration: "22 mins" }
                ]
            },
            {
                dayLabel: "Tomorrow's Workout",
                heading: "Lower Body Strength",
                title: "Leg Power Builder",
                duration: "50 Mins",
                description: "A lower body strength session focused on controlled tempo and progressive overload for better metabolic health.",
                exercises: [
                    { name: "Bodyweight Lunges", detail: "3 sets x 12 reps/side", description: "Improves unilateral strength and hip stability for daily movement quality.", duration: "10 mins" },
                    { name: "Romanian Deadlift", detail: "3 sets x 10 reps", description: "Targets posterior chain and helps improve posture and lower back resilience.", duration: "12 mins" },
                    { name: "Glute Bridge Hold", detail: "3 sets x 40 secs", description: "Activates glutes and core while supporting pelvic stability.", duration: "8 mins" },
                    { name: "Stepper March", detail: "Moderate pace", description: "A low-impact cardio finisher to increase endurance without joint overload.", duration: "20 mins" }
                ]
            },
            {
                dayLabel: "Day 3 Workout",
                heading: "Core & Stability",
                title: "Balance Core Session",
                duration: "40 Mins",
                description: "Core-focused training to improve trunk stability, posture, and movement efficiency.",
                exercises: [
                    { name: "Cat-Cow Mobility", detail: "2 sets x 10 reps", description: "Prepares the spine for core training and reduces stiffness.", duration: "5 mins" },
                    { name: "Dead Bug", detail: "3 sets x 12 reps", description: "Builds deep core control while protecting the lower back.", duration: "10 mins" },
                    { name: "Side Plank", detail: "3 sets x 30 secs/side", description: "Strengthens obliques and improves anti-rotation control.", duration: "10 mins" },
                    { name: "Bird Dog", detail: "3 sets x 12 reps", description: "Integrates core and hip coordination for better stability.", duration: "15 mins" }
                ]
            },
            {
                dayLabel: "Day 4 Workout",
                heading: "Upper Body Strength",
                title: "Push Pull Circuit",
                duration: "48 Mins",
                description: "Build upper-body endurance and strength with low-risk movements tailored for consistency.",
                exercises: [
                    { name: "Resistance Band Rows", detail: "3 sets x 15 reps", description: "Improves posture and upper back strength.", duration: "10 mins" },
                    { name: "Incline Push-Ups", detail: "3 sets x 12 reps", description: "Builds chest and triceps strength with manageable intensity.", duration: "10 mins" },
                    { name: "Dumbbell Shoulder Press", detail: "3 sets x 10 reps", description: "Develops shoulder strength and overhead control.", duration: "10 mins" },
                    { name: "Arm Ergometer Cardio", detail: "Steady pace", description: "Raises heart rate while reducing lower-body load.", duration: "18 mins" }
                ]
            },
            {
                dayLabel: "Day 5 Workout",
                heading: "Active Recovery",
                title: "Mobility & Stretch Flow",
                duration: "35 Mins",
                description: "Low-intensity recovery routine to improve flexibility and keep your training streak sustainable.",
                exercises: [
                    { name: "Neck and Shoulder Rolls", detail: "2 sets x 12 reps", description: "Releases upper body tension from daily posture.", duration: "5 mins" },
                    { name: "Hip Opener Sequence", detail: "2 sets", description: "Improves hip range of motion and reduces stiffness.", duration: "8 mins" },
                    { name: "Hamstring Stretch", detail: "3 sets x 30 secs", description: "Supports posterior chain recovery and mobility.", duration: "7 mins" },
                    { name: "Guided Breathing Walk", detail: "Easy pace", description: "Promotes active recovery and stress regulation.", duration: "15 mins" }
                ]
            },
            {
                dayLabel: "Day 6 Workout",
                heading: "Cardio Endurance",
                title: "Heart Health Builder",
                duration: "42 Mins",
                description: "Steady cardio blocks to improve endurance capacity while maintaining safe exertion levels.",
                exercises: [
                    { name: "March In Place", detail: "Warm up", description: "Gradually increases heart rate and circulation.", duration: "6 mins" },
                    { name: "Low-Impact Intervals", detail: "6 rounds", description: "Alternates effort and recovery for aerobic adaptation.", duration: "16 mins" },
                    { name: "Stationary Cycling", detail: "Moderate pace", description: "Builds stamina with minimal joint stress.", duration: "12 mins" },
                    { name: "Cooldown Walk", detail: "Easy pace", description: "Returns heart rate to baseline safely.", duration: "8 mins" }
                ]
            },
            {
                dayLabel: "Day 7 Workout",
                heading: "Full Body Reset",
                title: "Weekly Wrap-Up Session",
                duration: "45 Mins",
                description: "A final balanced session combining mobility, strength, and light cardio to close the week strong.",
                exercises: [
                    { name: "Joint Mobility Drill", detail: "Warm up", description: "Prepares major joints for full-body movement.", duration: "7 mins" },
                    { name: "Bodyweight Squat + Reach", detail: "3 sets x 12 reps", description: "Trains lower body strength and thoracic mobility together.", duration: "10 mins" },
                    { name: "Standing Core Twists", detail: "3 sets x 20 reps", description: "Improves core engagement and rotational control.", duration: "8 mins" },
                    { name: "Brisk Walk Cooldown", detail: "Low intensity", description: "Completes the week with controlled aerobic work.", duration: "20 mins" }
                ]
            }
        ];

        let currentWorkoutDayIndex = 0;

        const exerciseDetailMap = {
            "Dynamic Stretching": {
                bodyPart: "Full body mobility",
                equipment: "Yoga mat (optional)",
                image: "https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=1200&q=80",
                steps: [
                    "Stand upright and keep feet hip-width apart.",
                    "Do arm circles, hip openers, and leg swings in a controlled range.",
                    "Increase movement range gradually for 60-90 seconds each drill."
                ],
                instructions: "Move continuously without bouncing. Focus on smooth breathing and controlled transitions to warm up muscles and joints."
            },
            "Dumbbell Goblet Squats": {
                bodyPart: "Quadriceps, glutes, core",
                equipment: "1 dumbbell",
                image: "https://images.unsplash.com/photo-1534367610401-9f5ed68180aa?auto=format&fit=crop&w=1200&q=80",
                steps: [
                    "Hold one dumbbell close to chest with both hands.",
                    "Sit hips back and down while keeping chest up.",
                    "Push through heels to stand and squeeze glutes at the top."
                ],
                instructions: "Keep knees tracking over toes and spine neutral. Reduce load if your form starts to collapse."
            },
            "Plank Variations": {
                bodyPart: "Core, shoulders, lower back",
                equipment: "Exercise mat",
                image: "https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=1200&q=80",
                steps: [
                    "Start in forearm plank with elbows under shoulders.",
                    "Engage abs and keep body in a straight line.",
                    "Hold or alternate side plank variations as programmed."
                ],
                instructions: "Do not let hips sag. Maintain core tension and normal breathing for each hold."
            },
            "Low-Intensity Steady State (LISS)": {
                bodyPart: "Cardiovascular system, legs",
                equipment: "Treadmill, bike, or open space",
                image: "https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&fit=crop&w=1200&q=80",
                steps: [
                    "Choose a low-impact cardio activity.",
                    "Maintain moderate conversational pace consistently.",
                    "Finish with a gentle cooldown and hydration."
                ],
                instructions: "Keep intensity steady around light-to-moderate effort. The goal is endurance, not sprinting."
            }
        };

        function buildExerciseDetail(exercise) {
            // When the AI-generated exercise has a real dataset id, prefer the
            // animated GIF from /api/exercises/{id}/image and use the dataset's
            // metadata (body_part / equipment / target). Falls through to the
            // hardcoded `exerciseDetailMap` and finally to a generic stock photo
            // when there's no id.
            if (exercise.exercise_id) {
                const stepsSource = (exercise.description || '').trim();
                // Split the dataset's instruction text into bite-sized "steps"
                // — most instructions are one paragraph of run-on sentences.
                const steps = stepsSource
                    ? stepsSource.split(/(?<=[.!?])\s+(?=[A-Z])/).filter(s => s.length > 0).slice(0, 8)
                    : [
                        "Set up your posture and stabilize your core before starting.",
                        `Perform ${(exercise.name || 'this exercise').toLowerCase()} with controlled tempo.`,
                        "Stop if form breaks and rest before the next set."
                    ];
                return {
                    bodyPart: exercise.body_part || exercise.target || "Target area based on routine focus",
                    equipment: exercise.equipment || "Bodyweight or basic home equipment",
                    image: `/api/exercises/${exercise.exercise_id}/image`,
                    steps,
                    instructions: exercise.detail
                        ? `${exercise.detail}. ${stepsSource}`
                        : stepsSource || "Follow the steps to the right; keep movement controlled and breathing steady."
                };
            }

            const mapped = exerciseDetailMap[exercise.name];
            if (mapped) {
                return mapped;
            }

            return {
                bodyPart: "Target area based on routine focus",
                equipment: "Bodyweight or basic home equipment",
                image: "https://images.unsplash.com/photo-1518310383802-640c2de311b2?auto=format&fit=crop&w=1200&q=80",
                steps: [
                    "Prepare your posture and stabilize core before starting.",
                    `Perform ${exercise.name.toLowerCase()} with controlled tempo.`,
                    "Stop if form breaks and rest before next set."
                ],
                instructions: exercise.description
            };
        }

        function renderWorkoutDay(index) {
            const plan = weeklyWorkoutPlans[index];
            const heading = document.getElementById('workout-heading');
            const title = document.getElementById('workout-title');
            const description = document.getElementById('workout-description');
            const durationBadge = document.getElementById('workout-duration-badge');
            const exercisesTitle = document.getElementById('workout-exercises-title');
            const exerciseList = document.getElementById('workout-exercise-list');

            if (!plan || !heading || !title || !description || !durationBadge || !exercisesTitle || !exerciseList) {
                return;
            }

            heading.textContent = `${plan.dayLabel}: ${plan.heading}`;
            title.textContent = plan.title;
            description.textContent = plan.description;
            durationBadge.textContent = plan.duration;
            exercisesTitle.textContent = `Exercises (${plan.exercises.length})`;

            exerciseList.innerHTML = plan.exercises
                .map((exercise, idx) => {
                    // When the exercise has a dataset id, show a small animated
                    // GIF thumbnail in place of the numbered badge — much more
                    // useful for "what does this exercise look like?".
                    const badge = exercise.exercise_id
                        ? `<img src="/api/exercises/${exercise.exercise_id}/image" alt=""
                                loading="lazy"
                                class="w-12 h-12 shrink-0 rounded-lg object-cover bg-muted">`
                        : `<div class="w-10 h-10 shrink-0 rounded-lg bg-muted flex items-center justify-center font-bold text-muted-foreground group-hover:text-primary transition-colors">${idx + 1}</div>`;
                    return `
                    <div data-exercise-index="${idx}" class="flex items-start justify-between gap-3 p-4 rounded-xl border border-border bg-card hover:border-primary/50 transition-colors cursor-pointer group">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            ${badge}
                            <div class="min-w-0">
                                <p class="font-bold text-sm">${exercise.name}</p>
                                <p class="text-xs text-muted-foreground">${exercise.detail}</p>
                                <p class="text-xs text-muted-foreground leading-relaxed break-words">${exercise.description}</p>
                            </div>
                        </div>
                        <div class="text-sm font-semibold whitespace-nowrap shrink-0">${exercise.duration}</div>
                    </div>`;
                })
                .join('');
        }

        function openExerciseDetailModal(exerciseIndex) {
            const plan = weeklyWorkoutPlans[currentWorkoutDayIndex];
            if (!plan || !plan.exercises[exerciseIndex]) {
                return;
            }

            const exercise = plan.exercises[exerciseIndex];
            const detail = buildExerciseDetail(exercise);

            document.getElementById('exercise-modal-subtitle').textContent = `${plan.dayLabel} • ${exercise.detail}`;
            document.getElementById('exercise-modal-title').textContent = exercise.name;
            document.getElementById('exercise-modal-duration').textContent = exercise.duration;
            document.getElementById('exercise-modal-body-part').textContent = detail.bodyPart;
            document.getElementById('exercise-modal-equipment').textContent = detail.equipment;
            document.getElementById('exercise-modal-instructions').textContent = detail.instructions;
            document.getElementById('exercise-modal-image').src = detail.image;

            const stepsContainer = document.getElementById('exercise-modal-steps');
            stepsContainer.innerHTML = detail.steps
                .map((step, idx) => `
                    <li class="flex gap-3 text-sm text-muted-foreground leading-relaxed">
                        <span class="w-6 h-6 shrink-0 rounded-full bg-primary/10 text-primary-foreground font-semibold text-xs flex items-center justify-center mt-0.5">${idx + 1}</span>
                        <span>${step}</span>
                    </li>
                `)
                .join('');

            document.getElementById('exercise-detail-modal').classList.remove('hidden');
        }

        function closeExerciseDetailModal() {
            document.getElementById('exercise-detail-modal').classList.add('hidden');
        }

        function showPreviousWorkoutDay() {
            currentWorkoutDayIndex = (currentWorkoutDayIndex - 1 + weeklyWorkoutPlans.length) % weeklyWorkoutPlans.length;
            renderWorkoutDay(currentWorkoutDayIndex);
        }

        function showNextWorkoutDay() {
            currentWorkoutDayIndex = (currentWorkoutDayIndex + 1) % weeklyWorkoutPlans.length;
            renderWorkoutDay(currentWorkoutDayIndex);
        }

        function goToWorkoutDay(dayIndex) {
            const parsedIndex = Number(dayIndex);
            if (Number.isNaN(parsedIndex) || parsedIndex < 0 || parsedIndex >= weeklyWorkoutPlans.length) {
                return;
            }

            currentWorkoutDayIndex = parsedIndex;
            renderWorkoutDay(currentWorkoutDayIndex);
            switchToDailyView();
        }

        document.getElementById('workout-prev-day-btn')?.addEventListener('click', showPreviousWorkoutDay);
        document.getElementById('workout-next-day-btn')?.addEventListener('click', showNextWorkoutDay);
        document.querySelectorAll('[data-workout-day]').forEach((card) => {
            card.addEventListener('click', () => goToWorkoutDay(card.dataset.workoutDay));
        });
        document.getElementById('workout-exercise-list')?.addEventListener('click', (event) => {
            const exerciseCard = event.target.closest('[data-exercise-index]');
            if (!exerciseCard) return;

            const index = Number(exerciseCard.dataset.exerciseIndex);
            if (Number.isNaN(index)) return;
            openExerciseDetailModal(index);
        });
        document.getElementById('exercise-detail-backdrop')?.addEventListener('click', closeExerciseDetailModal);
        document.getElementById('exercise-modal-close-btn')?.addEventListener('click', closeExerciseDetailModal);
        renderWorkoutDay(currentWorkoutDayIndex);

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

        // Read a display element's textContent, trim whitespace, drop the
        // "Not set" placeholder, and strip an optional unit suffix. Returns
        // either a clean numeric string or "" (which is a valid value for a
        // number input — it shows the placeholder instead of NaN).
        function readNumericDisplay(elId, unitSuffix) {
            const raw = (document.getElementById(elId)?.textContent || '').trim();
            if (!raw || /not set/i.test(raw)) return '';
            const stripped = unitSuffix ? raw.replace(unitSuffix, '').trim() : raw;
            // Keep only digits, decimal, and minus — the rest is junk like "kg".
            const numeric = stripped.replace(/[^\d.\-]/g, '');
            return numeric || '';
        }

        function loadProfileData() {
            // Load data from profile display elements into modal inputs.
            // Robust to placeholder text ("Not set") and surrounding whitespace —
            // the previous version dumped the raw textContent into a number
            // input, which the browser then warned was unparseable.
            document.getElementById('profile-weight-input').value = readNumericDisplay('profile-weight', ' kg');
            document.getElementById('profile-height-input').value = readNumericDisplay('profile-height', ' cm');
            document.getElementById('profile-age-input').value = readNumericDisplay('profile-age', null);

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

        const userId = {{ session('user')->id }};

        async function saveActivityProfile() {
            const weight = document.getElementById('profile-weight-input').value.trim();
            const height = document.getElementById('profile-height-input').value.trim();
            const age = document.getElementById('profile-age-input').value.trim();

            if (!weight || !height || !age) {
                alert('Please complete all required profile fields.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let response;
            try {
                response = await fetch(`/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ weight, height, age }),
                });
            } catch (e) {
                alert('Network error: ' + (e?.message || e));
                return;
            }

            // Surface the real error message instead of a generic "Failed to save".
            const text = await response.text();
            let body;
            try { body = text ? JSON.parse(text) : {}; } catch { body = { error: text }; }

            if (!response.ok) {
                let msg = body.message || body.error || ('HTTP ' + response.status);
                if (body.errors) {
                    msg += '\n' + Object.values(body.errors).flat().join('\n');
                }
                alert('Failed to save profile:\n' + msg);
                return;
            }

            const saved = body.data || {};
            // Each display element gets its updated value, falling back to the
            // pre-existing text when the server omits a field (defensive — the
            // backend now returns all profile fields including `age`).
            if (saved.weight != null) document.getElementById('profile-weight').textContent = `${saved.weight} kg`;
            if (saved.height != null) document.getElementById('profile-height').textContent = `${saved.height} cm`;
            if (saved.age != null)    document.getElementById('profile-age').textContent = String(saved.age);

            alert('Activity profile updated successfully!');
            closeEditActivityModal();
        }

        // --- Real routine generation: POST to /api/ai/exercise/generate -----

        function collectChecked(name) {
            return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`))
                .map(el => el.value);
        }

        function collectRadio(name) {
            const el = document.querySelector(`input[name="${name}"]:checked`);
            return el ? el.value : null;
        }

        async function generateNewRoutine() {
            const weight = document.getElementById('profile-weight').textContent.replace(' kg', '').trim();
            const height = document.getElementById('profile-height').textContent.replace(' cm', '').trim();
            const age = document.getElementById('profile-age').textContent.trim();

            if (!weight || !height || !age || weight === 'Not set' || height === 'Not set' || age === 'Not set') {
                alert('Please complete your profile first (weight, height, age).');
                return;
            }

            // Pull the activity-form selections.
            const payload = {
                equipment:           collectChecked('equipment'),
                'fitness-goals':     collectRadio('fitness-goals'),
                'body-focus':        collectChecked('body-focus'),
                'available-days':    collectRadio('available-days'),
                'time-available':    collectRadio('time-available'),
                'exercise-preference': collectRadio('exercise-preference'),
            };

            setGenerateButtonBusy(true);

            try {
                const res = await fetch('/api/ai/exercise/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const text = await res.text();
                let data;
                try { data = text ? JSON.parse(text) : {}; } catch { data = { error: text }; }
                if (!res.ok) {
                    alert('Could not generate routine: ' + (data.error || data.message || ('HTTP ' + res.status)));
                    return;
                }
                applyAiPlanToExistingUi(data);
            } catch (e) {
                alert('Network error: ' + (e?.message || e));
            } finally {
                setGenerateButtonBusy(false);
            }
        }

        // --- Map AI response into the EXISTING weeklyWorkoutPlans / day cards ---

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        function setGenerateButtonBusy(busy) {
            // Find any button calling generateNewRoutine() and swap its label
            // to indicate progress. Catches both the floating button and the
            // modal "Generate" CTA.
            document.querySelectorAll('button').forEach(btn => {
                if (!btn.getAttribute('onclick')?.includes('generateNewRoutine')) return;
                if (busy) {
                    if (!btn.dataset.origLabel) btn.dataset.origLabel = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = `<iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon> Generating… (30-90s)`;
                } else {
                    if (btn.dataset.origLabel) btn.innerHTML = btn.dataset.origLabel;
                    btn.disabled = false;
                }
            });
        }

        // AI plan → existing-UI plan shape.
        function applyAiPlanToExistingUi(aiPlan) {
            const aiDays = aiPlan.weekly_workout_plan || [];
            if (aiDays.length === 0) {
                alert('The AI returned no workout days. Try adjusting your profile or equipment selection.');
                return;
            }

            // Build the new weeklyWorkoutPlans array using the SAME shape the
            // page already renders from. exercise_id is preserved on each
            // exercise so the detail modal can fetch the right GIF.
            weeklyWorkoutPlans = aiDays.map(d => ({
                dayLabel: d.day_label || 'Day',
                heading: d.heading || (d.title || 'Workout'),
                title: d.title || d.heading || 'Workout',
                duration: d.duration || '',
                description: d.description || '',
                exercises: (d.exercises || []).map(ex => ({
                    exercise_id: ex.exercise_id || null,
                    name: ex.name || '',
                    detail: ex.detail || '',
                    description: ex.description || '',
                    duration: ex.duration || '',
                    body_part: ex.body_part || null,
                    equipment: ex.equipment || null,
                    target: ex.target || null,
                })),
            }));

            currentWorkoutDayIndex = 0;

            // Update the 7-day weekly-view cards (only the ones that map to a
            // generated day; leave the rest as "Rest day" stubs).
            updateWeeklyViewCards();

            // Re-render the daily view with the first day of the new plan.
            renderWorkoutDay(currentWorkoutDayIndex);

            // Stash the assessment + disclaimer so the user can see them.
            if (aiPlan.assessment) {
                stashCoachAssessment(aiPlan.assessment, aiPlan.equipment_resolved || []);
            }

            // Scroll the user to the freshly-rendered routine.
            document.getElementById('daily-view-content')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // The page ships with 7 hardcoded day cards in #weekly-view-panel
        // (data-workout-day="0"..."6"). Update each one's text so the
        // weekly view reflects the new plan instead of the placeholder.
        function updateWeeklyViewCards() {
            document.querySelectorAll('[data-workout-day]').forEach(card => {
                const idx = Number(card.dataset.workoutDay);
                const day = weeklyWorkoutPlans[idx];
                // Each card has its own internal layout — find the title / focus
                // text nodes and update them. We use a simple heuristic: rewrite
                // the first <h3> (or strong/p.font-bold) inside the card.
                const titleEl = card.querySelector('h3, .font-bold, p strong');
                const subEl = card.querySelectorAll('p')[1] || card.querySelector('p.text-muted-foreground');
                if (!day) {
                    if (titleEl) titleEl.textContent = 'Rest day';
                    if (subEl)   subEl.textContent = '—';
                    card.classList.add('opacity-60');
                    return;
                }
                card.classList.remove('opacity-60');
                if (titleEl) titleEl.textContent = day.dayLabel;
                if (subEl)   subEl.textContent = day.title || day.heading;
            });
        }

        // Surface the coach's assessment + equipment list above the daily-view
        // content. Re-uses an existing container if available; otherwise
        // creates a small notice and inserts above #daily-view-content.
        function stashCoachAssessment(text, equipmentResolved) {
            let box = document.getElementById('ai-coach-assessment');
            if (!box) {
                box = document.createElement('section');
                box.id = 'ai-coach-assessment';
                box.className = 'bg-primary/5 border border-primary/20 rounded-2xl p-5 mb-6';
                const target = document.getElementById('daily-view-content');
                target?.parentNode?.insertBefore(box, target);
            }
            const equip = (equipmentResolved || []).join(', ') || '—';
            box.innerHTML = `
                <details>
                    <summary class="cursor-pointer flex items-center justify-between gap-3 font-semibold text-sm">
                        <span class="flex items-center gap-2">
                            <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                            Coach's assessment
                        </span>
                        <span class="text-xs text-muted-foreground">Equipment used: ${escapeHtml(equip)}</span>
                    </summary>
                    <div class="text-sm text-muted-foreground mt-3 whitespace-pre-wrap">${escapeHtml(text)}</div>
                </details>`;
        }
    </script>
</body>

</html>