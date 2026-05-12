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
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
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
                <div class="flex items-center gap-3">
                    <button
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:search" class="text-xl"></iconify-icon>
                    </button>
                    <button
                        class="p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-full transition-colors">
                        <iconify-icon icon="lucide:more-vertical" class="text-xl"></iconify-icon>
                    </button>
                </div>
            </header>

            <!-- Chat Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 sm:p-6 flex flex-col gap-6 scroll-smooth">

                <!-- Date Divider -->
                <div class="flex justify-center">
                    <span class="px-3 py-1 rounded-full bg-muted text-xs font-medium text-muted-foreground">Today</span>
                </div>

                <!-- AI Message -->
                <div class="flex gap-4 max-w-3xl animate-[fadeIn_0.3s_ease-out]">
                    <div
                        class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary flex-shrink-0 mt-1">
                        <iconify-icon icon="lucide:bot" class="text-sm"></iconify-icon>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-muted-foreground ml-1">Uplyfe AI</span>
                        <div
                            class="bg-card border border-border rounded-2xl rounded-tl-none p-4 shadow-sm text-sm leading-relaxed">
                            Hi Sarah! I've analyzed your latest checkup report. Your cholesterol is looking great, but I
                            noticed your Vitamin D is slightly low. How can I help you today?
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button
                                class="px-3 py-1.5 rounded-full border border-primary/30 bg-primary/5 text-primary text-xs font-semibold hover:bg-primary/10 transition-colors">Suggest
                                a Vitamin D rich recipe</button>
                            <button
                                class="px-3 py-1.5 rounded-full border border-border bg-background text-muted-foreground text-xs font-semibold hover:bg-muted transition-colors">Explain
                                my cholesterol levels</button>
                        </div>
                    </div>
                </div>

                <!-- User Message -->
                <div class="flex gap-4 max-w-3xl ml-auto flex-row-reverse animate-[fadeIn_0.3s_ease-out]">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User"
                        class="w-8 h-8 rounded-full border border-border flex-shrink-0 mt-1">
                    <div class="flex flex-col gap-1 items-end">
                        <span class="text-xs font-bold text-muted-foreground mr-1">You</span>
                        <div
                            class="bg-primary text-primary-foreground rounded-2xl rounded-tr-none p-4 shadow-sm text-sm leading-relaxed">
                            Can you give me a quick 15-minute workout I can do at home? I want something light.
                        </div>
                    </div>
                </div>

                <!-- AI Message (Typing Simulation) -->
                <div class="flex gap-4 max-w-3xl animate-[fadeIn_0.3s_ease-out]">
                    <div
                        class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary flex-shrink-0 mt-1">
                        <iconify-icon icon="lucide:bot" class="text-sm"></iconify-icon>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-muted-foreground ml-1">Uplyfe AI</span>
                        <div
                            class="bg-card border border-border rounded-2xl rounded-tl-none p-4 shadow-sm text-sm leading-relaxed">
                            <p class="mb-3">Absolutely! Based on your preference for light activity, here is a quick
                                15-minute mobility and stretching routine. It's gentle on the joints and perfect for
                                home.</p>

                            <div
                                class="bg-background rounded-xl border border-border p-3 flex items-center gap-3 hover:border-primary/50 cursor-pointer transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                                    <iconify-icon icon="lucide:play"></iconify-icon>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm">15-Min Light Mobility Flow</p>
                                    <p class="text-xs text-muted-foreground">No equipment needed</p>
                                </div>
                                <iconify-icon icon="lucide:chevron-right" class="text-muted-foreground"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spacer for input -->
                <div class="h-20"></div>
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
        document.getElementById('send-button').addEventListener('click', function() {
            const textarea = document.querySelector('textarea');
            const message = textarea.value.trim();
            if (message) {
                const chatMessages = document.getElementById('chat-messages');
                const userMessageHTML = `
                    <div class="flex gap-4 max-w-3xl ml-auto flex-row-reverse animate-[fadeIn_0.3s_ease-out]">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="User" class="w-8 h-8 rounded-full border border-border flex-shrink-0 mt-1">
                        <div class="flex flex-col gap-1 items-end">
                            <span class="text-xs font-bold text-muted-foreground mr-1">You</span>
                            <div class="bg-primary text-primary-foreground rounded-2xl rounded-tr-none p-4 shadow-sm text-sm leading-relaxed">
                                ${message.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', userMessageHTML);

                const botMessageHTML = `
                    <div class="flex gap-4 max-w-3xl animate-[fadeIn_0.3s_ease-out]">
                        <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary flex-shrink-0 mt-1">
                            <iconify-icon icon="lucide:bot" class="text-sm"></iconify-icon>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-muted-foreground ml-1">Uplyfe AI</span>
                            <div class="bg-card border border-border rounded-2xl rounded-tl-none p-4 shadow-sm text-sm leading-relaxed">
                                Hello, I am Uplyfe chatbot.
                            </div>
                        </div>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', botMessageHTML);

                textarea.value = '';
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
                chatMessages.scrollTop = chatMessages.scrollHeight;
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
    </script>
</body>

</html>