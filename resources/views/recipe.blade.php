<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
        <!-- Sidebar Navigation (Same as Dashboard) -->
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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
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
                    <h1 class="text-xl font-heading font-bold">Nutrition & Recipes</h1>
                </div>
                <div class="flex items-center gap-3 relative">
                    <div
                        class="hidden sm:flex items-center gap-2 bg-muted px-3 py-1.5 rounded-full text-sm font-medium">
                        <iconify-icon icon="lucide:flame" class="text-orange-500"></iconify-icon>
                        <span>2,150 kcal / day</span>
                    </div>
                    <button id="meal-history-toggle" title="Past meal plans"
                        class="bg-card border border-border px-3 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-muted transition-colors flex items-center gap-2">
                        <iconify-icon icon="lucide:history"></iconify-icon>
                        <span class="hidden sm:inline">History</span>
                    </button>
                    <div id="meal-history-dropdown"
                        class="hidden absolute right-12 top-12 w-80 max-h-96 overflow-y-auto bg-card border border-border rounded-xl shadow-lg z-50 p-2">
                        <p class="text-xs font-bold text-muted-foreground px-3 py-2 sticky top-0 bg-card">Your past meal plans</p>
                        <div id="meal-history-list" class="flex flex-col gap-1">
                            <p class="text-xs text-muted-foreground px-3 py-4 text-center">Loading…</p>
                        </div>
                    </div>
                    <img src="{{ $user->profile_photo ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="User"
                        class="w-8 h-8 rounded-full border border-border cursor-pointer">
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                <div class="max-w-6xl mx-auto space-y-8">

                    <!-- Preferences Header -->
                    <section class="bg-card rounded-3xl border border-border p-6 sm:p-8 shadow-sm">
                        <div class="flex flex-col lg:flex-row gap-8">
                            <div class="flex-1">
                                <h2 class="text-2xl font-heading font-bold mb-2 flex items-center gap-2">
                                    <iconify-icon icon="lucide:sliders-horizontal" class="text-primary"></iconify-icon>
                                    Dietary Preferences
                                </h2>
                                <p class="text-sm text-muted-foreground mb-6">
                                    Adjust your preferences. Our AI will align these with your medical report to
                                    generate the perfect recipes.
                                </p>

                                <div class="space-y-6">
                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Diet Type</label>
                                        <div id="diet-type-chips" class="flex flex-wrap gap-2">
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-primary bg-primary/10 text-primary-foreground text-sm font-semibold transition-colors">Balanced</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Keto</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Vegan</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Mediterranean</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Paleo</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Low-Glycemic</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">High-Protein</button>
                                            <button id="add-diet-type-btn" type="button"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-dashed border-border text-muted-foreground hover:text-foreground text-sm font-medium transition-colors">
                                                <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                                                Add Diet Type
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Meal Preferences</label>
                                        <div id="meal-preference-chips" class="flex flex-wrap gap-2">
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Quick
                                                & Easy</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Meal
                                                Prep</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">One-Pot
                                                Meals</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Grilled</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Baked</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="meal"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Stir-Fry</button>
                                            <button id="add-meal-preference-btn" type="button"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-dashed border-border text-muted-foreground hover:text-foreground text-sm font-medium transition-colors">
                                                <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                                                Add Meal Preference
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Allergies & Exclusions</label>
                                        <div id="exclusion-chip-group" class="flex flex-wrap gap-2">
                                            <button type="button" data-chip-toggle="true" data-chip-group="exclusion"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Gluten-free
                                            </button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="exclusion"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Dairy-free
                                            </button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="exclusion"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Nut-free
                                            </button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="exclusion"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Soy-free
                                            </button>
                                            <button id="add-exclusion-btn" type="button"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-dashed border-border text-muted-foreground hover:text-foreground text-sm font-medium transition-colors">
                                                <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                                                Add Exclusion
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Cuisine Preferences</label>
                                        <div id="cuisine-preference-chips" class="flex flex-wrap gap-2">
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Italian</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Mexican</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Asian</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Middle
                                                Eastern</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">American</button>
                                            <button id="add-cuisine-preference-btn" type="button"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-dashed border-border text-muted-foreground hover:text-foreground text-sm font-medium transition-colors">
                                                <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                                                Add Cuisine Preference
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="w-full lg:w-72 bg-background rounded-2xl p-6 border border-border flex flex-col justify-center gap-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mt-1">
                                        <iconify-icon icon="lucide:info" class="text-lg"></iconify-icon>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold mb-1">AI Medical Constraint</p>
                                        <p class="text-xs text-muted-foreground leading-relaxed">Based on your low
                                            Vitamin D, the AI will prioritize recipes rich in fortified foods, egg
                                            yolks, and fatty fish.</p>
                                    </div>
                                </div>
                                <button id="generate-recipes-btn"
                                    class="w-full mt-2 bg-primary text-primary-foreground py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                    <iconify-icon icon="lucide:sparkles"></iconify-icon>
                                    Generate New Recipes
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Recipe Grid -->
                    <section>
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-2xl font-heading font-bold" id="meal-plan-title">Your meal plan</h2>
                                <p class="text-xs text-muted-foreground mt-1" id="meal-plan-source">Loading…</p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <input type="date" id="week-start-date"
                                    class="bg-card border border-border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                                <button id="reset-today-btn"
                                    class="px-3 py-1.5 rounded-lg border border-border bg-card text-xs font-semibold hover:bg-muted transition-colors">Today</button>
                                <button id="prev-day-btn" title="Previous day"
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <iconify-icon icon="lucide:chevron-left"></iconify-icon>
                                </button>
                                <button id="next-day-btn" title="Next day"
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <!-- 7-day strip; populated by buildDateStrip() -->
                        <div id="date-strip" class="grid grid-cols-7 gap-2 mb-3"></div>
                        <!-- Active food exclusions banner; populated by loadExclusionsBanner() -->
                        <div id="exclusions-banner"
                            class="hidden mb-4 flex items-center flex-wrap gap-2 text-xs bg-red-50 border border-red-200 rounded-xl px-3 py-2"></div>
                        <div id="recipe-feedback" class="mb-4 text-sm text-muted-foreground"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="recipe-grid">

                            <!-- Recipe Card 1: Breakfast -->
                            <div onclick="openRecipeModal('breakfast')"
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col cursor-pointer" id="recipe-breakfast">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <!-- Placeholder for image -->
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm" id="breakfast-badge">
                                        Breakfast</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm" id="breakfast-heart">
                                        <iconify-icon icon="lucide:heart"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2" id="breakfast-tags">
                                        <span
                                            class="text-xs font-semibold text-tertiary bg-tertiary/10 px-2 py-0.5 rounded-full">High
                                            Vitamin D</span>
                                    </div>
                                    <h3 class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors" id="breakfast-title">
                                        Smoked Salmon & Avocado Toast</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2" id="breakfast-description">Rich in omega-3s and
                                        Vitamin D to support your recent checkup goals. Served on gluten-free bread.</p>

                                    <div class="mt-auto grid grid-cols-2 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold" id="breakfast-calories">420</p>
                                        </div>
                                        <div class="text-center border-l border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold" id="breakfast-protein">22g</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recipe Card 2: Lunch -->
                            <div onclick="openRecipeModal('lunch')"
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col cursor-pointer" id="recipe-lunch">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm">
                                        Lunch</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm" id="lunch-heart">
                                        <iconify-icon icon="lucide:heart"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2" id="lunch-tags">
                                        <span
                                            class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Heart
                                            Healthy</span>
                                    </div>
                                    <h3
                                        class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors" id="lunch-title">
                                        Mediterranean Quinoa Bowl</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2" id="lunch-description">Packed with fiber to help
                                        maintain your excellent cholesterol levels. Features olives, cucumber, and feta.
                                    </p>

                                    <div class="mt-auto grid grid-cols-2 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold" id="lunch-calories">550</p>
                                        </div>
                                        <div class="text-center border-l border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold" id="lunch-protein">18g</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recipe Card 3: Dinner -->
                            <div onclick="openRecipeModal('dinner')"
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col cursor-pointer" id="recipe-dinner">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm">
                                        Dinner</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm" id="dinner-heart">
                                        <iconify-icon icon="lucide:heart"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2" id="dinner-tags">
                                        <span
                                            class="text-xs font-semibold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Low
                                            Glycemic</span>
                                    </div>
                                    <h3
                                        class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors" id="dinner-title">
                                        Lemon Herb Grilled Chicken</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2" id="dinner-description">A lean protein dinner
                                        paired with roasted asparagus to keep your fasting blood sugar stable overnight.
                                    </p>

                                    <div class="mt-auto grid grid-cols-2 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold" id="dinner-calories">480</p>
                                        </div>
                                        <div class="text-center border-l border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold" id="dinner-protein">42g</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Favorites -->
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-2xl font-heading font-bold">Your liked meals</h2>
                                <p class="text-xs text-muted-foreground mt-1">Tap the heart on any meal to save it.</p>
                            </div>
                            <span id="favorites-count" class="text-xs text-muted-foreground"></span>
                        </div>
                        <div id="favorites-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <p class="col-span-full text-sm text-muted-foreground text-center py-8" id="favorites-empty">No liked meals yet.</p>
                        </div>
                    </section>
                </div>
            </div>
        </main>

        <!-- Recipe Detail Modal -->
        <div id="recipe-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeRecipeModal()"></div>
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm sm:max-w-2xl md:max-w-4xl mx-2 sm:mx-4 h-[95vh] sm:h-[90vh] flex flex-col">
                <div class="bg-card rounded-3xl border border-border shadow-xl flex-1 flex flex-col overflow-hidden">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-6 border-b border-border flex-shrink-0">
                        <div>
                            <h3 id="modal-recipe-title" class="text-2xl font-heading font-bold">Recipe Title</h3>
                            <p id="modal-recipe-subtitle" class="text-sm text-muted-foreground">Recipe subtitle</p>
                        </div>
                        <button onclick="closeRecipeModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <!-- Modal Content - Scrollable -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Recipe Image -->
                        <div class="h-64 bg-muted relative flex-shrink-0">
                            <div id="modal-recipe-image"
                                class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                <iconify-icon icon="lucide:image" class="text-6xl opacity-20"></iconify-icon>
                            </div>
                            <div class="absolute top-4 left-4">
                                <span id="modal-recipe-badge"
                                    class="px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm font-semibold">Badge</span>
                            </div>
                            <div class="absolute top-4 right-4 flex gap-2">
                                <button
                                    class="w-10 h-10 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm">
                                    <iconify-icon icon="lucide:heart"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Recipe Details -->
                        <div class="p-6 space-y-6">
                            <!-- Nutrition -->
                            <div class="grid grid-cols-3 gap-4 p-4 bg-background rounded-2xl border border-border">
                                <div class="text-center">
                                    <p class="text-xs text-muted-foreground">Calories</p>
                                    <p id="modal-calories" class="text-lg font-bold">420</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-muted-foreground">Protein</p>
                                    <p id="modal-protein" class="text-lg font-bold">22g</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-muted-foreground">Carbs</p>
                                    <p id="modal-carbs" class="text-lg font-bold">35g</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <h4 class="font-bold text-lg mb-3">Description</h4>
                                <p id="modal-description" class="text-muted-foreground leading-relaxed">
                                    Recipe description goes here.
                                </p>
                            </div>

                            <!-- Health Benefits -->
                            <div>
                                <h4 class="font-bold text-lg mb-3">Health Benefits</h4>
                                <div id="modal-benefits" class="flex flex-wrap gap-2">
                                    <!-- Benefits will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Ingredients -->
                            <div>
                                <h4 class="font-bold text-lg mb-3">Ingredients</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <ul id="modal-ingredients" class="space-y-2">
                                        <!-- Ingredients will be populated by JavaScript -->
                                    </ul>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div>
                                <h4 class="font-bold text-lg mb-3">How to Cook</h4>
                                <div id="modal-instructions" class="space-y-4">
                                    <!-- Instructions will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Tips -->
                            <div>
                                <h4 class="font-bold text-lg mb-3">Chef's Tips</h4>
                                <div id="modal-tips" class="bg-primary/5 rounded-2xl p-4 border border-primary/20">
                                    <!-- Tips will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Exclusion Modal -->
        <div id="add-exclusion-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeAddExclusionModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Add Exclusion</h3>
                        <button onclick="closeAddExclusionModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Exclusion Type</label>
                            <select id="exclusion-type"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Select type...</option>
                                <option value="allergy">Allergy</option>
                                <option value="intolerance">Intolerance</option>
                                <option value="preference">Preference</option>
                                <option value="religious">Religious</option>
                                <option value="medical">Medical</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Ingredient/Food</label>
                            <input id="exclusion-item" type="text" placeholder="e.g., peanuts, shellfish, pork..."
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="closeAddExclusionModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="addExclusion()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                                Add Exclusion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Diet Type Modal -->
        <div id="add-diet-type-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeAddDietTypeModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Add Diet Type</h3>
                        <button onclick="closeAddDietTypeModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Diet Type</label>
                            <input id="diet-item" type="text" placeholder="vegan, low-carb, high-protein..."
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="closeAddDietTypeModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="addDietType()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                                Add Diet Type
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Meal Preference Modal -->
        <div id="add-meal-preference-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeAddMealPreferenceModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Add Meal Preference</h3>
                        <button onclick="closeAddMealPreferenceModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Meal Preference</label>
                            <input id="meal-preference-item" type="text" placeholder="simple, healthy, quick..."
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="closeAddMealPreferenceModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="addMealPreference()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                                Add Meal Preference
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Cuisine Preference Modal -->
        <div id="add-cuisine-preference-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeAddCuisinePreferenceModal()"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-heading font-bold">Add Cuisine Preference</h3>
                        <button onclick="closeAddCuisinePreferenceModal()"
                            class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Cuisine Preference</label>
                            <input id="cuisine-preference-item" type="text" placeholder="Italian, Mexican, Asian..."
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="closeAddCuisinePreferenceModal()"
                                class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                                Cancel
                            </button>
                            <button onclick="addCuisinePreference()"
                                class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                                Add Cuisine Preference
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // POSTs to the Laravel AI route, which forwards to the gateway →
        // recipe-generator. The old static-JSON path (`/food/data/example.json`)
        // is gone — we generate fresh plans on demand now.
        const RECIPE_API_URL = '/api/ai/recipe/weekly-menu';
        let mealPlans = {};
        // `dayOrder` is the list of weekday keys present in the loaded plan
        // (monday..sunday or day_1..day_N). `weekDates` is the 7 ISO dates
        // the strip currently shows. `dateIndex` is which strip cell is
        // active. `currentDay` is the weekday key for the active date.
        let dayOrder = [];
        let currentDay = 'monday';
        let weekDates = [];
        let weekStart = startOfTodayIso();
        let dateIndex = 0;
        let activePlanId = null;
        let likedByKey = new Map(); // "planId:dayKey:mealType" -> {id,title}

        const WEEKDAY_KEYS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // Format a Date as YYYY-MM-DD using its LOCAL components. The naive
        // `.toISOString().slice(0,10)` path converts to UTC, which in any
        // non-UTC timezone (e.g. Jakarta UTC+7) flips local midnight to the
        // previous calendar day and made the page open on yesterday.
        function toLocalIso(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function startOfTodayIso() {
            return toLocalIso(new Date());
        }

        function parseIso(iso) {
            // Treat dates as local midnight so weekday lookup matches the
            // user's calendar, not UTC's.
            const [y, m, d] = iso.split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        function isoForOffset(startIso, offsetDays) {
            const d = parseIso(startIso);
            d.setDate(d.getDate() + offsetDays);
            return toLocalIso(d);
        }

        function weekdayKeyFor(iso) {
            return WEEKDAY_KEYS[parseIso(iso).getDay()];
        }

        function rebuildWeekDates() {
            weekDates = Array.from({ length: 7 }, (_, i) => isoForOffset(weekStart, i));
        }

        function likeKey(planId, dayKey, mealType) {
            return `${planId ?? 'none'}:${dayKey ?? 'none'}:${mealType}`;
        }

        // Title Case — capitalize each word except common minor words, which
        // stay lowercase UNLESS they're the first or last word. Roman-numeral
        // suffixes ("II", "III") and all-caps acronyms are left alone.
        const TITLE_MINORS = new Set([
            'a','an','and','as','at','but','by','for','in','nor','of','on','or',
            'so','the','to','up','yet','with','from','over','into','out','via',
        ]);
        function titleCase(str) {
            if (!str) return '';
            const words = String(str).trim().split(/\s+/);
            return words.map((w, i) => {
                if (!w) return w;
                // Preserve already-capitalized tokens like ROMAN, II, BBQ.
                if (/^[A-Z]{2,}$/.test(w)) return w;
                if (/^[IVXLCM]+$/i.test(w) && w.length <= 4) return w.toUpperCase();
                const lower = w.toLowerCase();
                const isMinor = TITLE_MINORS.has(lower) && i !== 0 && i !== words.length - 1;
                if (isMinor) return lower;
                // Capitalize first letter; preserve apostrophes / hyphens.
                return lower.replace(/(^|[\s\-'/])(\p{L})/gu, (_, sep, ch) => sep + ch.toUpperCase());
            }).join(' ');
        }

        function setRecipeFeedback(message, type = 'info') {
            const feedback = document.getElementById('recipe-feedback');
            if (!feedback) return;

            feedback.textContent = message || '';
            feedback.classList.remove('text-muted-foreground', 'text-destructive', 'text-tertiary');
            if (type === 'error') {
                feedback.classList.add('text-destructive');
            } else if (type === 'success') {
                feedback.classList.add('text-tertiary');
            } else {
                feedback.classList.add('text-muted-foreground');
            }
        }

        function formatDayTitle(dayKey) {
            return `${dayKey.charAt(0).toUpperCase()}${dayKey.slice(1)} AI Meal Plan`;
        }

        function normalizeDayPlan(dayData, dayKey) {
            if (!dayData || !dayData.breakfast || !dayData.lunch || !dayData.dinner) {
                return null;
            }

            return {
                title: dayData.title || formatDayTitle(dayKey),
                breakfast: dayData.breakfast,
                lunch: dayData.lunch,
                dinner: dayData.dinner,
                snack: dayData.snack || null
            };
        }

        // Collect the user's dietary preferences from the chip buttons on the
        // page. Each chip button has data-chip-group and toggles aria-pressed.
        function collectActiveChips(group) {
            return Array.from(document.querySelectorAll(
                `button[data-chip-group="${group}"][aria-pressed="true"]`
            )).map(b => (b.textContent || '').trim()).filter(Boolean);
        }

        function buildRecipeRequest() {
            return {
                target_calories: 2000,
                servings: 1,
                diet: 'none',
                allergies: collectActiveChips('allergy'),
                cuisine_preferences: collectActiveChips('cuisine'),
                days: 7,
            };
        }

        function updateRecipeCard(mealType, recipe) {
            if (!recipe) return;

            document.getElementById(`${mealType}-title`).textContent = titleCase(recipe.title || '-');
            document.getElementById(`${mealType}-description`).textContent = recipe.description || '-';
            document.getElementById(`${mealType}-calories`).textContent = recipe.calories || '-';
            document.getElementById(`${mealType}-protein`).textContent = recipe.protein || '-';

            const tagsContainer = document.getElementById(`${mealType}-tags`);
            tagsContainer.innerHTML = '';
            const tags = Array.isArray(recipe.tags) ? recipe.tags : [];
            tags.forEach((tag) => {
                const tagElement = document.createElement('span');
                tagElement.className = 'text-xs font-semibold text-tertiary bg-tertiary/10 px-2 py-0.5 rounded-full';
                tagElement.textContent = tag.text || '';
                tagsContainer.appendChild(tagElement);
            });
        }

        // Pick the right plan day for an ISO date. Strategy:
        //   1. If the plan is keyed by weekdays (monday..sunday), use the
        //      date's weekday → that key.
        //   2. Else (day_1..day_N), use the strip offset → `day_{i+1}`.
        function dayKeyForDate(iso) {
            if (!dayOrder.length) return null;
            const wk = weekdayKeyFor(iso);
            if (dayOrder.includes(wk)) return wk;
            const offset = weekDates.indexOf(iso);
            const numeric = `day_${(offset >= 0 ? offset : 0) + 1}`;
            if (dayOrder.includes(numeric)) return numeric;
            // Fallback: cycle through whatever the plan has.
            const idx = (offset >= 0 ? offset : 0) % dayOrder.length;
            return dayOrder[idx];
        }

        function updateMealPlan() {
            if (!weekDates.length) rebuildWeekDates();
            const activeIso = weekDates[dateIndex] || weekDates[0];
            currentDay = dayKeyForDate(activeIso);
            const plan = currentDay ? mealPlans[currentDay] : null;
            const titleEl = document.getElementById('meal-plan-title');
            const labelDate = parseIso(activeIso).toLocaleDateString(undefined, {
                weekday: 'long', month: 'short', day: 'numeric',
            });
            if (!plan) {
                titleEl.textContent = `${labelDate}`;
                ['breakfast', 'lunch', 'dinner'].forEach(t => {
                    const titleNode = document.getElementById(`${t}-title`);
                    if (titleNode) titleNode.textContent = '—';
                    const desc = document.getElementById(`${t}-description`);
                    if (desc) desc.textContent = 'No plan loaded for this day.';
                    const cal = document.getElementById(`${t}-calories`); if (cal) cal.textContent = '—';
                    const pro = document.getElementById(`${t}-protein`); if (pro) pro.textContent = '—';
                    syncHeartButton(t, null);
                });
                return;
            }
            titleEl.textContent = `${labelDate}${plan.title ? ' · ' + titleCase(plan.title) : ''}`;
            updateRecipeCard('breakfast', plan.breakfast);
            updateRecipeCard('lunch', plan.lunch);
            updateRecipeCard('dinner', plan.dinner);
            ['breakfast', 'lunch', 'dinner'].forEach(t => syncHeartButton(t, plan[t]));
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prev-day-btn');
            const nextBtn = document.getElementById('next-day-btn');
            if (!prevBtn || !nextBtn) return;
            prevBtn.disabled = dateIndex <= 0;
            nextBtn.disabled = dateIndex >= weekDates.length - 1;
        }

        function navigateDay(direction) {
            let next = dateIndex;
            if (direction === 'next') next = Math.min(dateIndex + 1, weekDates.length - 1);
            if (direction === 'prev') next = Math.max(dateIndex - 1, 0);
            if (next !== dateIndex) {
                dateIndex = next;
                buildDateStrip();
                updateMealPlan();
                updateNavigationButtons();
            }
        }

        function buildDateStrip() {
            rebuildWeekDates();
            const strip = document.getElementById('date-strip');
            if (!strip) return;
            strip.innerHTML = weekDates.map((iso, i) => {
                const d = parseIso(iso);
                const today = startOfTodayIso();
                const isActive = i === dateIndex;
                const isToday = iso === today;
                const weekday = d.toLocaleDateString(undefined, { weekday: 'short' });
                const dayNum = d.getDate();
                const cls = isActive
                    ? 'bg-primary text-primary-foreground border-primary shadow-md'
                    : 'bg-card border-border text-foreground hover:border-primary/40 hover:bg-muted';
                return `
                    <button type="button" data-iso="${iso}" data-idx="${i}"
                        class="date-cell flex flex-col items-center py-2 rounded-xl border transition-all ${cls}">
                        <span class="text-[10px] font-bold uppercase tracking-wide opacity-70">${weekday}</span>
                        <span class="text-lg font-bold leading-none mt-1">${dayNum}</span>
                        ${isToday ? '<span class="text-[9px] mt-1 opacity-80">today</span>' : ''}
                    </button>
                `;
            }).join('');
            strip.querySelectorAll('.date-cell').forEach(btn => {
                btn.addEventListener('click', () => {
                    dateIndex = Number(btn.dataset.idx);
                    buildDateStrip();
                    updateMealPlan();
                    updateNavigationButtons();
                });
            });
            // Keep the date input in sync (it's the week-start anchor).
            const dateInput = document.getElementById('week-start-date');
            if (dateInput && dateInput.value !== weekStart) {
                dateInput.value = weekStart;
            }
            const sourceEl = document.getElementById('meal-plan-source');
            if (sourceEl) {
                if (activePlanId) {
                    sourceEl.textContent = `Showing plan #${activePlanId} · 7 days starting ${weekStart}`;
                } else {
                    sourceEl.textContent = `No saved plan yet. Generate one or pick a date · 7 days starting ${weekStart}`;
                }
            }
        }

        // Toggle the busy state on the Generate button + open the staged
        // progress overlay so the user can see what's happening during the
        // multi-minute LLM wait.
        function setGenerateBusy(busy) {
            const btn = document.getElementById('generate-recipes-btn');
            if (btn) {
                if (busy) {
                    if (!btn.dataset.origLabel) btn.dataset.origLabel = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = `<iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon> Generating…`;
                } else {
                    if (btn.dataset.origLabel) btn.innerHTML = btn.dataset.origLabel;
                    btn.disabled = false;
                }
            }
            if (busy) openRecipeGenProgress();
            else closeRecipeGenProgress();
        }

        // ---------- Recipe generation progress modal ----------
        // Weekly menu = 7 days x 4 meals = ~28 LLM calls. Wall clock is
        // ~5-7 minutes on gemma4:26b. Show staged progress so the user
        // doesn't think the app froze.
        const RECIPE_GEN_STAGES = [
            { label: 'Retrieving healthy recipes',  pctStart: 0,  pctEnd: 15 },
            { label: 'Picking meals for each day',  pctStart: 15, pctEnd: 90 },
            { label: 'Formatting your weekly plan', pctStart: 90, pctEnd: 100 },
        ];
        const RECIPE_EXPECTED_SECS = 360;  // 6 minutes typical
        let recGenStart = 0;
        let recGenTimer = null;

        function fmtMMSS(s) {
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const ss = (s % 60).toString().padStart(2, '0');
            return `${m}:${ss}`;
        }
        function ensureRecipeGenModal() {
            if (document.getElementById('rec-gen-modal')) return;
            const html = `
                <div id="rec-gen-modal" class="fixed inset-0 z-[60] hidden">
                    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-4">
                        <div class="bg-card rounded-3xl border border-border shadow-2xl p-6 flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <iconify-icon icon="lucide:utensils" class="text-2xl"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg leading-tight">Building your weekly meal plan</h3>
                                    <p class="text-xs text-muted-foreground">This usually takes about 5-7 minutes. You can switch tabs — the page auto-refreshes when it's ready.</p>
                                </div>
                            </div>
                            <ol id="rec-gen-steps" class="space-y-2 mt-1"></ol>
                            <div class="flex items-center justify-between text-xs border-t border-border pt-3 mt-1">
                                <span class="text-muted-foreground">Elapsed</span>
                                <span id="rec-gen-elapsed" class="font-mono font-bold text-foreground">00:00</span>
                            </div>
                            <p class="text-[11px] text-muted-foreground italic">Each meal is picked from a curated set first, then formatted by the local gemma4:26b model.</p>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        }
        function renderRecipeStages(fraction) {
            const pct = Math.round(fraction * 100);
            const el = document.getElementById('rec-gen-steps');
            if (!el) return;
            el.innerHTML = RECIPE_GEN_STAGES.map(s => {
                let state, icon;
                if (pct >= s.pctEnd) { state = 'done'; icon = 'lucide:check-circle-2'; }
                else if (pct >= s.pctStart) { state = 'active'; icon = 'lucide:loader-2'; }
                else { state = 'idle'; icon = 'lucide:circle'; }
                const colour = state === 'done' ? 'text-tertiary' : state === 'active' ? 'text-primary' : 'text-muted-foreground';
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
        function openRecipeGenProgress() {
            ensureRecipeGenModal();
            document.getElementById('rec-gen-modal').classList.remove('hidden');
            recGenStart = Date.now();
            renderRecipeStages(0);
            if (recGenTimer) clearInterval(recGenTimer);
            recGenTimer = setInterval(() => {
                const elapsed = Math.floor((Date.now() - recGenStart) / 1000);
                document.getElementById('rec-gen-elapsed').textContent = fmtMMSS(elapsed);
                renderRecipeStages(Math.min(elapsed / RECIPE_EXPECTED_SECS, 0.99));
            }, 750);
        }
        function closeRecipeGenProgress() {
            const m = document.getElementById('rec-gen-modal');
            if (!m) return;
            renderRecipeStages(1);
            const elapsed = Math.floor((Date.now() - recGenStart) / 1000);
            const el = document.getElementById('rec-gen-elapsed');
            if (el) el.textContent = fmtMMSS(elapsed);
            setTimeout(() => { m.classList.add('hidden'); }, 350);
            if (recGenTimer) { clearInterval(recGenTimer); recGenTimer = null; }
        }

        async function loadGeneratedMealPlans() {
            setRecipeFeedback('Asking the AI to plan your week…', 'info');
            setGenerateBusy(true);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let response;
            try {
                response = await fetch(RECIPE_API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(buildRecipeRequest()),
                });
            } catch (e) {
                setGenerateBusy(false);
                setRecipeFeedback('Network error: ' + (e?.message || e), 'error');
                return;
            }

            const text = await response.text();
            let body;
            try { body = text ? JSON.parse(text) : {}; } catch { body = { error: text }; }

            if (!response.ok) {
                setGenerateBusy(false);
                // Surface the gateway / Laravel error message verbatim — the
                // empty-collection guard returns a helpful "drop JSON, run
                // ingest" message that users need to see.
                const msg = body.error || body.detail || body.message
                    || ('HTTP ' + response.status);
                setRecipeFeedback(msg, 'error');
                return;
            }

            // The recipe-generator returns { summary, plan: { monday: {...}, ... } }.
            const planData = body.plan || body;  // tolerate either shape
            const dayKeys = Object.keys(planData);
            const normalized = {};
            dayKeys.forEach((day) => {
                const normalizedDay = normalizeDayPlan(planData[day], day);
                if (normalizedDay) {
                    normalized[day] = normalizedDay;
                }
            });

            setGenerateBusy(false);

            if (!Object.keys(normalized).length) {
                setRecipeFeedback('AI returned an empty or malformed plan. Please try again.', 'error');
                return;
            }

            mealPlans = normalized;
            dayOrder = Object.keys(normalized);
            currentDay = normalized.monday ? 'monday' : dayOrder[0];

            updateMealPlan();
            updateNavigationButtons();
            setRecipeFeedback(body.summary || 'AI meal plan ready.', 'success');
        }

        function openRecipeModal(mealType) {
            const dayPlan = mealPlans[currentDay];
            if (!dayPlan || !dayPlan[mealType]) return;
            const recipe = dayPlan[mealType];

            document.getElementById('modal-recipe-title').textContent = titleCase(recipe.title || '-');
            document.getElementById('modal-recipe-subtitle').textContent = recipe.subtitle || '-';
            document.getElementById('modal-recipe-badge').textContent = recipe.badge || '-';
            document.getElementById('modal-calories').textContent = recipe.calories || '-';
            document.getElementById('modal-protein').textContent = recipe.protein || '-';
            document.getElementById('modal-carbs').textContent = recipe.carbs || '-';
            document.getElementById('modal-description').textContent = recipe.description || '-';

            const benefitsContainer = document.getElementById('modal-benefits');
            benefitsContainer.innerHTML = '';
            const benefits = Array.isArray(recipe.benefits) ? recipe.benefits : [];
            benefits.forEach((benefit) => {
                const badge = document.createElement('span');
                badge.className = 'px-2 py-1 rounded-full bg-tertiary/10 text-tertiary text-xs font-semibold';
                badge.textContent = benefit;
                benefitsContainer.appendChild(badge);
            });

            const ingredientsContainer = document.getElementById('modal-ingredients');
            ingredientsContainer.innerHTML = '';
            const ingredients = Array.isArray(recipe.ingredients) ? recipe.ingredients : [];
            ingredients.forEach((ingredient) => {
                // Accept BOTH legacy string ingredients ("2 eggs") and the new
                // {name, quantity} object shape coming from the recipe-generator.
                let label;
                if (ingredient && typeof ingredient === 'object') {
                    const qty = ingredient.quantity ? `${ingredient.quantity} ` : '';
                    label = `${qty}${ingredient.name || ''}`.trim();
                } else {
                    label = String(ingredient ?? '');
                }
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2';
                li.innerHTML = `
                    <iconify-icon icon="lucide:circle" class="text-primary text-xs"></iconify-icon>
                    <span class="text-sm"></span>
                `;
                li.querySelector('span').textContent = label;
                ingredientsContainer.appendChild(li);
            });

            const instructionsContainer = document.getElementById('modal-instructions');
            instructionsContainer.innerHTML = '';
            const instructions = Array.isArray(recipe.instructions) ? recipe.instructions : [];
            instructions.forEach((instruction, index) => {
                const step = document.createElement('div');
                step.className = 'flex gap-4';
                step.innerHTML = `
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-bold">
                        ${index + 1}
                    </div>
                    <p class="text-sm text-muted-foreground leading-relaxed pt-1">${instruction}</p>
                `;
                instructionsContainer.appendChild(step);
            });

            document.getElementById('modal-tips').textContent = recipe.tips || '-';
            document.getElementById('recipe-modal').classList.remove('hidden');
        }

        function closeRecipeModal() {
            document.getElementById('recipe-modal').classList.add('hidden');
        }

        // Exclusion functions
        document.getElementById('add-exclusion-btn').addEventListener('click', openAddExclusionModal);

        function openAddExclusionModal() {
            document.getElementById('add-exclusion-modal').classList.remove('hidden');
        }

        function closeAddExclusionModal() {
            document.getElementById('add-exclusion-modal').classList.add('hidden');
        }

        function addExclusion() {
            const type = document.getElementById('exclusion-type').value;
            const item = document.getElementById('exclusion-item').value.trim();

            if (!type || !item) {
                alert('Please fill in all fields.');
                return;
            }

            const label = `${type.charAt(0).toUpperCase() + type.slice(1)}: ${item}`;
            const exclusionGroup = document.getElementById('exclusion-chip-group');

            if (exclusionGroup) {
                exclusionGroup.insertBefore(createChip(label), document.getElementById('add-exclusion-btn'));
            }

            closeAddExclusionModal();

            // Reset form
            document.getElementById('exclusion-type').value = '';
            document.getElementById('exclusion-item').value = '';
        }

        // Add diet type functions
        document.getElementById('add-diet-type-btn').addEventListener('click', openAddDietTypeModal);

        function openAddDietTypeModal() {
            document.getElementById('add-diet-type-modal').classList.remove('hidden');
        }

        function closeAddDietTypeModal() {
            document.getElementById('add-diet-type-modal').classList.add('hidden');
        }

        function addDietType() {
            const item = document.getElementById('diet-item').value;

            if (!item) {
                alert('Please fill in all fields.');
                return;
            }

            const label = `${item}`;
            const dietGroup = document.getElementById('diet-type-chips');

            if (dietGroup) {
                dietGroup.insertBefore(createChip(label), document.getElementById('add-diet-type-btn'));
            }

            closeAddDietTypeModal();

            // Reset form
            document.getElementById('diet-item').value = '';
        }

        // Add meal prefs functions
        document.getElementById('add-meal-preference-btn').addEventListener('click', openAddMealPreferenceModal);

        function openAddMealPreferenceModal() {
            document.getElementById('add-meal-preference-modal').classList.remove('hidden');
        }

        function closeAddMealPreferenceModal() {
            document.getElementById('add-meal-preference-modal').classList.add('hidden');
        }

        function addMealPreference() {
            const item = document.getElementById('meal-preference-item').value;

            if (!item) {
                alert('Please fill in all fields.');
                return;
            }

            const label = `${item}`;
            const mealGroup = document.getElementById('meal-preference-chips');

            if (mealGroup) {
                mealGroup.insertBefore(createChip(label), document.getElementById('add-meal-preference-btn'));
            }

            closeAddMealPreferenceModal();

            // Reset form
            document.getElementById('meal-preference-item').value = '';
        }

        // Add cuisine prefs functions
        document.getElementById('add-cuisine-preference-btn').addEventListener('click', openAddCuisinePreferenceModal);

        function openAddCuisinePreferenceModal() {
            document.getElementById('add-cuisine-preference-modal').classList.remove('hidden');
        }

        function closeAddCuisinePreferenceModal() {
            document.getElementById('add-cuisine-preference-modal').classList.add('hidden');
        }

        function addCuisinePreference() {
            const item = document.getElementById('cuisine-preference-item').value;

            if (!item) {
                alert('Please fill in all fields.');
                return;
            }

            const label = `${item}`;
            const cuisineGroup = document.getElementById('cuisine-preference-chips');

            if (cuisineGroup) {
                cuisineGroup.insertBefore(createChip(label), document.getElementById('add-cuisine-preference-btn'));
            }

            closeAddCuisinePreferenceModal();

            // Reset form
            document.getElementById('cuisine-preference-item').value = '';
        }

        function createChip(name) {
            const chip = document.createElement('div');
            chip.dataset.name = name;
            chip.className = 'flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium';
            chip.innerHTML = `
                <span>${name}</span>
                <button type="button" onclick="removeChip(this, '${name}')" class="text-muted-foreground hover:text-destructive"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></button>
            `;
            return chip;
        }

        function removeChip(button, chipName) {
            button.parentElement.remove();
            console.log('Removing:', chipName);
            alert(`Removed ${chipName}.`);
        }

        document.addEventListener('click', function(event) {
            const chip = event.target.closest('[data-chip-toggle="true"]');
            if (!chip) return;

            const active = chip.classList.toggle('bg-primary/10');
            chip.classList.toggle('border-primary', active);
            chip.classList.toggle('text-primary-foreground', active);
            chip.classList.toggle('border-border', !active);
            chip.classList.toggle('text-muted-foreground', !active);
        });

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
            document.getElementById('prev-day-btn').addEventListener('click', () => navigateDay('prev'));
            document.getElementById('next-day-btn').addEventListener('click', () => navigateDay('next'));
            document.getElementById('generate-recipes-btn')?.addEventListener('click', loadGeneratedMealPlans);
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());

            // Week-start date picker.
            const dateInput = document.getElementById('week-start-date');
            if (dateInput) {
                dateInput.value = weekStart;
                dateInput.addEventListener('change', (e) => {
                    if (!e.target.value) return;
                    weekStart = e.target.value;
                    dateIndex = 0;
                    buildDateStrip();
                    updateMealPlan();
                    updateNavigationButtons();
                });
            }
            document.getElementById('reset-today-btn')?.addEventListener('click', () => {
                weekStart = startOfTodayIso();
                dateIndex = 0;
                buildDateStrip();
                updateMealPlan();
                updateNavigationButtons();
            });

            // Wire heart buttons on each meal card.
            ['breakfast', 'lunch', 'dinner'].forEach(t => {
                const btn = document.getElementById(`${t}-heart`);
                if (btn) btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleMealLike(t);
                });
            });

            // Default state: 7-day strip starting today, no plan loaded yet.
            dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            buildDateStrip();
            updateMealPlan();
            updateNavigationButtons();

            setRecipeFeedback('Loading your latest meal plan…', 'info');
            // Auto-load from DB so the page shows real data, not a blank state.
            // After it lands, honor any ?day=&meal= deep-link from the URL.
            loadActiveMealPlan().then(() => applyDeepLinkFromUrl());
            loadLikedMeals();
            loadExclusionsBanner();
            startActivePlanPolling();
        });

        // Surface the user's saved food exclusions at the top of the meal
        // plan so they know what's being filtered out. Pulled from /api/user
        // which already returns the session user (auth:sanctum). We hit a
        // small Laravel endpoint instead — /api/profile/me — that doesn't
        // require a Sanctum token.
        async function loadExclusionsBanner() {
            const banner = document.getElementById('exclusions-banner');
            if (!banner) return;
            try {
                const res = await fetch('/api/profile/me', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                const list = data.food_exclusions || [];
                if (!list.length) {
                    banner.classList.add('hidden');
                    return;
                }
                banner.classList.remove('hidden');
                banner.innerHTML = `
                    <iconify-icon icon="lucide:ban" class="text-red-600"></iconify-icon>
                    <span class="text-red-700 font-semibold mr-1">Avoiding:</span>
                    ${list.map(f => `<span class="px-2 py-0.5 rounded-full bg-white border border-red-200 text-red-700 font-semibold">${String(f).replace(/[&<>]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]))}</span>`).join('')}
                    <a href="/profile" class="ml-auto text-red-600 font-semibold hover:underline">Manage →</a>
                `;
            } catch (_) { /* swallow */ }
        }

        // Deep-link support: /recipe?day=monday&meal=lunch jumps the date
        // strip to the matching cell (within the current 7-day window) and
        // optionally pops the recipe modal for the chosen slot.
        function applyDeepLinkFromUrl() {
            try {
                const url = new URL(window.location.href);
                const wantDay = (url.searchParams.get('day') || '').toLowerCase();
                const wantMeal = (url.searchParams.get('meal') || '').toLowerCase();
                if (!wantDay && !wantMeal) return;
                if (wantDay) {
                    // Move dateIndex to the strip cell whose weekday matches.
                    rebuildWeekDates();
                    const idx = weekDates.findIndex(iso => weekdayKeyFor(iso) === wantDay);
                    if (idx >= 0) {
                        dateIndex = idx;
                    } else if (dayOrder.includes(wantDay)) {
                        // Day isn't in current 7-day window — shift weekStart
                        // so today's weekday lines up with the requested day.
                        const offsetFromToday = (WEEKDAY_KEYS.indexOf(wantDay) - new Date().getDay() + 7) % 7;
                        weekStart = isoForOffset(startOfTodayIso(), offsetFromToday);
                        dateIndex = 0;
                    }
                    buildDateStrip();
                    updateMealPlan();
                    updateNavigationButtons();
                }
                if (wantMeal && ['breakfast', 'lunch', 'dinner', 'snack'].includes(wantMeal)) {
                    // Modal is keyed by meal type; open it once the cards
                    // have rendered above.
                    setTimeout(() => openRecipeModal(wantMeal), 50);
                }
            } catch (e) {
                console.warn('applyDeepLinkFromUrl failed:', e);
            }
        }

        // ---------- Auto-load the user's current meal plan ----------
        async function loadActiveMealPlan({ silent = false } = {}) {
            try {
                const res = await fetch('/api/meal-plans/active', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!res.ok) return null;
                const data = await res.json();
                if (!data || !data.payload) {
                    if (!silent) {
                        setRecipeFeedback(
                            'No saved meal plan yet. Click "Generate" to build one — it takes a few minutes.',
                            'info',
                        );
                    }
                    return null;
                }
                // Only re-render if the plan id actually changed — avoids
                // wiping the user's date selection on the polling refresh.
                if (data.plan_id !== activePlanId) {
                    applySavedMealPlan({ ...data.payload, plan_id: data.plan_id, _plan_id: data.plan_id });
                    if (silent) {
                        setRecipeFeedback('Your meal plan was updated based on your latest preferences.', 'success');
                    }
                }
                return data;
            } catch (e) {
                console.warn('loadActiveMealPlan failed:', e);
                return null;
            }
        }

        // While the page is open, poll every 30s for a newer plan id. This
        // catches background regenerations triggered by the chat (e.g. the
        // user said "I can't eat fish" — a fresh weekly plan lands a few
        // minutes later and the page picks it up without a manual refresh).
        function startActivePlanPolling() {
            if (window.__planPoll) clearInterval(window.__planPoll);
            window.__planPoll = setInterval(() => {
                if (document.hidden) return;
                loadActiveMealPlan({ silent: true });
            }, 30000);
        }

        // ---------- Like / unlike meals ----------
        function csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function currentMealSnapshot(mealType) {
            const plan = mealPlans[currentDay];
            return plan ? plan[mealType] : null;
        }

        function syncHeartButton(mealType, meal) {
            const btn = document.getElementById(`${mealType}-heart`);
            if (!btn) return;
            if (!meal) {
                btn.classList.add('opacity-40', 'pointer-events-none');
                btn.dataset.likeId = '';
                return;
            }
            btn.classList.remove('opacity-40', 'pointer-events-none');
            const key = likeKey(activePlanId, currentDay, mealType);
            const liked = likedByKey.get(key);
            const icon = btn.querySelector('iconify-icon');
            if (liked) {
                btn.dataset.likeId = liked.id;
                btn.classList.add('text-red-500');
                btn.classList.remove('text-muted-foreground');
                if (icon) icon.setAttribute('style', 'fill: currentColor;');
            } else {
                btn.dataset.likeId = '';
                btn.classList.remove('text-red-500');
                btn.classList.add('text-muted-foreground');
                if (icon) icon.removeAttribute('style');
            }
        }

        async function toggleMealLike(mealType) {
            const meal = currentMealSnapshot(mealType);
            if (!meal) return;
            const btn = document.getElementById(`${mealType}-heart`);
            const likeId = btn?.dataset.likeId;
            if (likeId) {
                // Unlike.
                try {
                    const res = await fetch(`/api/meal-likes/${likeId}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                        credentials: 'same-origin',
                    });
                    if (res.ok) {
                        likedByKey.delete(likeKey(activePlanId, currentDay, mealType));
                        syncHeartButton(mealType, meal);
                        renderFavorites();
                    }
                } catch (e) { console.warn('unlike failed', e); }
                return;
            }
            try {
                const res = await fetch('/api/meal-likes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        meal_plan_id: activePlanId,
                        day_key: currentDay,
                        meal_type: mealType,
                        title: meal.title || mealType,
                        snapshot: meal,
                    }),
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.like) {
                    likedByKey.set(likeKey(activePlanId, currentDay, mealType), data.like);
                    syncHeartButton(mealType, meal);
                    renderFavorites();
                }
            } catch (e) { console.warn('like failed', e); }
        }

        async function loadLikedMeals() {
            try {
                const res = await fetch('/api/meal-likes', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                likedByKey = new Map();
                (data.likes || []).forEach(l => {
                    likedByKey.set(likeKey(l.meal_plan_id, l.day_key, l.meal_type), l);
                });
                renderFavorites();
                // Re-sync the current cards' hearts now that we know which are liked.
                ['breakfast', 'lunch', 'dinner'].forEach(t => {
                    syncHeartButton(t, currentMealSnapshot(t));
                });
            } catch (e) { console.warn('loadLikedMeals failed', e); }
        }

        function renderFavorites() {
            const grid = document.getElementById('favorites-grid');
            const count = document.getElementById('favorites-count');
            const likes = Array.from(likedByKey.values())
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            if (count) count.textContent = `${likes.length} saved`;
            if (!grid) return;
            if (!likes.length) {
                grid.innerHTML = '<p class="col-span-full text-sm text-muted-foreground text-center py-8">No liked meals yet.</p>';
                return;
            }
            grid.innerHTML = likes.map(l => {
                const snap = l.snapshot || {};
                const cals = snap.calories || '—';
                const prot = snap.protein || '—';
                const desc = (snap.description || '').slice(0, 120);
                const slot = (l.meal_type || '').replace(/\b\w/g, c => c.toUpperCase());
                return `
                    <div class="bg-card rounded-2xl border border-border p-4 shadow-sm flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wide text-muted-foreground">${_escMeal(slot)}</span>
                                <h3 class="font-bold text-base leading-tight mt-1">${_escMeal(titleCase(l.title || ''))}</h3>
                            </div>
                            <button data-unlike-id="${l.id}" title="Remove from favorites"
                                class="text-red-500 hover:text-red-600 transition-colors">
                                <iconify-icon icon="lucide:heart" style="fill: currentColor;"></iconify-icon>
                            </button>
                        </div>
                        ${desc ? `<p class="text-xs text-muted-foreground line-clamp-2">${_escMeal(desc)}</p>` : ''}
                        <div class="flex gap-4 text-xs text-muted-foreground mt-1">
                            <span>${_escMeal(String(cals))} cal</span>
                            <span>${_escMeal(String(prot))} protein</span>
                            ${l.day_key ? `<span class="ml-auto">${_escMeal(l.day_key)}</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
            grid.querySelectorAll('button[data-unlike-id]').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.unlikeId;
                    try {
                        const res = await fetch(`/api/meal-likes/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                            credentials: 'same-origin',
                        });
                        if (res.ok) {
                            // Remove from the map by id (key unknown without scan).
                            for (const [k, v] of likedByKey) {
                                if (v.id === Number(id)) { likedByKey.delete(k); break; }
                            }
                            renderFavorites();
                            ['breakfast', 'lunch', 'dinner'].forEach(t => {
                                syncHeartButton(t, currentMealSnapshot(t));
                            });
                        }
                    } catch (_) {}
                });
            });
        }

        // ---------- Past meal plans history dropdown ----------
        function _escMeal(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function applySavedMealPlan(payload) {
            const planData = payload.plan || payload;
            const dayKeys = Object.keys(planData);
            const normalized = {};
            dayKeys.forEach((day) => {
                const nd = normalizeDayPlan(planData[day], day);
                if (nd) normalized[day] = nd;
            });
            if (!Object.keys(normalized).length) {
                setRecipeFeedback('Saved plan was empty or unreadable.', 'error');
                return;
            }
            mealPlans = normalized;
            dayOrder = Object.keys(normalized);
            activePlanId = payload._plan_id || payload.plan_id || null;
            buildDateStrip();
            updateMealPlan();
            updateNavigationButtons();
            setRecipeFeedback(payload.summary || `Loaded plan #${activePlanId ?? ''}.`, 'success');
        }

        async function loadMealPlanList() {
            const listEl = document.getElementById('meal-history-list');
            try {
                const res = await fetch('/api/meal-plans', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = res.ok ? await res.json() : { plans: [] };
                const items = data.plans || [];
                if (items.length === 0) {
                    listEl.innerHTML = '<p class="text-xs text-muted-foreground px-3 py-4 text-center">No saved meal plans yet.</p>';
                    return;
                }
                listEl.innerHTML = items.map(p => {
                    const when = p.created_at ? new Date(p.created_at).toLocaleString() : '';
                    const badge = p.span === 'weekly' ? '7-day' : '1-day';
                    const cals = p.target_calories ? ` · ${p.target_calories} kcal` : '';
                    return `
                        <button data-pid="${p.id}"
                            class="meal-item text-left px-3 py-2 rounded-lg hover:bg-muted transition-colors">
                            <p class="text-sm font-medium">${badge}${cals}</p>
                            <p class="text-[10px] text-muted-foreground">Plan #${p.id} · ${_escMeal(when)}</p>
                        </button>
                    `;
                }).join('');
                listEl.querySelectorAll('.meal-item').forEach(btn => {
                    btn.addEventListener('click', () => loadMealPlanDetail(Number(btn.dataset.pid)));
                });
            } catch (e) {
                listEl.innerHTML = '<p class="text-xs text-destructive px-3 py-4 text-center">Failed to load.</p>';
            }
        }

        async function loadMealPlanDetail(id) {
            try {
                const res = await fetch(`/api/meal-plans/${id}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const payload = await res.json();
                document.getElementById('meal-history-dropdown').classList.add('hidden');
                applySavedMealPlan(payload);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (e) {
                console.warn('loadMealPlanDetail failed:', e);
            }
        }

        document.getElementById('meal-history-toggle')?.addEventListener('click', (e) => {
            e.stopPropagation();
            const dd = document.getElementById('meal-history-dropdown');
            const willOpen = dd.classList.contains('hidden');
            dd.classList.toggle('hidden');
            if (willOpen) loadMealPlanList();
        });

        document.addEventListener('click', (e) => {
            const dd = document.getElementById('meal-history-dropdown');
            const toggle = document.getElementById('meal-history-toggle');
            if (dd && !dd.classList.contains('hidden') && !dd.contains(e.target) && e.target !== toggle && !toggle?.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });
    </script>
</body>

</html>