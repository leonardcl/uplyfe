<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Screen</title>
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
  <div
    class="min-h-screen w-full bg-background flex flex-col relative items-center justify-center p-4 sm:p-8 font-sans">

    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
      <div class="absolute -top-[20%] -left-[10%] w-[70vw] h-[70vw] rounded-full bg-primary/10 blur-[120px]"></div>
      <div class="absolute -bottom-[20%] -right-[10%] w-[60vw] h-[60vw] rounded-full bg-tertiary/10 blur-[100px]"></div>
    </div>

    <div
      class="w-full max-w-5xl bg-card rounded-[2.5rem] shadow-2xl border border-border overflow-hidden flex flex-col md:flex-row min-h-[600px] animate-[fadeIn_0.5s_ease-out]">

      <!-- Left Side: Branding / Visual -->
      <div
        class="hidden md:flex flex-col justify-between w-1/2 bg-gradient-to-br from-primary/20 to-tertiary/20 p-12 relative overflow-hidden">
        <!-- Overlay Pattern -->
        <div
          class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent">
        </div>

        <div class="relative z-10 flex items-center gap-2 mb-12">
          <a href="/"
            class="flex items-center gap-2">
            <div
              class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-md">
              <iconify-icon icon="lucide:leaf" class="text-2xl"></iconify-icon>
            </div>
            <span class="text-2xl font-heading font-bold tracking-tight text-foreground">Uplyfe</span>
          </a>
        </div>

        <div class="relative z-10">
          <h2 class="text-4xl font-heading font-bold text-foreground leading-tight mb-6">
            Your journey to a <br /> healthier you starts here.
          </h2>
          <p class="text-lg text-foreground/80 mb-8 max-w-sm">
            Join thousands of users optimizing their health with AI-driven insights, nutrition, and fitness plans.
          </p>

          <div
            class="flex items-center gap-4 bg-background/50 backdrop-blur-sm p-4 rounded-2xl border border-border/50 w-max shadow-sm">
            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-primary-foreground">
              <iconify-icon icon="lucide:shield-check" class="text-xl"></iconify-icon>
            </div>
            <div>
              <p class="font-bold text-foreground text-sm">Secure & Private</p>
              <p class="text-xs text-foreground/70">Your medical data is encrypted.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side: Form -->
      <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-card">

        <div class="md:hidden flex items-center gap-2 mb-10 justify-center">
          <div
            class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-primary-foreground shadow-md">
            <iconify-icon icon="lucide:leaf" class="text-xl"></iconify-icon>
          </div>
          <span class="text-2xl font-heading font-bold tracking-tight text-foreground">Uplyfe</span>
        </div>

        <div class="mb-8 text-center md:text-left">
          <h3 class="text-3xl font-heading font-bold text-foreground mb-2">Welcome Back</h3>
          <p class="text-muted-foreground text-sm">Enter your details to access your health dashboard.</p>
        </div>

        <div class="flex gap-4 mb-8">
          <button
            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 border border-border rounded-xl hover:bg-muted transition-colors text-sm font-medium text-foreground shadow-sm">
            <iconify-icon icon="lucide:mail" class="text-lg text-muted-foreground"></iconify-icon>
            Google
          </button>
          <button
            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 border border-border rounded-xl hover:bg-muted transition-colors text-sm font-medium text-foreground shadow-sm">
            <iconify-icon icon="lucide:apple" class="text-lg text-muted-foreground"></iconify-icon>
            Apple
          </button>
        </div>

        <div class="flex items-center gap-4 mb-8">
          <div class="flex-1 h-px bg-border"></div>
          <span class="text-xs text-muted-foreground font-medium uppercase tracking-wider">or sign in with email</span>
          <div class="flex-1 h-px bg-border"></div>
        </div>

        <form class="flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground ml-1">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:mail" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="email" placeholder="you@example.com"
                class="w-full pl-11 pr-4 py-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <div class="flex items-center justify-between ml-1">
              <label class="text-sm font-medium text-foreground">Password</label>
              <a href="#" class="text-xs font-medium text-primary hover:text-tertiary transition-colors">Forgot
                password?</a>
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:lock" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="password" placeholder="••••••••"
                class="w-full pl-11 pr-11 py-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
              <button type="button"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted-foreground hover:text-foreground transition-colors">
                <iconify-icon icon="lucide:eye"></iconify-icon>
              </button>
            </div>
          </div>

          <button type="button"
            class="w-full bg-primary text-primary-foreground py-3.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 mt-2 flex items-center justify-center gap-2">
            Sign In
            <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
          </button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
          Don't have an account?
          <a href="#" class="font-semibold text-primary hover:text-tertiary transition-colors ml-1">Sign up for free</a>
        </p>

      </div>
    </div>
  </div>
</body>

</html>