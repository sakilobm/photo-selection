<?php use Aether\Session; ?>

<style>
  /* ═══ ALBUM 3D ENGINE ═══ */
  .album-viewer {
    perspective: 2200px;
  }

  .album-book {
    display: flex;
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: #0a0e18;
    box-shadow:
      0 40px 100px rgba(0, 0, 0, 0.8),
      0 0 60px rgba(var(--theme-accentRGB), 0.12),
      inset 0 1px 0 rgba(255, 255, 255, 0.06);
    transition: box-shadow 0.5s ease, transform 0.5s ease;
  }

  .album-border-overlay {
    position: absolute;
    inset: 0;
    border-radius: 20px;
    border: 2px solid rgba(255, 255, 255, 0.08);
    pointer-events: none;
    z-index: 25;
    transition: border-color 0.3s ease;
  }

  .album-book:hover {
    box-shadow:
      0 50px 120px rgba(0, 0, 0, 0.9),
      0 0 80px rgba(var(--theme-accentRGB), 0.2),
      inset 0 1px 0 rgba(255, 255, 255, 0.08);
    transform: translateY(-4px);
  }

  .album-book::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 40px;
    transform: translateX(-50%);
    z-index: 20;
    pointer-events: none;
    background: linear-gradient(to right,
        rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.15) 30%,
        rgba(255, 255, 255, 0.03) 50%,
        rgba(0, 0, 0, 0.15) 70%, rgba(0, 0, 0, 0.6) 100%);
  }

  .album-book::before {
    content: '';
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 50%;
    width: 1px;
    transform: translateX(-0.5px);
    z-index: 21;
    pointer-events: none;
    background: linear-gradient(to bottom,
        transparent 0%, rgba(var(--theme-accentRGB), 0.3) 20%,
        rgba(var(--theme-accentRGB), 0.5) 50%,
        rgba(var(--theme-accentRGB), 0.3) 80%, transparent 100%);
  }

  .album-page {
    flex: 1;
    min-height: 520px;
    position: relative;
    overflow: hidden;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .album-page img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .album-page:hover img {
    transform: scale(1.03);
  }

  .album-page-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(7, 9, 14, 0.9) 0%, rgba(7, 9, 14, 0.3) 25%, transparent 50%);
  }

  .page-turning {
    animation: pageTurn3D 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
  }

  @keyframes pageTurn3D {
    0% {
      opacity: 0.2;
      transform: scale(0.95) rotateY(8deg);
    }

    50% {
      opacity: 0.8;
      transform: scale(0.98) rotateY(-2deg);
    }

    100% {
      opacity: 1;
      transform: scale(1) rotateY(0deg);
    }
  }

  .chapter-btn-active {
    background: var(--theme-accent) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 20px rgba(var(--theme-accentRGB), 0.4);
  }

  .thumb-strip {
    display: flex;
    gap: 10px;
    padding: 8px 0;
    overflow-x: auto;
    scroll-behavior: smooth;
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  .thumb-strip::-webkit-scrollbar {
    display: none;
  }

  .thumb-card {
    flex-shrink: 0;
    width: 120px;
    height: 68px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    border: 2px solid transparent;
    opacity: 0.5;
    transition: all 0.3s ease;
  }

  .thumb-card.active,
  .thumb-card:hover {
    opacity: 1;
    border-color: var(--theme-accent);
    transform: scale(1.02);
  }

  .thumb-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .thumb-label {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: bold;
  }

  .spread-dots {
    display: flex;
    gap: 6px;
    align-items: center;
    justify-content: center;
  }

  .spread-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .spread-dot.active {
    width: 20px;
    border-radius: 10px;
    background: var(--theme-accent);
  }

  .kbd {
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
    font-family: monospace;
    font-size: 9px;
  }

  /* Light mode theme remapping overrides for digital-album indicators and buttons */
  html.theme-light .spread-dot {
    background: rgba(0, 0, 0, 0.15) !important;
  }

  html.theme-light .kbd {
    background: rgba(0, 0, 0, 0.04) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    color: #475569 !important;
  }

  html.theme-light .bg-slate-800\/80 {
    background: rgba(0, 0, 0, 0.03) !important;
    color: #0f172a !important;
  }

  html.theme-light .bg-slate-800\/80:hover {
    background: rgba(0, 0, 0, 0.06) !important;
  }

  html.theme-light .bg-slate-800\/80.text-amber-400 {
    color: #d97706 !important;
  }

  html.theme-light .bg-slate-800\/80.text-\[var\(--theme-accent\)\] {
    color: var(--theme-accent) !important;
  }

  /* Force white text on image overlays in light mode */
  html.theme-light h3.text-white-force {
    color: #ffffff !important;
  }

  /* Adaptive light mode overrides for album book card frame */
  html.theme-light .album-book {
    background: #ffffff !important;
    box-shadow: 
      0 40px 100px rgba(0, 0, 0, 0.08), 
      0 0 60px rgba(var(--theme-accentRGB), 0.06), 
      inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
  }

  html.theme-light .album-border-overlay {
    border: 2px solid rgba(0, 0, 0, 0.12) !important;
  }

  html.theme-light .album-book:hover {
    box-shadow:
      0 50px 120px rgba(0, 0, 0, 0.12),
      0 0 80px rgba(var(--theme-accentRGB), 0.1),
      inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
  }

  /* Fullscreen overlay light mode overrides */
  html.theme-light .album-fullscreen {
    background: rgba(248, 250, 252, 0.98) !important;
  }

  html.theme-light .album-fullscreen .bg-white\/10 {
    background: rgba(0, 0, 0, 0.05) !important;
    color: #0f172a !important;
  }

  html.theme-light .album-fullscreen .bg-white\/10:hover {
    background: rgba(0, 0, 0, 0.08) !important;
  }

  html.theme-light .album-fullscreen .bg-slate-900\/40 {
    background: rgba(255, 255, 255, 0.75) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    color: #0f172a !important;
  }

  html.theme-light .album-fullscreen .bg-slate-900\/40:hover {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #0f172a !important;
  }

  html.theme-light .album-fullscreen .text-white\/80 {
    color: #475569 !important;
  }

  html.theme-light .album-fullscreen .hover\:text-white:hover {
    color: #0f172a !important;
  }

  .album-fullscreen {
    position: fixed;
    inset: 0;
    background: rgba(3, 4, 7, 0.98);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.4s ease;
  }

  .album-fullscreen.active {
    opacity: 1;
    pointer-events: auto;
  }

  .stat-bar {
    width: 100%;
    height: 6px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    overflow: hidden;
  }

  .stat-bar-fill {
    height: 100%;
    border-radius: 999px;
    background: var(--theme-accent);
  }

  @media (max-width: 768px) {
    .album-page {
      min-height: 280px;
    }

    .album-book::after {
      width: 20px;
    }

    .thumb-card {
      width: 100px;
      height: 56px;
    }
  }
</style>

<!-- ══════ FULLSCREEN ALBUM OVERLAY ══════ -->
<div id="album-fullscreen" class="album-fullscreen">
  <!-- Circular frosted glass exit close button -->
  <button onclick="obmExitFullscreen()"
    class="absolute top-6 right-6 p-3.5 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/10 text-white/80 hover:text-white hover:scale-110 hover:bg-slate-900/60 transition-all duration-300 z-50 flex items-center justify-center shadow-lg hover:shadow-cyan-500/10 group"
    title="Exit Fullscreen (Esc)">
    <i data-lucide="x" class="w-5 h-5 transition-transform duration-300 group-hover:rotate-90"></i>
  </button>

  <!-- LEFT FLOATING ARROW -->
  <button onclick="prevSpread()" 
    class="absolute left-8 top-1/2 -translate-y-1/2 p-4 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/10 text-white/80 hover:text-white hover:scale-115 hover:bg-slate-900/60 hover:shadow-cyan-500/10 transition-all duration-300 z-50 flex items-center justify-center shadow-xl group"
    title="Previous (←)">
    <i data-lucide="chevron-left" class="w-6 h-6 transition-transform duration-300 group-hover:-translate-x-0.5"></i>
  </button>

  <!-- RIGHT FLOATING ARROW -->
  <button onclick="nextSpread()" 
    class="absolute right-8 top-1/2 -translate-y-1/2 p-4 rounded-full bg-slate-900/40 backdrop-blur-md border border-white/10 text-white/80 hover:text-white hover:scale-115 hover:bg-slate-900/60 hover:shadow-cyan-500/10 transition-all duration-300 z-50 flex items-center justify-center shadow-xl group"
    title="Next (→)">
    <i data-lucide="chevron-right" class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-0.5"></i>
  </button>

  <div class="album-book max-w-6xl" id="fs-album-book">
    <div class="album-page">
      <img id="fs-img-left" src="<?= get_config('base_path') ?>assets/wedding.jpg" alt="Album Left">
      <div class="album-page-overlay"></div>
    </div>
    <div class="album-page">
      <img id="fs-img-right" src="<?= get_config('base_path') ?>assets/drone.jpg" alt="Album Right">
      <div class="album-page-overlay"></div>
    </div>
  </div>

  <!-- Bottom glass capsule dots indicator -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-50 bg-slate-900/40 backdrop-blur-md py-2 px-4 rounded-full border border-white/10 shadow-lg">
    <div id="fs-spread-dots" class="spread-dots"></div>
  </div>
</div>

<!-- ══════ MAIN ALBUM SECTION ══════ -->
<section class="pt-32 pb-8 px-6 max-w-7xl mx-auto">
  <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10" data-reveal>
    <div class="space-y-3">
      <div class="flex items-center gap-3">
        <span class="badge badge-cyan">Interactive Client Album</span>
        <span
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-400 text-[10px] font-bold uppercase">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Preview
        </span>
      </div>
      <h1 class="text-3xl md:text-5xl font-black text-white font-['Outfit'] leading-tight">
        Your Wedding <span class="grad-cyan">Album</span>
      </h1>
      <p class="text-sm text-slate-400 max-w-xl">
        Experience every moment in a luxury double-page flipbook. Navigate with keyboard arrows, zoom into details, or
        go fullscreen for the cinematic view.
      </p>
      <div class="flex items-center gap-4 pt-1">
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
          <i data-lucide="user" class="w-3.5 h-3.5"></i>
          <span>Client: <strong class="text-white">Vikram & Ananya</strong></span>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
          <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
          <span>Event: <strong class="text-white">Dec 2025</strong></span>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
          <i data-lucide="lock" class="w-3.5 h-3.5"></i>
          <span>Code: <strong class="text-[var(--theme-accent)] font-mono">DEMO2026</strong></span>
        </div>
      </div>
    </div>

    <!-- Chapter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
      <?php
      $first = true;
      foreach ($albums as $alb):
        ?>
        <button onclick="switchChapter('<?= htmlspecialchars($alb['id']) ?>', this)"
          class="chapter-btn <?= $first ? 'chapter-btn-active' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700' ?> py-2.5 px-5 rounded-full text-xs transition-all flex items-center gap-2"
          id="<?= htmlspecialchars($alb['id']) ?>">
          <i data-lucide="book-open" class="w-3.5 h-3.5"></i> <?= htmlspecialchars($alb['chapter']) ?>
        </button>
        <?php
        $first = false;
      endforeach;
      ?>
    </div>
  </div>

  <!-- ALBUM SPREAD VIEWER -->
  <div class="album-viewer relative" data-reveal="scale">
    <div id="album-book" class="album-book page-turning max-w-5xl mx-auto">
      <!-- BORDER OVERLAY TO ENSURE VISIBILITY ON TOP OF IMAGES -->
      <div class="album-border-overlay"></div>

      <!-- LEFT PAGE -->
      <div class="album-page" id="page-left">
        <img id="album-img-left" src="<?= get_config('base_path') ?>assets/wedding.jpg" alt="Album Spread Left Page">
        <div class="album-page-overlay"></div>
        <div class="absolute bottom-6 left-6 z-10 space-y-1.5">
          <span id="page-num-left" class="badge badge-gold text-[9px]">Page 04</span>
          <h3 id="page-title-left" class="text-lg font-bold text-white-force font-['Outfit'] drop-shadow-lg">Sacred Fire
            Rituals</h3>
        </div>
        <button onclick="zoomPage('left')"
          class="absolute top-4 right-4 z-10 p-2 rounded-lg bg-black/40 hover:bg-black/60 text-white/70 hover:text-white transition-all opacity-0 group-hover:opacity-100"
          title="Zoom In">
          <i data-lucide="zoom-in" class="w-4 h-4"></i>
        </button>
      </div>
      <!-- RIGHT PAGE -->
      <div class="album-page" id="page-right">
        <img id="album-img-right" src="<?= get_config('base_path') ?>assets/drone.jpg" alt="Album Spread Right Page">
        <div class="album-page-overlay"></div>
        <div class="absolute bottom-6 right-6 z-10 space-y-1.5 text-right">
          <span id="page-num-right" class="badge badge-cyan text-[9px]">Page 05</span>
          <h3 id="page-title-right" class="text-lg font-bold text-white-force font-['Outfit'] drop-shadow-lg">Aerial
            Venue View</h3>
        </div>
        <button onclick="zoomPage('right')"
          class="absolute top-4 left-4 z-10 p-2 rounded-lg bg-black/40 hover:bg-black/60 text-white/70 hover:text-white transition-all opacity-0 group-hover:opacity-100"
          title="Zoom In">
          <i data-lucide="zoom-in" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- ALBUM CONTROLS TOOLBAR -->
  <div class="max-w-5xl mx-auto mt-5" data-reveal>
    <div class="glass-card p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4">
        <!-- Left: Brand Identifier (takes up 1/3 grid width on desktop to keep center perfectly aligned) -->
        <div
          class="hidden md:flex items-center gap-2 justify-start text-xs text-gray-400 font-extrabold uppercase tracking-widest">
          <i data-lucide="book-open" class="w-4.5 h-4.5 text-[var(--theme-accent)]"></i>
          <span>Digital Album</span>
        </div>

        <!-- Center: Navigation controls -->
        <div class="flex items-center justify-center gap-3">
          <button onclick="prevSpread()" class="btn-ghost py-2.5 px-4 text-xs flex items-center gap-1.5"
            title="Previous (←)">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> Prev
          </button>
          <div id="spread-dots" class="spread-dots"></div>
          <button onclick="nextSpread()" class="btn-ghost py-2.5 px-4 text-xs flex items-center gap-1.5"
            title="Next (→)">
            Next <i data-lucide="chevron-right" class="w-4 h-4"></i>
          </button>
        </div>

        <!-- Right: Action items -->
        <div class="flex items-center justify-end gap-2 w-full">
          <button onclick="toggleFavoriteSpread()"
            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 transition-all hover:scale-105"
            title="Favorite">
            <i data-lucide="star" class="w-4 h-4"></i>
          </button>
          <button onclick="addRetouchNote()"
            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-[var(--theme-accent)] transition-all hover:scale-105"
            title="Add Note">
            <i data-lucide="message-square-plus" class="w-4 h-4"></i>
          </button>
          <button onclick="obmEnterFullscreen()"
            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-white transition-all hover:scale-105"
            title="Fullscreen (F)">
            <i data-lucide="maximize-2" class="w-4 h-4"></i>
          </button>
          <button onclick="downloadSpread()" class="btn-primary py-2.5 px-5 text-xs flex items-center gap-2 shrink-0">
            <i data-lucide="download" class="w-4 h-4"></i> Download
          </button>
        </div>
      </div>
      <div class="hidden md:flex items-center justify-center gap-6 mt-3 pt-3 border-t border-white/5">
        <span class="flex items-center gap-1.5 text-[10px] text-slate-500">
          <span class="kbd">←</span><span class="kbd">→</span> Navigate
        </span>
        <span class="flex items-center gap-1.5 text-[10px] text-slate-500">
          <span class="kbd">F</span> Fullscreen
        </span>
        <span class="flex items-center gap-1.5 text-[10px] text-slate-500">
          <span class="kbd">S</span> Favorite
        </span>
        <span class="flex items-center gap-1.5 text-[10px] text-slate-500">
          <span class="kbd">Esc</span> Exit
        </span>
      </div>
    </div>
  </div>

  <!-- THUMBNAIL STRIP -->
  <div class="max-w-5xl mx-auto mt-5" data-reveal>
    <div class="glass-card p-4">
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <i data-lucide="layout-grid" class="w-4 h-4 text-[var(--theme-accent)]"></i>
          <span class="text-xs font-bold text-white uppercase tracking-wider">All Spreads</span>
        </div>
        <span id="spread-counter" class="text-[11px] font-mono text-[var(--theme-accent)]">Spread 1 of 3</span>
      </div>
      <div id="thumb-strip" class="thumb-strip"></div>
    </div>
  </div>
</section>

<!-- ══════ ALBUM STATS & INFO ══════ -->
<section class="py-12 px-6 max-w-7xl mx-auto">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-5" data-reveal>
    <!-- Album Details -->
    <div class="glass-card p-6 space-y-4">
      <div class="flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-xl bg-[var(--theme-accent)]/10 border border-[var(--theme-accent)]/20 flex items-center justify-center text-[var(--theme-accent)]">
          <i data-lucide="book-open" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="text-sm font-bold text-white">Album Details</h4>
          <p class="text-[10px] text-slate-500">Specifications</p>
        </div>
      </div>
      <div class="space-y-3">
        <div class="flex justify-between text-xs">
          <span class="text-slate-400">Album Type</span>
          <span class="text-white font-semibold">Flush Mount Luxury</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-slate-400">Page Count</span>
          <span class="text-white font-semibold">50 Pages (25 Spreads)</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-slate-400">Print Resolution</span>
          <span class="text-white font-semibold">300 DPI</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-slate-400">Cover Material</span>
          <span class="text-white font-semibold">Italian Leather + Gold Foil</span>
        </div>
        <div class="flex justify-between text-xs">
          <span class="text-slate-400">Presentation Box</span>
          <span class="text-white font-semibold">Acrylic Glass Case</span>
        </div>
      </div>
    </div>

    <!-- Retouching Notes -->
    <div class="glass-card p-6 space-y-4">
      <div class="flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-400/20 flex items-center justify-center text-purple-400">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="text-sm font-bold text-white">Retouching Notes</h4>
          <p class="text-[10px] text-slate-500">Auto-saved to cloud</p>
        </div>
      </div>
      <div class="flex gap-2">
        <input type="text" id="retouch-note-input" placeholder="e.g. Enhance background on Page 05..."
          class="flex-grow px-3 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-[var(--theme-accent)] transition-colors">
        <button onclick="saveNote()" class="btn-primary text-xs py-2.5 px-4">Save</button>
      </div>
      <div id="saved-notes" class="space-y-2 max-h-32 overflow-y-auto">
        <p class="text-[10px] text-slate-500 italic">No notes yet. Add retouching instructions above.</p>
      </div>
    </div>

    <!-- Progress -->
    <div class="glass-card p-6 space-y-4">
      <div class="flex items-center gap-3">
        <div
          class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-400/20 flex items-center justify-center text-emerald-400">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="text-sm font-bold text-white">Album Progress</h4>
          <p class="text-[10px] text-slate-500">Review & Approval</p>
        </div>
      </div>
      <div class="space-y-3">
        <div>
          <div class="flex justify-between text-xs mb-1">
            <span class="text-slate-400">Photo Selection</span>
            <span class="text-emerald-400 font-semibold">100%</span>
          </div>
          <div class="stat-bar">
            <div class="stat-bar-fill" style="width: 100%"></div>
          </div>
        </div>
        <div>
          <div class="flex justify-between text-xs mb-1">
            <span class="text-slate-400">Album Layout</span>
            <span class="text-[var(--theme-accent)] font-semibold">85%</span>
          </div>
          <div class="stat-bar">
            <div class="stat-bar-fill" style="width: 85%"></div>
          </div>
        </div>
        <div>
          <div class="flex justify-between text-xs mb-1">
            <span class="text-slate-400">Retouching</span>
            <span class="text-amber-400 font-semibold">60%</span>
          </div>
          <div class="stat-bar">
            <div class="stat-bar-fill" style="width: 60%"></div>
          </div>
        </div>
        <div>
          <div class="flex justify-between text-xs mb-1">
            <span class="text-slate-400">Print Ready</span>
            <span class="text-slate-500 font-semibold">Pending</span>
          </div>
          <div class="stat-bar">
            <div class="stat-bar-fill" style="width: 0%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  // ── ALBUM DATA ──
  const chapters = {
    'ch-wedding': [
      {
        left: '<?= get_config("base_path") ?>assets/wedding.jpg', leftTitle: 'Sacred Fire Rituals', leftNum: 'Page 04',
        right: '<?= get_config("base_path") ?>assets/drone.jpg', rightTitle: 'Aerial Venue View', rightNum: 'Page 05'
      },
      {
        left: '<?= get_config("base_path") ?>assets/pre_wedding.jpg', leftTitle: 'Golden Hour Romance', leftNum: 'Page 06',
        right: '<?= get_config("base_path") ?>assets/led_wall.jpg', rightTitle: 'Stage LED Grand Entrance', rightNum: 'Page 07'
      },
      {
        left: '<?= get_config("base_path") ?>assets/hero_bg.jpg', leftTitle: 'Cinema Studio Production', leftNum: 'Page 08',
        right: '<?= get_config("base_path") ?>assets/wedding.jpg', rightTitle: 'Candid Joyful Moments', rightNum: 'Page 09'
      }
    ],
    prewedding: [
      {
        left: '<?= get_config("base_path") ?>assets/pre_wedding.jpg', leftTitle: 'Beach Sunset Walk', leftNum: 'Page 01',
        right: '<?= get_config("base_path") ?>assets/hero_bg.jpg', rightTitle: 'Studio Dramatic Lighting', rightNum: 'Page 02'
      },
      {
        left: '<?= get_config("base_path") ?>assets/drone.jpg', leftTitle: 'Aerial Beach Panorama', leftNum: 'Page 03',
        right: '<?= get_config("base_path") ?>assets/pre_wedding.jpg', rightTitle: 'Golden Hour Embrace', rightNum: 'Page 04'
      }
    ],
    reception: [
      {
        left: '<?= get_config("base_path") ?>assets/led_wall.jpg', leftTitle: 'LED Stage Grand Entry', leftNum: 'Page 01',
        right: '<?= get_config("base_path") ?>assets/wedding.jpg', rightTitle: 'Dance Floor Celebration', rightNum: 'Page 02'
      },
      {
        left: '<?= get_config("base_path") ?>assets/hero_bg.jpg', leftTitle: 'Couple First Dance', leftNum: 'Page 03',
        right: '<?= get_config("base_path") ?>assets/drone.jpg', rightTitle: 'Venue Night Aerial', rightNum: 'Page 04'
      }
    ]
  };

  let currentChapter = 'ch-wedding';
  let currentSpreadIndex = 0;
  let isFullscreen = false;
  let savedNotes = [];

  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    const ro = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('revealed'), i * 100);
          ro.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-reveal]').forEach(el => ro.observe(el));

    buildThumbnails();
    buildDots();
    renderSpread();

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') nextSpread();
      else if (e.key === 'ArrowLeft') prevSpread();
      else if (e.key === 'f' || e.key === 'F') obmEnterFullscreen();
      else if (e.key === 'Escape') obmExitFullscreen();
      else if (e.key === 's' || e.key === 'S') {
        if (!e.ctrlKey && !e.metaKey) toggleFavoriteSpread();
      }
    });

    // Backdrop click to exit fullscreen
    const fsOverlay = document.getElementById('album-fullscreen');
    if (fsOverlay) {
      fsOverlay.addEventListener('click', (e) => {
        if (e.target.id === 'album-fullscreen') {
          obmExitFullscreen();
        }
      });
    }
  });

  function getSpreads() { return chapters[currentChapter] || chapters['ch-wedding']; }

  function renderSpread() {
    const spreads = getSpreads();
    const sp = spreads[currentSpreadIndex];
    if (!sp) return;
    const bookEl = document.getElementById('album-book');

    bookEl.classList.remove('page-turning');
    void bookEl.offsetWidth;
    bookEl.classList.add('page-turning');

    document.getElementById('album-img-left').src = sp.left;
    document.getElementById('page-title-left').textContent = sp.leftTitle;
    document.getElementById('page-num-left').textContent = sp.leftNum;
    document.getElementById('album-img-right').src = sp.right;
    document.getElementById('page-title-right').textContent = sp.rightTitle;
    document.getElementById('page-num-right').textContent = sp.rightNum;
    document.getElementById('spread-counter').textContent = `Spread ${currentSpreadIndex + 1} of ${spreads.length}`;

    if (isFullscreen) {
      document.getElementById('fs-img-left').src = sp.left;
      document.getElementById('fs-img-right').src = sp.right;
    }

    updateDots();
    updateThumbnails();
  }

  function nextSpread() {
    const spreads = getSpreads();
    if (currentSpreadIndex < spreads.length - 1) {
      currentSpreadIndex++;
      renderSpread();
    } else {
      showToast('End of Album', 'You have reached the final spread.', 'warning');
    }
  }

  function prevSpread() {
    if (currentSpreadIndex > 0) {
      currentSpreadIndex--;
      renderSpread();
    } else {
      showToast('First Page', 'You are at the beginning of the album.', 'sapphire');
    }
  }

  function goToSpread(index) {
    currentSpreadIndex = index;
    renderSpread();
  }

  function switchChapter(chapter, btn) {
    document.querySelectorAll('.chapter-btn').forEach(b => {
      b.classList.remove('chapter-btn-active');
      b.classList.add('bg-slate-800/80', 'text-slate-300');
    });
    btn.classList.remove('bg-slate-800/80', 'text-slate-300');
    btn.classList.add('chapter-btn-active');
    currentChapter = chapter;
    currentSpreadIndex = 0;
    buildThumbnails();
    buildDots();
    renderSpread();
    showToast('Chapter Loaded', `Viewing: ${btn.innerText.trim()} collection`, 'gold');
  }

  function buildThumbnails() {
    const spreads = getSpreads();
    const strip = document.getElementById('thumb-strip');
    if (!strip) return;
    strip.innerHTML = '';
    spreads.forEach((sp, i) => {
      const card = document.createElement('div');
      card.className = `thumb-card${i === currentSpreadIndex ? ' active' : ''}`;
      card.onclick = () => goToSpread(i);
      card.innerHTML = `
        <img src="${sp.left}" alt="Spread ${i + 1}" onerror="this.style.background='#1e293b'">
        <div class="thumb-label">Spread ${i + 1}</div>
      `;
      strip.appendChild(card);
    });
  }

  function updateThumbnails() {
    document.querySelectorAll('.thumb-card').forEach((c, i) => {
      c.classList.toggle('active', i === currentSpreadIndex);
    });
  }

  function buildDots() {
    const spreads = getSpreads();
    const dotsEl = document.getElementById('spread-dots');
    const fsDots = document.getElementById('fs-spread-dots');
    if (!dotsEl || !fsDots) return;
    [dotsEl, fsDots].forEach(container => {
      container.innerHTML = '';
      spreads.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = `spread-dot${i === currentSpreadIndex ? ' active' : ''}`;
        dot.onclick = () => goToSpread(i);
        container.appendChild(dot);
      });
    });
  }

  function updateDots() {
    const spreads = getSpreads();
    document.querySelectorAll('.spread-dot').forEach((d, i) => {
      d.classList.toggle('active', (i % spreads.length) === currentSpreadIndex);
    });
  }

  function obmEnterFullscreen() {
    isFullscreen = true;
    const sp = getSpreads()[currentSpreadIndex];
    document.getElementById('fs-img-left').src = sp.left;
    document.getElementById('fs-img-right').src = sp.right;
    document.getElementById('album-fullscreen').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function obmExitFullscreen() {
    isFullscreen = false;
    document.getElementById('album-fullscreen').classList.remove('active');
    document.body.style.overflow = '';
  }

  function zoomPage(side) {
    const imgId = side === 'left' ? 'album-img-left' : 'album-img-right';
    const img = document.getElementById(imgId);
    window.open(img.src, '_blank');
    showToast('Full Resolution', 'Opening image in full resolution...', 'sapphire');
  }

  function toggleFavoriteSpread() {
    showToast('Added to Favorites!', `Spread ${currentSpreadIndex + 1} marked for album cover highlight.`, 'gold');
  }

  function addRetouchNote() {
    const input = document.getElementById('retouch-note-input');
    if (input) input.focus();
    showToast('Note Mode', 'Type your retouching instructions and click Save.', 'purple');
  }

  function saveNote() {
    const input = document.getElementById('retouch-note-input');
    const val = input.value.trim();
    if (!val) { showToast('Empty Note', 'Please type a note before saving.', 'warning'); return; }

    savedNotes.push({ spread: currentSpreadIndex + 1, text: val, time: new Date().toLocaleTimeString() });
    input.value = '';

    const container = document.getElementById('saved-notes');
    if (container) {
      container.innerHTML = savedNotes.map(n => `
        <div class="flex items-start gap-2 p-2 rounded-lg bg-slate-900/50 border border-slate-800">
          <span class="badge badge-purple text-[8px] flex-shrink-0 mt-0.5">S${n.spread}</span>
          <div>
            <p class="text-[11px] text-white">${n.text}</p>
            <p class="text-[9px] text-slate-500 mt-0.5">${n.time}</p>
          </div>
        </div>
      `).join('');
    }

    showToast('Note Saved!', `Instruction for Spread ${currentSpreadIndex + 1} saved.`, 'success');
  }

  function downloadSpread() {
    showToast('Preparing Download', 'Generating 300 DPI album spread layout...', 'sapphire');
  }
</script>