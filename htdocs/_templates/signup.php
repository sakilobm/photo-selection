<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — <?= htmlspecialchars(get_config('project_title', 'OBM Studio')) ?></title>
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_config('base_path') ?>assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_config('base_path') ?>assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_config('base_path') ?>assets/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="<?= get_config('base_path') ?>favicon.ico">

    <!-- Design System Engines & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= get_config('base_path') ?>styles.css">
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">
    
    <script src="<?= get_config('base_path') ?>theme.js"></script>
    <style>
      html.theme-light body {
        background-color: #f8fafc !important;
        color: #0f172a !important;
      }
      html.theme-light .auth-glass-card {
        background: rgba(255, 255, 255, 0.88) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07) !important;
      }
      html.theme-light .auth-input {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.15) !important;
        color: #0f172a !important;
      }
      html.theme-light label {
        color: #334155 !important;
      }
      html.theme-light .auth-subtext {
        color: #64748b !important;
      }
    </style>
</head>
<body class="antialiased font-['Inter'] selection:bg-cyan-500 selection:text-slate-950 min-h-screen flex items-center justify-center relative p-4 overflow-x-hidden">
    
    <!-- ══════ ANIMATED AURORA BACKGROUND ══════ -->
    <div class="aurora-mesh">
        <div class="aurora-blob aurora-blob-1"></div>
        <div class="aurora-blob aurora-blob-2"></div>
        <div class="aurora-blob aurora-blob-3"></div>
        <div class="aurora-blob aurora-blob-4"></div>
    </div>

    <!-- Custom Cursor -->
    <div class="ball" id="ball"></div>

    <!-- Top Floating Theme Switcher -->
    <div class="fixed top-6 right-6 z-50 flex items-center gap-3">
        <div class="global-theme-switcher bg-white/10 backdrop-blur-md border border-white/10 p-1.5 rounded-full flex items-center shadow-lg">
            <div class="theme-mode-toggle flex items-center">
                <button type="button" class="mode-toggle-btn px-3 py-1 text-xs font-bold rounded-full transition-all" data-mode="dark" data-mode-val="dark">Dark</button>
                <button type="button" class="mode-toggle-btn px-3 py-1 text-xs font-bold rounded-full transition-all" data-mode="light" data-mode-val="light">Light</button>
            </div>
        </div>
    </div>

    <!-- Auth Container -->
    <div class="w-full max-w-md relative z-10 my-8">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/20 mb-3">
                <i data-lucide="user-plus" class="w-7 h-7 text-slate-950 font-black"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-black font-['Outfit'] tracking-tight text-white">OBM STUDIO</h1>
            <p class="text-xs uppercase font-extrabold tracking-widest text-[var(--theme-accent,#00d2ff)] mt-1">Client Registration Portal</p>
        </div>

        <!-- Glass Auth Card -->
        <div class="auth-glass-card bg-slate-900/75 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold font-['Outfit'] text-white">Create Account</h2>
                <p class="text-xs text-slate-400 mt-1 auth-subtext">Join OBM Studio to access your personalized album</p>
            </div>

            <form id="signup-form" class="space-y-4" novalidate>
                <div class="space-y-1.5">
                    <label for="signup-username" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Username</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="signup-username" name="username" autocomplete="username" required
                               placeholder="e.g. sakil_admin"
                               class="auth-input w-full bg-slate-950/60 border border-white/10 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="signup-email" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="email" id="signup-email" name="email_address" autocomplete="email" required
                               placeholder="e.g. name@example.com"
                               class="auth-input w-full bg-slate-950/60 border border-white/10 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="signup-phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Phone Number</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="tel" id="signup-phone" name="phone" autocomplete="tel"
                               placeholder="e.g. +91 98765 43210"
                               class="auth-input w-full bg-slate-950/60 border border-white/10 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="signup-password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Password</label>
                    <div class="relative">
                        <i data-lucide="key-round" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" id="signup-password" name="password" autocomplete="new-password" required
                               placeholder="••••••••••••"
                               class="auth-input w-full bg-slate-950/60 border border-white/10 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-black text-sm tracking-wide shadow-lg shadow-cyan-500/25 transition-all flex items-center justify-center gap-2 group mt-2">
                    <span>Create Account</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/10 text-center">
                <p class="text-xs text-slate-400 auth-subtext">
                    Already have an account? <a href="/login" class="font-bold text-cyan-400 hover:underline ml-1">Sign In Here</a>
                </p>
            </div>
        </div>
    </div>

    <div class="toast-panel" id="toast-container"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js?v=3.5.5"></script>
    <script src="<?= get_config('base_path') ?>assets/js/ball.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>
    <script>
      if (window.lucide) { lucide.createIcons(); }
    </script>
</body>
</html>
