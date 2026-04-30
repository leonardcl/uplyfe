<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Uplyfe - Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:wght@100..900&family=Fira+Code:wght@300..700&family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
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
        <aside class="hidden md:flex w-64 lg:w-72 bg-card border-r border-border flex-shrink-0 flex-col transition-all duration-300">
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
        </aside>
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
            <div class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col gap-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <p class="text-sm text-muted-foreground uppercase tracking-[0.3em]">Account</p>
                    <h1 class="text-3xl sm:text-4xl font-heading font-bold mt-2">My Profile</h1>
                    <p class="mt-3 text-sm text-muted-foreground max-w-2xl">Manage your personal details, health goals, preferences, and security settings from one place.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm hover:shadow-md transition">Edit Profile</button>
                    <button class="inline-flex items-center gap-2 rounded-full border border-border bg-card px-5 py-3 text-sm font-medium text-foreground hover:bg-muted transition">Share Report</button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
                <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile avatar" class="w-24 h-24 rounded-3xl border border-border shadow-sm object-cover">
                            <div>
                                <h2 class="text-2xl font-heading font-bold">Sarah Jenkins</h2>
                                <p class="text-sm text-muted-foreground mt-1">Member since January 2025</p>
                            </div>
                        </div>
                        <div class="bg-primary/10 text-primary-foreground rounded-3xl px-4 py-3 text-sm font-semibold inline-flex items-center gap-2">
                            <iconify-icon icon="lucide:check-circle" class="text-lg"></iconify-icon>
                            Premium Plan
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-3xl border border-border bg-background p-5">
                            <p class="text-sm text-muted-foreground">Health Goal</p>
                            <p class="mt-2 font-semibold">Improve Vitamin D & manage cholesterol</p>
                        </div>
                        <div class="rounded-3xl border border-border bg-background p-5">
                            <p class="text-sm text-muted-foreground">Weekly Progress</p>
                            <p class="mt-2 font-semibold">84% target completion</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-3xl border border-border bg-background p-6 space-y-3">
                            <h3 class="text-lg font-semibold">Personal Information</h3>
                            <div class="grid gap-3 text-sm text-muted-foreground">
                                <div class="flex justify-between"><span>Name</span><span class="text-foreground font-medium">Sarah Jenkins</span></div>
                                <div class="flex justify-between"><span>Email</span><span class="text-foreground font-medium">sarah@uplyfe.com</span></div>
                                <div class="flex justify-between"><span>Phone</span><span class="text-foreground font-medium">+1 555 123 4567</span></div>
                                <div class="flex justify-between"><span>Location</span><span class="text-foreground font-medium">Austin, TX</span></div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-border bg-background p-6 space-y-3">
                            <h3 class="text-lg font-semibold">Health Summary</h3>
                            <div class="grid gap-3 text-sm text-muted-foreground">
                                <div class="flex justify-between"><span>Last Checkup</span><span class="text-foreground font-medium">Apr 24, 2026</span></div>
                                <div class="flex justify-between"><span>Allergies</span><span class="text-foreground font-medium">Gluten, Dairy</span></div>
                                <div class="flex justify-between"><span>Preferred Diet</span><span class="text-foreground font-medium">Balanced</span></div>
                                <div class="flex justify-between"><span>Weekly Calories</span><span class="text-foreground font-medium">2,150 kcal</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Security</h3>
                            <button class="text-sm text-primary font-semibold hover:text-tertiary transition">Manage</button>
                        </div>
                        <div class="space-y-4 text-sm text-muted-foreground">
                            <div class="rounded-3xl border border-border bg-background p-4">
                                <p class="font-semibold text-foreground mb-1">Password</p>
                                <p>Last changed 8 weeks ago</p>
                            </div>
                            <div class="rounded-3xl border border-border bg-background p-4">
                                <p class="font-semibold text-foreground mb-1">Two-factor Authentication</p>
                                <p>Disabled</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Balanced</span>
                            <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">High Protein</span>
                            <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Low Glycemic</span>
                            <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Gluten Free</span>
                        </div>
                    </section>

                    <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <p class="text-sm text-muted-foreground">Need help with your account or health plan? Our team is ready to assist.</p>
                        <a href="mailto:support@uplyfe.com" class="mt-4 inline-flex items-center rounded-full bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground shadow-sm hover:shadow-md transition">Contact Support</a>
                    </section>
                </aside>
            </div>
        </div>
    </div>
            </div>
        </main>
    </div>
</body>

</html>
