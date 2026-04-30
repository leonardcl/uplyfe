<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recipe</title>
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

                <a href="#"
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
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
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
                                        <div class="flex flex-wrap gap-2">
                                            <button
                                                class="px-4 py-2 rounded-full border border-primary bg-primary/10 text-primary-foreground text-sm font-semibold transition-colors">Balanced</button>
                                            <button
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Keto</button>
                                            <button
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Vegan</button>
                                            <button
                                                class="px-4 py-2 rounded-full border border-border bg-background text-muted-foreground hover:border-primary/50 text-sm font-medium transition-colors">Mediterranean</button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-bold mb-3 block">Allergies & Exclusions</label>
                                        <div class="flex flex-wrap gap-2">
                                            <div
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                Gluten-free
                                                <button
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
                                            <div
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full bg-muted text-sm font-medium">
                                                Dairy-free
                                                <button
                                                    class="text-muted-foreground hover:text-destructive"><iconify-icon
                                                        icon="lucide:x" class="text-sm"></iconify-icon></button>
                                            </div>
                                            <button
                                                class="flex items-center gap-1 px-3 py-1.5 rounded-full border border-dashed border-border text-muted-foreground hover:text-foreground text-sm font-medium transition-colors">
                                                <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                                                Add Exclusion
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
                                <button
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
                            <h2 class="text-2xl font-heading font-bold">Today's AI Meal Plan</h2>
                            <div class="flex gap-2">
                                <button
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors"><iconify-icon
                                        icon="lucide:chevron-left"></iconify-icon></button>
                                <button
                                    class="p-2 rounded-lg border border-border bg-card text-muted-foreground hover:text-foreground transition-colors"><iconify-icon
                                        icon="lucide:chevron-right"></iconify-icon></button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                            <!-- Recipe Card 1: Breakfast -->
                            <div
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <!-- Placeholder for image -->
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm">
                                        Breakfast</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm">
                                        <iconify-icon icon="lucide:heart"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="text-xs font-semibold text-tertiary bg-tertiary/10 px-2 py-0.5 rounded-full">High
                                            Vitamin D</span>
                                    </div>
                                    <h3
                                        class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors">
                                        Smoked Salmon & Avocado Toast</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2">Rich in omega-3s and
                                        Vitamin D to support your recent checkup goals. Served on gluten-free bread.</p>

                                    <div class="mt-auto grid grid-cols-3 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold">420</p>
                                        </div>
                                        <div class="text-center border-x border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold">22g</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Time</p>
                                            <p class="text-sm font-bold">10m</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recipe Card 2: Lunch -->
                            <div
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm">
                                        Lunch</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-red-500 transition-colors shadow-sm">
                                        <iconify-icon icon="lucide:heart" class="text-red-500" data-icon="lucide:heart"
                                            style="fill: currentColor;"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Heart
                                            Healthy</span>
                                    </div>
                                    <h3
                                        class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors">
                                        Mediterranean Quinoa Bowl</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2">Packed with fiber to help
                                        maintain your excellent cholesterol levels. Features olives, cucumber, and feta.
                                    </p>

                                    <div class="mt-auto grid grid-cols-3 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold">550</p>
                                        </div>
                                        <div class="text-center border-x border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold">18g</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Time</p>
                                            <p class="text-sm font-bold">15m</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recipe Card 3: Dinner -->
                            <div
                                class="bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col">
                                <div class="h-48 relative overflow-hidden bg-muted">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                                        <iconify-icon icon="lucide:image" class="text-4xl opacity-20"></iconify-icon>
                                    </div>
                                    <div
                                        class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-background/90 backdrop-blur-sm text-xs font-bold shadow-sm">
                                        Dinner</div>
                                    <button
                                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-muted-foreground hover:text-red-500 transition-colors shadow-sm">
                                        <iconify-icon icon="lucide:heart"></iconify-icon>
                                    </button>
                                </div>
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="text-xs font-semibold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Low
                                            Glycemic</span>
                                    </div>
                                    <h3
                                        class="font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors">
                                        Lemon Herb Grilled Chicken</h3>
                                    <p class="text-xs text-muted-foreground mb-4 line-clamp-2">A lean protein dinner
                                        paired with roasted asparagus to keep your fasting blood sugar stable overnight.
                                    </p>

                                    <div class="mt-auto grid grid-cols-3 gap-2 border-t border-border pt-4">
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Cals</p>
                                            <p class="text-sm font-bold">480</p>
                                        </div>
                                        <div class="text-center border-x border-border">
                                            <p class="text-xs text-muted-foreground">Protein</p>
                                            <p class="text-sm font-bold">42g</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-muted-foreground">Time</p>
                                            <p class="text-sm font-bold">25m</p>
                                        </div>
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