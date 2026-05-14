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
    @php
        $avatarInitials = collect([$user->first_name ?? '', $user->last_name ?? ''])
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr(trim($part), 0, 1)))
            ->take(2)
            ->implode('') ?: 'U';
        $avatarSvg = 'data:image/svg+xml;utf8,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">' .
            '<rect width="96" height="96" rx="24" fill="#90ee90"/>' .
            '<text x="50%" y="56%" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="34" font-weight="800" fill="#0f172a">' .
            e($avatarInitials) .
            '</text></svg>'
        );
        $avatarSrc = $user->profile_photo ?: $avatarSvg;
    @endphp
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
                    <img src="{{ $avatarSrc }}" alt="User"
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
                <div class="flex items-center gap-3 relative">
                    <button id="weekly-view-btn" onclick="toggleWeeklyView()"
                        class="bg-card border border-border px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-muted transition-colors flex items-center gap-2">
                        <iconify-icon icon="lucide:calendar"></iconify-icon>
                        Weekly View
                    </button>
                    <button id="plan-history-toggle" title="Past plans"
                        class="bg-card border border-border px-3 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-muted transition-colors flex items-center gap-2">
                        <iconify-icon icon="lucide:history"></iconify-icon>
                        <span class="hidden sm:inline">History</span>
                    </button>
                    <div id="plan-history-dropdown"
                        class="hidden absolute right-0 top-12 w-80 max-h-96 overflow-y-auto bg-card border border-border rounded-xl shadow-lg z-50 p-2">
                        <p class="text-xs font-bold text-muted-foreground px-3 py-2 sticky top-0 bg-card">Your past plans</p>
                        <div id="plan-history-list" class="flex flex-col gap-1">
                            <p class="text-xs text-muted-foreground px-3 py-4 text-center">Loading…</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                <div class="max-w-5xl mx-auto space-y-8">
                    <div id="workout-regen-banner"
                        class="hidden rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 flex items-center gap-2">
                        <iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon>
                        <span>A fresh workout is being generated in the background. This page will update when it is ready.</span>
                    </div>

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
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="1" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Tuesday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="2" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Wednesday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="3" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Thursday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="4" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Friday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="5" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Saturday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
	                                </article>
	                                <article data-workout-day="6" class="bg-background rounded-3xl border border-border p-5 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors">
	                                    <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Sunday</p>
	                                    <h3 class="font-bold mb-2">No plan yet</h3>
	                                    <p class="text-sm text-muted-foreground">Generate a routine to fill this day</p>
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
                                    <p class="font-bold text-sm" id="profile-exercise-pref">No plan yet</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Time Available</p>
                                    <p class="font-bold text-sm" id="profile-time">No plan yet</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Available Days</p>
                                    <p class="font-bold text-sm" id="profile-days">No plan yet</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Equipment</p>
                                    <p class="font-bold text-sm" id="profile-equipment">No plan yet</p>
                                </div>
                                <div class="bg-background rounded-xl p-4 border border-border">
                                    <p class="text-xs text-muted-foreground mb-1">Fitness Goals</p>
                                    <p class="font-bold text-sm" id="profile-goals">No plan yet</p>
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
                                    <p id="medical-alignment-text" class="text-xs text-muted-foreground leading-relaxed">
                                        Generate a routine to see how your saved profile and latest health context shape the plan.
                                    </p>
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
	                            <h2 id="workout-heading" class="text-2xl font-heading font-bold">No workout plan yet</h2>
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
	                                            class="px-2.5 py-1 rounded-md bg-muted text-muted-foreground text-xs font-bold">0 Mins</span>
	                                    </div>
	                                    <h3 id="workout-title" class="text-xl font-bold mb-2">Generate your AI workout</h3>
	                                    <p id="workout-description" class="text-sm text-muted-foreground mb-6">Your saved routine will appear here after generation.</p>

                                </div>
                            </div>

                            <div class="bg-background p-6 sm:px-8">
	                                <h4 id="workout-exercises-title" class="font-bold text-sm mb-4 uppercase tracking-wider text-muted-foreground">
	                                    Exercises (0)</h4>

	                                <div id="workout-exercise-list" class="space-y-3">
	                                    <div class="rounded-xl border border-dashed border-border bg-card p-4 text-sm text-muted-foreground">
	                                        Generate a routine to see exercise details.
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

        <!-- Generation progress overlay — shown while the 3-stage LLM
             pipeline runs. Replaces the previous "spinner only" UX so the
             user can see what's actually happening during the ~80s wait. -->
        <div id="gen-progress-modal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border shadow-2xl p-6 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <iconify-icon icon="lucide:sparkles" class="text-2xl"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <h3 id="gen-progress-title" class="font-bold text-lg leading-tight">Building your workout</h3>
                            <p class="text-xs text-muted-foreground">This usually takes about 80 seconds. Feel free to leave the page open.</p>
                        </div>
                    </div>

                    <ol id="gen-progress-steps" class="space-y-2 mt-1">
                        <!-- populated by JS -->
                    </ol>

                    <div class="flex items-center justify-between text-xs border-t border-border pt-3 mt-1">
                        <span class="text-muted-foreground">Elapsed</span>
                        <span id="gen-progress-elapsed" class="font-mono font-bold text-foreground">00:00</span>
                    </div>
                    <p id="gen-progress-tip" class="text-[11px] text-muted-foreground italic"></p>
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

        const WORKOUT_REGEN_FLAG = 'uplyfe_workout_regen';
        const WORKOUT_REGEN_TTL = 15 * 60 * 1000;
        let activeExercisePlanId = null;

        function showWorkoutRegenBanner() {
            document.getElementById('workout-regen-banner')?.classList.remove('hidden');
        }

        function hideWorkoutRegenBanner() {
            document.getElementById('workout-regen-banner')?.classList.add('hidden');
            localStorage.removeItem(WORKOUT_REGEN_FLAG);
        }

        function checkWorkoutRegenBanner() {
            const ts = parseInt(localStorage.getItem(WORKOUT_REGEN_FLAG) || '0', 10);
            if (ts && Date.now() - ts < WORKOUT_REGEN_TTL) {
                showWorkoutRegenBanner();
            } else if (ts) {
                localStorage.removeItem(WORKOUT_REGEN_FLAG);
            }
        }

        function isWorkoutRegenSatisfied(createdAt) {
            const ts = parseInt(localStorage.getItem(WORKOUT_REGEN_FLAG) || '0', 10);
            if (!ts || !createdAt) return false;
            const createdMs = new Date(createdAt).getTime();
            return Number.isFinite(createdMs) && createdMs >= ts - 5000;
        }

        function labelize(value, fallback = 'Not set') {
            const raw = Array.isArray(value) ? value.join(', ') : String(value ?? '').trim();
            if (!raw) return fallback;
            return raw
                .split(/([,\s/]+)/)
                .map(part => /^\d+(?:-\d+)?$/.test(part)
                    ? part
                    : /^[a-z0-9-]+$/i.test(part)
                    ? part.replace(/-/g, ' ').replace(/\b\w/g, ch => ch.toUpperCase())
                    : part)
                .join('')
                .replace(/\s+,/g, ',')
                .replace(/\s{2,}/g, ' ')
                .trim();
        }

        function setProfileTile(id, value, fallback = 'Not set') {
            const el = document.getElementById(id);
            if (el) el.textContent = labelize(value, fallback);
        }

        function applyExerciseRequestProfile(profile = {}) {
            setProfileTile('profile-exercise-pref', profile.exercise_preference);
            const time = profile.time_available
                ? `${String(profile.time_available).replace(/\s*mins?$/i, '')} mins`
                : null;
            setProfileTile('profile-time', time);
            const days = profile.available_days
                ? `${String(profile.available_days).replace(/\s*days?(\/week)?$/i, '')} days/week`
                : null;
            setProfileTile('profile-days', days);
            setProfileTile('profile-equipment', profile.equipment_available);
            setProfileTile('profile-goals', profile.fitness_goals);
        }

        function updateMedicalAlignment(assessment, createdAt = null) {
            const el = document.getElementById('medical-alignment-text');
            if (!el) return;
            const text = String(assessment || '').trim();
            if (!text) {
                el.textContent = 'Generate a routine to see how your saved profile and latest health context shape the plan.';
                return;
            }
            const short = text.length > 240 ? `${text.slice(0, 237).trim()}...` : text;
            const when = createdAt ? ` Updated ${new Date(createdAt).toLocaleDateString()}.` : '';
            el.textContent = `${short}${when}`;
        }

	        // `let` (not `const`) so the saved or AI-generated plan can replace this in-place.
	        let weeklyWorkoutPlans = [
	            {
	                dayLabel: "Workout",
	                heading: "No plan yet",
	                title: "Generate your AI workout",
	                duration: "0 Mins",
	                description: "Your saved routine will appear here after generation.",
	                exercises: []
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
	            const exercises = Array.isArray(plan.exercises) ? plan.exercises : [];
	            exercisesTitle.textContent = `Exercises (${exercises.length})`;

	            if (!exercises.length) {
	                exerciseList.innerHTML = `
	                    <div class="rounded-xl border border-dashed border-border bg-card p-4 text-sm text-muted-foreground">
	                        Generate a routine to see exercise details.
	                    </div>`;
	                return;
	            }

	            exerciseList.innerHTML = exercises
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

        // Deep-link: /exercise?day=monday switches to that day on load.
        function applyExerciseDeepLink() {
            try {
                const url = new URL(window.location.href);
                const want = (url.searchParams.get('day') || '').toLowerCase();
                if (!want) return;
                const idx = weeklyWorkoutPlans.findIndex(d =>
                    (d.dayLabel || '').toLowerCase() === want
                );
                if (idx >= 0) goToWorkoutDay(idx);
            } catch (e) { /* swallow */ }
        }

        // Pull the user's most recent saved exercise plan from the API and
	        // render it. Without this a freshly-generated plan would not persist
	        // visually across page visits.
        async function loadActiveExercisePlan() {
            try {
                const list = await fetch('/api/exercise-plans', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!list.ok) return;
                const data = await list.json();
                const plans = data.plans || [];
                if (!plans.length) return; // never generated — keep the placeholder
                const detail = await fetch(`/api/exercise-plans/${plans[0].id}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!detail.ok) return;
                const payload = await detail.json();
                activeExercisePlanId = payload._plan_id || plans[0].id;
                lastSeenExercisePlanId = activeExercisePlanId;
                applyAiPlanToExistingUi(payload);
                if (payload._request_payload?.profile) {
                    applyExerciseRequestProfile(payload._request_payload.profile);
                }
                updateMedicalAlignment(payload.assessment, payload._created_at);
                if (typeof stashCoachAssessment === 'function' && payload.assessment) {
                    stashCoachAssessment(payload.assessment, payload.equipment_resolved || []);
                }
                if (isWorkoutRegenSatisfied(payload._created_at)) {
                    hideWorkoutRegenBanner();
                }
            } catch (e) {
                console.warn('loadActiveExercisePlan failed:', e);
            }
        }

        // Poll every 30s while the page is visible — picks up a workout
        // regenerated in the background by the chat without a manual reload.
        let exercisePlanPollHandle = null;
        let lastSeenExercisePlanId = null;
        async function pollLatestExercisePlan() {
            try {
                const res = await fetch('/api/exercise-plans', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!res.ok) return;
                const data = await res.json();
                const plans = data.plans || [];
                if (!plans.length) return;
                const newest = plans[0].id;
                if (lastSeenExercisePlanId === null) {
                    lastSeenExercisePlanId = newest;
                    return;
                }
                if (newest !== lastSeenExercisePlanId) {
                    lastSeenExercisePlanId = newest;
                    await loadActiveExercisePlan();
                }
            } catch (_) { /* swallow */ }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // ORDER MATTERS: load the saved plan first, then honor any
            // ?day= deep-link against the freshly-loaded weeklyWorkoutPlans.
            checkWorkoutRegenBanner();
            await loadActiveExercisePlan();
            applyExerciseDeepLink();
            if (exercisePlanPollHandle) clearInterval(exercisePlanPollHandle);
            exercisePlanPollHandle = setInterval(() => {
                if (!document.hidden) pollLatestExercisePlan();
            }, 30000);
        });

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
            document.querySelectorAll('button').forEach(btn => {
                if (!btn.getAttribute('onclick')?.includes('generateNewRoutine')) return;
                if (busy) {
                    if (!btn.dataset.origLabel) btn.dataset.origLabel = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = `<iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon> Generating…`;
                } else {
                    if (btn.dataset.origLabel) btn.innerHTML = btn.dataset.origLabel;
                    btn.disabled = false;
                }
            });
            if (busy) openGenerationProgress();
            else closeGenerationProgress();
        }

        // ---------- Generation progress modal ----------
        // 3 stages that roughly match the gateway pipeline. We don't have
        // real progress events from the server (no SSE / WebSocket yet) so
        // we time-slice the bar based on the typical 80-100s wall clock.
        const GEN_STAGES = [
            { id: 'assessment', label: 'Analyzing your profile',     pctStart: 0,   pctEnd: 25 },
            { id: 'structure',  label: 'Designing your weekly split', pctStart: 25,  pctEnd: 55 },
            { id: 'exercises',  label: 'Picking matching exercises',  pctStart: 55,  pctEnd: 95 },
            { id: 'finalize',   label: 'Finalizing',                  pctStart: 95,  pctEnd: 100 },
        ];
        const GEN_TIPS = [
            "Each stage uses the local gemma4:26b model on your machine — it's smart but heavy.",
            "You can leave the page open or even switch tabs; the server keeps working.",
            "The exercise picker also searches a real exercise dataset, then asks the model to shape the picks into your plan.",
            "Faster generation would mean a smaller model — gemma4:26b is the quality tradeoff.",
        ];
        const EXPECTED_SECS = 90; // typical wall clock for the 3-stage pipeline

        let genStart = 0;
        let genTimer = null;
        function fmtMMSS(s) {
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const ss = (s % 60).toString().padStart(2, '0');
            return `${m}:${ss}`;
        }
        function openGenerationProgress(opts = {}) {
            const modal = document.getElementById('gen-progress-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            document.getElementById('gen-progress-title').textContent =
                opts.title || 'Building your workout';
            document.getElementById('gen-progress-tip').textContent =
                GEN_TIPS[Math.floor(Math.random() * GEN_TIPS.length)];
            genStart = Date.now();
            renderStages(0);
            if (genTimer) clearInterval(genTimer);
            genTimer = setInterval(() => {
                const elapsed = Math.floor((Date.now() - genStart) / 1000);
                document.getElementById('gen-progress-elapsed').textContent = fmtMMSS(elapsed);
                renderStages(Math.min(elapsed / EXPECTED_SECS, 0.99));
            }, 500);
        }
        function closeGenerationProgress() {
            const modal = document.getElementById('gen-progress-modal');
            if (!modal) return;
            // Snap to 100% briefly so the user sees the finish.
            renderStages(1);
            const elapsed = Math.floor((Date.now() - genStart) / 1000);
            document.getElementById('gen-progress-elapsed').textContent = fmtMMSS(elapsed);
            setTimeout(() => { modal.classList.add('hidden'); }, 350);
            if (genTimer) { clearInterval(genTimer); genTimer = null; }
        }
        function renderStages(fraction) {
            const pct = Math.round(fraction * 100);
            const stepsEl = document.getElementById('gen-progress-steps');
            if (!stepsEl) return;
            stepsEl.innerHTML = GEN_STAGES.map((s) => {
                let state, icon;
                if (pct >= s.pctEnd) {
                    state = 'done'; icon = 'lucide:check-circle-2';
                } else if (pct >= s.pctStart) {
                    state = 'active'; icon = 'lucide:loader-2';
                } else {
                    state = 'idle'; icon = 'lucide:circle';
                }
                const colour = state === 'done'  ? 'text-tertiary'
                            :  state === 'active'? 'text-primary'
                            :                      'text-muted-foreground';
                const spin = state === 'active' ? 'animate-spin' : '';
                const fill = state === 'active' ? Math.round(((pct - s.pctStart) / (s.pctEnd - s.pctStart)) * 100) : (state === 'done' ? 100 : 0);
                return `
                    <li class="flex items-center gap-3 text-sm">
                        <iconify-icon icon="${icon}" class="${colour} ${spin} text-lg"></iconify-icon>
                        <div class="flex-1">
                            <p class="${state !== 'idle' ? 'font-semibold' : 'text-muted-foreground'}">${s.label}</p>
                            <div class="h-1 mt-1 rounded-full bg-muted overflow-hidden">
                                <div class="h-full bg-primary transition-all" style="width:${Math.max(0, Math.min(100, fill))}%"></div>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
        }

        // AI plan → existing-UI plan shape.
        function applyAiPlanToExistingUi(aiPlan) {
            const aiDays = aiPlan.weekly_workout_plan || [];
            if (aiDays.length === 0) {
                // Don't silently fail — surface a clear message ON the page
                // (not just an alert) so the user knows their generation hit
                // an empty result and the visible workout is the OLD one.
                const headingEl = document.getElementById('workout-heading');
                if (headingEl) {
                    headingEl.textContent = 'AI returned no workout days — try Generate again';
                }
                alert('The AI returned no workout days. This is usually a transient model failure — try Generate again. If it keeps happening, simplify your equipment or goals.');
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
            activeExercisePlanId = aiPlan._plan_id || activeExercisePlanId;

            // Update the 7-day weekly-view cards (only the ones that map to a
            // generated day; leave the rest as "Rest day" stubs).
            updateWeeklyViewCards();

            // Re-render the daily view with the first day of the new plan.
            renderWorkoutDay(currentWorkoutDayIndex);

            // Stash the assessment + disclaimer so the user can see them.
            if (aiPlan.assessment) {
                stashCoachAssessment(aiPlan.assessment, aiPlan.equipment_resolved || []);
            }
            if (aiPlan._request_payload?.profile) {
                applyExerciseRequestProfile(aiPlan._request_payload.profile);
            }
            updateMedicalAlignment(aiPlan.assessment, aiPlan._created_at);
            if (isWorkoutRegenSatisfied(aiPlan._created_at)) {
                hideWorkoutRegenBanner();
            }

            // Scroll the user to the freshly-rendered routine.
            document.getElementById('daily-view-content')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

	        // The page ships with 7 pre-rendered day slots in #weekly-view-panel
	        // (data-workout-day="0"..."6"). Update each one's text so the
	        // weekly view reflects the saved/generated plan.
        function updateWeeklyViewCards() {
            document.querySelectorAll('[data-workout-day]').forEach(card => {
                const idx = Number(card.dataset.workoutDay);
                const day = weeklyWorkoutPlans[idx];
                const labelEl = card.querySelector('p:first-child');
                const titleEl = card.querySelector('h3');
                const subEl = card.querySelectorAll('p')[1] || card.querySelector('p.text-muted-foreground');
                if (!day) {
                    if (labelEl) labelEl.textContent = `Day ${idx + 1}`;
                    if (titleEl) titleEl.textContent = 'Rest day';
                    if (subEl) subEl.textContent = 'No scheduled workout';
                    card.classList.add('opacity-60');
                    return;
                }
                card.classList.remove('opacity-60');
                if (labelEl) labelEl.textContent = day.dayLabel || `Day ${idx + 1}`;
                if (titleEl) titleEl.textContent = day.title || day.heading || 'Workout';
                if (subEl) {
                    const count = Array.isArray(day.exercises) ? day.exercises.length : 0;
                    subEl.textContent = [day.duration, count ? `${count} exercises` : null]
                        .filter(Boolean)
                        .join(' · ') || (day.description || 'Workout scheduled');
                }
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

        // ---------- Past-plan history dropdown ----------
        async function loadExercisePlanList() {
            const listEl = document.getElementById('plan-history-list');
            try {
                const res = await fetch('/api/exercise-plans', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = res.ok ? await res.json() : { plans: [] };
                const items = data.plans || [];
                if (items.length === 0) {
                    listEl.innerHTML = '<p class="text-xs text-muted-foreground px-3 py-4 text-center">No saved plans yet. Generate one and it\'ll appear here.</p>';
                    return;
                }
                listEl.innerHTML = items.map(p => {
                    const when = p.created_at ? new Date(p.created_at).toLocaleString() : '';
                    const goal = p.fitness_goals || '—';
                    const days = p.available_days ? ` · ${escapeHtml(p.available_days)} days` : '';
                    return `
                        <button data-pid="${p.id}"
                            class="plan-item text-left px-3 py-2 rounded-lg hover:bg-muted transition-colors">
                            <p class="text-sm font-medium">Plan #${p.id} <span class="text-muted-foreground">· ${escapeHtml(goal)}${days}</span></p>
                            <p class="text-[10px] text-muted-foreground">${escapeHtml(when)}</p>
                        </button>
                    `;
                }).join('');
                listEl.querySelectorAll('.plan-item').forEach(btn => {
                    btn.addEventListener('click', () => loadExercisePlan(Number(btn.dataset.pid)));
                });
            } catch (e) {
                listEl.innerHTML = '<p class="text-xs text-destructive px-3 py-4 text-center">Failed to load.</p>';
            }
        }

        async function loadExercisePlan(id) {
            try {
                const res = await fetch(`/api/exercise-plans/${id}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const payload = await res.json();
                document.getElementById('plan-history-dropdown').classList.add('hidden');
                applyAiPlanToExistingUi(payload);
                if (typeof stashCoachAssessment === 'function' && payload.assessment) {
                    stashCoachAssessment(payload.assessment, payload.equipment_resolved || []);
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                console.warn('loadExercisePlan failed:', e);
            }
        }

        document.getElementById('plan-history-toggle')?.addEventListener('click', (e) => {
            e.stopPropagation();
            const dd = document.getElementById('plan-history-dropdown');
            const willOpen = dd.classList.contains('hidden');
            dd.classList.toggle('hidden');
            if (willOpen) loadExercisePlanList();
        });

        document.addEventListener('click', (e) => {
            const dd = document.getElementById('plan-history-dropdown');
            const toggle = document.getElementById('plan-history-toggle');
            if (dd && !dd.classList.contains('hidden') && !dd.contains(e.target) && e.target !== toggle && !toggle?.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        // Helper used by the dropdown when the page's own escapeHtml isn't in scope.
        if (typeof escapeHtml !== 'function') {
            window.escapeHtml = function (s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            };
        }
    </script>
</body>

</html>
