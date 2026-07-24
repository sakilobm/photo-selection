# Changelog

All notable changes to the `obm-new-version` project will be documented in this file.

## [2.1.0] - 2026-07-24

### Added — Global Theme Engine & Full-Site Adoption
- **theme.js** — New global theme persistence engine
  - 5 themes: Sapphire Ice, Amethyst Glow, Emerald Forest, Rose Quartz, Amber Sunset
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
- **index.html** — Added `theme.js` before `styles.css`; replaced portfolio theme switcher in nav with global `OBMTheme.set()` theme switcher pill
- **packages.html** — Full rewrite: unified aurora nav, global theme switcher in nav, aurora background blobs, consistent glass-card system, scroll-reveal, all buttons use CSS var accent
- **digital-album.html** — Full rewrite: unified aurora nav, global theme switcher, chapter button active state follows `--theme-accent`, spread counter color follows theme, `obmthemechange` event listener
- **photo-selection.html** — Added `theme.js` in head; `setAppTheme()` now also calls `OBMTheme.apply()` to sync CSS vars and blobs
- **photo-selection.js** — `setAppTheme()` now calls `OBMTheme.apply(themeName)` after localStorage write

### Result
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
