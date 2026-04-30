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
    <div class="min-h-screen w-full bg-background flex flex-col relative overflow-x-hidden text-foreground font-sans">

        <!-- Navigation -->
        <header
            class="fixed inset-x-0 top-0 z-50 w-full bg-card/80 backdrop-blur-md border-b border-border transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">


                <div class="flex items-center gap-2 cursor-pointer group">
                    <button onclick="toggleDashboardMenu()"
                        class="text-foreground p-2 hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <div
                        class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground transform group-hover:scale-110 transition-transform duration-300 shadow-md">
                        <iconify-icon icon="lucide:leaf" class="text-xl"></iconify-icon>
                    </div>
                    <span class="text-xl font-heading font-bold tracking-tight text-foreground">Uplyfe</span>
                </div>

                <nav class="hidden md:flex items-center gap-8">
                    <a href="/features"
                        class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">Features</a>
                    <a href="/how-it-works"
                        class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">How it
                        Works</a>
                    <a href="/testimonials"
                        class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">Testimonials</a>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="/login"
                        class="hidden sm:block text-sm font-medium text-foreground hover:text-primary transition-colors">Log
                        In</a>
                    <a href="/health-check"
                        class="bg-primary text-primary-foreground px-5 py-2.5 rounded-full text-sm font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        Get Started
                        <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                    </a>
                </div>
            </div>
        </header>

        <div id="mobile-dashboard-menu" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="toggleMenu()"></div>
            <aside
                class="absolute left-0 top-0 h-full w-72 bg-card border-r border-border shadow-xl p-6 overflow-y-auto">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-primary-foreground">
                            <iconify-icon icon="lucide:leaf" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <p class="font-bold">Uplyfe Menu</p>
                            <p class="text-xs text-muted-foreground">Quick access</p>
                        </div>
                    </div>
                    <button onclick="toggleMenu()"
                        class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                    </button>
                </div>
                <nav class="space-y-3">
                    <a href="/health-check"
                        class="block rounded-2xl px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">Health
                        Checkup</a>
                    <a href="/recipe"
                        class="block rounded-2xl px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">Nutrition
                        & Recipes</a>
                    <a href="/exercise"
                        class="block rounded-2xl px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">Exercise
                        Routine</a>
                    <a href="/chat"
                        class="block rounded-2xl px-4 py-3 text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">AI
                        Assistant</a>
                </nav>
            </aside>
        </div>

        <div class="flex flex-1 mt-16">
            <aside id="dashboard-sidebar"
                class="hidden md:flex fixed top-16 left-0 bottom-0 w-72 flex-col bg-card border-r border-border overflow-y-auto">
                <div class="flex-1 p-6 space-y-3">

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
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                        <iconify-icon icon="lucide:bot" class="text-lg"></iconify-icon>
                        AI Assistant
                    </a>
                </div>
            </aside>

            <!-- Hero Section -->
            <main id="dashboard-main" class="flex-1 flex flex-col md:ml-72">
                <section class="relative w-full py-12 lg:py-24 overflow-hidden">
                    <!-- Decorative background blur -->
                    <div
                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/20 rounded-full blur-[100px] -z-10 animate-pulse">
                    </div>

                    <div
                        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="flex flex-col items-start gap-6 animate-[slideInLeft_0.8s_ease-out]">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 text-primary-foreground text-sm font-medium border border-primary/30 shadow-sm">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tertiary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-tertiary"></span>
                                </span>
                                Powered by AI
                            </div>
                            <h1
                                class="text-4xl sm:text-5xl lg:text-6xl font-heading font-extrabold leading-tight text-foreground tracking-tight">
                                Your Health, <br />
                                <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-tertiary to-primary">Personalized</span>
                            </h1>
                            <p class="text-lg sm:text-xl text-muted-foreground max-w-lg leading-relaxed">
                                Upload your medical reports and let AI design your perfect nutrition plan, exercise
                                routine,
                                and daily health habits. Uplyfe is your intelligent companion for a better life.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto mt-4">
                                <button
                                    class="bg-primary text-primary-foreground px-8 py-4 rounded-full text-base font-semibold shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-center flex items-center justify-center gap-2">
                                    <a href="/health-check" class="flex items-center gap-2">
                                        Start Your Journey
                                        <iconify-icon icon="lucide:sparkles" class="text-lg"></iconify-icon>
                                    </a>
                                </button>
                                <button
                                    class="bg-card text-foreground border border-border px-8 py-4 rounded-full text-base font-semibold shadow-sm hover:bg-muted transition-all duration-300 w-full sm:w-auto text-center flex items-center justify-center gap-2">
                                    <a href="https://youtube.com" target="_blank" class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:play-circle" class="text-lg"></iconify-icon>
                                        See How It Works
                                    </a>
                                </button>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-border w-full">
                                <div class="flex -space-x-3">
                                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                                        class="w-10 h-10 rounded-full border-2 border-background shadow-sm">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"
                                        class="w-10 h-10 rounded-full border-2 border-background shadow-sm">
                                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="User"
                                        class="w-10 h-10 rounded-full border-2 border-background shadow-sm">
                                    <div
                                        class="w-10 h-10 rounded-full border-2 border-background bg-muted flex items-center justify-center text-xs font-bold text-muted-foreground shadow-sm">
                                        +2k</div>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    <span class="font-bold text-foreground">2,000+</span> users improving their health
                                    daily.
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] animate-[slideInRight_0.8s_ease-out]">
                            <img src="https://uxmagic.blob.core.windows.net/public/agent-images/hero-uplyfe-1777293298522-g8cnp0esh0g.png"
                                alt="Uplyfe Hero"
                                class="w-full h-full object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-700 ease-in-out">

                            <!-- Floating UI Elements -->
                            <div
                                class="absolute top-1/4 -left-4 sm:left-4 bg-card p-4 rounded-2xl shadow-xl border border-border flex items-center gap-4 animate-[bounce_4s_infinite]">
                                <div
                                    class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-primary-foreground">
                                    <iconify-icon icon="lucide:activity" class="text-2xl"></iconify-icon>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground font-medium">Health Score</p>
                                    <p class="text-lg font-bold text-foreground">94/100</p>
                                </div>
                            </div>

                            <div
                                class="absolute bottom-1/4 -right-4 sm:right-4 bg-card p-4 rounded-2xl shadow-xl border border-border flex items-center gap-4 animate-[bounce_5s_infinite_reverse]">
                                <div
                                    class="w-12 h-12 rounded-full bg-tertiary/20 flex items-center justify-center text-tertiary">
                                    <iconify-icon icon="lucide:utensils" class="text-2xl"></iconify-icon>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground font-medium">Daily Calories</p>
                                    <p class="text-lg font-bold text-foreground">2,150 kcal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="w-full py-16 bg-card border-y border-border">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center max-w-2xl mx-auto mb-16">
                            <h2 class="text-3xl font-heading font-bold text-foreground mb-4">Intelligent Health
                                Management
                            </h2>
                            <p class="text-muted-foreground">Everything you need to optimize your lifestyle, powered by
                                advanced medical AI analysis.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Feature 1 -->
                            <div
                                class="bg-background p-8 rounded-[2rem] border border-border shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary-foreground mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors duration-300">
                                    <iconify-icon icon="lucide:file-text" class="text-2xl"></iconify-icon>
                                </div>
                                <h3 class="text-xl font-bold text-foreground mb-3 font-heading">Smart Checkup Analysis
                                </h3>
                                <p class="text-muted-foreground leading-relaxed">Upload your blood work and medical
                                    reports.
                                    Our AI translates complex medical jargon into actionable, easy-to-understand
                                    insights.
                                </p>
                            </div>

                            <!-- Feature 2 -->
                            <div
                                class="bg-background p-8 rounded-[2rem] border border-border shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary-foreground mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors duration-300">
                                    <iconify-icon icon="lucide:apple" class="text-2xl"></iconify-icon>
                                </div>
                                <h3 class="text-xl font-bold text-foreground mb-3 font-heading">Personalized Nutrition
                                </h3>
                                <p class="text-muted-foreground leading-relaxed">Get daily meal plans and recipes
                                    specifically tailored to your health markers, allergies, and personal taste
                                    preferences.
                                </p>
                            </div>

                            <!-- Feature 3 -->
                            <div
                                class="bg-background p-8 rounded-[2rem] border border-border shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary-foreground mb-6 group-hover:bg-primary group-hover:text-primary-foreground transition-colors duration-300">
                                    <iconify-icon icon="lucide:dumbbell" class="text-2xl"></iconify-icon>
                                </div>
                                <h3 class="text-xl font-bold text-foreground mb-3 font-heading">Adaptive Workouts</h3>
                                <p class="text-muted-foreground leading-relaxed">Receive exercise routines that adapt to
                                    your fitness level and medical constraints, ensuring safe and effective progress.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script>
        function toggleDashboardMenu() {
            const sidebar = document.getElementById('dashboard-sidebar');
            const main = document.getElementById('dashboard-main');
            const mobileMenu = document.getElementById('mobile-dashboard-menu');
            const isDesktop = window.matchMedia('(min-width: 768px)').matches;

            if (isDesktop) {
                if (sidebar) {
                    const isHidden = sidebar.classList.contains('hidden');
                    sidebar.classList.toggle('hidden');

                    if (isHidden) {
                        sidebar.classList.add('md:flex');
                        if (main) main.classList.add('md:ml-72');
                    } else {
                        sidebar.classList.remove('md:flex');
                        if (main) main.classList.remove('md:ml-72');
                    }
                }

                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            } else {
                if (mobileMenu) mobileMenu.classList.toggle('hidden');
            }
        }

        function toggleMenu() {
            const mobileMenu = document.getElementById('mobile-dashboard-menu');
            if (mobileMenu) mobileMenu.classList.toggle('hidden');
        }
    </script>
</body>

</html>