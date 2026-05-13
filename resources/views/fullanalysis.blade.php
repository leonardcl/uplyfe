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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-primary text-primary-foreground shadow-sm transition-all">
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
                    <button
                        class="text-muted-foreground hover:text-foreground p-1 rounded-md hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:settings" class="text-lg"></iconify-icon>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"
            onclick="toggleSidebar(false)"></div>
            
        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-xl"></iconify-icon>
                    </button>
                    <button onclick="history.back()"
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:arrow-left" class="text-xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Full Health Analysis</h1>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:download" class="text-xl"></iconify-icon>
                    </button>
                    <button
                        class="bg-primary text-primary-foreground px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                        <iconify-icon icon="lucide:share" class="text-base"></iconify-icon>
                        Share Report
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-6xl mx-auto space-y-8">

                    <!-- Report Header (data-driven from /api/health-reports) -->
                    <section id="fa-report-header" class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div class="flex-1">
                                <h2 id="fa-report-title" class="text-2xl font-heading font-bold mb-2">Loading your latest report…</h2>
                                <p id="fa-report-desc" class="text-muted-foreground text-sm mb-4">
                                    We'll pull the most recent lab report from your account.
                                </p>
                                <div id="fa-report-meta" class="flex items-center gap-3 text-sm flex-wrap"></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-center">
                                    <div id="fa-report-score" class="text-3xl font-heading font-extrabold text-muted-foreground">—</div>
                                    <div class="text-xs text-muted-foreground">Severity</div>
                                </div>
                            </div>
                        </div>
                        <p id="fa-no-report" class="hidden mt-4 text-sm text-muted-foreground">
                            No lab reports yet. <a href="/health-check" class="text-primary font-semibold">Upload one →</a>
                        </p>
                    </section>

                    <!-- Today's plan (meal + workout) -->
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-card rounded-2xl border border-border p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:utensils" class="text-primary"></iconify-icon>
                                    Today's meals
                                </h3>
                                <a href="/recipe" class="text-xs text-primary font-semibold hover:underline">Open plan →</a>
                            </div>
                            <div id="fa-today-meals" class="space-y-2">
                                <p class="text-xs text-muted-foreground">Loading…</p>
                            </div>
                        </div>
                        <div class="bg-card rounded-2xl border border-border p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:dumbbell" class="text-primary"></iconify-icon>
                                    Today's workout
                                </h3>
                                <a href="/exercise" class="text-xs text-primary font-semibold hover:underline">Open plan →</a>
                            </div>
                            <div id="fa-today-workout" class="space-y-2">
                                <p class="text-xs text-muted-foreground">Loading…</p>
                            </div>
                        </div>
                    </section>

                    <!-- Recent activity -->
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-card rounded-2xl border border-border p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:message-circle" class="text-primary"></iconify-icon>
                                    Recent conversations
                                </h3>
                                <a href="/chat" class="text-xs text-primary font-semibold hover:underline">Open chat →</a>
                            </div>
                            <ul id="fa-recent-chats" class="space-y-2 text-sm"><li class="text-xs text-muted-foreground">Loading…</li></ul>
                        </div>
                        <div class="bg-card rounded-2xl border border-border p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold flex items-center gap-2">
                                    <iconify-icon icon="lucide:heart" class="text-red-500"></iconify-icon>
                                    Liked meals
                                </h3>
                                <a href="/favorite-recipes" class="text-xs text-primary font-semibold hover:underline">See all →</a>
                            </div>
                            <ul id="fa-recent-likes" class="space-y-2 text-sm"><li class="text-xs text-muted-foreground">Loading…</li></ul>
                        </div>
                    </section>

                    <!-- Key health insights from the latest report -->
                    <section>
                        <h2 class="text-xl font-heading font-bold mb-4 flex items-center gap-2">
                            <iconify-icon icon="lucide:activity" class="text-primary"></iconify-icon>
                            Key health insights
                        </h2>
                        <div id="fa-insights-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                        <p id="fa-no-insights" class="hidden text-sm text-muted-foreground">Upload a lab report to see your key insights.</p>
                    </section>

                    <!-- Clinical summary from the latest report -->
                    <section class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <h2 class="text-xl font-heading font-bold mb-4 flex items-center gap-2">
                            <iconify-icon icon="lucide:file-text" class="text-primary"></iconify-icon>
                            Summary
                        </h2>
                        <p id="fa-summary" class="text-sm text-muted-foreground leading-relaxed whitespace-pre-wrap">
                            Loading the most recent analysis…
                        </p>
                    </section>

                    <!-- AI-extracted recommendations (diet + exercise advice from the report) -->
                    <section class="bg-gradient-to-r from-primary/5 to-tertiary/5 rounded-3xl border border-primary/20 p-8">
                        <h2 class="text-xl font-heading font-bold mb-6 flex items-center gap-2">
                            <iconify-icon icon="lucide:sparkles" class="text-primary"></iconify-icon>
                            From your report
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-card rounded-2xl p-6 border border-border">
                                <h3 class="font-bold mb-3 text-tertiary flex items-center gap-2">
                                    <iconify-icon icon="lucide:apple"></iconify-icon>
                                    Diet advice
                                </h3>
                                <p id="fa-diet-advice" class="text-sm text-muted-foreground whitespace-pre-wrap">—</p>
                            </div>
                            <div class="bg-card rounded-2xl p-6 border border-border">
                                <h3 class="font-bold mb-3 text-blue-600 flex items-center gap-2">
                                    <iconify-icon icon="lucide:dumbbell"></iconify-icon>
                                    Exercise advice
                                </h3>
                                <p id="fa-exercise-advice" class="text-sm text-muted-foreground whitespace-pre-wrap">—</p>
                            </div>
                        </div>
                    </section>

                    <!-- Abnormal findings list -->
                    <section class="bg-card rounded-3xl border border-border p-8 shadow-sm">
                        <h2 class="text-xl font-heading font-bold mb-4 flex items-center gap-2">
                            <iconify-icon icon="lucide:alert-triangle" class="text-orange-500"></iconify-icon>
                            Abnormal findings
                        </h2>
                        <ul id="fa-abnormal" class="space-y-3 text-sm"><li class="text-xs text-muted-foreground">Loading…</li></ul>
                    </section>

                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle functionality
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

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
        }

        function severityClass(sev) {
            return {
                normal:     'bg-tertiary/20 text-tertiary',
                borderline: 'bg-amber-100 text-amber-700',
                abnormal:   'bg-orange-100 text-orange-700',
                critical:   'bg-red-100 text-red-700',
            }[sev] || 'bg-muted text-muted-foreground';
        }

        function fmtDate(iso) {
            if (!iso) return '';
            return new Date(iso).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        async function fetchJson(url) {
            try {
                const r = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
                if (!r.ok) return null;
                return await r.json();
            } catch (_) { return null; }
        }

        async function loadHealthHeader() {
            const list = await fetchJson('/api/health-reports');
            const reports = list?.reports || [];
            const noReport = document.getElementById('fa-no-report');
            if (!reports.length) {
                document.getElementById('fa-report-title').textContent = 'No lab reports yet';
                document.getElementById('fa-report-desc').textContent = 'Upload one on the Health Check page and your analysis lands here automatically.';
                noReport?.classList.remove('hidden');
                document.getElementById('fa-summary').textContent = 'No report to summarize yet.';
                document.getElementById('fa-no-insights')?.classList.remove('hidden');
                document.getElementById('fa-abnormal').innerHTML = '<li class="text-xs text-muted-foreground">No findings yet.</li>';
                return;
            }
            const head = reports[0];
            const full = await fetchJson(`/api/health-reports/${head.id}`);
            const when = fmtDate(head.created_at);
            document.getElementById('fa-report-title').textContent = `${head.original_filename || 'Health report'} · ${when}`;
            document.getElementById('fa-report-desc').textContent = `Analyzed ${head.biomarker_count ?? '—'} biomarkers · ${head.abnormal_count ?? 0} outside range${head.critical_count ? ` · ${head.critical_count} critical` : ''}.`;
            const meta = document.getElementById('fa-report-meta');
            meta.innerHTML = `
                <span class="flex items-center gap-2"><iconify-icon icon="lucide:file-type-pdf" class="text-red-500"></iconify-icon>${escapeHtml(head.original_filename || '')}</span>
                ${head.original_size_bytes ? `<span>${(head.original_size_bytes/1024).toFixed(0)} KB</span>` : ''}
                <span class="px-2 py-1 rounded-md text-xs font-bold ${severityClass(head.overall_severity)}">${escapeHtml(head.overall_severity || 'normal')}</span>
            `;
            const score = document.getElementById('fa-report-score');
            score.textContent = head.overall_severity || '—';
            score.className = 'text-2xl font-heading font-extrabold px-3 py-1 rounded-xl ' + severityClass(head.overall_severity);
            // Summary
            document.getElementById('fa-summary').textContent = (full?.summary || head.summary || '—');
            // Key insights
            const grid = document.getElementById('fa-insights-grid');
            const insights = full?.key_insights || [];
            if (!insights.length) {
                grid.innerHTML = '';
                document.getElementById('fa-no-insights')?.classList.remove('hidden');
            } else {
                grid.innerHTML = insights.map(i => `
                    <div class="bg-card rounded-2xl p-5 border border-border shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold">${escapeHtml(i.label || i.key)}</h3>
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold ${severityClass(i.status)}">${escapeHtml(i.status || '')}</span>
                        </div>
                        <p class="text-2xl font-heading font-extrabold">${escapeHtml(i.value ?? '—')} <span class="text-sm font-medium text-muted-foreground">${escapeHtml(i.unit || '')}</span></p>
                        <p class="text-xs text-muted-foreground mt-1">${escapeHtml(i.summary || '')}</p>
                    </div>
                `).join('');
            }
            // Advice
            const diet = full?.diet_advice || '—';
            const ex = full?.exercise_advice || '—';
            document.getElementById('fa-diet-advice').textContent = diet;
            document.getElementById('fa-exercise-advice').textContent = ex;
            // Abnormal findings
            const abn = full?.abnormal_findings || [];
            const abnEl = document.getElementById('fa-abnormal');
            if (!abn.length) {
                abnEl.innerHTML = '<li class="text-xs text-muted-foreground">No abnormal findings.</li>';
            } else {
                abnEl.innerHTML = abn.map(f => `
                    <li class="flex items-center justify-between gap-3 border-b border-border pb-2">
                        <div>
                            <div class="font-medium capitalize">${escapeHtml(String(f.biomarker || '').replace(/_/g,' '))}</div>
                            <div class="text-xs text-muted-foreground">${escapeHtml(f.rationale || f.label || '')}</div>
                        </div>
                        <span class="text-right">
                            <div class="font-mono text-sm">${escapeHtml(f.value)} ${escapeHtml(f.unit || '')}</div>
                            <span class="px-2 py-0.5 rounded text-xs font-bold ${severityClass(f.severity)}">${escapeHtml(f.severity || '')}</span>
                        </span>
                    </li>
                `).join('');
            }
        }

        async function loadTodayMeals() {
            const data = await fetchJson('/api/meal-plans/active');
            const slot = document.getElementById('fa-today-meals');
            if (!data?.payload) {
                slot.innerHTML = '<p class="text-xs text-muted-foreground">No meal plan yet. <a href="/recipe" class="text-primary font-semibold">Generate one →</a></p>';
                return;
            }
            const plans = data.payload.plan || {};
            const today = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'][new Date().getDay()];
            const dayKey = plans[today] ? today : Object.keys(plans)[0];
            const day = plans[dayKey];
            if (!day) { slot.innerHTML = '<p class="text-xs text-muted-foreground">No meals for today.</p>'; return; }
            const items = ['breakfast','lunch','dinner','snack']
                .filter(m => day[m])
                .map(m => {
                    const meal = day[m];
                    const href = `/recipe?day=${encodeURIComponent(dayKey)}&meal=${encodeURIComponent(m)}`;
                    return `<a href="${href}" class="flex items-center justify-between p-2 rounded-lg hover:bg-muted transition-colors">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">${m}</span>
                            <p class="text-sm font-medium leading-tight">${escapeHtml(meal.title || '')}</p>
                        </div>
                        <span class="text-xs text-muted-foreground">${escapeHtml(meal.calories || '—')} cal</span>
                    </a>`;
                }).join('');
            slot.innerHTML = items || '<p class="text-xs text-muted-foreground">No meals for today.</p>';
        }

        async function loadTodayWorkout() {
            const data = await fetchJson('/api/exercise-plans');
            const slot = document.getElementById('fa-today-workout');
            const plans = data?.plans || [];
            if (!plans.length) {
                slot.innerHTML = '<p class="text-xs text-muted-foreground">No exercise plan yet. <a href="/exercise" class="text-primary font-semibold">Generate one →</a></p>';
                return;
            }
            const full = await fetchJson(`/api/exercise-plans/${plans[0].id}`);
            const days = full?.weekly_workout_plan || [];
            if (!days.length) { slot.innerHTML = '<p class="text-xs text-muted-foreground">No workouts in the latest plan.</p>'; return; }
            const today = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'][new Date().getDay()];
            const picked = days.find(d => (d.day_label || '').toLowerCase() === today) || days[0];
            const exs = (picked.exercises || []).slice(0, 4);
            const items = exs.map(e =>
                `<li class="text-sm flex items-center justify-between"><span>${escapeHtml(e.name || '')}</span><span class="text-xs text-muted-foreground">${escapeHtml(e.detail || '')}</span></li>`
            ).join('');
            slot.innerHTML = `
                <div class="text-xs text-muted-foreground">${escapeHtml(picked.day_label || 'Day')}${picked.duration ? ' · ' + escapeHtml(picked.duration) : ''}</div>
                <p class="font-medium text-sm">${escapeHtml(picked.title || picked.heading || 'Workout')}</p>
                <ul class="space-y-1 mt-2">${items}</ul>
            `;
        }

        async function loadRecentChats() {
            const data = await fetchJson('/api/chat-conversations');
            const list = data?.conversations || [];
            const ul = document.getElementById('fa-recent-chats');
            if (!list.length) {
                ul.innerHTML = '<li class="text-xs text-muted-foreground">No conversations yet.</li>';
                return;
            }
            ul.innerHTML = list.slice(0, 5).map(c => `
                <li><a href="/chat" class="block p-2 rounded-lg hover:bg-muted transition-colors">
                    <p class="text-sm font-medium truncate">${escapeHtml(c.title || 'Untitled chat')}</p>
                    <p class="text-[10px] text-muted-foreground">${fmtDate(c.last_message_at || c.created_at)}</p>
                </a></li>
            `).join('');
        }

        async function loadRecentLikes() {
            const data = await fetchJson('/api/meal-likes');
            const list = data?.likes || [];
            const ul = document.getElementById('fa-recent-likes');
            if (!list.length) {
                ul.innerHTML = '<li class="text-xs text-muted-foreground">No liked meals yet.</li>';
                return;
            }
            ul.innerHTML = list.slice(0, 5).map(l => {
                const href = `/recipe?day=${encodeURIComponent(l.day_key || '')}&meal=${encodeURIComponent(l.meal_type || '')}`;
                return `<li><a href="${href}" class="flex items-center justify-between p-2 rounded-lg hover:bg-muted transition-colors">
                    <span class="text-sm truncate">${escapeHtml(l.title || '')}</span>
                    <span class="text-[10px] text-muted-foreground capitalize">${escapeHtml(l.meal_type || '')}</span>
                </a></li>`;
            }).join('');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());
            loadHealthHeader();
            loadTodayMeals();
            loadTodayWorkout();
            loadRecentChats();
            loadRecentLikes();
        });

    </script>
</body>

</html>