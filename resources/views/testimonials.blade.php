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
                            class="text-sm font-medium text-primary font-semibold border-b-2 border-primary pb-0.5 transition-colors">Testimonials</a>
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
                        <h2 class="text-3xl md:text-4xl font-heading font-bold mb-4">What Our Users Say</h2>
                        <p class="text-muted-foreground text-lg max-w-2xl mx-auto">
                            Join thousands of satisfied users who have transformed their health with Uplyfe.
                        </p>
                    </section>

                    <!-- Stats -->
                    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-card rounded-2xl p-6 border border-border text-center">
                            <div class="text-3xl font-bold text-primary mb-1">10K+</div>
                            <div class="text-sm text-muted-foreground">Active Users</div>
                        </div>
                        <div class="bg-card rounded-2xl p-6 border border-border text-center">
                            <div class="text-3xl font-bold text-primary mb-1">4.9</div>
                            <div class="text-sm text-muted-foreground">App Rating</div>
                        </div>
                        <div class="bg-card rounded-2xl p-6 border border-border text-center">
                            <div class="text-3xl font-bold text-primary mb-1">50K+</div>
                            <div class="text-sm text-muted-foreground">Reports Analyzed</div>
                        </div>
                        <div class="bg-card rounded-2xl p-6 border border-border text-center">
                            <div class="text-3xl font-bold text-primary mb-1">98%</div>
                            <div class="text-sm text-muted-foreground">Satisfaction</div>
                        </div>
                    </section>

                    <!-- Testimonials Grid -->
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Testimonial 1 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "Uplyfe has completely changed how I approach my health. The AI analysis of my blood
                                work revealed issues I never knew about. Now I have a clear plan to improve!"
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">Emily Richardson</p>
                                    <p class="text-xs text-muted-foreground">Marketing Manager</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 2 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "The personalized nutrition plans are amazing! I've lost 15 pounds in 3 months and feel
                                more energetic than ever. The recipes are delicious and easy to prepare."
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">Michael Chen</p>
                                    <p class="text-xs text-muted-foreground">Software Engineer</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 3 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "As someone with chronic health issues, having the AI assistant available 24/7 has been
                                a game-changer. It answers my questions and helps me stay on track."
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">Sarah Jenkins</p>
                                    <p class="text-xs text-muted-foreground">Teacher</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 4 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "The exercise routines are tailored perfectly to my fitness level. I've built muscle and
                                improved my stamina without any injuries. Highly recommend Uplyfe!"
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">David Thompson</p>
                                    <p class="text-xs text-muted-foreground">Financial Analyst</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 5 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "I was skeptical at first, but the health score tracking showed real improvements over
                                time. It's motivating to see my progress visually. Great app!"
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/women/28.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">Jessica Martinez</p>
                                    <p class="text-xs text-muted-foreground">Nurse</p>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 6 -->
                        <div class="bg-card rounded-2xl p-6 border border-border shadow-sm">
                            <div class="flex items-center gap-1 mb-4">
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                                <iconify-icon icon="lucide:star" class="text-yellow-500 fill-yellow-500"></iconify-icon>
                            </div>
                            <p class="text-sm text-muted-foreground mb-6">
                                "Privacy is important to me, and Uplyfe's secure encryption made me feel comfortable
                                uploading my medical records. Highly trustworthy platform!"
                            </p>
                            <div class="flex items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/men/52.jpg" alt="User"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-bold">Robert Williams</p>
                                    <p class="text-xs text-muted-foreground">Attorney</p>
                                </div>
                            </div>
                        </div>

                    </section>

                    <!-- CTA Section -->
                    <section
                        class="bg-gradient-to-r from-primary/20 to-tertiary/20 rounded-3xl p-8 md:p-12 text-center">
                        <h2 class="text-2xl md:text-3xl font-heading font-bold mb-4">Ready to Join Thousands of Happy
                            Users?</h2>
                        <p class="text-muted-foreground mb-6 max-w-xl mx-auto">
                            Start your health journey today and see the difference Uplyfe can make in your life.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/health-check"
                                class="bg-primary text-primary-foreground px-6 py-3 rounded-full font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300">
                                Get Started Free
                            </a>
                            <a href="/how-it-works"
                                class="bg-card text-foreground border border-border px-6 py-3 rounded-full font-semibold shadow-sm hover:bg-muted transition-all">
                                See How It Works
                            </a>
                        </div>
                    </section>

                </div>
            </div>
        </main>
    </div>
</body>

</html>