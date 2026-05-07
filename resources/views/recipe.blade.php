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
                    <h1 class="text-xl font-heading font-bold">Nutrition & Recipes</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div
                        class="hidden sm:flex items-center gap-2 bg-muted px-3 py-1.5 rounded-full text-sm font-medium">
                        <iconify-icon icon="lucide:flame" class="text-orange-500"></iconify-icon>
                        <span>2,150 kcal / day</span>
                    </div>
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
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
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Low-Carb</button>
                                            <button type="button" data-chip-toggle="true" data-chip-group="diet"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">High-Protein</button>
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
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Allergies & Exclusions</label>
                                        <div id="exclusion-chip-group" class="flex flex-wrap gap-2">
                                            <div data-exclusion-name="Gluten-free"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                <span>Gluten-free</span>
                                                <button type="button" onclick="removeExclusion(this, 'Gluten-free')"
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
                                            <div data-exclusion-name="Dairy-free"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                <span>Dairy-free</span>
                                                <button type="button" onclick="removeExclusion(this, 'Dairy-free')"
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
                                            <div data-exclusion-name="Nut-free"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                <span>Nut-free</span>
                                                <button type="button" onclick="removeExclusion(this, 'Nut-free')"
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
                                            <div data-exclusion-name="Soy-free"
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                <span>Soy-free</span>
                                                <button type="button" onclick="removeExclusion(this, 'Soy-free')"
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
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
                                            <button type="button" data-chip-toggle="true" data-chip-group="cuisine"
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Mediterranean</button>
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
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-heading font-bold" id="meal-plan-title">Today's AI Meal Plan</h2>
                            <div class="flex gap-2">
                                <button id="prev-day-btn"
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors disabled:opacity-50 disabled:cursor-not-allowed"><iconify-icon
                                        icon="lucide:chevron-left"></iconify-icon></button>
                                <button id="next-day-btn"
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors"><iconify-icon
                                        icon="lucide:chevron-right"></iconify-icon></button>
                            </div>
                        </div>
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
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-red-500 transition-colors shadow-sm" id="lunch-heart">
                                        <iconify-icon icon="lucide:heart" class="text-red-500" data-icon="lucide:heart"
                                            style="fill: currentColor;"></iconify-icon>
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
                                <button
                                    class="w-10 h-10 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-foreground transition-colors shadow-sm">
                                    <iconify-icon icon="lucide:share-2"></iconify-icon>
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
    </div>

    <script>
        const GENERATED_RECIPE_JSON_PATH = '/food/data/example.json';
        let mealPlans = {};
        let dayOrder = [];
        let currentDay = 'monday';

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
                dinner: dayData.dinner
            };
        }

        function updateRecipeCard(mealType, recipe) {
            if (!recipe) return;

            document.getElementById(`${mealType}-title`).textContent = recipe.title || '-';
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

        function updateMealPlan() {
            const plan = mealPlans[currentDay];
            if (!plan) return;

            document.getElementById('meal-plan-title').textContent = plan.title;
            updateRecipeCard('breakfast', plan.breakfast);
            updateRecipeCard('lunch', plan.lunch);
            updateRecipeCard('dinner', plan.dinner);
        }

        function updateNavigationButtons() {
            const currentIndex = dayOrder.indexOf(currentDay);
            const prevBtn = document.getElementById('prev-day-btn');
            const nextBtn = document.getElementById('next-day-btn');

            prevBtn.disabled = currentIndex <= 0;
            nextBtn.disabled = currentIndex === -1 || currentIndex >= dayOrder.length - 1;
        }

        function navigateDay(direction) {
            const currentIndex = dayOrder.indexOf(currentDay);
            if (currentIndex === -1) return;

            let newIndex = currentIndex;
            if (direction === 'next') newIndex = Math.min(currentIndex + 1, dayOrder.length - 1);
            if (direction === 'prev') newIndex = Math.max(currentIndex - 1, 0);

            if (newIndex !== currentIndex) {
                currentDay = dayOrder[newIndex];
                updateMealPlan();
                updateNavigationButtons();
            }
        }

        async function loadGeneratedMealPlans() {
            const response = await fetch(`${GENERATED_RECIPE_JSON_PATH}?t=${Date.now()}`, { cache: 'no-store' });

            if (response.status === 404) {
                setRecipeFeedback('Generated recipes are not available yet. Please generate recipes first.', 'info');
                return;
            }

            if (!response.ok) {
                setRecipeFeedback('Unable to load generated recipes right now. Please try again.', 'error');
                return;
            }

            let jsonData;
            try {
                jsonData = await response.json();
            } catch (error) {
                setRecipeFeedback('Generated recipe file is invalid JSON. Please regenerate it.', 'error');
                return;
            }

            const rawDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
                .filter((day) => jsonData[day]);

            const normalized = {};
            rawDays.forEach((day) => {
                const normalizedDay = normalizeDayPlan(jsonData[day], day);
                if (normalizedDay) {
                    normalized[day] = normalizedDay;
                }
            });

            if (!Object.keys(normalized).length) {
                setRecipeFeedback('Generated recipe data format is incomplete. Please regenerate recipes.', 'error');
                return;
            }

            mealPlans = normalized;
            dayOrder = Object.keys(normalized);
            currentDay = normalized.monday ? 'monday' : dayOrder[0];

            updateMealPlan();
            updateNavigationButtons();
            setRecipeFeedback('Generated recipes loaded.', 'success');
        }

        function openRecipeModal(mealType) {
            const dayPlan = mealPlans[currentDay];
            if (!dayPlan || !dayPlan[mealType]) return;
            const recipe = dayPlan[mealType];

            document.getElementById('modal-recipe-title').textContent = recipe.title || '-';
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
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2';
                li.innerHTML = `
                    <iconify-icon icon="lucide:circle" class="text-primary text-xs"></iconify-icon>
                    <span class="text-sm">${ingredient}</span>
                `;
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
                exclusionGroup.insertBefore(createExclusionChip(label), document.getElementById('add-exclusion-btn'));
            }

            closeAddExclusionModal();

            // Reset form
            document.getElementById('exclusion-type').value = '';
            document.getElementById('exclusion-item').value = '';
        }

        function createExclusionChip(name) {
            const chip = document.createElement('div');
            chip.dataset.exclusionName = name;
            chip.className = 'flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium';
            chip.innerHTML = `
                <span>${name}</span>
                <button type="button" onclick="removeExclusion(this, '${name}')" class="text-muted-foreground hover:text-destructive"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></button>
            `;
            return chip;
        }

        function removeExclusion(button, exclusionName) {
            button.parentElement.remove();
            console.log('Removing exclusion:', exclusionName);
            alert(`Removed ${exclusionName} from exclusions.`);
        }

        document.addEventListener('click', function (event) {
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
            dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            updateNavigationButtons();
            loadGeneratedMealPlans();
        });
    </script>
</body>

</html>