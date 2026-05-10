<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Uplyfe - Sign Up</title>
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
      class="w-full max-w-5xl bg-card rounded-[2.5rem] shadow-2xl border border-border overflow-hidden flex flex-col md:flex-row min-h-[700px] animate-[fadeIn_0.5s_ease-out]">

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
            Start your journey to <br /> better health today.
          </h2>
          <p class="text-lg text-foreground/80 mb-8 max-w-sm">
            Join thousands of users optimizing their health with AI-driven insights, nutrition, and fitness plans.
          </p>

          <div
            class="flex items-center gap-4 bg-background/50 backdrop-blur-sm p-4 rounded-2xl border border-border/50 w-max shadow-sm">
            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-primary-foreground">
              <iconify-icon icon="lucide:sparkles" class="text-xl"></iconify-icon>
            </div>
            <div>
              <p class="font-bold text-foreground text-sm">AI-Powered</p>
              <p class="text-xs text-foreground/70">Personalized health insights.</p>
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
          <h3 class="text-3xl font-heading font-bold text-foreground mb-2">Create Your Account</h3>
          <p class="text-muted-foreground text-sm">Join Uplyfe and start your personalized health journey.</p>
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
          <span class="text-xs text-muted-foreground font-medium uppercase tracking-wider">or sign up with email</span>
          <div class="flex-1 h-px bg-border"></div>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="flex flex-col gap-5">
          @csrf

          @if ($errors->any())
            <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-700">
              <p class="font-bold mb-1">Please fix the following:</p>
              <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-foreground ml-1">First Name</label>
              <input type="text" name="first_name" placeholder="John" required value="{{ old('first_name') }}"
                class="w-full px-4 py-3 rounded-xl border @error('first_name') border-red-400 @else border-input @enderror bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-foreground ml-1">Last Name</label>
              <input type="text" name="last_name" placeholder="Doe" required value="{{ old('last_name') }}"
                class="w-full px-4 py-3 rounded-xl border @error('last_name') border-red-400 @else border-input @enderror bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground ml-1">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:mail" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="email" name="email" placeholder="you@example.com" required value="{{ old('email') }}"
                class="w-full pl-11 pr-4 py-3 rounded-xl border @error('email') border-red-400 @else border-input @enderror bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
            </div>
            @error('email')
              <p class="text-xs text-red-600 ml-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground ml-1">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:lock" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="password" name="password" placeholder="••••••••" required
                class="w-full pl-11 pr-11 py-3 rounded-xl border @error('password') border-red-400 @else border-input @enderror bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
              <button type="button"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted-foreground hover:text-foreground transition-colors">
                <iconify-icon icon="lucide:eye"></iconify-icon>
              </button>
            </div>
            @error('password')
              <p class="text-xs text-red-600 ml-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-foreground ml-1">Confirm Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <iconify-icon icon="lucide:lock" class="text-muted-foreground"></iconify-icon>
              </div>
              <input type="password" name="password_confirmation" placeholder="••••••••" required
                class="w-full pl-11 pr-11 py-3 rounded-xl border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm">
              <button type="button"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-muted-foreground hover:text-foreground transition-colors">
                <iconify-icon icon="lucide:eye"></iconify-icon>
              </button>
            </div>
          </div>

          <div class="flex items-start gap-3">
            <input type="checkbox" name="terms" value="1" required
              class="mt-1 w-4 h-4 text-primary border-border rounded focus:ring-primary">
            <label for="terms" class="text-sm text-muted-foreground leading-relaxed">
              I agree to the <button type="button" onclick="openTermsModal()" class="text-primary hover:text-tertiary transition-colors font-medium underline">Terms of Service</button> and <button type="button" onclick="openPrivacyModal()" class="text-primary hover:text-tertiary transition-colors font-medium underline">Privacy Policy</button>
            </label>
          </div>

          <button type="submit"
            class="w-full bg-primary text-primary-foreground py-3.5 rounded-xl font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 mt-2 flex items-center justify-center gap-2">
            Create Account
            <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
          </button>
        </form>

        <p class="mt-8 text-center text-sm text-muted-foreground">
          Already have an account?
          <a href="/login" class="font-semibold text-primary hover:text-tertiary transition-colors ml-1">Sign in</a>
        </p>
      </div>
    </div>
  </div>

  <!-- Terms of Service Modal -->
  <div id="terms-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeTermsModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
      <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-heading font-bold text-foreground">Terms of Service</h3>
          <button onclick="closeTermsModal()" class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition-colors">
            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
          </button>
        </div>
        <div class="prose prose-sm max-w-none text-muted-foreground">
          <h4 class="text-lg font-semibold text-foreground mb-4">1. Acceptance of Terms</h4>
          <p class="mb-4">By accessing and using Uplyfe, you accept and agree to be bound by the terms and provision of this agreement.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">2. Use License</h4>
          <p class="mb-4">Permission is granted to temporarily use Uplyfe for personal, non-commercial transitory viewing only.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">3. Disclaimer</h4>
          <p class="mb-4">The materials on Uplyfe are provided on an 'as is' basis. Uplyfe makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">4. Limitations</h4>
          <p class="mb-4">In no event shall Uplyfe or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use Uplyfe.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">5. Accuracy of Materials</h4>
          <p class="mb-4">The materials appearing on Uplyfe could include technical, typographical, or photographic errors. Uplyfe does not warrant that any of the materials on its website are accurate, complete, or current.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">6. Links</h4>
          <p class="mb-4">Uplyfe has not reviewed all of the sites linked to its Internet website and is not responsible for the contents of any such linked site.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">7. Modifications</h4>
          <p class="mb-4">Uplyfe may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>
        </div>
        <div class="flex justify-end mt-8">
          <button onclick="closeTermsModal()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
            I Understand
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div id="privacy-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closePrivacyModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
      <div class="bg-card rounded-3xl border border-border p-8 shadow-xl">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-heading font-bold text-foreground">Privacy Policy</h3>
          <button onclick="closePrivacyModal()" class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition-colors">
            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
          </button>
        </div>
        <div class="prose prose-sm max-w-none text-muted-foreground">
          <h4 class="text-lg font-semibold text-foreground mb-4">1. Information We Collect</h4>
          <p class="mb-4">We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support. This may include your name, email address, health information, and usage data.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">2. How We Use Your Information</h4>
          <p class="mb-4">We use the information we collect to provide, maintain, and improve our services, process transactions, send you technical notices and support messages, and respond to your comments and questions.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">3. Information Sharing</h4>
          <p class="mb-4">We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy. We may share your information in response to legal requests or to protect our rights.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">4. Data Security</h4>
          <p class="mb-4">We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">5. Your Rights</h4>
          <p class="mb-4">You have the right to access, update, or delete your personal information. You may also object to or restrict certain processing of your information. To exercise these rights, please contact us.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">6. Cookies</h4>
          <p class="mb-4">We use cookies and similar technologies to enhance your experience, analyze usage, and assist in our marketing efforts. You can control cookie settings through your browser.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">7. Changes to This Policy</h4>
          <p class="mb-4">We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "last updated" date.</p>

          <h4 class="text-lg font-semibold text-foreground mb-4">8. Contact Us</h4>
          <p class="mb-4">If you have any questions about this privacy policy, please contact us at privacy@uplyfe.com.</p>
        </div>
        <div class="flex justify-end mt-8">
          <button onclick="closePrivacyModal()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
            I Understand
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openTermsModal() {
      document.getElementById('terms-modal').classList.remove('hidden');
    }

    function closeTermsModal() {
      document.getElementById('terms-modal').classList.add('hidden');
    }

    function openPrivacyModal() {
      document.getElementById('privacy-modal').classList.remove('hidden');
    }

    function closePrivacyModal() {
      document.getElementById('privacy-modal').classList.add('hidden');
    }
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
  $(document).ready(function() {
      $("form").on("submit", function(e) {
          let password = $("input[name='password']").val();
          let confirmPassword = $("input[name='password_confirmation']").val();
          if (password !== confirmPassword) {
              e.preventDefault();
              alert("Password and Confirm Password do not match!");
              return;
          }
      });
  });
  </script>
</body>

</html>