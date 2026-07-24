# Changelog

All notable changes to the `obm-new-version` project will be documented in this file.

## [2.1.0] - 2026-07-24

### Added — Global Theme Engine & Full-Site Adoption
- **theme.js** — Added **Pearl White** (`white`) theme option:
  - Base background: `#f8fafc` (Light mode)
  - Toggles `.theme-light` on `document.documentElement`
  - Accent colors: Royal Blue (`#2563eb`) & Sky Blue (`#0284c7`)
- **Dashboard Scroll Reveal Visibility Fix (`admin.html`)** — Added `IntersectionObserver` scroll-reveal initialization and `revealAllDashboard()` helper execution on DOM load, passcode authentication, and tab switching. Fixes hidden `opacity: 0` states on `[data-reveal]` elements so the Hero Command Banner, KPI cards, and sub-tab panels render instantly with full visibility upon unlocking the dashboard.
- **Exact Dashboard UI & Liquid Aurora Background Overhaul (`admin.html`)** — Transformed the Admin Command Center to adopt the exact glowing liquid aurora backdrop (`radial-gradient(circle at 50% -20%, #2e1065, #0f172a, #030712)`) with floating animated blobs. Added the top glass header bar (`PREMIUM PORTAL`), view switcher pill toggle (`[ 🖼️ Gallery Workspace ] [ ⚡ Studio Dashboard ]`), hero command banner card, and a scalable horizontal pill navigation bar (`[ 📊 Overview ] [ 💼 Packages & Rates ] [ 📸 Client Directory ] [ 📡 Live Broadcast ] [ 📖 Digital Albums ] [ 🎨 Story & Metrics ] [ 🔍 Deleted Detection ] [ 📁 Upload & Send ]`) engineered to scale comfortably as more pages and tools are added.
- **Toast Notifications Light Mode Fix** — Added explicit `html.theme-light .toast-item` overrides in `styles.css` so toast popups render as clean white glass boxes (`rgba(255,255,255,0.96)`) with dark slate title (`#0f172a`) and message text (`#475569`) instead of pitch black boxes.
- **Active Chapter & Tab Buttons Contrast Fix** — Updated `.chapter-btn-active` in `digital-album.html` and `styles.css` to enforce crisp white text (`#ffffff !important`) over blue gradient backgrounds. Inactive chapter buttons reset cleanly to white pills with slate borders.
- **Pearl White Light Mode Overrides** — Fixed text & contrast across light mode:
  - Active filter & primary gradient buttons now strictly enforce `#ffffff !important` text contrast.
  - Inactive filter buttons (`.filter-btn:not(.active)`) reset to clean `#f1f5f9` background with slate `#475569` text.
  - Dark slate boxes (`[class*="bg-slate-900"]`, founder quote card, calculator option cards) adapt to soft white cards with dark slate typography.
  - Custom investment bar & Gold Elite pricing card subtext adapt to warm amber `#92400e` / `#d97706` contrast.
- **HTML Body Class Cleanup** — Removed hardcoded Tailwind `bg-[#020407]` and `text-slate-100` classes from `<body>` across `index.html`, `packages.html`, `digital-album.html` so `styles.css` and `theme.js` have 100% full control over background and text contrast.
- **Nav & Theme Selectors** — Added ⚪ **Pearl White** button dot to all global theme switchers across `index.html`, `packages.html`, `digital-album.html`, and `photo-selection.html`
  - Persists selection via `localStorage` key `obm_theme` (shared with photo-selection.js)
  - Applies CSS custom properties: `--theme-accent`, `--theme-accent2`, `--theme-accentRGB`
  - Recolors aurora blobs (`.aurora-blob-1..4`) and liquid blobs on all pages dynamically
  - Dispatches `obmthemechange` CustomEvent for reactive scripts
  - `OBMTheme.set(name)` — persist + apply. `OBMTheme.get()` — read stored theme.
- **styles.css** — Theme engine CSS wiring
  - `--cyan`, `--grad-cyan`, `--gradient-card-border` now use `var(--theme-accent)` dynamically
  - `.global-theme-switcher` + `.theme-switcher-dot` shared nav component styles
  - Smooth transition on body + all aurora/liquid blobs (`0.6s ease`)
  - `.btn-primary`, `.nav-link.active`, `.badge-cyan`, `.grad-cyan` all follow `--theme-accent`

### Changed — All Pages Unified
- **index.html** — Added `theme.js` before `styles.css`; replaced section-specific portfolio theme switcher with a sleek **Production Quality & Live Stats Pill** (`4K Cinema Mastered | 1,200+ Archived Shots`) since global theme selection is handled seamlessly in the floating nav bar across all pages.
- **packages.html** — Full rewrite: unified aurora nav, global theme switcher in nav, aurora background blobs, consistent glass-card system, scroll-reveal, all buttons use CSS var accent
- **digital-album.html** — Full rewrite: unified aurora nav, global theme switcher, chapter button active state follows `--theme-accent`, spread counter color follows theme, `obmthemechange` event listener
- **photo-selection.html** — Added `theme.js` in head; `setAppTheme()` now also calls `OBMTheme.apply()` to sync CSS vars and blobs
- **photo-selection.js** — `setAppTheme()` now calls `OBMTheme.apply(themeName)` after localStorage write

### Fixed
- **theme.js** — Fixed `Uncaught TypeError: can't access property "style", document.body is null` error when loaded synchronously in `<head>` before `<body>` element is parsed by browser. Now sets `--bg-primary` on `document.documentElement` immediately and safely verifies `document.body` existence.

- Changing theme on ANY page (index, packages, album, photo-selection) persists via localStorage
- On next page load, `theme.js` auto-reads localStorage and applies theme before first paint (no flash)
- Aurora background blob colors change dynamically per theme on all pages



## [2.0.0] - 2026-07-24

### Changed (Major Redesign)
- **index.html** — Complete rewrite as a storytelling, cinematic homepage
  - Animated 4-blob aurora mesh background (fixed, full-screen)
  - Floating pill navigation bar with gradient border & scroll-spy active states
  - §1 Hero: Large cinematic headlines with scroll-reveal, stats strip with glassmorphism cards
  - §2 Our Story: Full husband-and-wife founder narrative split into 4 chapters; alternating left-right desktop timeline with animated center line & quote cards; stacked mobile version
  - §3 Services: 6 cards with distinct color-coded service banners (gold/cyan/purple/rose/emerald)
  - §4 Awards: Award cards + animated infinite scrolling marquee tag strip (section has distinct border/gradient bg)
  - §5 Portfolio: Dynamic theme switcher (5 themes: sapphire/purple/emerald/rose/amber) + category filter buttons; gallery cards with color-coded icons
  - §6 CTA: Founder quote block + CTA with gradient story text
  - Footer with links + brand icon
  - Client Login Modal with passcode UX, demo code, error display, loading state
- **styles.css** — Full rewrite with Aurora Luxury Design System v2.0
  - Scroll reveal system: `[data-reveal]` base, `left`, `right`, `scale`, `blur` variants
  - Aurora blob animations (4 blobs, alternate keyframes)
  - Section visual variance backgrounds for story, services, awards, portfolio, cta
  - Service banners (colored radial gradient tops)
  - Award cards with hover glow
  - Infinite scrolling marquee
  - Glass card gradient borders
  - Theme switcher dots
  - Full toast styling (6 themes, progress bar, all positions)
- **toast.js** — New standalone toast engine
  - 6 color themes, 6 positions, progress bar, pause-on-hover, close-on-click
- **portfolio.js** — Rewritten
  - Scroll-spy nav active states, login modal logic, gallery category filters, welcome toast

## [1.0.0] - 2026-07-24

### Added
- Created `index.html` main portfolio landing page for OBM Studio featuring:
  - Hero section showcasing studio stats, brand statement, and Client Photo Selection CTA.
  - Interactive Services & Equipment Showcase (Drone 4K, LED Wall Stage & Live Stream Setup, Traditional Photography, Wedding Photography, Candid Shoots, Pre-Wedding Video, Outdoor Shoots).
  - Filterable Portfolio Gallery with category tabs.
  - Client Portal Login Modal with access code verification (`DEMO2026` / custom event codes) linking directly to `photo-selection.html`.
- Created `styles.css` with dark luxury obsidian-sapphire theme, custom glassmorphism components, glow accents, and responsive layout system.
## [1.3.0] - 2026-07-24

### Changed
- Major UI Polish & Aesthetic Upgrade:
  - Added an animated **Aurora Cosmic Gradient Mesh** (`.aurora-mesh`) with multi-layered glowing color waves (Midnight Blue, Electric Sapphire, Cyber Purple, Gold Amber).
  - Upgraded all glass cards to feature **Shimmering Gradient Borders** (`gradient-border-card`), inner radial light flares, and luminous hover halos.
  - Enhanced Floating Navigation Bar with a dual-tone glowing gradient border and frosted blur.
  - Added animated gradient badges, glowing typography, and shimmer button effects across `index.html` and `styles.css`.

### Changed
- Redesigned `index.html` and `styles.css` UI:
  - Removed background images in favor of a sleek, dark vector mesh gradient background with ambient animated glow spheres (`#06080d`).
  - Redesigned Floating Pill Navigation Bar (`glass-nav-pill`) with blurred glassmorphism, accent borders, and smooth hover states.
  - Enhanced Hero section with gradient text highlights, glowing stat badges, and clean vector layout.
  - Polished service cards & portfolio items with refined glass borders, subtle hover lighting, and clean typography.

### Added
- Created `packages.html` featuring pricing tier cards in Indian Rupees (₹ INR):
  - **Silver Royal** (₹65,000) - Traditional & Candid Photography, Digital Selection, Printed Album.
  - **Gold Elite** (₹1,45,000) - Cinematic Videography, 4K Drone Aerials, Candid Photography & Pre-wedding Shoot.
  - **Platinum Plus** (₹2,85,000) - Full 4K Cinema Production, Drone Team, Pre-Wedding Film & Premium Leather Album.
  - **Imperial Stage & LED Production** (₹4,50,000) - Dual Drones, LED Wall Stage Displays, Live Streaming & Full Studio Crew.
- Created `digital-album.html` (Dedicated Client Digital Album Showcase & Interactive Flipbook Spreads):
  - Double-page spread viewer with album chapter filter tabs (Haldi, Sangeet, Wedding, Reception).
  - High-resolution photo zoom, album spread download, client comment notes, and favorite highlights.
- Implemented `toast.js` - Fully customizable Toast Notification Engine:
  - Interactive Toast Playground on packages & album pages (custom titles, positions, themes: Success, Warning, Error, Luxury Gold, Sapphire Blue, custom duration & progress bars).
- Updated top navigation and footer across `index.html`, `packages.html`, and `digital-album.html`.
