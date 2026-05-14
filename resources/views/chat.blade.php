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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                    <iconify-icon icon="lucide:dumbbell" class="text-lg"></iconify-icon>
                    Exercise Routine
                </a>

                <a href="/chat"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
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

        <!-- Main Content (Chat Interface) -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header
                class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary border border-primary/30">
                                <iconify-icon icon="lucide:bot" class="text-xl"></iconify-icon>
                            </div>
                            <span
                                class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-tertiary border-2 border-card"></span>
                        </div>
                        <div>
                            <h1 class="text-base font-heading font-bold leading-none">Uplyfe AI</h1>
                            <p class="text-xs text-tertiary font-medium mt-1">Online & ready</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 relative">
                    <button id="new-chat-button" title="Start a new chat"
                        class="px-3 py-1.5 rounded-full border border-primary/30 bg-primary/5 text-primary text-xs font-semibold hover:bg-primary/10 transition-colors flex items-center gap-1">
                        <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                        <span class="hidden sm:inline">New chat</span>
                    </button>
                    <button id="history-toggle" title="Conversation history"
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:history" class="text-xl"></iconify-icon>
                    </button>
                    <div id="history-dropdown"
                        class="hidden absolute right-0 top-12 w-80 max-h-96 overflow-y-auto bg-card border border-border rounded-xl shadow-lg z-50 p-2">
                        <p class="text-xs font-bold text-muted-foreground px-3 py-2 sticky top-0 bg-card">Recent conversations</p>
                        <div id="history-list" class="flex flex-col gap-1">
                            <p class="text-xs text-muted-foreground px-3 py-4 text-center">Loading…</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Chat Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-6 flex flex-col gap-6 scroll-smooth">
                <div id="chat-empty-state" class="m-auto text-center max-w-md py-12">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mx-auto mb-4">
                        <iconify-icon icon="lucide:sparkles" class="text-3xl"></iconify-icon>
                    </div>
                    <h2 class="text-lg font-heading font-bold">Hi {{ $user->first_name ?? 'there' }} — how can I help today?</h2>
                    <p class="text-sm text-muted-foreground mt-2">Ask about your habits, diet, recipes, or workouts. Conversations are saved automatically.</p>
                </div>
                <div id="chat-spacer" class="h-20"></div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-background border-t border-border mt-auto">
                <div
                    class="max-w-4xl mx-auto relative flex items-end gap-2 bg-card border border-border rounded-3xl p-2 shadow-sm focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent transition-all">
                    <button class="p-3 text-muted-foreground hover:text-primary transition-colors flex-shrink-0">
                        <iconify-icon icon="lucide:plus-circle" class="text-xl"></iconify-icon>
                    </button>

                    <textarea placeholder="Ask anything about your health, diet, or workouts..."
                        class="flex-1 bg-transparent border-none outline-none resize-none max-h-32 min-h-[44px] py-3 text-sm text-foreground placeholder:text-muted-foreground"
                        rows="1"></textarea>

                    <button id="send-button"
                        class="w-11 h-11 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-md hover:shadow-lg hover:scale-105 transition-all flex-shrink-0 mb-0.5 mr-0.5">
                        <iconify-icon icon="lucide:send" class="text-lg ml-0.5"></iconify-icon>
                    </button>
                </div>
                <p class="text-center text-[10px] text-muted-foreground mt-2">
                    Uplyfe AI can make mistakes. Always consult your doctor for serious medical advice.
                </p>
            </div>

        </main>
    </div>

    <script>
        const USER_AVATAR_SRC = @json($avatarSrc);
        const chatHistory = [];
        // Server-assigned conversation id. Persisted in localStorage so it
        // survives page navigation — when the user comes back to /chat, we
        // resume the same thread rather than starting fresh.
        const CID_KEY = 'uplyfe.chat.conversationId';
        let conversationId = (() => {
            const raw = localStorage.getItem(CID_KEY);
            return raw ? Number(raw) : null;
        })();

        function setConversationId(id) {
            conversationId = id;
            if (id) {
                localStorage.setItem(CID_KEY, String(id));
            } else {
                localStorage.removeItem(CID_KEY);
            }
        }

        function clearMessageArea() {
            const chatMessages = document.getElementById('chat-messages');
            // Keep the empty state + spacer, drop every other child.
            [...chatMessages.children].forEach((child) => {
                if (child.id !== 'chat-empty-state' && child.id !== 'chat-spacer') {
                    child.remove();
                }
            });
        }

        function hideEmptyState() {
            const el = document.getElementById('chat-empty-state');
            if (el) el.classList.add('hidden');
        }

        function showEmptyState() {
            const el = document.getElementById('chat-empty-state');
            if (el) el.classList.remove('hidden');
        }

        function escapeHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Tiny safe markdown renderer for chat replies. Escapes HTML FIRST
        // so the LLM can't inject tags, then converts a small subset of
        // markdown back into formatted HTML.
        //   **text**   → <strong>
        //   *text* / _text_ → <em>   (only when not surrounded by other *)
        //   `code`     → <code>
        //   leading `- ` or `* ` → bullet
        //   leading `N. ` → numbered list item
        //   blank line → paragraph break
        function renderMarkdown(text) {
            if (text == null) return '';
            const escaped = escapeHtml(text);

            // Code spans first so subsequent formatting doesn't touch them.
            let s = escaped.replace(/`([^`\n]+)`/g, '<code class="px-1 py-0.5 rounded bg-muted text-[12px]">$1</code>');

            // Bold (** … **). Run before italic so single-* inside isn't matched.
            s = s.replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>');
            // Italic with *…* (not part of a **) or _…_.
            s = s.replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
            s = s.replace(/(^|[^_])_([^_\n]+?)_(?!_)/g, '$1<em>$2</em>');

            // Lists + paragraphs, line by line.
            const lines = s.split('\n');
            const out = [];
            let inUl = false, inOl = false;
            const closeLists = () => {
                if (inUl) { out.push('</ul>'); inUl = false; }
                if (inOl) { out.push('</ol>'); inOl = false; }
            };
            for (const raw of lines) {
                const line = raw.trimEnd();
                if (line === '') { closeLists(); out.push(''); continue; }
                const mUl = /^\s*[-*]\s+(.*)$/.exec(line);
                const mOl = /^\s*\d+\.\s+(.*)$/.exec(line);
                if (mUl) {
                    if (!inUl) { closeLists(); out.push('<ul class="list-disc ml-5 space-y-1">'); inUl = true; }
                    out.push(`<li>${mUl[1]}</li>`);
                } else if (mOl) {
                    if (!inOl) { closeLists(); out.push('<ol class="list-decimal ml-5 space-y-1">'); inOl = true; }
                    out.push(`<li>${mOl[1]}</li>`);
                } else {
                    closeLists();
                    out.push(line);
                }
            }
            closeLists();

            // Collapse consecutive empties into a paragraph break.
            return out.join('\n')
                .replace(/\n{2,}/g, '<br><br>')
                .replace(/\n/g, '<br>');
        }

        function renderUserMessage(message) {
            return `
                <div class="flex gap-4 max-w-3xl ml-auto flex-row-reverse animate-[fadeIn_0.3s_ease-out]">
                    <img src="${USER_AVATAR_SRC}" alt="User" class="w-8 h-8 rounded-full border border-border flex-shrink-0 mt-1">
                    <div class="flex flex-col gap-1 items-end">
                        <span class="text-xs font-bold text-muted-foreground mr-1">You</span>
                        <div class="bg-primary text-primary-foreground rounded-2xl rounded-tr-none p-4 shadow-sm text-sm leading-relaxed">
                            ${escapeHtml(message).replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderBotMessage(content, opts = {}) {
            const id = opts.id ? `id="${opts.id}"` : '';
            const body = opts.isHtml ? content : renderMarkdown(content);
            const cardsHtml = Array.isArray(opts.cards) && opts.cards.length
                ? `<div class="mt-3 grid gap-3">${opts.cards.map(renderCard).join('')}</div>`
                : '';
            return `
                <div ${id} class="flex gap-4 max-w-3xl animate-[fadeIn_0.3s_ease-out]">
                    <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary flex-shrink-0 mt-1">
                        <iconify-icon icon="lucide:bot" class="text-sm"></iconify-icon>
                    </div>
                    <div class="flex flex-col gap-1 flex-1 min-w-0">
                        <span class="text-xs font-bold text-muted-foreground ml-1">Uplyfe AI</span>
                        <div class="bg-card border border-border rounded-2xl rounded-tl-none p-4 shadow-sm text-sm leading-relaxed">
                            ${body}
                        </div>
                        ${cardsHtml}
                    </div>
                </div>
            `;
        }

        // ---------- Liked-meals cache ----------
        // Keyed by `${planId|none}:${dayKey|none}:${mealType}` so we can
        // mark hearts on recipe cards as filled when the user already
        // liked that exact slot. Re-fetched on page load + after every
        // like/unlike so the chat stays in sync with /favorite-recipes.
        const likedByKey = new Map();
        function likeKeyFor(card) {
            const pid = card?.meal_plan_id ?? 'none';
            const day = card?.day_key ?? 'none';
            const slot = card?.meal_type ?? '';
            return `${pid}:${day}:${slot}`;
        }
        async function refreshLikedSet() {
            try {
                const res = await fetch('/api/meal-likes', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!res.ok) return;
                const data = await res.json();
                likedByKey.clear();
                for (const like of data.likes || []) {
                    const k = `${like.meal_plan_id ?? 'none'}:${like.day_key ?? 'none'}:${like.meal_type ?? ''}`;
                    likedByKey.set(k, like.id);
                }
                // Repaint every recipe card already in the DOM so their
                // hearts reflect the freshly-loaded state.
                document.querySelectorAll('button[data-action="like-recipe"]').forEach(btn => {
                    try {
                        const card = JSON.parse(decodeURIComponent(btn.dataset.card || ''));
                        markHeart(btn, likedByKey.get(likeKeyFor(card)) ?? null);
                    } catch (_) {}
                });
            } catch (_) {}
        }
        function markHeart(btn, likeId) {
            if (!btn) return;
            const icon = btn.querySelector('iconify-icon');
            if (likeId) {
                btn.classList.remove('text-muted-foreground');
                btn.classList.add('text-red-500');
                if (icon) icon.setAttribute('style', 'fill: currentColor;');
                btn.dataset.likeId = String(likeId);
                btn.title = 'Tap to remove from favorites';
            } else {
                btn.classList.remove('text-red-500');
                btn.classList.add('text-muted-foreground');
                if (icon) icon.removeAttribute('style');
                delete btn.dataset.likeId;
                btn.title = 'Save to favorites';
            }
        }

        // ---------- Functional cards (recipe / exercise) ----------
        function renderCard(card) {
            if (!card || typeof card !== 'object') return '';
            if (card.type === 'recipe') return renderRecipeCard(card);
            if (card.type === 'exercise') return renderExerciseCard(card);
            if (card.type === 'dietary_update') return renderDietaryUpdateCard(card);
            return '';
        }

        function renderDietaryUpdateCard(card) {
            const added = Array.isArray(card.added) ? card.added : [];
            const removed = Array.isArray(card.removed) ? card.removed : [];
            const now = Array.isArray(card.exclusions_now) ? card.exclusions_now : [];
            const chips = (arr, cls) => arr.map(t =>
                `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold ${cls}">${escapeHtml(t)}</span>`
            ).join(' ');
            const addedLine = added.length
                ? `<div class="flex items-center gap-2 text-xs"><span class="text-muted-foreground">Added:</span>${chips(added, 'bg-red-100 text-red-700')}</div>`
                : '';
            const removedLine = removed.length
                ? `<div class="flex items-center gap-2 text-xs"><span class="text-muted-foreground">Removed:</span>${chips(removed, 'bg-tertiary/20 text-tertiary')}</div>`
                : '';
            const nowLine = now.length
                ? `<div class="flex items-start gap-2 text-xs"><span class="text-muted-foreground mt-0.5">You avoid:</span><div class="flex flex-wrap gap-1">${chips(now, 'bg-muted text-foreground border border-border')}</div></div>`
                : '<div class="text-xs text-muted-foreground">No active exclusions.</div>';
            const status = card.regenerating
                ? '<span class="inline-flex items-center gap-1 text-tertiary"><iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon>Regenerating your weekly menu…</span>'
                : '<span class="text-muted-foreground">No saved plan yet — generate one on the recipe page.</span>';
            return `
                <div class="bg-card border border-tertiary/40 rounded-2xl p-4 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="lucide:check-circle-2" class="text-tertiary"></iconify-icon>
                        <h4 class="font-bold text-sm">Food exclusions updated</h4>
                    </div>
                    ${addedLine}
                    ${removedLine}
                    ${nowLine}
                    <div class="text-xs border-t border-border pt-2 mt-1 flex items-center justify-between gap-2">
                        ${status}
                        <a href="/recipe" class="text-primary font-semibold hover:underline whitespace-nowrap">Open recipe page →</a>
                    </div>
                </div>
            `;
        }

        function renderRecipeCard(card) {
            const slot = (card.meal_type || 'meal').replace(/\b\w/g, c => c.toUpperCase());
            const title = escapeHtml(card.title || slot);
            const desc = escapeHtml(card.description || '');
            const cals = escapeHtml(String(card.calories || '—'));
            const prot = escapeHtml(String(card.protein || '—'));
            const tags = Array.isArray(card.tags) ? card.tags.filter(Boolean) : [];
            const tagHtml = tags.slice(0, 3).map(t =>
                `<span class="text-[10px] font-semibold text-tertiary bg-tertiary/10 px-2 py-0.5 rounded-full">${escapeHtml(t)}</span>`
            ).join(' ');
            // Encode the snapshot so the like button can POST it without
            // round-tripping through a global.
            const snap = encodeURIComponent(JSON.stringify(card));
            const noteHtml = card.note
                ? `<p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-2 py-1">${escapeHtml(card.note)}</p>`
                : '';
            // Heart state — if this exact meal/day/plan slot is already
            // liked, render the heart filled-red instead of muted.
            const likeId = likedByKey.get(likeKeyFor(card)) ?? null;
            const heartCls = likeId
                ? 'text-red-500 hover:text-red-600'
                : 'text-muted-foreground hover:text-red-500';
            const heartStyle = likeId ? 'fill: currentColor;' : '';
            const heartTitle = likeId ? 'Tap to remove from favorites' : 'Save to favorites';
            const likeAttr = likeId ? `data-like-id="${likeId}"` : '';
            return `
                <div class="bg-card border border-border rounded-2xl p-4 shadow-sm flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wide text-muted-foreground">${escapeHtml(slot)}</span>
                            <h4 class="font-bold text-sm leading-tight mt-1 truncate">${title}</h4>
                        </div>
                        <button data-action="like-recipe" data-card="${snap}" ${likeAttr}
                            class="${heartCls} transition-colors flex-shrink-0"
                            title="${heartTitle}">
                            <iconify-icon icon="lucide:heart" style="${heartStyle}"></iconify-icon>
                        </button>
                    </div>
                    ${noteHtml}
                    ${desc ? `<p class="text-xs text-muted-foreground line-clamp-2">${desc}</p>` : ''}
                    <div class="flex flex-wrap gap-1">${tagHtml}</div>
                    <div class="flex items-center gap-4 text-xs text-muted-foreground border-t border-border pt-2 mt-1">
                        <span><strong class="text-foreground">${cals}</strong> cal</span>
                        <span><strong class="text-foreground">${prot}</strong> protein</span>
                        ${card.ingredient_count ? `<span>${card.ingredient_count} ingredients</span>` : ''}
                        <a href="/recipe?day=${encodeURIComponent(card.day_key || '')}&meal=${encodeURIComponent(card.meal_type || '')}"
                           class="ml-auto text-primary font-semibold hover:underline">Open in plan →</a>
                    </div>
                </div>
            `;
        }

        function renderExerciseCard(card) {
            const day = escapeHtml(card.day_label || 'Workout');
            const title = escapeHtml(card.title || 'Workout');
            const desc = escapeHtml(card.description || '');
            const dur = escapeHtml(String(card.duration || ''));
            const exs = Array.isArray(card.exercises_preview) ? card.exercises_preview : [];
            const ids = Array.isArray(card.image_ids) ? card.image_ids.slice(0, 3) : [];
            const thumbs = ids.map(id =>
                `<img src="/api/exercises/${encodeURIComponent(id)}/image?kind=jpg" alt="" class="w-10 h-10 rounded object-cover border border-border" />`
            ).join('');
            const exList = exs.slice(0, 4).map(e => {
                const n = escapeHtml(e.name || '');
                const d = e.detail ? ` <span class="text-muted-foreground">· ${escapeHtml(e.detail)}</span>` : '';
                return `<li class="text-xs">${n}${d}</li>`;
            }).join('');
            return `
                <div class="bg-card border border-border rounded-2xl p-4 shadow-sm flex flex-col gap-2">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <iconify-icon icon="lucide:dumbbell"></iconify-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wide text-muted-foreground">${day}${dur ? ' · ' + dur : ''}</span>
                            <h4 class="font-bold text-sm leading-tight mt-1">${title}</h4>
                        </div>
                    </div>
                    ${desc ? `<p class="text-xs text-muted-foreground line-clamp-2">${desc}</p>` : ''}
                    ${thumbs ? `<div class="flex gap-1 mt-1">${thumbs}</div>` : ''}
                    ${exList ? `<ul class="space-y-1 mt-1">${exList}</ul>` : ''}
                    <div class="flex items-center gap-3 text-xs text-muted-foreground border-t border-border pt-2 mt-1">
                        ${card.exercise_count ? `<span>${card.exercise_count} exercises</span>` : ''}
                        <a href="/exercise?day=${encodeURIComponent((card.day_label || '').toLowerCase())}"
                           class="ml-auto text-primary font-semibold hover:underline">Open in plan →</a>
                    </div>
                </div>
            `;
        }

        async function likeRecipeCard(btn) {
            try {
                const card = JSON.parse(decodeURIComponent(btn.dataset.card || ''));
                if (!card) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const existingLikeId = btn.dataset.likeId || likedByKey.get(likeKeyFor(card));

                if (existingLikeId) {
                    // Already liked — toggle off.
                    const res = await fetch(`/api/meal-likes/${existingLikeId}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        credentials: 'same-origin',
                    });
                    if (res.ok) {
                        likedByKey.delete(likeKeyFor(card));
                        markHeart(btn, null);
                    }
                    return;
                }
                if (!card.snapshot) return;
                const res = await fetch('/api/meal-likes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        meal_plan_id: card.meal_plan_id || null,
                        day_key: card.day_key || null,
                        meal_type: card.meal_type || 'snack',
                        title: card.title || 'Saved meal',
                        snapshot: card.snapshot,
                    }),
                });
                if (res.ok) {
                    const body = await res.json().catch(() => ({}));
                    const newId = body?.like?.id;
                    if (newId) {
                        likedByKey.set(likeKeyFor(card), newId);
                        markHeart(btn, newId);
                    }
                }
            } catch (e) {
                console.warn('likeRecipeCard failed:', e);
            }
        }

        // Delegate clicks on card like-buttons (cards are rendered into the
        // chat stream dynamically, so direct listeners would miss new ones).
        document.addEventListener('click', (e) => {
            const btn = e.target.closest?.('button[data-action="like-recipe"]');
            if (btn) {
                e.preventDefault();
                likeRecipeCard(btn);
            }
        });

        async function sendChat() {
            const textarea = document.querySelector('textarea');
            const sendBtn = document.getElementById('send-button');
            const message = textarea.value.trim();
            if (!message) return;

            hideEmptyState();
            const chatMessages = document.getElementById('chat-messages');
            const spacer = document.getElementById('chat-spacer');
            const insertBefore = (html) => {
                if (spacer) {
                    spacer.insertAdjacentHTML('beforebegin', html);
                } else {
                    chatMessages.insertAdjacentHTML('beforeend', html);
                }
            };
            insertBefore(renderUserMessage(message));

            const pendingId = 'pending-' + Date.now();
            const pendingHtml = `
                <span class="inline-flex gap-1 items-center text-muted-foreground">
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:300ms"></span>
                </span>
            `;
            insertBefore(renderBotMessage(pendingHtml, { id: pendingId, isHtml: true }));

            textarea.value = '';
            textarea.style.height = 'auto';
            chatMessages.scrollTop = chatMessages.scrollHeight;
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const res = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message,
                        conversation_id: conversationId,
                        history: chatHistory.slice(-10),
                    }),
                });

                let reply;
                let cards = null;
                if (!res.ok) {
                    let detail = '';
                    try {
                        const j = await res.json();
                        detail = j.error || j.message || '';
                    } catch (_) { /* ignore */ }
                    reply = `Sorry — something went wrong (HTTP ${res.status})${detail ? `: ${detail}` : ''}.`;
                } else {
                    const data = await res.json();
                    reply = data.reply || 'I had no response. Try again.';
                    cards = Array.isArray(data.cards) ? data.cards : null;
                    if (data.conversation_id) {
                        setConversationId(data.conversation_id);
                        // Refresh history dropdown so the new conversation
                        // (and its auto-generated title) shows up immediately.
                        loadConversationList();
                    }
                    // Signal to /recipe and /exercise pages that a background
                    // regen is in flight so they can show a loading banner.
                    if (data.menu_regen) {
                        localStorage.setItem('uplyfe_menu_regen', Date.now());
                    }
                    if (data.workout_regen) {
                        localStorage.setItem('uplyfe_workout_regen', Date.now());
                    }
                    chatHistory.push({ role: 'user', content: message });
                    chatHistory.push({ role: 'assistant', content: reply });
                }

                const pending = document.getElementById(pendingId);
                if (pending) pending.outerHTML = renderBotMessage(reply, { cards });
            } catch (err) {
                const pending = document.getElementById(pendingId);
                const msg = `Network error: ${err?.message || err}`;
                if (pending) pending.outerHTML = renderBotMessage(msg);
            } finally {
                sendBtn.disabled = false;
                sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        document.getElementById('send-button').addEventListener('click', sendChat);
        document.querySelector('textarea').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChat();
            }
        });

        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
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

        document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());

        // ---------- Conversation history wiring ----------
        // Tracks an active poll loop so we don't start a second one when the
        // user clicks around. Cleared when the assistant reply arrives.
        let pollHandle = null;
        let lastSeenMessageId = 0;

        function clearPoll() {
            if (pollHandle) {
                clearTimeout(pollHandle);
                pollHandle = null;
            }
        }

        function ensurePendingBubble() {
            if (document.getElementById('pending-poll')) return;
            hideEmptyState();
            const spacer = document.getElementById('chat-spacer');
            const html = `
                <span class="inline-flex gap-1 items-center text-muted-foreground">
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-current animate-bounce" style="animation-delay:300ms"></span>
                </span>
            `;
            const bubble = renderBotMessage(html, { id: 'pending-poll', isHtml: true });
            if (spacer) spacer.insertAdjacentHTML('beforebegin', bubble);
        }

        async function pollForAssistantReply(cid) {
            // Stop polling when the user navigates away.
            if (document.hidden) {
                pollHandle = setTimeout(() => pollForAssistantReply(cid), 4000);
                return;
            }
            try {
                const res = await fetch(`/api/chat-conversations/${cid}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!res.ok) {
                    clearPoll();
                    return;
                }
                const data = await res.json();
                const msgs = data.messages || [];
                // Append any messages we haven't seen yet (could be the
                // assistant reply, or a user message sent from another tab).
                const newOnes = msgs.filter(m => m.id > lastSeenMessageId);
                if (newOnes.length > 0) {
                    const spacer = document.getElementById('chat-spacer');
                    const insertBefore = (html) =>
                        spacer ? spacer.insertAdjacentHTML('beforebegin', html) : null;
                    for (const m of newOnes) {
                        // Don't double-render messages that the synchronous
                        // sendChat() path already inserted into the DOM —
                        // those are tracked in chatHistory by content.
                        const alreadyShown = chatHistory.some(h => h.role === m.role && h.content === m.content);
                        if (!alreadyShown) {
                            insertBefore(m.role === 'user'
                                ? renderUserMessage(m.content)
                                : renderBotMessage(m.content, { cards: m.cards || null }));
                            chatHistory.push({ role: m.role, content: m.content });
                        }
                        lastSeenMessageId = Math.max(lastSeenMessageId, m.id);
                    }
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                // If the tail is now an assistant message, we're done.
                const tail = msgs[msgs.length - 1];
                if (tail && tail.role === 'assistant') {
                    const pending = document.getElementById('pending-poll');
                    if (pending) pending.remove();
                    clearPoll();
                    return;
                }
            } catch (e) {
                // Network blip — keep polling.
            }
            pollHandle = setTimeout(() => pollForAssistantReply(cid), 2000);
        }

        async function loadConversation(id) {
            try {
                const res = await fetch(`/api/chat-conversations/${id}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    // Stored id may point to a conversation that was deleted
                    // or that belongs to a different user (after logout/login).
                    if (res.status === 404) setConversationId(null);
                    return;
                }
                const data = await res.json();
                setConversationId(data.id);
                clearMessageArea();
                clearPoll();
                lastSeenMessageId = 0;
                if (!data.messages || data.messages.length === 0) {
                    showEmptyState();
                    return;
                }
                hideEmptyState();
                const spacer = document.getElementById('chat-spacer');
                const insertBefore = (html) =>
                    spacer ? spacer.insertAdjacentHTML('beforebegin', html) : null;
                for (const m of data.messages) {
                    insertBefore(m.role === 'user'
                        ? renderUserMessage(m.content)
                        : renderBotMessage(m.content, { cards: m.cards || null }));
                    chatHistory.push({ role: m.role, content: m.content });
                    lastSeenMessageId = Math.max(lastSeenMessageId, m.id);
                }
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
                // If the last message is the user's, the assistant reply is
                // still in flight on the server — start polling so it appears
                // automatically when ready.
                const tail = data.messages[data.messages.length - 1];
                if (tail && tail.role === 'user') {
                    ensurePendingBubble();
                    pollForAssistantReply(data.id);
                }
            } catch (e) {
                console.warn('loadConversation failed:', e);
            }
        }

        async function loadConversationList() {
            const listEl = document.getElementById('history-list');
            try {
                const res = await fetch('/api/chat-conversations', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = res.ok ? await res.json() : { conversations: [] };
                const items = data.conversations || [];
                if (items.length === 0) {
                    listEl.innerHTML = '<p class="text-xs text-muted-foreground px-3 py-4 text-center">No conversations yet.</p>';
                    return;
                }
                listEl.innerHTML = items.map(c => {
                    const isActive = c.id === conversationId;
                    const title = escapeHtml(c.title || 'Untitled chat');
                    const when = c.last_message_at
                        ? new Date(c.last_message_at).toLocaleString()
                        : '';
                    return `
                        <button data-cid="${c.id}"
                            class="conv-item text-left px-3 py-2 rounded-lg hover:bg-muted transition-colors ${isActive ? 'bg-muted' : ''}">
                            <p class="text-sm font-medium truncate">${title}</p>
                            <p class="text-[10px] text-muted-foreground">${escapeHtml(when)}</p>
                        </button>
                    `;
                }).join('');
                listEl.querySelectorAll('.conv-item').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const cid = Number(btn.dataset.cid);
                        document.getElementById('history-dropdown').classList.add('hidden');
                        chatHistory.length = 0;
                        await loadConversation(cid);
                    });
                });
            } catch (e) {
                listEl.innerHTML = '<p class="text-xs text-destructive px-3 py-4 text-center">Failed to load.</p>';
            }
        }

        document.getElementById('new-chat-button').addEventListener('click', () => {
            setConversationId(null);
            chatHistory.length = 0;
            clearMessageArea();
            showEmptyState();
            document.getElementById('history-dropdown').classList.add('hidden');
        });

        document.getElementById('history-toggle').addEventListener('click', (e) => {
            e.stopPropagation();
            const dd = document.getElementById('history-dropdown');
            const willOpen = dd.classList.contains('hidden');
            dd.classList.toggle('hidden');
            if (willOpen) loadConversationList();
        });

        document.addEventListener('click', (e) => {
            const dd = document.getElementById('history-dropdown');
            const toggle = document.getElementById('history-toggle');
            if (!dd.classList.contains('hidden') && !dd.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        // On page open: load the user's likes FIRST so cards render with
        // correct heart state, then resume the conversation. We re-fetch
        // after the conversation loads so any cards rendered from history
        // also get their hearts repainted.
        (async () => {
            await refreshLikedSet();
            if (conversationId) {
                await loadConversation(conversationId);
                await refreshLikedSet();  // repaint cards from history
            }
        })();
        // Pre-load the dropdown list in the background so it opens instantly.
        loadConversationList();
    </script>
</body>

</html>
