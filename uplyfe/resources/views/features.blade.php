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
        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">

            <!-- Topbar -->
            <header
                class="w-full bg-card/80 backdrop-blur-md border-b border-border sticky top-0 z-50 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-2 cursor-pointer group">
                        <div
                            class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground transform group-hover:scale-110 transition-transform duration-300 shadow-md">
                            <iconify-icon icon="lucide:leaf" class="text-xl"></iconify-icon>
                        </div>
                        <span class="text-xl font-heading font-bold tracking-tight text-foreground">Uplyfe</span>
                    </div>

                    <nav class="hidden md:flex items-center gap-8">
                        <a href="/"
                            class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">Dashboard</a>
                        <a href="/features"
                            class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">Features</a>
                        <a href="/how-it-works"
                            class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">How
                            it
                            Works</a>
                        <a href="/testimonials"
                            class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">Testimonials</a>
                    </nav>

                    <div class="flex items-center gap-4">
                        <a href="/login"
                            class="hidden sm:block text-sm font-medium text-foreground hover:text-primary transition-colors">Log
                            In</a>
                        <a href="/"
                            class="bg-primary text-primary-foreground px-5 py-2.5 rounded-full text-sm font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 flex items-center gap-2">
                            Get Started
                            <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                        </a>
                        <button onclick="toggleMenu()" class="md:hidden text-foreground p-2">
                            <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="max-w-5xl mx-auto space-y-8">

                    <!-- Hero Section -->
                    <section class="text-center py-8">
                        <h2 class="text-3xl md:text-4xl font-heading font-bold mb-4">Powerful Features for Your Health
                            Journey</h2>
                        <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
                            Discover all the tools Uplyfe provides to help you achieve your health goals with AI-powered
                            insights.
                        </p>
                    </section>

                    <!-- Features Grid -->
                    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Feature 1: Health Checkup -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 mb-4">
                                <iconify-icon icon="lucide:file-text" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">AI Health Analysis</h3>
                            <p class="text-sm text-muted-foreground">
                                Upload your medical reports and let our AI analyze them, translating complex medical
                                terms into easy-to-understand insights.
                            </p>
                        </div>

                        <!-- Feature 2: Nutrition & Recipes -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 mb-4">
                                <iconify-icon icon="lucide:apple" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Personalized Nutrition</h3>
                            <p class="text-sm text-muted-foreground">
                                Get AI-generated recipes tailored to your dietary preferences and health conditions.
                                Track calories and nutritional intake effortlessly.
                            </p>
                        </div>

                        <!-- Feature 3: Exercise Routines -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 mb-4">
                                <iconify-icon icon="lucide:dumbbell" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Custom Workouts</h3>
                            <p class="text-sm text-muted-foreground">
                                Receive personalized exercise routines based on your fitness level and health goals.
                                Track progress and stay motivated.
                            </p>
                        </div>

                        <!-- Feature 4: AI Assistant -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 mb-4">
                                <iconify-icon icon="lucide:bot" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">24/7 AI Companion</h3>
                            <p class="text-sm text-muted-foreground">
                                Get instant answers to your health questions anytime. Our AI assistant is available
                                round the clock to support your journey.
                            </p>
                        </div>

                        <!-- Feature 5: Health Score -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mb-4">
                                <iconify-icon icon="lucide:activity" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Health Score Tracking</h3>
                            <p class="text-sm text-muted-foreground">
                                Monitor your health score over time with detailed analytics. Understand what factors
                                impact your wellness the most.
                            </p>
                        </div>

                        <!-- Feature 6: Secure & Private -->
                        <div
                            class="bg-card rounded-2xl p-6 border border-border shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 mb-4">
                                <iconify-icon icon="lucide:shield-check" class="text-2xl"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Secure & Private</h3>
                            <p class="text-sm text-muted-foreground">
                                Your health data is encrypted and protected. We prioritize your privacy and never share
                                your information.
                            </p>
                        </div>

                    </section>

                    <!-- CTA Section -->
                    <section
                        class="bg-gradient-to-r from-primary/20 to-tertiary/20 rounded-3xl p-8 md:p-12 text-center">
                        <h2 class="text-2xl md:text-3xl font-heading font-bold mb-4">Ready to Transform Your Health?
                        </h2>
                        <p class="text-muted-foreground mb-6 max-w-xl mx-auto">
                            Join thousands of users who are already improving their lives with Uplyfe's AI-powered
                            health platform.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/healthcheck"
                                class="bg-primary text-primary-foreground px-6 py-3 rounded-full font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
                                Get Started Now
                            </a>
                            <a href="/chat"
                                class="bg-card text-foreground border border-border px-6 py-3 rounded-full font-semibold shadow-sm hover:bg-muted transition-all">
                                Try AI Assistant
                            </a>
                        </div>
                    </section>

                </div>
            </div>
        </main>
    </div>
</body>

</html>