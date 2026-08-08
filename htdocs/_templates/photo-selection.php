<?php
use Aether\Session;

// Retrieve client session variables if logged in
$clientEmail = Session::get('client_email', '');
$clientName = Session::get('client_name', '');
$clientCode = Session::get('client_code', '');
?>

<!-- Pass PHP sessions to JS variables for automatic authentication bypass -->
<script>
    window.__OBM_CLIENT_SESSION = {
        email: <?= json_encode($clientEmail) ?>,
        name: <?= json_encode($clientName) ?>,
        code: <?= json_encode($clientCode) ?>
    };
</script>

<style>
    /* Standalone custom tweaks for photo-selection page */
    .slider-wrapper {
        touch-action: pan-y;
    }

    .slider-img {
        user-select: none;
        -webkit-user-drag: none;
    }

    .badge-cyan {
        background: rgba(6, 182, 212, 0.1);
        border: 1px solid rgba(6, 182, 212, 0.2);
        color: rgb(34, 211, 238);
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-gold {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: rgb(251, 191, 36);
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
    }

    .grad-cyan {
        background: linear-gradient(to right, #22d3ee, #818cf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ═══ LIGHT MODE OVERRIDES ═══ */
    html.theme-light body {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }
    html.theme-light .glass-panel {
        background: rgba(255, 255, 255, 0.88) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07) !important;
    }
    html.theme-light .glass-input {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.15) !important;
        color: #0f172a !important;
    }
    html.theme-light .glass-input::placeholder {
        color: #94a3b8 !important;
    }
    html.theme-light label {
        color: #334155 !important;
    }
    html.theme-light h1:not(.grad-cyan),
    html.theme-light h2:not(.grad-cyan),
    html.theme-light h3:not(.grad-cyan) {
        color: #0f172a !important;
    }
    html.theme-light p,
    html.theme-light span:not(.text-white-force) {
        color: #475569 !important;
    }
    html.theme-light .photo-card {
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }
    html.theme-light .category-btn {
        background: rgba(0, 0, 0, 0.04) !important;
        color: #334155 !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    html.theme-light .category-btn.active {
        background: var(--theme-accent, #00d2ff) !important;
        color: #ffffff !important;
        border-color: transparent !important;
    }
    html.theme-light .stat-card {
        background: rgba(255, 255, 255, 0.85) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    html.theme-light .stat-card h3 {
        color: #0f172a !important;
    }
    html.theme-light .modal-content {
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid rgba(0, 0, 0, 0.12) !important;
    }
</style>

<!-- Theme Ripple Wave Effect Container -->
<div id="themeWaveContainer" class="fixed inset-0 z-[60] pointer-events-none overflow-hidden"></div>

<!-- ========================================== -->
<!-- 1. AUTHENTICATION VIEW (Frosted Login)     -->
<!-- ========================================== -->
<div id="authView" class="app-view active min-h-screen flex items-center justify-center p-4 relative z-10">
    <div
        class="glass-panel w-full max-w-md rounded-[2rem] p-8 md:p-10 shadow-2xl relative overflow-hidden border border-white/10">
        <!-- Light Flare Overlay -->
        <div
            class="absolute -top-32 -right-32 w-64 h-64 bg-[var(--theme-accent)] opacity-20 rounded-full blur-[80px] transition-colors duration-500">
        </div>

        <div class="text-center mb-8 relative">
            <div class="inline-flex p-3 rounded-2xl bg-white/5 border border-white/10 mb-4 shadow-inner">
                <i data-lucide="aperture"
                    class="w-10 h-10 text-[var(--theme-accent)] animate-spin-slow transition-colors duration-500"></i>
            </div>
            <h1 class="text-3xl font-extrabold tracking-wider text-white font-['Outfit']">OBM <span
                    class="text-[var(--theme-accent)] transition-colors duration-500">STUDIO</span></h1>
            <p class="text-xs text-gray-400 font-light mt-2 uppercase tracking-widest" id="authSubtitle">Select &
                Personalize Your Shots</p>
        </div>

        <form id="authForm" onsubmit="handleAuth(event)" class="space-y-5">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2 pl-1">Client Email
                    Address</label>
                <div class="relative">
                    <i data-lucide="mail"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10 pointer-events-none"></i>
                    <input type="email" id="authEmail" required
                        class="w-full glass-input rounded-xl py-3 pl-11 pr-4 text-white focus:outline-none text-sm"
                        placeholder="Enter email (e.g., client@example.com)">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2 pl-1">Passcode /
                    Portal Key</label>
                <div class="relative">
                    <i data-lucide="key-round"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10 pointer-events-none"></i>
                    <input type="text" id="authCode" required
                        class="w-full glass-input rounded-xl py-3 pl-11 pr-4 text-white focus:outline-none text-sm"
                        placeholder="Enter key (e.g., OBM-2026)">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[var(--theme-accent)] text-white-force font-bold py-3.5 rounded-xl transition-all duration-300 hover:shadow-[0_0_25px_rgba(var(--theme-accent-rgb),0.45)] hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-4">
                <span id="authBtnText">Unlock My Gallery</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-white-force"></i>
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="<?= Session::url('index') ?>"
                class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Back to Studio Portfolio</span>
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. MAIN GALLERY SYSTEM (Frosted Dashboard) -->
<!-- ========================================== -->
<div id="galleryView" class="app-view flex-col min-h-screen z-10 relative hidden">
    <!-- Floating Header -->
    <header
        class="glass-panel sticky top-4 z-40 mx-4 md:mx-6 my-4 rounded-2xl border border-white/10 shadow-lg px-6 py-4 transition-all duration-300">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <!-- Return to Website Home Link -->
                <a href="<?= Session::url('index') ?>"
                    class="p-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:text-[var(--theme-accent)] text-gray-300 transition-colors cursor-pointer flex items-center justify-center shrink-0 custom-cursor-hide"
                    title="Return to Website">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </a>
                <div class="p-2 rounded-xl bg-white/5 border border-white/10 shrink-0">
                    <i data-lucide="aperture"
                        class="w-6 h-6 text-[var(--theme-accent)] animate-spin-slow transition-colors"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-wider text-white">OBM <span
                            class="text-[var(--theme-accent)] transition-colors">STUDIO</span></h2>
                    <p class="text-[9px] text-gray-400 tracking-widest uppercase">Premium Portal</p>
                </div>
            </div>

            <!-- Client Profile Badge -->
            <div
                class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-2xl px-4 py-2 hover:bg-white/10 transition-colors shadow-inner shrink-0">
                <div class="relative shrink-0">
                    <div
                        class="absolute -inset-0.5 rounded-full bg-gradient-to-tr from-[var(--theme-accent)] to-transparent opacity-75 blur-[1px]">
                    </div>
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&h=150&q=80"
                        alt="Client Avatar" class="w-8 h-8 rounded-full object-cover relative border border-white/10">
                    <span
                        class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-[#0f172a] flex items-center justify-center">
                        <span class="absolute w-full h-full rounded-full bg-emerald-500 opacity-75 animate-ping"></span>
                    </span>
                </div>
                <div class="text-left leading-tight">
                    <span class="block text-[8px] text-gray-400 font-bold uppercase tracking-widest">Active
                        Session</span>
                    <span id="clientNameDisplay" class="block text-xs font-bold text-white tracking-wide">Valued
                        Client</span>
                </div>
            </div>

            <!-- Interactive Settings/Themes & Stats -->
            <div class="flex items-center flex-wrap gap-4">
                <!-- Dynamic Theme Selector -->
                <div class="flex items-center gap-1.5 bg-white/5 border border-white/10 p-1 rounded-xl">
                    <span class="text-[10px] uppercase font-bold text-gray-400 px-2">Theme</span>
                    <button onclick="setAppTheme('sapphire', event)"
                        class="w-6 h-6 rounded-full bg-sky-500 border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Sapphire Ice"></button>
                    <button onclick="setAppTheme('amethyst', event)"
                        class="w-6 h-6 rounded-full bg-purple-500 border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Amethyst Glow"></button>
                    <button onclick="setAppTheme('emerald', event)"
                        class="w-6 h-6 rounded-full bg-emerald-500 border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Emerald Forest"></button>
                    <button onclick="setAppTheme('rose', event)"
                        class="w-6 h-6 rounded-full bg-rose-500 border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Rose Quartz"></button>
                    <button onclick="setAppTheme('amber', event)"
                        class="w-6 h-6 rounded-full bg-amber-500 border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Amber Sunset"></button>
                    <button onclick="setAppTheme('white', event)"
                        class="w-6 h-6 rounded-full bg-white border-2 border-white/20 hover:scale-110 transition-transform"
                        title="Pearl White"></button>
                </div>

                <!-- Selected Counter Badge -->
                <div class="bg-white/5 px-4 py-2 rounded-xl border border-white/10 flex items-center gap-3">
                    <div class="text-right">
                        <span class="block text-[9px] text-gray-400 uppercase tracking-widest font-bold">Selected</span>
                        <span id="selectionCounter"
                            class="text-sm font-extrabold text-[var(--theme-accent)] transition-colors">0 / 0</span>
                    </div>
                    <button onclick="submitSelections()"
                        class="bg-[var(--theme-accent)] hover:shadow-[0_0_15px_rgba(var(--theme-accent-rgb),0.4)] text-white-force font-extrabold text-xs px-3.5 py-2 rounded-lg transition-all flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-white-force"></i>
                        Finalize
                    </button>
                </div>

                <!-- Reset & Logout Controls -->
                <div class="flex gap-1.5">
                    <button onclick="triggerResetGallery()"
                        class="p-2.5 bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white rounded-xl border border-white/10 transition-colors"
                        title="Reset Gallery Selections & Deletions">
                        <i data-lucide="rotate-ccw" class="w-4.5 h-4.5"></i>
                    </button>
                    <button onclick="logout()"
                        class="p-2.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 rounded-xl border border-red-500/20 transition-colors"
                        title="Logout">
                        <i data-lucide="log-out" class="w-4.5 h-4.5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-7xl w-full mx-auto px-4 md:px-6 py-6 flex-grow flex flex-col gap-8">
        <!-- Empty Workspace State (Shown when no photos are uploaded/assigned yet) -->
        <div id="emptyWorkspaceState" class="hidden w-full max-w-2xl mx-auto my-12 text-center glass-panel rounded-[2.5rem] border border-white/10 p-10 md:p-14 flex flex-col items-center justify-center gap-8 shadow-2xl relative overflow-hidden">
            <!-- Sleek background radial glows -->
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-[var(--theme-accent)] opacity-10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-amber-500 opacity-5 rounded-full blur-[100px] pointer-events-none"></div>

            <!-- Animated Status Icon -->
            <div class="relative flex items-center justify-center">
                <div class="absolute w-24 h-24 rounded-full bg-amber-500/10 border border-amber-500/20 animate-ping duration-1000"></div>
                <div class="absolute w-32 h-32 rounded-full bg-amber-500/5 animate-pulse"></div>
                <div class="w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-lg backdrop-blur-md relative z-10">
                    <i data-lucide="cloud-lightning" class="w-10 h-10 animate-bounce"></i>
                </div>
            </div>

            <!-- Typography Header -->
            <div class="space-y-3 relative z-10">
                <h3 class="text-3xl font-black text-white font-['Outfit'] tracking-wide">Workspace Preparing</h3>
                <p class="text-sm text-gray-400 max-w-md mx-auto leading-relaxed">
                    Your secure portal workspace has been successfully initialized, but there are no photos assigned to your gallery yet. 
                    Once OBM Studio uploads and publishes your event catalog, they will automatically appear here.
                </p>
            </div>

            <!-- Status Tracker Timeline (Incredibly polished detail) -->
            <div class="w-full max-w-md bg-black/40 border border-white/5 rounded-2xl p-6 text-left space-y-4 relative z-10 backdrop-blur-xl">
                <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest border-b border-white/5 pb-2">Workspace Lifecycle</p>
                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-200">Secure Handshake Established</p>
                            <p class="text-[10px] text-gray-500">Client handshake token generated successfully.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-200">Authentication Portal Active</p>
                            <p class="text-[10px] text-gray-500">Secure end-to-end authorization locks active.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0 mt-0.5 animate-pulse">
                            <i data-lucide="loader" class="w-3 h-3 animate-spin"></i>
                        </div>
                        <div>
                            <p class="font-bold text-amber-400">Assets Upload Pending</p>
                            <p class="text-[10px] text-gray-400">OBM Studio team is sorting and compiling your photo list catalog.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Container -->
            <div class="flex flex-col sm:flex-row items-center gap-4 relative z-10 w-full justify-center">
                <a href="mailto:obmdigitalstudio@gmail.com" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i> Contact Studio Support
                </a>
                <button onclick="logout()" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-[var(--theme-accent)] text-white-force text-xs font-extrabold hover:shadow-[0_0_20px_rgba(var(--theme-accent-rgb),0.4)] transition-all flex items-center justify-center gap-2 shadow-md hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="log-out" class="w-4 h-4 text-white-force"></i> Disconnect Session
                </button>
            </div>
        </div>

        <div id="workspaceMetaHeader" class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-2">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="badge badge-cyan">Interactive Client Portal</span>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-400 text-[10px] font-bold uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Workspace
                    </span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white font-['Outfit'] leading-tight">
                    Your Photo <span class="grad-cyan">Selection</span>
                </h1>
                <p class="text-sm text-slate-400 max-w-xl">
                    Experience every moment in a luxury photo gallery. Review your captures, filter by event categories,
                    select your favorites, and finalize your list.
                </p>
                <div class="flex items-center gap-4 pt-1">
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span>Client: <strong class="text-white" id="clientMetaName">sakil@obmstudio.com</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Event Date: <strong class="text-white" id="clientMetaDate">Dec 2025</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>Passcode: <strong class="text-[var(--theme-accent)] font-mono"
                                id="clientMetaCode">ADMIN2026</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. TOUCH CAROUSEL (Hero Slideshow) -->
        <section id="carouselSection" class="w-full relative">
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-[var(--theme-accent)]"></i>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-200">Featured Masterpieces</h3>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="slideCarouselPrev()"
                        class="p-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 text-gray-400 hover:text-white transition-all">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button onclick="slideCarouselNext()"
                        class="p-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 text-gray-400 hover:text-white transition-all">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Featured Slider Box -->
            <div class="relative w-full overflow-hidden rounded-[1.75rem] glass-panel p-1.5">
                <div class="w-full overflow-hidden rounded-2xl relative">
                    <div id="carouselTrack"
                        class="flex w-full transition-transform duration-500 ease-out cursor-grab active:cursor-grabbing slider-wrapper"
                        style="transform: translateX(0%);">
                        <!-- Carousel Slides injects dynamically -->
                    </div>
                </div>
                <!-- Indicator dots -->
                <div id="carouselDots"
                    class="absolute bottom-6 left-1/2 -translate-y-1/2 -translate-x-1/2 flex gap-1.5 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                </div>
            </div>
        </section>

        <!-- 2. SEARCH, CATEGORIES & INTERACTION CONTROL BAR -->
        <section id="filtersSection"
            class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-white/5 p-4 rounded-2xl border border-white/10 glass-panel">
            <!-- Filters & Category Navigation -->
            <div class="flex flex-wrap items-center gap-1.5" id="categoryContainer">
                <!-- Dynamically generated buttons -->
            </div>

            <!-- Live Search Input -->
            <div class="flex-grow max-w-md relative flex items-center">
                <input type="text" id="gallerySearchInput" oninput="handleSearch(this.value)"
                    placeholder="Search photos by filename..."
                    class="w-full glass-input rounded-xl py-2.5 px-4 pl-10 text-white text-xs focus:outline-none focus:border-[var(--theme-accent)] transition-all">
                <i data-lucide="search" class="w-4.5 h-4.5 text-gray-400 absolute left-3.5 pointer-events-none"></i>
            </div>
        </section>

        <!-- 3. ACTIONS TOOLBAR (BULK SELECTION & UNDO) -->
        <section id="actionToolbar"
            class="hidden flex-wrap items-center justify-between gap-4 p-4 rounded-xl border border-white/10 bg-black/40 backdrop-blur-lg">
            <div class="flex items-center gap-3">
                <i data-lucide="settings-2" class="w-5 h-5 text-[var(--theme-accent)]"></i>
                <p class="text-sm text-gray-300"><span id="toolbarSelectedCount" class="font-bold text-white">0</span>
                    items selected</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="bulkDeselect()"
                    class="text-xs font-medium text-gray-400 hover:text-white px-3 py-2 rounded-lg bg-white/5 border border-white/10 transition-colors">Clear
                    Selection</button>
                <button onclick="bulkDelete()"
                    class="text-xs font-semibold text-red-400 hover:text-red-300 px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/20 transition-colors">Delete
                    Selected</button>
            </div>
        </section>

        <!-- 4. CORE PHOTO GRID -->
        <section id="gridSection" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white tracking-wide flex items-center gap-2">
                    <i data-lucide="image" class="w-5 h-5 text-[var(--theme-accent)]"></i>
                    Selection Catalog
                </h3>
                <p class="text-xs text-gray-400 hidden sm:block">
                    <i data-lucide="mouse-pointer" class="w-3.5 h-3.5 inline mr-1 text-[var(--theme-accent)]"></i>
                    Hover to reveal options. Click to enlarge and slide.
                </p>
            </div>
            <div id="imageGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- Photo cards inject dynamically -->
            </div>
        </section>
    </main>
</div>

<!-- ========================================== -->
<!-- 3. LIQUID LIGHTBOX WITH SWIPE GESTURE      -->
<!-- ========================================== -->
<div id="lightboxModal"
    class="fixed inset-0 z-50 bg-black/95 backdrop-blur-xl hidden flex-col items-center justify-center p-4 select-none">
    <div class="absolute inset-0 z-0 pointer-events-auto cursor-zoom-out" onclick="closeLightbox()"></div>

    <!-- Lightbox Top Info Badge -->
    <div class="absolute top-6 left-1/2 -translate-x-1/2 z-20 pointer-events-auto">
        <div
            class="px-4 py-2 rounded-full bg-black/50 border border-white/10 backdrop-blur-md flex items-center gap-2.5">
            <p id="lightboxFilename" class="text-xs font-bold text-gray-200 truncate max-w-[150px] sm:max-w-xs">
                IMG_0001.jpg</p>
            <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
            <p id="lightboxIndex" class="text-[10px] font-semibold text-[var(--theme-accent)]">1 of 12</p>
        </div>
    </div>

    <!-- Swipe Navigators -->
    <button onclick="navigateLightbox(-1)"
        class="absolute left-6 z-20 hidden md:flex p-4 rounded-2xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:scale-105 active:scale-95 transition-all"
        title="Previous Image">
        <i data-lucide="chevron-left" class="w-6 h-6"></i>
    </button>
    <button onclick="navigateLightbox(1)"
        class="absolute right-6 z-20 hidden md:flex p-4 rounded-2xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:scale-105 active:scale-95 transition-all"
        title="Next Image">
        <i data-lucide="chevron-right" class="w-6 h-6"></i>
    </button>

    <!-- Slide Content Zone -->
    <div id="lightboxTrack"
        class="w-full max-w-[85vw] max-h-[80vh] flex items-center justify-center z-10 cursor-grab active:cursor-grabbing slider-wrapper">
        <img id="lightboxImage" src="" alt="Zoomed view"
            class="w-full max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10 slider-img transition-transform duration-300 select-none">
    </div>

    <!-- Thumbnails bar -->
    <div class="absolute bottom-24 left-4 right-4 z-10 hidden sm:flex justify-center gap-2 overflow-x-auto py-2 px-4 bg-black/40 backdrop-blur-md border border-white/10 rounded-2xl max-w-xl mx-auto"
        id="lightboxThumbs"></div>

    <!-- Lightbox Bottom Controls Capsule -->
    <div
        class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 pointer-events-auto flex items-center gap-3.5 bg-black/50 border border-white/10 backdrop-blur-md px-5 py-3 rounded-2xl shadow-2xl">
        <button onclick="navigateLightbox(-1)"
            class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white active:scale-95 transition-all md:hidden"
            title="Previous">
            <i data-lucide="chevron-left" class="w-4 h-4"></i>
        </button>

        <button id="lightboxSelectBtn"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/5 border border-white/10 text-white hover:bg-white/10 active:scale-95 transition-all"></button>

        <button onclick="lightboxDeleteCurrent()"
            class="p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white hover:bg-red-500 hover:border-red-500 transition-all"
            title="Delete Photo">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>

        <span class="w-px h-5 bg-white/10"></span>

        <button onclick="submitSelections()"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-extrabold bg-[var(--theme-accent)] text-white-force hover:opacity-90 active:scale-95 transition-all shadow-md"
            title="Finalize selections">
            <i data-lucide="check-circle" class="w-4 h-4 text-white-force"></i>
            <span>Finalize</span>
        </button>

        <span class="w-px h-5 bg-white/10"></span>

        <button onclick="closeLightbox()"
            class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 active:scale-95 transition-all"
            title="Close Preview">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>

        <button onclick="navigateLightbox(1)"
            class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white active:scale-95 transition-all md:hidden"
            title="Next">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. SUBMIT CONFIRMATION SUMMARY MODAL       -->
<!-- ========================================== -->
<div id="submitModal" class="fixed inset-0 z-[60] bg-black/90 backdrop-blur-xl hidden items-center justify-center p-4">
    <div id="confettiContainer" class="absolute inset-0 pointer-events-none overflow-hidden z-0"></div>
    <div
        class="glass-panel w-full max-w-lg rounded-[2.5rem] p-8 md:p-10 shadow-2xl border border-white/15 relative z-10 text-center max-h-[90vh] flex flex-col justify-between">
        <div>
            <div
                class="inline-flex p-4 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 mb-6">
                <i data-lucide="send" class="w-12 h-12"></i>
            </div>
            <h3 class="text-3xl font-extrabold text-white mb-2">Finalize Selections</h3>
            <p class="text-sm text-gray-400">Please review your selected list below before sending to OBM Photo Studio.
            </p>

            <div class="my-6 max-h-48 overflow-y-auto bg-black/30 border border-white/10 rounded-xl p-4 text-left space-y-2.5 scrollbar-thin"
                id="submitSelectedList"></div>
        </div>

        <div class="space-y-3 mt-4">
            <button onclick="confirmSubmitChoice()"
                class="w-full bg-[var(--theme-accent)] text-white font-extrabold py-3.5 rounded-xl transition-all duration-300 hover:shadow-[0_0_20px_rgba(var(--theme-accent-rgb),0.3)] hover:scale-[1.01] active:scale-[0.99] custom-cursor-hide">Send
                Selections to OBM Studio</button>
            <button onclick="closeSubmitModal()"
                class="w-full bg-white/5 border border-white/10 text-gray-300 hover:text-white font-semibold py-3 rounded-xl transition-colors custom-cursor-hide">Go
                Back &amp; Edit Selection</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SUCCESS ANIMATION OVERLAY                  -->
<!-- ========================================== -->
<div id="successOverlay"
    class="fixed inset-0 z-[70] bg-black/95 backdrop-blur-xl hidden flex-col items-center justify-center p-4">
    <div id="successConfetti" class="absolute inset-0 pointer-events-none overflow-hidden z-0"></div>
    <div
        class="glass-panel max-w-md w-full rounded-[2.5rem] p-8 md:p-10 border border-white/10 text-center relative z-10 flex flex-col items-center justify-center gap-6 animate-viewEnter">
        <div class="relative flex items-center justify-center">
            <div class="absolute w-24 h-24 rounded-full bg-emerald-500/20 animate-ping"></div>
            <div class="absolute w-32 h-32 rounded-full bg-emerald-500/10 animate-pulse"></div>
            <div
                class="w-20 h-20 rounded-full bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400 flex items-center justify-center relative shadow-lg shadow-emerald-500/20">
                <i data-lucide="check" class="w-10 h-10 animate-bounce"></i>
            </div>
        </div>

        <div>
            <h3 class="text-2xl font-extrabold text-white tracking-wide">Transmission Successful!</h3>
            <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mt-1 text-[var(--theme-accent)]">
                Selections Dispatched</p>
            <p class="text-sm text-gray-300 mt-4 leading-relaxed">
                Your selection of <span id="successPhotoCount" class="font-bold text-white">0</span> photos has been
                successfully transmitted directly to the OBM Photo Studio database.
            </p>
            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Our editing team has been notified and will start
                preparing your album draft layout immediately.</p>
        </div>

        <button onclick="closeSuccessOverlay()"
            class="w-full bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-3.5 rounded-xl transition-all duration-300 active:scale-95 flex items-center justify-center gap-2 mt-4 custom-cursor-hide">
            <span>Back to Gallery</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- MODERN PROGRESSIVE LOADING OVERLAY         -->
<!-- ========================================== -->
<div id="loadingOverlay"
    class="fixed inset-0 z-[70] bg-black/95 backdrop-blur-xl hidden flex-col items-center justify-center p-4 select-none">
    <div
        class="glass-panel max-w-md w-full rounded-[2.5rem] p-8 md:p-10 border border-white/10 relative z-10 flex flex-col gap-6 animate-viewEnter">
        <div class="text-center">
            <div class="inline-flex p-3 rounded-2xl bg-white/5 border border-white/10 mb-4 animate-pulse">
                <i data-lucide="cloud-lightning" class="w-8 h-8 text-[var(--theme-accent)] animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-extrabold text-white tracking-wide">Transmitting Assets</h3>
            <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mt-1 text-[var(--theme-accent)]">
                Uploading selections to OBM cloud</p>
        </div>

        <div class="space-y-1.5">
            <div class="flex justify-between text-xs text-gray-300 px-1 font-semibold">
                <span>Overall Progress</span>
                <span id="globalUploadProgressText">0%</span>
            </div>
            <div class="w-full h-2.5 bg-white/5 rounded-full overflow-hidden border border-white/5">
                <div id="globalUploadProgressBar"
                    class="h-full bg-[var(--theme-accent)] rounded-full transition-all duration-300 ease-out"
                    style="width: 0%;"></div>
            </div>
        </div>

        <div class="bg-black/30 border border-white/10 rounded-2xl p-4 flex flex-col gap-2">
            <div class="flex items-center justify-between text-xs">
                <span id="currentItemName" class="text-gray-200 font-semibold truncate max-w-[200px]">Initializing
                    connection...</span>
                <span id="currentItemPercent" class="text-[var(--theme-accent)] font-bold">0%</span>
            </div>
            <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                <div id="currentItemProgressBar" class="h-full bg-emerald-500 rounded-full transition-all duration-100"
                    style="style: 0%;"></div>
            </div>
        </div>

        <div class="space-y-2">
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-wider pl-1">Activity Log</p>
            <div id="uploadLogContainer"
                class="h-36 overflow-y-auto bg-black/40 border border-white/10 rounded-xl p-3.5 space-y-2 text-xs scrollbar-thin scroll-smooth text-left text-gray-300">
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- PORTAL FETCHING / ASSETS ALLOCATION SCREEN -->
<!-- ========================================== -->
<div id="portalLoadingScreen"
    class="fixed inset-0 z-[70] bg-black/95 backdrop-blur-xl hidden flex-col items-center justify-center p-4 select-none">
    <div
        class="glass-panel max-w-md w-full rounded-[2.5rem] p-8 md:p-10 border border-white/10 relative z-10 flex flex-col gap-6 animate-viewEnter text-center">
        <div class="relative flex items-center justify-center mx-auto mb-2">
            <div class="absolute w-20 h-20 rounded-full bg-[var(--theme-accent)]/10 animate-ping"></div>
            <div class="absolute w-28 h-28 rounded-full bg-[var(--theme-accent)]/5 animate-pulse"></div>
            <div
                class="w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center relative shadow-lg">
                <i data-lucide="refresh-cw" class="w-8 h-8 text-[var(--theme-accent)] animate-spin-slow"></i>
            </div>
        </div>

        <div>
            <h3 class="text-2xl font-extrabold text-white tracking-wide">Syncing Portal Assets</h3>
            <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mt-1 text-[var(--theme-accent)]"
                id="portalLoadingStatus">Connecting to OBM Core Server</p>
            <div
                class="mt-3 flex items-center justify-center gap-2 bg-white/5 border border-white/5 rounded-xl px-4 py-1.5 max-w-xs mx-auto">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i>
                <span id="portalLoadingEmail" class="text-xs font-mono text-gray-300 truncate">client@example.com</span>
            </div>
        </div>

        <div class="space-y-1.5">
            <div class="flex justify-between text-[10px] text-gray-400 px-1 font-bold uppercase tracking-wider">
                <span>Allocating Workspace Disk</span>
                <span id="portalProgressText">0%</span>
            </div>
            <div class="w-full h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                <div id="portalProgressBar"
                    class="h-full bg-[var(--theme-accent)] rounded-full transition-all duration-300 ease-out"
                    style="width: 0%;"></div>
            </div>
        </div>

        <div
            class="bg-black/30 border border-white/5 rounded-2xl p-4 text-left space-y-2.5 text-xs font-mono text-gray-400">
            <div class="flex items-center gap-2" id="step-connect">
                <i data-lucide="circle-dot" class="w-3.5 h-3.5 text-gray-500 animate-pulse"></i>
                <span>Initialize studio handshake...</span>
            </div>
            <div class="flex items-center gap-2 opacity-50" id="step-query">
                <i data-lucide="circle" class="w-3.5 h-3.5 text-gray-500"></i>
                <span>Query allocations for user email...</span>
            </div>
            <div class="flex items-center gap-2 opacity-50" id="step-download">
                <i data-lucide="circle" class="w-3.5 h-3.5 text-gray-500"></i>
                <span>Retrieve metadata &amp; details...</span>
            </div>
            <div class="flex items-center gap-2 opacity-50" id="step-render">
                <i data-lucide="circle" class="w-3.5 h-3.5 text-gray-500"></i>
                <span>Build liquid interactive workspace...</span>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- REUSABLE MODERN CONFIRMATION MODAL          -->
<!-- ========================================== -->
<div id="obmModal" class="obm-modal-overlay" aria-hidden="true">
    <div class="obm-modal-backdrop"></div>
    <div class="obm-modal-container">
        <div class="obm-modal-icon-ring" id="obmModalIconRing">
            <div class="obm-modal-icon-glow"></div>
            <div class="obm-modal-icon-circle">
                <i data-lucide="alert-triangle" id="obmModalIcon" class="w-7 h-7"></i>
            </div>
        </div>

        <div class="obm-modal-content">
            <h3 id="obmModalTitle" class="obm-modal-title">Are you sure?</h3>
            <p id="obmModalMessage" class="obm-modal-message">This action cannot be undone.</p>
        </div>

        <div class="obm-modal-actions">
            <button id="obmModalCancel" class="obm-modal-btn obm-modal-btn-cancel" onclick="closeModal(false)">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                <span id="obmModalCancelText">Cancel</span>
            </button>
            <button id="obmModalConfirm" class="obm-modal-btn obm-modal-btn-confirm" onclick="closeModal(true)">
                <i data-lucide="check" class="w-3.5 h-3.5" id="obmModalConfirmIcon"></i>
                <span id="obmModalConfirmText">Confirm</span>
            </button>
        </div>
    </div>
</div>

<!-- Premium Custom Cursor -->
<div id="customCursor"
    class="fixed pointer-events-none z-[999] w-12 h-12 rounded-full border border-white/20 bg-black/45 backdrop-blur-md text-[var(--theme-accent)] flex items-center justify-center opacity-0 scale-50 transition-all duration-300 ease-out shadow-2xl -translate-x-1/2 -translate-y-1/2 pointer-events-none">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
        stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
</div>

<script src="<?= get_config('base_path') ?>photo-selection.js"></script>