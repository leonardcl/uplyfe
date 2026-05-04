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
        </aside>
        <!-- Mobile Sidebar Backdrop -->
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm hidden md:hidden"></div>
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-background">
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-button" class="md:hidden text-foreground p-1 rounded-md hover:bg-muted">
                        <iconify-icon icon="lucide:menu" class="text-2xl"></iconify-icon>
                    </button>
                    <h1 class="text-xl font-heading font-bold">Account</h1>
                </div>
            </header>
            <div class="flex-1 overflow-y-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col gap-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-heading font-bold mt-2">My Profile</h1>
                    <p class="mt-3 text-sm text-muted-foreground max-w-2xl">Manage your personal details, health goals, preferences, and security settings from one place.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button id="edit-profile-btn" type="button" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm hover:shadow-md transition">Edit Profile</button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
                <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile avatar" class="w-24 h-24 rounded-3xl border border-border shadow-sm object-cover">
                            <div>
                                <h2 id="profile-display-name" class="text-2xl font-heading font-bold">Sarah Jenkins</h2>
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
                                <div class="flex justify-between"><span>Name</span><span id="profile-card-name" class="text-foreground font-medium">Sarah Jenkins</span></div>
                                <div class="flex justify-between"><span>Email</span><span id="profile-card-email" class="text-foreground font-medium">sarah@uplyfe.com</span></div>
                                <div class="flex justify-between"><span>Phone</span><span id="profile-card-phone" class="text-foreground font-medium">+1 555 123 4567</span></div>
                                <div class="flex justify-between"><span>Location</span><span class="text-foreground font-medium">Austin, TX</span></div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-border bg-background p-6 space-y-3">
                            <h3 class="text-lg font-semibold">Health Summary</h3>
                            <div class="grid gap-3 text-sm text-muted-foreground">
                                <div class="flex justify-between"><span>Last Checkup</span><span class="text-foreground font-medium">Apr 24, 2026</span></div>
                                <div class="flex justify-between"><span>Allergies</span><span class="text-foreground font-medium">Gluten, Dairy</span></div>
                                <div class="flex justify-between"><span>Preferred Diet</span><span id="profile-card-diet" class="text-foreground font-medium">Balanced</span></div>
                                <div class="flex justify-between"><span>Weekly Calories</span><span class="text-foreground font-medium">2,150 kcal</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">Security</h3>
                            <button id="security-manage-btn" type="button" class="text-sm text-primary font-semibold hover:text-tertiary transition">Manage</button>
                        </div>
                        <div class="space-y-4 text-sm text-muted-foreground">
                            <div class="rounded-3xl border border-border bg-background p-4">
                                <p class="font-semibold text-foreground mb-1">Password</p>
                                <p>Last changed 8 weeks ago</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                        <div id="profile-preferences-list" class="flex flex-wrap gap-2">
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

    <!-- Edit Profile Modal -->
        <div id="edit-profile-modal" class="fixed inset-0 z-50 hidden" style="display:none;">
        <div id="edit-profile-backdrop" class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm sm:max-w-2xl md:max-w-4xl mx-2 sm:mx-4 h-[95vh] sm:h-[90vh] flex flex-col">
            <div class="bg-card rounded-3xl border border-border shadow-xl flex-1 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-border flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-heading font-bold">Edit Profile</h3>
                        <p class="text-sm text-muted-foreground">Update your personal information and preferences.</p>
                    </div>
                    <button id="edit-profile-close-btn" type="button"
                        class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <div class="space-y-6">
                        <!-- Profile Picture -->
                        <div class="flex flex-col items-center space-y-4">
                            <div class="relative">
                                <img id="profile-picture-preview" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile avatar" class="w-24 h-24 rounded-3xl border border-border shadow-sm object-cover">
                                <button id="profile-picture-change-btn" type="button" class="absolute bottom-0 right-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-primary-foreground shadow-sm hover:bg-primary/80 transition-colors">
                                    <iconify-icon icon="lucide:camera" class="text-sm"></iconify-icon>
                                </button>
                            </div>
                            <input type="file" id="profile-picture-input" accept="image/*" class="hidden">
                            <p class="text-sm text-muted-foreground">Click the camera icon to change your profile picture</p>
                        </div>

                        <!-- Personal Information -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">First Name</label>
                                <input id="profile-first-name" type="text" value="Sarah" placeholder="Enter your first name"
                                    class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Last Name</label>
                                <input id="profile-last-name" type="text" value="Jenkins" placeholder="Enter your last name"
                                    class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Email Address</label>
                            <input id="profile-email" type="email" value="sarah.jenkins@email.com" placeholder="Enter your email"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Phone Number</label>
                            <input id="profile-phone" type="tel" value="+1 (555) 123-4567" placeholder="Enter your phone number"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <!-- Health Information -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Health Information</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Date of Birth</label>
                                    <input id="profile-dob" type="date" value="1995-06-15"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Gender</label>
                                    <select id="profile-gender"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                                        <option value="female" selected>Female</option>
                                        <option value="male">Male</option>
                                        <option value="prefer-not-to-say">Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Height (cm)</label>
                                    <input id="profile-height" type="number" value="165" placeholder="165"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Weight (kg)</label>
                                    <input id="profile-weight" type="number" step="0.1" value="68.5" placeholder="68.5"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                            </div>
                        </div>

                        <!-- Preferences -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Dietary Preferences</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="balanced" checked class="text-primary">
                                    <span class="text-sm">Balanced</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="high-protein" checked class="text-primary">
                                    <span class="text-sm">High Protein</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="low-glycemic" checked class="text-primary">
                                    <span class="text-sm">Low Glycemic</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-primary bg-primary/10 rounded-xl cursor-pointer hover:bg-primary/20 transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="gluten-free" checked class="text-primary">
                                    <span class="text-sm">Gluten Free</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="vegan" class="text-primary">
                                    <span class="text-sm">Vegan</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="keto" class="text-primary">
                                    <span class="text-sm">Keto</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="paleo" class="text-primary">
                                    <span class="text-sm">Paleo</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="mediterranean" class="text-primary">
                                    <span class="text-sm">Mediterranean</span>
                                </label>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Notification Preferences</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" checked class="text-primary">
                                    <span class="text-sm">Email notifications for health reports</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" checked class="text-primary">
                                    <span class="text-sm">Weekly progress summaries</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" class="text-primary">
                                    <span class="text-sm">Medication reminders</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" checked class="text-primary">
                                    <span class="text-sm">Appointment reminders</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 p-6 border-t border-border">
                    <button id="edit-profile-cancel-btn" type="button"
                        class="flex-1 bg-muted text-muted-foreground px-4 py-3 rounded-xl text-sm font-semibold hover:bg-muted/80 transition-colors">
                        Cancel
                    </button>
                    <button id="edit-profile-save-btn" type="button" onclick="saveProfileChanges()"
                        class="flex-1 bg-primary text-primary-foreground px-4 py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings Modal -->
    <div id="security-modal" class="fixed inset-0 z-50 hidden" style="display:none;">
        <div id="security-modal-backdrop" class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-sm sm:max-w-lg mx-4">
            <div class="bg-card rounded-3xl border border-border shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-heading font-bold">Security Settings</h3>
                        <p class="text-sm text-muted-foreground">Manage your account security and authentication.</p>
                    </div>
                    <button id="security-close-btn" type="button"
                        class="text-muted-foreground p-2 rounded-full hover:bg-muted transition-colors">
                        <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Change Password -->
                    <div class="space-y-4">
                        <h4 class="font-semibold">Change Password</h4>
                        <div>
                            <label class="block text-sm font-medium mb-2">Current Password</label>
                            <input id="security-current-password" type="password" placeholder="Enter current password"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">New Password</label>
                            <input id="security-new-password" type="password" placeholder="Enter new password"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Confirm New Password</label>
                            <input id="security-confirm-password" type="password" placeholder="Confirm new password"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <p id="security-password-feedback" class="hidden rounded-xl border px-4 py-3 text-sm"></p>
                        <button id="security-update-password-btn" type="button" class="w-full bg-primary text-primary-foreground py-3 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                            Update Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

        // Edit Profile Modal Functions
        function openEditProfileModal() {
            const editModal = document.getElementById('edit-profile-modal');
            editModal.classList.remove('hidden');
            editModal.style.display = 'block';
            // Initialize form styling for checkboxes
            initializeProfileFormStyling();
        }

        function closeEditProfileModal() {
            const editModal = document.getElementById('edit-profile-modal');
            editModal.classList.add('hidden');
            editModal.style.display = 'none';
        }

        function openSecurityModal() {
            const securityModal = document.getElementById('security-modal');
            securityModal.classList.remove('hidden');
            securityModal.style.display = 'block';
        }

        function closeSecurityModal() {
            const securityModal = document.getElementById('security-modal');
            securityModal.classList.add('hidden');
            securityModal.style.display = 'none';
        }

        function showSecurityPasswordFeedback(message, type) {
            const feedback = document.getElementById('security-password-feedback');
            if (!feedback) return;

            const isSuccess = type === 'success';
            feedback.textContent = message;
            feedback.classList.remove('hidden', 'border-destructive', 'bg-destructive/10', 'text-destructive', 'border-primary', 'bg-primary/10', 'text-foreground');
            if (isSuccess) {
                feedback.classList.add('border-primary', 'bg-primary/10', 'text-foreground');
            } else {
                feedback.classList.add('border-destructive', 'bg-destructive/10', 'text-destructive');
            }
        }

        function clearSecurityPasswordFeedback() {
            const feedback = document.getElementById('security-password-feedback');
            if (!feedback) return;

            feedback.textContent = '';
            feedback.classList.add('hidden');
            feedback.classList.remove('border-destructive', 'bg-destructive/10', 'text-destructive', 'border-primary', 'bg-primary/10', 'text-foreground');
        }

        function updatePassword() {
            const currentPasswordInput = document.getElementById('security-current-password');
            const newPasswordInput = document.getElementById('security-new-password');
            const confirmPasswordInput = document.getElementById('security-confirm-password');
            const updateButton = document.getElementById('security-update-password-btn');

            if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput) {
                return;
            }

            clearSecurityPasswordFeedback();

            const currentPassword = currentPasswordInput.value.trim();
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            if (!currentPassword || !newPassword || !confirmPassword) {
                showSecurityPasswordFeedback('Please fill in all password fields.', 'error');
                return;
            }

            if (newPassword.length < 8) {
                showSecurityPasswordFeedback('New password must be at least 8 characters long.', 'error');
                return;
            }

            if (newPassword === currentPassword) {
                showSecurityPasswordFeedback('New password must be different from current password.', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showSecurityPasswordFeedback('Password confirmation does not match.', 'error');
                return;
            }

            if (updateButton) {
                updateButton.disabled = true;
                updateButton.classList.add('opacity-60', 'cursor-not-allowed');
            }

            setTimeout(() => {
                showSecurityPasswordFeedback('Password updated successfully.', 'success');
                currentPasswordInput.value = '';
                newPasswordInput.value = '';
                confirmPasswordInput.value = '';

                if (updateButton) {
                    updateButton.disabled = false;
                    updateButton.classList.remove('opacity-60', 'cursor-not-allowed');
                }
            }, 500);
        }

        function saveProfileChanges() {
            // Collect form data
            const profileData = {
                firstName: document.getElementById('profile-first-name').value,
                lastName: document.getElementById('profile-last-name').value,
                email: document.getElementById('profile-email').value,
                phone: document.getElementById('profile-phone').value,
                dob: document.getElementById('profile-dob').value,
                gender: document.getElementById('profile-gender').value,
                height: document.getElementById('profile-height').value,
                weight: document.getElementById('profile-weight').value,
                dietPreferences: Array.from(document.querySelectorAll('input[name="diet-preferences"]:checked')).map(cb => cb.value)
            };

            const formatPreferenceLabel = (value) => value
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');

            const fullName = `${profileData.firstName} ${profileData.lastName}`.trim();
            const updateText = (id, text) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = text;
                }
            };
            const updatePreferenceChips = (preferences) => {
                const container = document.getElementById('profile-preferences-list');
                if (!container) return;

                const safePreferences = preferences.length ? preferences : ['balanced'];
                container.innerHTML = safePreferences
                    .map((preference) => `<span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">${formatPreferenceLabel(preference)}</span>`)
                    .join('');
            };

            updateText('profile-display-name', fullName || 'Your Name');
            updateText('profile-card-name', fullName || 'Your Name');
            updateText('profile-card-email', profileData.email || 'Email not set');
            updateText('profile-card-phone', profileData.phone || 'Phone not set');
            updateText('profile-card-diet', profileData.dietPreferences.length ? profileData.dietPreferences.map(formatPreferenceLabel).join(', ') : 'Balanced');
            updatePreferenceChips(profileData.dietPreferences);

            console.log('Profile updated:', profileData);
            alert('Profile updated successfully!');
            closeEditProfileModal();
        }

        // Profile Picture Functions
        function changeProfilePicture() {
            document.getElementById('profile-picture-input').click();
        }

        function handleProfilePictureChange(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }

                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Please select an image smaller than 5MB.');
                    return;
                }

                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-picture-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);

                console.log('Profile picture selected:', file.name);
            }
        }

        // Expose modal functions globally for inline onclick handlers
        window.openEditProfileModal = openEditProfileModal;
        window.closeEditProfileModal = closeEditProfileModal;
        window.openSecurityModal = openSecurityModal;
        window.closeSecurityModal = closeSecurityModal;
        window.saveProfileChanges = saveProfileChanges;
        window.changeProfilePicture = changeProfilePicture;
        window.handleProfilePictureChange = handleProfilePictureChange;
        window.updatePassword = updatePassword;

        // Initialize form styling for profile modal
        function initializeProfileFormStyling() {
            // Handle checkbox groups
            const checkboxGroups = ['diet-preferences'];
            checkboxGroups.forEach(groupName => {
                const checkboxes = document.querySelectorAll(`input[name="${groupName}"]`);
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            this.closest('label').classList.remove('border-border', 'bg-background');
                            this.closest('label').classList.add('border-primary', 'bg-primary/10');
                        } else {
                            this.closest('label').classList.remove('border-primary', 'bg-primary/10');
                            this.closest('label').classList.add('border-border', 'bg-background');
                        }
                    });
                });
            });

            // Apply initial styling
            setTimeout(() => {
                document.querySelectorAll('input[name="diet-preferences"]:checked').forEach(cb => {
                    cb.closest('label').classList.remove('border-border', 'bg-background');
                    cb.closest('label').classList.add('border-primary', 'bg-primary/10');
                });
            }, 10);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar
            document.getElementById('mobile-menu-button')?.addEventListener('click', () => toggleSidebar());
            document.getElementById('mobile-sidebar-backdrop')?.addEventListener('click', () => toggleSidebar(false));

            // Modal buttons
            document.getElementById('edit-profile-btn')?.addEventListener('click', openEditProfileModal);
            document.getElementById('security-manage-btn')?.addEventListener('click', openSecurityModal);
            document.getElementById('edit-profile-backdrop')?.addEventListener('click', closeEditProfileModal);
            document.getElementById('edit-profile-close-btn')?.addEventListener('click', closeEditProfileModal);
            document.getElementById('edit-profile-cancel-btn')?.addEventListener('click', closeEditProfileModal);
            document.getElementById('edit-profile-save-btn')?.addEventListener('click', saveProfileChanges);
            document.getElementById('security-modal-backdrop')?.addEventListener('click', closeSecurityModal);
            document.getElementById('security-close-btn')?.addEventListener('click', closeSecurityModal);
            document.getElementById('security-update-password-btn')?.addEventListener('click', updatePassword);

            // Profile picture input
            document.getElementById('profile-picture-change-btn')?.addEventListener('click', changeProfilePicture);
            document.getElementById('profile-picture-input')?.addEventListener('change', handleProfilePictureChange);
        });
    </script>
</body>

</html>
