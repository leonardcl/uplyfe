<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
                    <img src="{{ $avatarSrc }}" alt="User"
                        class="w-10 h-10 rounded-full border border-border">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
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
                    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <h2 class="text-2xl font-heading font-bold">Your Favorite Recipes</h2>
                            <p class="text-xs text-muted-foreground mt-1" id="favorite-meta">Loading…</p>
                        </div>
                        <a href="/recipe" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-card border border-border text-sm font-semibold hover:bg-muted">
                            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                            Back to meal plan
                        </a>
                    </div>
                    <div id="favorite-recipe-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                    <p id="favorite-empty" class="text-center text-sm text-muted-foreground py-16 hidden">
                        You haven't liked any meals yet. Tap a ❤ on the recipe page to save it here.
                    </p>
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
        // Real data only — pulled from /api/meal-likes. The legacy
        // localStorage / dummy lists are gone.
        const FAVORITE_API = '/api/meal-likes';
        let favoriteRecipes = [];

        // Light Title-Case to match what /recipe shows.
        function favTitleCase(str) {
            if (!str) return '';
            const minors = new Set(['a','an','and','as','at','but','by','for','in','nor','of','on','or','so','the','to','up','yet','with']);
            const words = String(str).trim().split(/\s+/);
            return words.map((w, i) => {
                if (!w) return w;
                if (/^[A-Z]{2,}$/.test(w)) return w;
                if (/^[ivxlcm]+$/i.test(w) && w.length <= 4) return w.toUpperCase();
                const lower = w.toLowerCase();
                if (minors.has(lower) && i !== 0 && i !== words.length - 1) return lower;
                return lower.replace(/(^|[\s\-'/])(\p{L})/gu, (_, sep, ch) => sep + ch.toUpperCase());
            }).join(' ');
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        async function loadFavorites() {
            const meta = document.getElementById('favorite-meta');
            try {
                const res = await fetch(FAVORITE_API, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                favoriteRecipes = (data.likes || []).map(like => ({
                    id: like.id,
                    mealType: like.meal_type,
                    dayKey: like.day_key,
                    mealPlanId: like.meal_plan_id,
                    createdAt: like.created_at,
                    recipe: like.snapshot || {},
                }));
                meta.textContent = `${favoriteRecipes.length} saved meal${favoriteRecipes.length === 1 ? '' : 's'}`;
            } catch (e) {
                favoriteRecipes = [];
                meta.textContent = 'Could not load favorites — check that you are logged in.';
            }
            renderFavoriteRecipes();
        }

        function renderFavoriteRecipes() {
            const grid = document.getElementById('favorite-recipe-grid');
            const empty = document.getElementById('favorite-empty');
            if (!grid) return;
            grid.innerHTML = '';
            if (!favoriteRecipes.length) {
                empty?.classList.remove('hidden');
                return;
            }
            empty?.classList.add('hidden');
            favoriteRecipes.forEach((favorite, index) => {
                const recipe = favorite.recipe || {};
                const tagsRaw = Array.isArray(recipe.tags) ? recipe.tags : [];
                const tagsHTML = tagsRaw.map((tag) => {
                    const text = typeof tag === 'string' ? tag : (tag.text || '');
                    if (!text) return '';
                    return `<span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">${text}</span>`;
                }).join('');
                const slot = (favorite.mealType || '').replace(/\b\w/g, c => c.toUpperCase());
                const slotDay = [slot, favorite.dayKey].filter(Boolean).join(' · ');
                const cardHref = `/recipe?day=${encodeURIComponent(favorite.dayKey || '')}&meal=${encodeURIComponent(favorite.mealType || '')}`;
                const card = document.createElement('div');
                card.className = 'bg-card rounded-2xl border border-border overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col';
                card.innerHTML = `
                    <div class="h-32 relative overflow-hidden bg-muted cursor-pointer" data-action="open" data-idx="${index}">
                        <div class="absolute inset-0 flex items-center justify-center text-muted-foreground">
                            <iconify-icon icon="lucide:utensils" class="text-4xl opacity-30"></iconify-icon>
                        </div>
                        <button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-background/90 backdrop-blur-sm flex items-center justify-center text-red-500 shadow-sm hover:bg-red-50"
                            data-action="unlike" data-like-id="${favorite.id}" title="Remove from favorites">
                            <iconify-icon icon="lucide:heart" style="fill: currentColor;"></iconify-icon>
                        </button>
                        ${slotDay ? `<span class="absolute top-3 left-3 px-2 py-0.5 rounded-md bg-background/90 backdrop-blur-sm text-[10px] font-bold">${slotDay}</span>` : ''}
                    </div>
                    <div class="p-4 flex flex-col flex-1 gap-2">
                        <div class="flex flex-wrap items-center gap-1">${tagsHTML}</div>
                        <h3 class="font-bold text-base leading-tight group-hover:text-primary transition-colors cursor-pointer"
                            data-action="open" data-idx="${index}">${favTitleCase(recipe.title || '')}</h3>
                        <p class="text-xs text-muted-foreground line-clamp-2">${recipe.description || ''}</p>
                        <div class="mt-auto grid grid-cols-3 gap-1 border-t border-border pt-3 text-center text-xs">
                            <div><p class="text-muted-foreground">Cals</p><p class="font-bold">${recipe.calories || '—'}</p></div>
                            <div class="border-l border-border"><p class="text-muted-foreground">Protein</p><p class="font-bold">${recipe.protein || '—'}</p></div>
                            <div class="border-l border-border"><p class="text-muted-foreground">Carbs</p><p class="font-bold">${recipe.carbs || '—'}</p></div>
                        </div>
                        <a href="${cardHref}" class="text-xs text-primary font-semibold hover:underline self-end">Open in plan →</a>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function getMealLabel(slot) {
            if (!slot) return 'Meal';
            return slot.charAt(0).toUpperCase() + slot.slice(1);
        }

        async function unlikeFavorite(likeId) {
            try {
                const res = await fetch(`${FAVORITE_API}/${likeId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                    credentials: 'same-origin',
                });
                if (res.ok) {
                    favoriteRecipes = favoriteRecipes.filter(f => f.id !== Number(likeId));
                    renderFavoriteRecipes();
                    const meta = document.getElementById('favorite-meta');
                    if (meta) meta.textContent = `${favoriteRecipes.length} saved meal${favoriteRecipes.length === 1 ? '' : 's'}`;
                }
            } catch (e) { /* swallow */ }
        }

        function openRecipeModal(index) {
            const favorite = favoriteRecipes[index];
            if (!favorite || !favorite.recipe) return;
            const recipe = favorite.recipe;

            document.getElementById('modal-recipe-title').textContent = favTitleCase(recipe.title || '-');
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
                const text = typeof ingredient === 'string'
                    ? ingredient
                    : [(ingredient.quantity || '').toString().trim(), (ingredient.name || '').toString().trim()]
                        .filter(Boolean).join(' ');
                if (!text) return;
                const li = document.createElement('li');
                li.className = 'flex items-center gap-2';
                li.innerHTML = `<iconify-icon icon="lucide:circle" class="text-emerald-600 text-xs"></iconify-icon><span class="text-sm">${text}</span>`;
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
            // Pull real favorites from the API (no more dummy data).
            loadFavorites();

            // Delegate clicks: unlike + open modal.
            document.getElementById('favorite-recipe-grid')?.addEventListener('click', (e) => {
                const unlikeBtn = e.target.closest?.('[data-action="unlike"]');
                if (unlikeBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    unlikeFavorite(unlikeBtn.dataset.likeId);
                    return;
                }
                const openEl = e.target.closest?.('[data-action="open"]');
                if (openEl) {
                    openRecipeModal(Number(openEl.dataset.idx));
                }
            });
        });
    </script>
</body>

</html>
