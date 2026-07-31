# Changelog

All notable changes to the `obm-new-version` project will be documented in this file.

## [2.3.0] - 2026-07-31

### Added — Client Selection Status Tracker (`admin.html`, `photo-selection.js`)
- **New Tab — Selection Tracker**: Added a dedicated dashboard tab focused exclusively on tracking live photo selection progress.
  - **Dynamic Statistics Grid**: Displays cards showing Active Portals, Selections Completed, Average Progress %, and Pending Completion counts computed in real-time.
  - **Stage filters**: Added quick filtering pills (`All`, `Unassigned`, `Not Started`, `In Progress`, `Completed`) with live counter tags.
  - **Interactive Search**: Users can search for specific client portals by client name, email, or passcode.
  - **Visual Progress & Previews**: Displays selection completion ratio progress bars, status labels, event date metadata, and a horizontal thumbnail strip of the first 6 approved photos.
  - **Polished Card Divider**: Replaced the harsh `border-slate-800/80` dark divider line above "Approved Photo Selections Preview" with a custom `.selection-divider` class. Renders as a soft slate tint (`rgba(15, 23, 42, 0.08)`) in Light Theme and a subtle white tint (`rgba(255, 255, 255, 0.08)`) in Dark Theme to provide high-quality visual hierarchy without eye fatigue.
  - **Action Group**: Allows admins to copy approved filenames as a newline list for Lightroom/Photoshop import, trigger simulated reminders, or force-finalize selections.
- **Active Tab State Persistence**: Added automatic tab state persistence via LocalStorage (`obm_admin_active_tab`). The admin command center now remembers the last opened tab across page reloads and browser sessions, preventing reverting to the "Overview" tab by default.
- **Bi-directional Data Sync**: Integrated the client-side `photo-selection.js` engine to write choices back to the central `obm_admin_store_v1` LocalStorage database automatically during selection and final submission. Added a "Sync Portals" force sync option in the admin header.

## [2.2.0] - 2026-07-25

### Added — Studio Dashboard Full Feature Upgrade (`admin.html`, `admin-store.js`)
- **Packages & Price Tab Upgrade (Dynamic Visual Editor)**:
  - **Dynamic Card Manager**: Completely redesigned the Packages tab into a modern, responsive grid of styled editors matching each package's visual accents (gold for Gold Elite, purple for Imperial, etc.).
  - **Full Edit Capabilities**: Studio administrators can now edit the **Package Name, Price, Pill Badge, Subtitle/Description**, and dynamically manage the **Highlights list** (allowing adding/removing feature bullet points dynamically).
  - **Restore to Default**: Added a *"Reset to Defaults"* fallback option to restore the original preset studio packages at any time.
  - **Dynamic Public Integration**: Replaced the hardcoded cards in `packages.html` with a dynamic template generator reading directly from `OBMStore.data.packages`, propagating edits instantly to public visitors.
- **Visual Contrast & Light Theme Fixes**:
  - **"Upload Photos" Button**: Updated `.cm-unassigned-banner button` to solid Indigo fill (`#4f46e5`) with **crisp bold pure white text** (`color: #ffffff !important`), fixing the dull washed-out text issue in Light Theme.
  - **Navbar Hover Expanded Title Contrast**: Updated `.nav-link:hover .nav-label` and `.floating-nav-pill .nav-link:hover .nav-label` in Light Theme (Pearl White) to render expanded title text ("Albums", "Selection", "Packages", etc.) in **high-contrast dark slate** (`#0f172a !important`), fixing the white-on-white text invisibility issue.
  - **Most Popular Card Badge**: Removed `overflow: hidden` from the base `.glass-card` styling in `styles.css` and added custom high-contrast opaque background overrides for badges in Light Theme. This resolves the badge clipping issue on the package cards, making the "Most Popular" badge fully visible.
  - **Dashboard Tab Switcher & Dropdown Pre-selection**: Fixed a JavaScript error in `switchDashTab` by dynamically resolving the navbar button if none is passed. Updated `preselectUploadClient` to map the client's email to their dynamic code inside the `upload-client-select` dropdown to ensure target client email is automatically selected upon redirection.
- **Client Manager (Tab 3)** — Complete professional client directory rewrite:
  - **Unassigned Client Handling (`totalPhotos === 0`)**:
    - **Single Action Button (Delete Only)**: Newly registered clients with 0 allocated photos hide Download, Flag, and Block action buttons, showing **ONLY the Delete button** (`trash-2`).
    - **UNASSIGNED Badge**: Displays cool indigo/slate `UNASSIGNED` pill badge.
    - **Upload Hint & Shortcut Banner**: Displays a hint box inside the card with a direct *"Upload Photos"* button that switches to Tab 8 (Upload & Send) with that client preselected.
    - **Unassigned Filter Pill**: Added `Unassigned (0)` pill to the Status Quick-Filter Bar.
    - **Auto-Unlock**: Uploading photos to the client automatically unlocks full action buttons (Download, Flag, Block, Delete).
  - **Visual Status Differentiation for Client Cards**:
    - **Completed**: Rich Emerald Green scheme (`background: #d1fae5` in Light theme, 6px emerald left border `#10b981`, mint green gradient & ambient glow)
    - **Flagged**: Warm amber left accent bar (`border-left: 5px solid #f59e0b`) & golden glass glow with high-contrast info banner
    - **Pending**: Sky blue left accent bar (`border-left: 5px solid #0284c7`) & subtle cyan glass backdrop
    - **Blocked**: Rose red left accent bar (`border-left: 5px solid #f43f5e`) & red tinted overlay
  - **Light Theme (Pearl White) Visual Detail & Contrast Overrides**:
    - Fixed washed-out left border bars in Light Theme by applying explicit 5px `border-left` status overrides and soft status ambient glows (`box-shadow: 0 12px 35px rgba(..., 0.18)`).
    - Fixed unreadable yellow info text in Light Theme by targeting `.cm-flagged-banner` with high-contrast dark amber text (`#78350f`), amber icon (`#d97706`), and soft yellow fill (`rgba(254, 243, 199, 0.95)`).
    - Fixed low-contrast email & meta counts in Light Theme (`.cm-client-email`, `.cm-client-meta`) using crisp dark slate (`#475569` and `#334155`).
  - **Status Quick-Filter Bar**: Interactive status pills (`All`, `Unassigned`, `Flagged`, `Completed`, `Pending`, `Blocked`) with dynamic live count indicators
  - Avatar circle with gradient color derived from client name hash
  - Status badges: `UNASSIGNED`, `COMPLETED` (green), `FLAGGED` (yellow), `PENDING` (amber), `BLOCKED` (red)
  - Quick Register Client inline form (name + email)
  - Action buttons per client: **Download** (ZIP archive), **Flag** (toggle completion), **Block** (toggle access), **Delete** (with confirmation)
  - Stats per client: photos allocated, photos selected, registration date
- **Deleted Detection (Tab 7)** — Full approved vs rejected comparison panel:
  - Client selector dropdown with selection counts
  - Split-panel layout: Approved/Selected (left) vs Rejected/Not Selected (right)
  - Photo items with thumbnails, filenames, and category labels (CANDID, PORTRAIT, TRADITIONAL)
  - Download Approved and Download Rejected buttons with toast feedback
  - Permanently Removed section with empty state: "Workspace fully intact"
  - Finalize Workspace button to flag client as completed
- **Upload & Send (Tab 8)** — Full dispatch engine:
  - Target client dropdown populated from Client Manager (excludes blocked clients)
  - Drag-and-drop zone (JPG, PNG, WEBP, PSD • Max 20 files)
  - Queued files list with filename, file size, and remove button
  - Sequential upload simulation with individual progress bars per file
  - Dispatch to Client Portal button with loading spinner animation
- **admin-store.js** — Expanded data model:
  - Client portal schema: `email`, `flag`, `blocked`, `flagged`, `addedDate`, `photos: { approved[], rejected[], deleted[] }`
  - New methods: `toggleClientFlag()`, `toggleClientBlock()`, `updateClientPortal()`, `getActiveClients()`, `getClientByCode()`
  - Deep merge on load to ensure new fields from defaults are present in persisted data

## [2.1.0] - 2026-07-24

### Added — Global Theme Engine & Full-Site Adoption
- **theme.js** — Added **Pearl White** (`white`) theme option:
  - Base background: `#f8fafc` (Light mode)
  - Toggles `.theme-light` on `document.documentElement`
  - Accent colors: Royal Blue (`#2563eb`) & Sky Blue (`#0284c7`)
- **Navigation Bar Centering, Light Theme Hover Icons & Logo Text Contrast Fix (`styles.css`)** — Resolved 3 visual bugs in the floating navigation bar:
  - **100% Centered Collapsed Icons**: Fixed icon off-center alignment in resting collapsed state by setting `.nav-link` to fixed circular pill dimensions (`width: 36px; height: 36px; padding: 0; justify-content: center; borderRadius: 50%`) when collapsed.
  - **Light Theme Hover Icon Colors**: Updated `.floating-nav-pill .nav-link:hover i` in Light Theme mode to render in high-contrast dark slate (`#0f172a`) instead of invisible white.
  - **Logo Text Light Theme Contrast**: Created `.nav-logo-text` utility class so "OBM STUDIO" renders as crisp dark slate in Pearl White mode and glowing metallic white in Dark mode.
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
