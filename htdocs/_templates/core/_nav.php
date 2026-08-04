<?php use Aether\Session; ?>

<!-- ══════ FLOATING PILL NAVIGATION ══════ -->
<div class="floating-nav-container">
    <nav class="floating-nav-pill">
        <a href="<?= get_config('base_path') ?>" class="flex items-center gap-3 no-underline shrink-0">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-400 via-purple-500 to-amber-400 flex items-center justify-center shadow-lg shadow-cyan-500/30 shrink-0">
                <i data-lucide="camera" class="w-4 h-4 text-slate-950 font-black"></i>
            </div>
            <div class="shrink-0">
                <span class="font-extrabold text-lg tracking-tight nav-logo-text font-['Outfit'] whitespace-nowrap">OBM STUDIO</span>
                <span class="block text-[9px] text-[var(--theme-accent)] font-bold tracking-widest uppercase -mt-1 whitespace-nowrap">Photography & Cinema</span>
            </div>
        </a>

        <div class="hidden md:flex items-center gap-1.5 shrink-0">
            <a href="<?= Session::url('index') ?>" class="nav-link <?= Session::currentScript() === 'index' && !str_contains($_SERVER['REQUEST_URI'] ?? '', '#') ? 'active' : '' ?>" title="Home">
                <i data-lucide="home"></i><span class="nav-label">Home</span>
            </a>
            <a href="<?= Session::url('index') ?>#story" class="nav-link" title="Our Story">
                <i data-lucide="heart-handshake"></i><span class="nav-label">Story</span>
            </a>
            <a href="<?= Session::url('index') ?>#services" class="nav-link" title="Services">
                <i data-lucide="camera"></i><span class="nav-label">Services</span>
            </a>
            <a href="<?= Session::url('index') ?>#portfolio" class="nav-link" title="Portfolio">
                <i data-lucide="grid"></i><span class="nav-label">Portfolio</span>
            </a>
            <a href="<?= Session::url('packages') ?>" class="nav-link <?= Session::currentScript() === 'packages' ? 'active' : '' ?>" style="color:#ffb703" title="Packages ₹">
                <i data-lucide="crown"></i><span class="nav-label">Packages</span>
            </a>
            <a href="<?= Session::url('photo-selection') ?>" class="nav-link <?= Session::currentScript() === 'photo-selection' ? 'active' : '' ?>" title="Photo Selection">
                <i data-lucide="check-square"></i><span class="nav-label">Selection</span>
            </a>
            <a href="<?= Session::url('digital-album') ?>" class="nav-link <?= Session::currentScript() === 'digital-album' ? 'active' : '' ?>" title="Digital Albums">
                <i data-lucide="book-open"></i><span class="nav-label">Albums</span>
            </a>
            <a href="<?= Session::url('live-event') ?>" class="nav-link <?= Session::currentScript() === 'live-event' ? 'active' : '' ?>" title="Live Broadcast">
                <i data-lucide="radio"></i><span class="nav-label">Live Event</span>
            </a>
            <a href="<?= Session::url('admin') ?>" class="nav-link <?= Session::currentScript() === 'admin' ? 'active' : '' ?>" style="color:#a78bfa" title="Admin Command Center">
                <i data-lucide="shield-check"></i><span class="nav-label">Admin</span>
            </a>
        </div>

        <!-- DUAL-LAYER THEME SWITCHER -->
        <div class="global-theme-switcher shrink-0">
            <div class="theme-mode-toggle">
                <button class="mode-toggle-btn active" data-mode="dark" onclick="OBMTheme.setMode('dark')" title="Black Theme (Dark)">Dark</button>
                <button class="mode-toggle-btn" data-mode="light" onclick="OBMTheme.setMode('light')" title="White Theme (Light)">Light</button>
            </div>
            <div class="theme-color-dots">
                <button class="theme-switcher-dot bg-sky-500" data-theme="sapphire" title="Sky Blue" onclick="OBMTheme.setAccent('sapphire')"></button>
                <button class="theme-switcher-dot bg-purple-500" data-theme="amethyst" title="Purple" onclick="OBMTheme.setAccent('amethyst')"></button>
                <button class="theme-switcher-dot bg-emerald-500" data-theme="emerald" title="Green" onclick="OBMTheme.setAccent('emerald')"></button>
                <button class="theme-switcher-dot bg-rose-500" data-theme="rose" title="Red" onclick="OBMTheme.setAccent('rose')"></button>
                <button class="theme-switcher-dot bg-amber-500" data-theme="amber" title="Yellow" onclick="OBMTheme.setAccent('amber')"></button>
            </div>
        </div>

        <?php if (Session::isset('client_email')): ?>
            <button onclick="logoutClient()" class="btn-primary text-xs py-2.5 px-4 shrink-0 whitespace-nowrap bg-rose-600 hover:bg-rose-700">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Exit Client Portal
            </button>
        <?php else: ?>
            <button class="open-login-btn btn-primary text-xs py-2.5 px-4 shrink-0 whitespace-nowrap">
                <i data-lucide="lock" class="w-3.5 h-3.5"></i> Client Portal
            </button>
        <?php endif; ?>
    </nav>
</div>
