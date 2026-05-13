<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <div class="p-4 border-t border-border">
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="{{ $user->profile_photo ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="User"
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
                                <a href="/logout"
                                    id="log-out-btn"
                                    class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm hover:shadow-md transition">
                                    Log Out
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-6">
                            <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6 space-y-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $user->profile_photo ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="Profile avatar" class="w-24 h-24 rounded-3xl border border-border shadow-sm object-cover">
                                        <div>
                                            <h2 id="profile-display-name" class="text-2xl font-heading font-bold">{{ $user->first_name }} {{ $user->last_name }}</h2>
                                            <p class="text-sm text-muted-foreground mt-1">Member since {{ \Carbon\Carbon::parse($user->created_at)->format('F Y') }}</p>
                                        </div>
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
                                            <div class="flex justify-between"><span>Name</span><span id="profile-card-name" class="text-foreground font-medium">{{ $user->first_name }} {{ $user->last_name }}</span></div>
                                            <div class="flex justify-between"><span>Email</span><span id="profile-card-email" class="text-foreground font-medium">{{ $user->email ?? 'Email not set' }}</span></div>
                                            <div class="flex justify-between"><span>Phone</span><span id="profile-card-phone" class="text-foreground font-medium">{{ $user->phone_number ?? 'Phone not set' }}</span></div>
                                            <div class="flex justify-between"><span>Height</span><span id="profile-card-height" class="text-foreground font-medium">{{ $user->height ?? 'Height not set' }}</span></div>
                                            <div class="flex justify-between"><span>Weight</span><span id="profile-card-weight" class="text-foreground font-medium">{{ $user->weight ?? 'Weight not set' }}</span></div>
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

                                <div class="mt-4 space-y-2">
                                    <p class="mt-4 text-sm text-muted-foreground">Want to see your liked recipes?</p>
                                    <a href="/favorite-recipes" class="inline-flex items-center rounded-full bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground shadow-sm hover:shadow-md transition">Liked Recipes</a>
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
                                            <p id="password-changed-text">
                                                @if($user->password_changed_at)
                                                    Last changed {{ \Carbon\Carbon::parse($user->password_changed_at)->diffForHumans() }}
                                                @else
                                                    Password never changed
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </section>

                                <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                                    <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                                    @php
                                        $dietPrefs = $user->dietary_preferences ?? [];

                                        $formatPreferenceLabel = function ($value) {
                                            return collect(explode('-', $value))
                                                ->map(fn ($word) => ucfirst($word))
                                                ->join(' ');
                                        };
                                    @endphp
                                    <div id="profile-preferences-list" class="flex flex-wrap gap-2">
                                        @if(count($dietPrefs))
                                            @foreach($dietPrefs as $pref)
                                                <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">
                                                    {{ $formatPreferenceLabel($pref) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">
                                                No preferences set
                                            </span>
                                        @endif
                                        <!-- <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Balanced</span>
                                        <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">High Protein</span>
                                        <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Low Glycemic</span>
                                        <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">Gluten Free</span> -->
                                    </div>
                                </section>

                                <section class="bg-card rounded-[2rem] border border-border shadow-sm p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold">Foods you avoid</h3>
                                        <button id="open-edit-from-exclusions"
                                            class="text-xs text-primary font-semibold hover:underline">Edit</button>
                                    </div>
                                    <p class="text-xs text-muted-foreground mb-3">
                                        Recipe generation and the chat avoid these. Update via the chat
                                        (<em>"I can't eat fish"</em>) or by editing your profile.
                                    </p>
                                    @php
                                        $foodExclusionsView = $user->food_exclusions ?? [];
                                    @endphp
                                    <div id="profile-exclusions-view" class="flex flex-wrap gap-2">
                                        @if(count($foodExclusionsView))
                                            @foreach($foodExclusionsView as $f)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                                                    <iconify-icon icon="lucide:ban" class="text-xs"></iconify-icon>
                                                    {{ $f }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-muted-foreground">No food exclusions yet.</span>
                                        @endif
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
                                <img id="profile-picture-preview" src="{{ $user->profile_photo ?? 'https://randomuser.me/api/portraits/women/44.jpg' }}" alt="Profile avatar" class="w-24 h-24 rounded-3xl border border-border shadow-sm object-cover">
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
                                <input id="profile-first-name" type="text" value="{{ $user->first_name }}" placeholder="Enter your first name"
                                    class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Last Name</label>
                                <input id="profile-last-name" type="text" value="{{ $user->last_name }}" placeholder="Enter your last name"
                                    class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Email Address</label>
                            <input id="profile-email" type="email" value="{{ $user->email }}" placeholder="Enter your email"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Phone Number</label>
                            <input id="profile-phone" type="tel" value="{{ $user->phone_number }}" placeholder="Enter your phone number"
                                class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <!-- Health Information -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Health Information</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Date of Birth</label>
                                    <input id="profile-dob" type="date" value="{{ $user->date_of_birth }}"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Gender</label>
                                    <select id="profile-gender"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                                        <option value="female" @selected($user->gender === 'female')>Female</option>
                                        <option value="male" @selected($user->gender === 'male')>Male</option>
                                        <option value="prefer-not-to-say" @selected($user->gender === 'prefer-not-to-say')>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Height (cm)</label>
                                    <input id="profile-height" type="number" value="{{ $user->height }}" placeholder="165"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Weight (kg)</label>
                                    <input id="profile-weight" type="number" step="0.1" value="{{ $user->weight }}" placeholder="68.5"
                                        class="w-full bg-background border border-border rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary" />
                                </div>
                            </div>
                        </div>

                        <!-- Preferences -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Dietary Preferences</h4>
                            @php
                                $dietPrefs = $user->dietary_preferences ?? [];
                            @endphp
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="balanced" @checked(in_array('balanced', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Balanced</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="high-protein" @checked(in_array('high-protein', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">High Protein</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="low-glycemic" @checked(in_array('low-glycemic', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Low Glycemic</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="vegan" @checked(in_array('vegan', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Vegan</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="keto" @checked(in_array('keto', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Keto</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="paleo" @checked(in_array('paleo', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Paleo</span>
                                </label>
                                <label class="flex items-center gap-2 p-3 border border-border rounded-xl cursor-pointer hover:bg-muted transition-colors">
                                    <input type="checkbox" name="diet-preferences" value="mediterranean" @checked(in_array('mediterranean', $dietPrefs)) class="text-primary">
                                    <span class="text-sm">Mediterranean</span>
                                </label>
                            </div>
                        </div>

                        <!-- Food exclusions -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-1">Food Exclusions</h4>
                            <p class="text-xs text-muted-foreground mb-3">
                                Foods you don't eat. Recipe generation and the chat will avoid these.
                                You can also tell the chat — e.g. <em>"I can't eat fish"</em> — and they'll appear here.
                            </p>
                            @php
                                $foodExclusions = $user->food_exclusions ?? [];
                            @endphp
                            <div id="profile-exclusions-chips" class="flex flex-wrap gap-2 mb-3 min-h-[1.5rem]">
                                @foreach ($foodExclusions as $f)
                                    <span data-exclusion-chip="{{ $f }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-muted border border-border text-xs">
                                        {{ $f }}
                                        <button type="button" class="text-muted-foreground hover:text-destructive"
                                            data-remove-exclusion="{{ $f }}" aria-label="Remove {{ $f }}">
                                            <iconify-icon icon="lucide:x" class="text-sm"></iconify-icon>
                                        </button>
                                    </span>
                                @endforeach
                                <span id="profile-exclusions-empty" class="text-xs text-muted-foreground @if(!empty($foodExclusions)) hidden @endif">
                                    No exclusions yet.
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <input id="profile-exclusion-input" type="text" placeholder="e.g. fish, peanuts, dairy"
                                    class="flex-1 bg-background border border-border rounded-xl px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary" />
                                <button id="profile-exclusion-add" type="button"
                                    class="px-4 py-2 bg-primary text-primary-foreground rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors">
                                    Add
                                </button>
                            </div>
                            <input type="hidden" id="profile-exclusions-json" value="{{ json_encode(array_values($foodExclusions)) }}" />
                        </div>

                        <!-- Notifications -->
                        <div class="border-t border-border pt-6">
                            <h4 class="text-lg font-semibold mb-4">Notification Preferences</h4>
                            @php
                                $notifPrefs = $user->notification_preferences ?? [];
                            @endphp
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="notification-preferences" value="health-reports" @checked(in_array('health-reports', $notifPrefs)) class="text-primary">
                                    <span class="text-sm">Email notifications for health reports</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="notification-preferences" value="weekly-progress" @checked(in_array('weekly-progress', $notifPrefs)) class="text-primary">
                                    <span class="text-sm">Weekly progress summaries</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="notification-preferences" value="medication-reminders" @checked(in_array('medication-reminders', $notifPrefs)) class="text-primary">
                                    <span class="text-sm">Medication reminders</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="notification-preferences" value="appointment-reminders" @checked(in_array('appointment-reminders', $notifPrefs)) class="text-primary">
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
                    <button id="edit-profile-save-btn" type="button"
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

        function resetEditProfileModal() {
            document.getElementById('profile-first-name').value = currentUserData.first_name ?? '';
            document.getElementById('profile-last-name').value = currentUserData.last_name ?? '';
            document.getElementById('profile-email').value = currentUserData.email ?? '';
            document.getElementById('profile-phone').value = currentUserData.phone_number ?? '';
            document.getElementById('profile-dob').value = currentUserData.date_of_birth ?? '';
            document.getElementById('profile-gender').value = currentUserData.gender ?? '';
            document.getElementById('profile-height').value = currentUserData.height ?? '';
            document.getElementById('profile-weight').value = currentUserData.weight ?? '';

            const dietaryPrefs = currentUserData.dietary_preferences || [];
            const notifPrefs = currentUserData.notification_preferences || [];

            document.querySelectorAll('input[name="diet-preferences"]').forEach(cb => {
                cb.checked = dietaryPrefs.includes(cb.value);

                cb.closest('label').classList.toggle('border-primary', cb.checked);
                cb.closest('label').classList.toggle('bg-primary/10', cb.checked);
                cb.closest('label').classList.toggle('border-border', !cb.checked);
                cb.closest('label').classList.toggle('bg-background', !cb.checked);
            });

            document.querySelectorAll('input[name="notification-preferences"]').forEach(cb => {
                cb.checked = notifPrefs.includes(cb.value);
            });
        }

        // Edit Profile Modal Functions
        function openEditProfileModal() {
            resetEditProfileModal();

            const editModal = document.getElementById('edit-profile-modal');
            editModal.classList.remove('hidden');
            editModal.style.display = 'block';

            const scrollContainer = editModal.querySelector('.overflow-y-auto');

            if (scrollContainer) {
                scrollContainer.scrollTop = 0;
            }

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
            document.getElementById('security-current-password').value = '';
            document.getElementById('security-new-password').value = '';
            document.getElementById('security-confirm-password').value = '';

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

        async function updatePassword() {
            const currentPasswordInput = document.getElementById('security-current-password');
            const newPasswordInput = document.getElementById('security-new-password');
            const confirmPasswordInput = document.getElementById('security-confirm-password');
            const updateButton = document.getElementById('security-update-password-btn');

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

            updateButton.disabled = true;
            updateButton.classList.add('opacity-60', 'cursor-not-allowed');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch('/profile/password', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                }),
            });

            const result = await response.json();

            if (!response.ok) {
                showSecurityPasswordFeedback(result.message || 'Failed to update password.', 'error');
                updateButton.disabled = false;
                updateButton.classList.remove('opacity-60', 'cursor-not-allowed');
                return;
            }

            showSecurityPasswordFeedback(result.message || 'Password updated successfully.', 'success');
            document.getElementById('password-changed-text').textContent = 'Last changed just now';

            currentPasswordInput.value = '';
            newPasswordInput.value = '';
            confirmPasswordInput.value = '';
            updateButton.disabled = false;
            updateButton.classList.remove('opacity-60', 'cursor-not-allowed');            
        }

        let currentUserData = @json($user);

        async function saveProfileChanges() {
            const userId = @json($user->id);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const dob = document.getElementById('profile-dob').value;
            let age = null;

            if (dob) {
                const birthDate = new Date(dob);
                const today = new Date();

                age = today.getFullYear() - birthDate.getFullYear();

                const monthDiff = today.getMonth() - birthDate.getMonth();
                const dayDiff = today.getDate() - birthDate.getDate();

                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age--;
                }
            }

            const formData = new FormData();

            formData.append('first_name', document.getElementById('profile-first-name').value);
            formData.append('last_name', document.getElementById('profile-last-name').value);
            formData.append('email', document.getElementById('profile-email').value);
            formData.append('phone_number', document.getElementById('profile-phone').value);
            formData.append('date_of_birth', dob ?? '');
            formData.append('age', age ?? '');
            formData.append('gender', document.getElementById('profile-gender').value);
            formData.append('height', document.getElementById('profile-height').value);
            formData.append('weight', document.getElementById('profile-weight').value);

            const dietaryPreferences = Array.from(
                document.querySelectorAll('input[name="diet-preferences"]:checked')
            ).map(cb => cb.value);

            const notificationPreferences = Array.from(
                document.querySelectorAll('input[name="notification-preferences"]:checked')
            ).map(cb => cb.value);

            formData.append('dietary_preferences', JSON.stringify(dietaryPreferences));
            formData.append('notification_preferences', JSON.stringify(notificationPreferences));

            // Food exclusions managed via chip UI; the hidden input holds
            // the canonical JSON list.
            const exclusionsJson = document.getElementById('profile-exclusions-json')?.value || '[]';
            formData.append('food_exclusions', exclusionsJson);

            const photoInput = document.getElementById('profile-picture-input');

            if (photoInput.files.length > 0) {
                formData.append('profile_photo', photoInput.files[0]);
            }
            
            formData.append('_method', 'PUT');
            const response = await fetch(@json(url('/users/' . $user->id)), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                alert('Failed to update profile.');
                return;
            }

            const result = await response.json();
            const saved = result.data;
            const fullName = `${saved.first_name} ${saved.last_name}`;

            currentUserData = saved;
            document.getElementById('profile-first-name').value = saved.first_name ?? '';
            document.getElementById('profile-last-name').value = saved.last_name ?? '';
            document.getElementById('profile-email').value = saved.email ?? '';
            document.getElementById('profile-phone').value = saved.phone_number ?? '';
            document.getElementById('profile-dob').value = saved.date_of_birth ?? '';
            document.getElementById('profile-gender').value = saved.gender ?? '';
            document.getElementById('profile-height').value = saved.height ?? '';
            document.getElementById('profile-weight').value = saved.weight ?? '';

            const dietaryPrefs = currentUserData.dietary_preferences || [];
            const notifPrefs = currentUserData.notification_preferences || [];

            document.querySelectorAll('input[name="diet-preferences"]').forEach(cb => {
                cb.checked = dietaryPrefs.includes(cb.value);

                cb.closest('label').classList.toggle('border-primary', cb.checked);
                cb.closest('label').classList.toggle('bg-primary/10', cb.checked);
                cb.closest('label').classList.toggle('border-border', !cb.checked);
                cb.closest('label').classList.toggle('bg-background', !cb.checked);
            });

            document.querySelectorAll('input[name="notification-preferences"]').forEach(cb => {
                cb.checked = notifPrefs.includes(cb.value);
            });
            
            document.getElementById('profile-display-name').textContent = fullName;
            document.getElementById('profile-card-name').textContent = fullName;
            document.getElementById('profile-card-email').textContent = saved.email ?? 'Email not set';
            document.getElementById('profile-card-phone').textContent = saved.phone_number ?? 'Phone not set';
            document.getElementById('profile-card-height').textContent = saved.height ?? 'Height not set';
            document.getElementById('profile-card-weight').textContent = saved.weight ?? 'Weight not set';
            
            const formatPreferenceLabel = (value) => value
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');

            const preferencesContainer = document.getElementById('profile-preferences-list');

            if (preferencesContainer) {
                const dietaryPrefs = saved.dietary_preferences || [];

                preferencesContainer.innerHTML = dietaryPrefs.length
                    ? dietaryPrefs.map(pref => `
                        <span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">
                            ${formatPreferenceLabel(pref)}
                        </span>
                    `).join('')
                    : '<span class="px-3 py-2 rounded-full bg-primary/10 text-primary-foreground text-sm">No preferences set</span>';
            }

            // Re-render the read-only "Foods you avoid" section.
            const exclusionsView = document.getElementById('profile-exclusions-view');
            if (exclusionsView) {
                const list = saved.food_exclusions || [];
                const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
                exclusionsView.innerHTML = list.length
                    ? list.map(f => `
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                            <iconify-icon icon="lucide:ban" class="text-xs"></iconify-icon>
                            ${esc(f)}
                        </span>
                    `).join('')
                    : '<span class="text-sm text-muted-foreground">No food exclusions yet.</span>';
            }

            const sidebarName = document.getElementById('sidebar-user-name');

            if (sidebarName) {
                sidebarName.textContent = fullName;
            }

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
            document.getElementById('open-edit-from-exclusions')?.addEventListener('click', openEditProfileModal);
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

            // ----- Food exclusions chip editor -----
            const exclusionsJsonEl = document.getElementById('profile-exclusions-json');
            const chipsEl = document.getElementById('profile-exclusions-chips');
            const emptyEl = document.getElementById('profile-exclusions-empty');
            const inputEl = document.getElementById('profile-exclusion-input');
            const addBtn = document.getElementById('profile-exclusion-add');

            function readExclusions() {
                try { return JSON.parse(exclusionsJsonEl?.value || '[]'); } catch { return []; }
            }
            function writeExclusions(list) {
                if (exclusionsJsonEl) exclusionsJsonEl.value = JSON.stringify(list);
                renderChips(list);
            }
            function renderChips(list) {
                if (!chipsEl) return;
                chipsEl.querySelectorAll('[data-exclusion-chip]').forEach(el => el.remove());
                list.forEach(f => {
                    const chip = document.createElement('span');
                    chip.dataset.exclusionChip = f;
                    chip.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-muted border border-border text-xs';
                    chip.innerHTML = `${escapeHtmlSimple(f)} <button type="button" class="text-muted-foreground hover:text-destructive" aria-label="Remove ${escapeHtmlSimple(f)}"><iconify-icon icon="lucide:x" class="text-sm"></iconify-icon></button>`;
                    chip.querySelector('button').addEventListener('click', () => removeExclusion(f));
                    chipsEl.insertBefore(chip, emptyEl);
                });
                if (emptyEl) emptyEl.classList.toggle('hidden', list.length > 0);
            }
            function escapeHtmlSimple(s) {
                return String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
            }
            function addExclusion(raw) {
                const cleaned = String(raw || '').trim().toLowerCase();
                if (!cleaned) return;
                const list = readExclusions();
                if (list.includes(cleaned)) return;
                list.push(cleaned);
                writeExclusions(list);
            }
            function removeExclusion(food) {
                const list = readExclusions().filter(f => f !== food);
                writeExclusions(list);
            }

            // Wire the pre-rendered chips (Blade-painted on first load).
            chipsEl?.querySelectorAll('button[data-remove-exclusion]').forEach(btn => {
                btn.addEventListener('click', () => removeExclusion(btn.dataset.removeExclusion));
            });

            addBtn?.addEventListener('click', () => {
                // Support comma-separated paste: "fish, peanuts, dairy" → 3 chips.
                (inputEl?.value || '').split(',').forEach(piece => addExclusion(piece));
                if (inputEl) inputEl.value = '';
            });
            inputEl?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addBtn.click();
                }
            });
        });
    </script>
</body>

</html>