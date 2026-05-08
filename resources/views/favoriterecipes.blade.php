<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Uplyfe - Liked Recipes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
        :root {
            --border: #e2e8f0;
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
            --secondary: #e2e8f0;
        }
    </style>
</head>

<body class="min-h-screen bg-background font-sans text-foreground">
    <div class="min-h-screen w-full bg-background flex flex-col md:flex-row relative font-sans text-foreground">
        <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 -translate-x-full md:relative md:translate-x-0 md:flex bg-card border-r border-border flex-shrink-0 flex-col transition-transform duration-300 shadow-xl md:shadow-none">
            <div class="h-16 flex items-center px-6 border-b border-border">
                <div class="flex items-center gap-2 cursor-pointer group">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-sm">
                        <iconify-icon icon="lucide:leaf" class="text-lg"></iconify-icon>
                    </div>
                    <span class="text-xl font-heading font-bold tracking-tight">Uplyfe</span>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
                <p class="px-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Main Menu</p>
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:layout-dashboard" class="text-lg"></iconify-icon>
                    Dashboard
                </a>
                <a href="/health-check" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:file-text" class="text-lg"></iconify-icon>
                    Health Checkup
                </a>
                <a href="/recipe" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:apple" class="text-lg"></iconify-icon>
                    Nutrition & Recipes
                </a>
                <a href="/exercise" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:dumbbell" class="text-lg"></iconify-icon>
                    Exercise Routine
                </a>
                <a href="/chat" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
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
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"></div>

        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Liked Recipes</h1>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="mb-6">
                        <h2 class="text-2xl font-heading font-bold">Your Favorite Recipes</h2>
                    </div>
                    <div id="favorite-recipe-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                </div>
            </div>
        </main>
    </div>

    <div id="recipe-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeRecipeModal()"></div>
        <div class="absolute top-1/2 left-1/2 w-[95vw] max-w-4xl h-[90vh] -translate-x-1/2 -translate-y-1/2">
            <div class="bg-white rounded-3xl border border-[var(--border)] shadow-xl h-full flex flex-col overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-[var(--border)]">
                    <div>
                        <h3 id="modal-recipe-title" class="text-2xl font-bold">Recipe Title</h3>
                        <p id="modal-recipe-subtitle" class="text-sm text-[var(--muted-foreground)]">Recipe subtitle</p>
                    </div>
                    <button onclick="closeRecipeModal()" class="p-2 rounded-full hover:bg-slate-100">
                        <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div class="h-56 bg-slate-100 flex items-center justify-center">
                        <iconify-icon icon="lucide:image" class="text-5xl opacity-30"></iconify-icon>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-3 gap-4 p-4 rounded-2xl border border-[var(--border)] bg-slate-50">
                            <div class="text-center">
                                <p class="text-xs text-[var(--muted-foreground)]">Calories</p>
                                <p id="modal-calories" class="font-bold"></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-[var(--muted-foreground)]">Protein</p>
                                <p id="modal-protein" class="font-bold"></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-[var(--muted-foreground)]">Carbs</p>
                                <p id="modal-carbs" class="font-bold"></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Description</h4>
                            <p id="modal-description" class="text-[var(--muted-foreground)]"></p>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Health Benefits</h4>
                            <div id="modal-benefits" class="flex flex-wrap gap-2"></div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Ingredients</h4>
                            <ul id="modal-ingredients" class="space-y-2"></ul>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">How to Cook</h4>
                            <div id="modal-instructions" class="space-y-3"></div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Chef's Tips</h4>
                            <div id="modal-tips" class="p-4 rounded-2xl border border-[var(--border)] bg-slate-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const FAVORITE_RECIPES_STORAGE_KEY = 'uplyfeFavoriteRecipes';
        const DUMMY_FAVORITE_RECIPES = [{
                recipe: {
                    title: 'Greek Yogurt Berry Bowl',
                    subtitle: 'Protein-rich quick breakfast',
                    calories: '380',
                    protein: '24g',
                    carbs: '42g',
                    description: 'Creamy Greek yogurt topped with berries, chia seeds, and granola for sustained morning energy.',
                    tags: [{
                        text: 'High Protein'
                    }, {
                        text: 'Gut Friendly'
                    }],
                    benefits: ['Supports muscle recovery', 'Rich in probiotics', 'Steady morning energy'],
                    ingredients: ['1 cup Greek yogurt', '1/2 cup mixed berries', '1 tbsp chia seeds', '1/4 cup granola', '1 tsp honey'],
                    instructions: ['Add yogurt into a bowl.', 'Top with berries, chia seeds, and granola.', 'Drizzle honey and serve immediately.'],
                    tips: 'Prepare toppings in small containers the night before for a faster morning routine.'
                }
            },
            {
                recipe: {
                    title: 'Salmon Quinoa Power Salad',
                    subtitle: 'Heart healthy midday meal',
                    calories: '540',
                    protein: '34g',
                    carbs: '45g',
                    description: 'A balanced lunch with baked salmon, quinoa, leafy greens, cucumber, and lemon dressing.',
                    tags: [{
                        text: 'Omega-3'
                    }, {
                        text: 'Heart Healthy'
                    }],
                    benefits: ['Supports heart health', 'Anti-inflammatory nutrients', 'High satiety'],
                    ingredients: ['120g salmon fillet', '1/2 cup cooked quinoa', '2 cups mixed greens', '1/2 cucumber', '1 tbsp olive oil', '1 tbsp lemon juice'],
                    instructions: ['Bake salmon until cooked through.', 'Combine quinoa, greens, and cucumber.', 'Flake salmon on top and drizzle dressing.'],
                    tips: 'Use leftover salmon to make this in under 10 minutes.'
                }
            },
            {
                recipe: {
                    title: 'Lemon Herb Chicken & Veggies',
                    subtitle: 'Light and filling dinner',
                    calories: '490',
                    protein: '40g',
                    carbs: '28g',
                    description: 'Grilled chicken breast with roasted broccoli, carrots, and a zesty lemon-herb finish.',
                    tags: [{
                        text: 'Low Glycemic'
                    }, {
                        text: 'Lean Protein'
                    }],
                    benefits: ['Supports blood sugar control', 'High protein recovery meal', 'Micronutrient dense'],
                    ingredients: ['150g chicken breast', '1 cup broccoli', '1 cup carrots', '1 tbsp olive oil', '1 tsp mixed herbs', '1/2 lemon'],
                    instructions: ['Season chicken and grill until done.', 'Roast vegetables with olive oil and herbs.', 'Serve with lemon squeezed on top.'],
                    tips: 'Marinate the chicken for 30 minutes to boost flavor and tenderness.'
                }
            }
        ];
        let favoriteRecipes = [];

        function getFavoriteRecipes() {
            try {
                const stored = localStorage.getItem(FAVORITE_RECIPES_STORAGE_KEY);
                const parsed = stored ? JSON.parse(stored) : {};
                return Object.values(parsed);
            } catch (_) {
                return [];
            }
        }

        function renderFavoriteRecipes() {
            const grid = document.getElementById('favorite-recipe-grid');
            if (!grid) return;

            favoriteRecipes = getFavoriteRecipes();
            if (!favoriteRecipes.length) {
                favoriteRecipes = DUMMY_FAVORITE_RECIPES;
            }
            grid.innerHTML = '';
            favoriteRecipes.forEach((favorite, index) => {
                const recipe = favorite.recipe || {};
                const tags = Array.isArray(recipe.tags) ? recipe.tags : [];
                const tagsHTML = tags.map((tag) => `<span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">${tag.text || ''}</span>`).join('');

                const card = document.createElement('div');
                card.className = 'bg-white rounded-2xl border border-[var(--border)] overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col cursor-pointer';
                card.onclick = () => openRecipeModal(index);
                card.innerHTML = `
                    <div class="h-48 relative overflow-hidden bg-slate-100">
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400">
                            <iconify-icon icon="lucide:image" class="text-4xl opacity-30"></iconify-icon>
                        </div>
                        <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center text-red-500 shadow-sm">
                            <iconify-icon icon="lucide:heart" class="text-red-500" style="fill: currentColor;"></iconify-icon>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-2">${tagsHTML}</div>
                        <h3 class="font-bold text-lg mb-1 leading-tight group-hover:text-emerald-700 transition-colors">${recipe.title || '-'}</h3>
                        <p class="text-xs text-[var(--muted-foreground)] mb-4 line-clamp-2">${recipe.description || '-'}</p>
                        <div class="mt-auto grid grid-cols-2 gap-2 border-t border-[var(--border)] pt-4">
                            <div class="text-center"><p class="text-xs text-[var(--muted-foreground)]">Cals</p><p class="text-sm font-bold">${recipe.calories || '-'}</p></div>
                            <div class="text-center border-l border-[var(--border)]"><p class="text-xs text-[var(--muted-foreground)]">Protein</p><p class="text-sm font-bold">${recipe.protein || '-'}</p></div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function openRecipeModal(index) {
            const favorite = favoriteRecipes[index];
            if (!favorite || !favorite.recipe) return;
            const recipe = favorite.recipe;

            document.getElementById('modal-recipe-title').textContent = recipe.title || '-';
            document.getElementById('modal-recipe-subtitle').textContent = recipe.subtitle || `${getMealLabel(favorite.mealType)} recipe`;
            document.getElementById('modal-calories').textContent = recipe.calories || '-';
            document.getElementById('modal-protein').textContent = recipe.protein || '-';
            document.getElementById('modal-carbs').textContent = recipe.carbs || '-';
            document.getElementById('modal-description').textContent = recipe.description || '-';

            const benefitsContainer = document.getElementById('modal-benefits');
            benefitsContainer.innerHTML = '';
            (Array.isArray(recipe.benefits) ? recipe.benefits : []).forEach((benefit) => {
                const badge = document.createElement('span');
                badge.className = 'px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold';
                badge.textContent = benefit;
                benefitsContainer.appendChild(badge);
            });

            const ingredientsContainer = document.getElementById('modal-ingredients');
            ingredientsContainer.innerHTML = '';
            (Array.isArray(recipe.ingredients) ? recipe.ingredients : []).forEach((ingredient) => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2';
                li.innerHTML = `<iconify-icon icon="lucide:circle" class="text-emerald-600 text-xs"></iconify-icon><span class="text-sm">${ingredient}</span>`;
                ingredientsContainer.appendChild(li);
            });

            const instructionsContainer = document.getElementById('modal-instructions');
            instructionsContainer.innerHTML = '';
            (Array.isArray(recipe.instructions) ? recipe.instructions : []).forEach((instruction, i) => {
                const step = document.createElement('div');
                step.className = 'flex gap-4';
                step.innerHTML = `<div class="flex-shrink-0 w-8 h-8 rounded-full bg-[var(--primary)] text-[var(--primary-foreground)] flex items-center justify-center text-sm font-bold">${i + 1}</div><p class="text-sm text-[var(--muted-foreground)] leading-relaxed pt-1">${instruction}</p>`;
                instructionsContainer.appendChild(step);
            });

            document.getElementById('modal-tips').textContent = recipe.tips || '-';
            document.getElementById('recipe-modal').classList.remove('hidden');
        }

        function closeRecipeModal() {
            document.getElementById('recipe-modal').classList.add('hidden');
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());
            document.getElementById('mobile-sidebar-backdrop')?.addEventListener('click', () => toggleSidebar(false));
            renderFavoriteRecipes();
        });
    </script>
</body>

</html>