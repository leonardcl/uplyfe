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
  <style>
    #toast-container {
      position: fixed;
      top: 1.5rem;
      right: 1.5rem;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      pointer-events: none;
    }
    .toast {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 1rem 1.25rem;
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.12);
      min-width: 280px;
      max-width: 380px;
      pointer-events: all;
      animation: toastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
      backdrop-filter: blur(12px);
    }
    .toast.success {
      background: rgba(255,255,255,0.95);
      border: 1px solid #bbf7d0;
    }
    .toast.error {
      background: rgba(255,255,255,0.95);
      border: 1px solid #fecaca;
    }
    .toast-icon {
      flex-shrink: 0;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }
    .toast.success .toast-icon { background: #dcfce7; color: #16a34a; }
    .toast.error   .toast-icon { background: #fee2e2; color: #dc2626; }
    .toast-body { flex: 1; }
    .toast-title { font-weight: 600; font-size: 0.875rem; color: #0f172a; margin-bottom: 0.125rem; }
    .toast-msg   { font-size: 0.8rem; color: #64748b; line-height: 1.4; }
    .toast-close {
      flex-shrink: 0;
      background: none;
      border: none;
      cursor: pointer;
      color: #94a3b8;
      font-size: 1rem;
      padding: 0;
      line-height: 1;
      transition: color 0.15s;
    }
    .toast-close:hover { color: #0f172a; }
    .toast.hiding { animation: toastOut 0.25s ease-in forwards; }
    @keyframes toastIn  { from { opacity:0; transform: translateX(2rem) scale(0.95); } to { opacity:1; transform: translateX(0) scale(1); } }
    @keyframes toastOut { from { opacity:1; transform: translateX(0) scale(1); }       to { opacity:0; transform: translateX(2rem) scale(0.95); } }
  </style>
</head>

<div id="toast-container"></div>

<script>
  function showToast(type, title, message) {
    const container = document.getElementById('toast-container');
    const icon = type === 'success' ? '✓' : '✕';
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = `
      <div class="toast-icon">${icon}</div>
      <div class="toast-body">
        <div class="toast-title">${title}</div>
        <div class="toast-msg">${message}</div>
      </div>
      <button class="toast-close" onclick="dismissToast(this.parentElement)">✕</button>
    `;
    container.appendChild(toast);
    setTimeout(() => dismissToast(toast), 5000);
  }
  function dismissToast(toast) {
    if (!toast || toast.classList.contains('hiding')) return;
    toast.classList.add('hiding');
    setTimeout(() => toast.remove(), 250);
  }
  @if (session('success'))
    document.addEventListener('DOMContentLoaded', () =>
      showToast('success', 'Success', @json(session('success')))
    );
  @endif
  @if ($errors->any())
    document.addEventListener('DOMContentLoaded', () =>
      showToast('error', 'Something went wrong', @json($errors->first()))
    );
  @endif
</script>

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
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google
          </button>
          <button
            class="flex-1 flex items-center justify-center gap-2 py-3 px-4 border border-border rounded-xl hover:bg-muted transition-colors text-sm font-medium text-foreground shadow-sm">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            Apple
          </button>
        </div>

        <div class="flex items-center gap-4 mb-8">
          <div class="flex-1 h-px bg-border"></div>
          <span class="text-xs text-muted-foreground font-medium uppercase tracking-wider">or sign in with email</span>
          <div class="flex-1 h-px bg-border"></div>
        </div>

        <form action="{{ route('login.process') }}" method="POST" class="flex flex-col gap-5">
          @csrf
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground ml-1">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:mail" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="email" name="email" placeholder="you@example.com" required
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
              <input type="password" name="password" placeholder="••••••••" required
                class="w-full pl-11 pr-11 py-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
              <button type="button"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted-foreground hover:text-foreground transition-colors">
                <iconify-icon icon="lucide:eye"></iconify-icon>
              </button>
            </div>
          </div>

          <button type="submit"
            class="w-full bg-primary text-primary-foreground py-3.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 mt-2 flex items-center justify-center gap-2">
            Sign In
            <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
          </button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
          Don't have an account?
          <a href="/signup" class="font-semibold text-primary hover:text-tertiary transition-colors ml-1">Sign up for free</a>
        </p>

      </div>
    </div>
  </div>
</body>

</html>