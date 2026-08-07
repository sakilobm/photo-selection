# Changelog

All notable changes to the `obm-new-version` project will be documented in this file.

## [3.5.2] - 2026-08-07

### Fixed — Deep Contrast Light Theme Overrides & Reusable Modal Styling (`admin.php`)
- **Deep Light Theme Typography Overrides**: Mapped `html.theme-light .text-white`, `.text-slate-300`, and `.text-slate-400` to high-contrast slate shades (`#0f172a`, `#334155`, `#64748b`) across ratio cards, category breakdowns, and header titles.
- **Progress Bar Track Contrast**: Remapped progress bar container tracks (`.bg-slate-900`) in Light Mode to soft slate-gray (`#e2e8f0`).
- **Confirmation Modal Styles**: Added complete fixed-overlay modal CSS rules for `#obmModal` (`.obm-modal-overlay`, `.obm-modal-container`, `.obm-modal-backdrop`), hiding unstyled raw modal text from displaying at the bottom-left of the viewport.

## [3.5.1] - 2026-08-07

### Fixed — Admin Dashboard Layout Wrapper, Bimodal Theme Adaptivity, and PHP Warnings (`admin.php`, `_masterForAdmin.php`, `admin/_head.php`)
- **Document Shell Integration**: Modified `_templates/admin.php` to define the standard HTML document shell and wrapped the view layout inside proper `<head>` and `<body>` structures.
- **Enabled Header Engines**: Loaded Tailwind CSS, Lucide Icons, Google Font 'Outfit', and the global `theme.js` script inside `admin/_head.php` and `_templates/admin.php` to restore layout grids and icons on the dashboard.
- **Removed Layout Hardcoding**: Removed the hardcoded `.dark-mode` class inside `_masterForAdmin.php` to allow bimodal theme changes, defaulting to Light Mode (white theme).
- **Added Bimodal Style Overrides**: Embedded comprehensive light mode CSS overrides inside the local style block in `_templates/admin.php` to correctly style top navbar elements, hero cards, tab switches, KPI cards, and custom user input fields under the white theme.
- **Resolved PHP Syntax Warning**: Corrected Javascript `Math.round` calls to PHP standard `round()` function at line 574 of `_templates/admin.php` to resolve undefined constant parser warnings.

## [3.5.0] - 2026-08-07

### Fixed — Assets 404 Resolution, Multi-Device Favicon Meta Tags, and Graceful Fingerprint Failures
- **Resolved toastv3.css 404**: Created the missing `assets/css/toastv3.css` stylesheet file populated with core toast styling and progression animation declarations to resolve HTTP 404 failures on auth pages.
- **Resolved favicon.ico 404**: Copied `favicon.ico` from the assets folder directly to the web root `htdocs/` directory to resolve browser-native favicon lookups.
- **Favicon Meta Tags Integration**: Injected responsive apple-touch-icon, safari-pinned-tab, and android manifest link tags inside `_head.php`, `admin/_head.php`, `login.php`, and `signup.php`.
- **Safe Dynamic Fingerprint Loading**: Wrapped FingerprintJS loading in `login.php` using a dynamic import block enclosed in a `try-catch` scope to prevent console crashes when tracking blockers block the external script.

## [3.4.9] - 2026-08-07

### Fixed — Toast Notification Rendering Engine & Layout Displacement (`toastv3.js`, `_toastv3.php`)
- **Fixed Container Positioning**: Updated the toast container class in `_toastv3.php` to use `.toast-container` and `.toast-pos-bottom-right` instead of `.toast-panel` to ensure it is fixed-positioned and doesn't push the document layout at the bottom of the page.
- **Rewrote JS Toast Engine**: Updated `toastv3.js` to construct the exact modern HTML toast variant markup styled in `styles.css`. Integrated Lucide icon packs and handled CSS transition overrides (`.toast-show` / `.toast-hide`).
- **Smart Parameter Switcher**: Added a smart argument format detector to `toastv3.js` so it automatically supports both `(type, title, message)` and `(title, message, type)` layout signatures.

## [3.4.8] - 2026-08-07

### Changed — Premium Cinematic Overlays for Fullscreen Showcase (`digital-album.php`)
- **Cinematic Floating Navigation**: Replaced bottom navigation buttons inside the fullscreen view with large, floating, frosted glass circle buttons (`bg-slate-900/40`) absolute-positioned on the left/right screen edges with hover sliding transitions.
- **Glass Close Badge**: Upgraded the top-right close control to a circular frosted close button with a 90-degree hover rotation animation on the `X` icon.
- **Capsule Dots Indicator**: Wrapped the bottom spread indicators inside a floating glass capsule badge.
- **Light Theme Compatibility**: Added color-remapping rules to switch the glass overlays to white frosted glass (`rgba(255,255,255,0.75)`) with slate arrows and borders under Light Mode.

## [3.4.7] - 2026-08-07

### Fixed — Specificity Conflict in HTML Inline Event Scopes & Fullscreen Exit (`digital-album.php`)
- **Resolved Naming Collision**: Renamed `enterFullscreen` and `exitFullscreen` to `obmEnterFullscreen` and `obmExitFullscreen` to prevent browser inline event scopes from evaluating `exitFullscreen()` to `document.exitFullscreen()` (which fails when not in browser native fullscreen).
- **Backdrop Click Event**: Attached an explicit click listener to the fullscreen overlay container background to close/exit the showcase view when clicked.

## [3.4.6] - 2026-08-07

### Fixed — Light Mode Overrides for Fullscreen Album Overlay (`digital-album.php`)
- **Theme-Adaptive Fullscreen Backdrop**: Added overrides under `html.theme-light` to convert the fullscreen backdrop (`.album-fullscreen`) from solid black (`rgba(3, 4, 7, 0.98)`) to light-themed frosted slate (`rgba(248, 250, 252, 0.98)`).
- **Light Mode Navigation Buttons**: Remapped utility controls (Exit, Prev, Next buttons) inside the fullscreen overlay to render as dark-glassmorphic buttons (`bg-black/5` with charcoal text) instead of white overlays when Light Mode is active.

## [3.4.5] - 2026-08-07

### Fixed — Stacking Context Border Overlay for Album Cover (`digital-album.php`)
- **Stretched Border Overlay**: Inserted an absolute `.album-border-overlay` inside the album book wrapper. Since the child images have relative positioning and would overlap container borders, mapping the border to a high-z-index (`z-index: 25`) pointer-events-free overlay ensures the border is drawn clearly on top of the photographs in both themes.

## [3.4.4] - 2026-08-07

### Fixed — Explicit Visible Border for Adaptive Album Frame (`digital-album.php`)
- **Explicit Border Declaration**: Updated `.album-book` light-theme override to use `border: 2px solid rgba(0, 0, 0, 0.12) !important`, rendering a clearly visible, premium outline that distinguishes the white album card from the light theme page body.

## [3.4.3] - 2026-08-07

### Changed — Adaptive Album Book Frame Theme Overrides (`digital-album.php`)
- **Theme-Adaptive Album Frame**: Added light mode style overrides for `.album-book` and `.album-book:hover`, switching the book container background to solid white, outline border to soft gray, and shadows to a subtle dark tint. This ensures the frame transitions cleanly across light/dark themes.

## [3.4.2] - 2026-08-07

### Fixed — Typography Exclusion Filter for Force White Elements (`styles.css`)
- **Exclusion Filters**: Appended `:not(.text-white-force)` to the light mode heading and text-white remapping rules in `styles.css`, completely protecting force-white elements from being targeted by global color overrides.

## [3.4.1] - 2026-08-07

### Fixed — Specificity Conflict Resolution for Overlay Titles (`photo-selection.css`, `digital-album.php`)
- **Overrode Title Selector Specificity**: Defined high-specificity selectors for `.text-white-force` (`html.theme-light h3.text-white-force`) globally and locally, forcing overlays on top of dark photo layouts to remain bright white under Light Mode.

## [3.4.0] - 2026-08-07

### Fixed — Digital Album Mathematical Grid Centering & Text Contrast Overrides (`digital-album.php`)
- **True Mathematical Grid Centering**: Refactored the toolbar row layout into a CSS Grid (`grid-cols-3` on desktop), ensuring that the navigation block (Prev, Dots, Next) is mathematically placed in the exact center of the page.
- **Title Overlay Contrast Override**: Changed the `.text-white` utility on left/right page title overlay tags (`#page-title-left` and `#page-title-right`) to `.text-white-force` to prevent the labels from remapping to dark slate in Light Mode, keeping them readable on top of the photographs.

## [3.3.9] - 2026-08-07

### Changed — Digital Album Navigation Toolbar Alignments & Light Mode Styles (`digital-album.php`)
- **Center-Aligned Navigation Controls**: Centered the navigation block (Prev, Dots, Next) inside the toolbar by structuring the elements in a three-column layout (Left: branding label, Center: navigation block, Right: action buttons).
- **Light Theme Styles**: Remapped inactive dots (`.spread-dot`) to soft charcoal overlays, keyboard badge classes (`.kbd`) to light-bordered badges, and action buttons (`bg-slate-800/80`) to translucent glass controls under light mode.

## [3.3.8] - 2026-08-07

### Fixed — Light Mode Color Contrast Overrides (`photo-selection.css`)
- **Remapped Overlay Classes**: Added CSS overrides for `.bg-black/40` and `.bg-black/50` under `html.theme-light`, translating dark glass overlays to soft translucent pearl gray layers.
- **Contrast Typography Remappings**: Remapped `.text-gray-500` under light mode to guarantee legible color contrast hierarchy across the workspace lifecycle timeline tracking lists.

## [3.3.7] - 2026-08-07

### Changed — Premium Empty Workspace Aesthetic Overhaul (`photo-selection.php`)
- **Luxury UI Polishing**: Restyled the empty workspace view with glassmorphism overlays, radial glows behind content zones, micro-animations (bounce loaders, pulse indicators, and hover displacements), and an interactive status tracker timeline depicting workspace milestones.

## [3.3.6] - 2026-08-07

### Added — Premium Empty Workspace Placeholder State (`photo-selection.php`, `photo-selection.js`)
- **Empty Workspace Handler**: Designed and integrated a styled empty state card (`#emptyWorkspaceState`) with Lucide status indicators, info guides, support email redirections, and logout controls.
- **Dynamic Layout Visibility Toggles**: Added the `checkWorkspaceEmpty` helper in the selection UI to dynamically suppress all dashboards, search bars, category sliders, action panels, and photo grids when a client logs into a newly allocated workspace that has 0 assigned photos.

## [3.3.5] - 2026-08-07

### Changed — Zero-Photo Workspace Restoration Fix (`photo-selection.js`)
- **Completion-Based Loader Bypassing**: Replaced the unsafe `apiPhotos.length > 0` condition in the session restoration wait loops with an explicit API request completion flag (`fetchDone`), preventing the client portal view from hanging indefinitely and rendering a blank screen when restoring workspaces that do not contain any uploaded photos.

## [3.3.4] - 2026-08-07

### Changed — Homepage Portal Login Modal Refactoring (`_master.php`, `_footer.php`, `portfolio.js`)
- **Email Input Integration**: Added the "Email Address" field to the homepage's client portal login modal form in the page footer, aligning credential inputs with the new backend validation standards.
- **Autofill and Submit Synchronization**: Updated the homepage modal scripts to collect both the email and passcode parameters, querying the live backend validator. Configured the "Auto-fill DEMO2026" testing shortcut to populate the mock credentials for both inputs.
- **Redundant Mock Handler Removal**: Removed the mock client-side login form event listener from `portfolio.js` to prevent conflicts and double-firing alongside the unified master layout submit listener.

## [3.3.3] - 2026-08-07

### Changed — Dual-Factor Authentication Validation (`client_login.php`, `photo-selection.js`)
- **Required Email Matching**: Updated the client login handler API to require and validate both the passcode (`code`) and client email address (`email`), matching the input email case-insensitively with the registered database record, resolving the discrepancy where the UI email input field was not validated.

## [3.3.2] - 2026-08-07

### Changed — Light Theme Confirmation Dialog Color Fix (`photo-selection.css`)
- **Corrected Modal Card Class Selector**: Replaced the mismatched `.obm-modal-card` selector override with `.obm-modal-container` inside the light-theme CSS blocks, allowing the reusable confirmation dialog's background to correctly render as white/translucent in light mode rather than remaining stuck as dark charcoal.

## [3.3.1] - 2026-08-07

### Removed — Client Self-Registration Flow (`photo-selection.php`, `photo-selection.js`, `client_signup.php`)
- **Self-Signup Elimination**: Removed switcher tabs, name fields, and self-registration UI elements from the portal entry view to ensure that only administrators can configure workspace slots and allocate client credentials.
- **Simplified Dispatcher**: Cleaned up the frontend javascript state variables and handlers (`currentAuthMode`, `switchAuthTab`), focusing the submit button purely on login passcode validation with async toast messages.
- **Removed Stub Controller**: Deleted `client_signup.php` to prevent unauthenticated client insertions into the database.

## [3.3.0] - 2026-08-07

### Added — Public Client Workspace Registration Endpoint (`client_signup.php`)
- **Public Signup API Controller**: Created the endpoint `htdocs/libs/api/auth/client_signup.php` which allows new clients to register their name, email, and desired passcode to request a new selection portal database record without requiring administrator authorization.

### Changed — Client Authentication Toast Feedback (`photo-selection.js`)
- **Login Toast Messages**: Integrated dynamic feedback triggers inside the login handler to show connecting status indicators ("Validating access passcode...") and detailed success/failure toast cards based on API outcomes.
- **Signup Request Form Routing**: Programmed the client portal registration tab to query the public `/api/auth/client_signup` endpoint upon form submission, resetting fields and auto-switching back to the login tab upon successful registrations.

## [3.2.7] - 2026-08-07

### Changed — High-Resolution Image Loading in Lightbox (`photo-selection.php`, `photo-selection.js`)
- **Dynamic HD Image Swapping**: Created a `getHighResUrl` utility inside `photo-selection.js` to dynamically swap the source URL's thumbnail width parameter `w=500` with `w=1600` on the fly when loading image previews inside the lightbox.
- **Fluid Layout Stretch Enforcement**: Added `w-full` class to `#lightboxImage` to ensure the image tag scales up fluidly to match the container width, allowing wide landscape captures to fill the screen landscape area in full resolution.

## [3.2.6] - 2026-08-07

### Changed — Lightbox Preview Dimensions Upgrade (`photo-selection.php`)
- **Expanded Lightbox Image View**: Increased the preview image maximum dimensions from `max-w-4xl` (896px width) and `max-h-[75vh]` (75% viewport height) to `max-w-[85vw]` (85% viewport width) and `max-h-[80vh]` (80% viewport height), allowing high-resolution landscape and couple portrait captures to display larger and clearer on widescreen displays.

## [3.2.5] - 2026-08-06

### Changed — Contrast & Accessibility Tweak (`photo-selection.php`)
- **Workspace Finalize Contrast Correction**: Replaced standard `text-white` with the custom specificity-preserving `text-white-force` class on both the workspace gallery header "Finalize" counter button and the lightbox overlay preview "Finalize" action button, ensuring text and icons remain visible white in light mode templates.

## [3.2.4] - 2026-08-06

### Changed — Dynamic Navigation Bar Replacement & Redirects (`photo-selection.php`, `photo-selection.js`, `photo-selection.css`)
- **Main Navbar Suppression inside Workspace**: Programmed the client workspace state toggles to dynamically add/remove a `portal-active` state class on the HTML document element. Configured CSS layout rules to automatically hide the top floating website navigation bar inside the portal workspace, resolving stacked-header visual clutter.
- **Home Navigation Shortcut**: Inserted a clean "Return to Website" home icon link inside the client portal navigation header, allowing authenticated clients to easily navigate back to the main site portfolio pages.

## [3.2.3] - 2026-08-06

### Changed — Smart Explicit Login Loader Routing (`photo-selection.js`)
- **Explicit Login Loader Action**: Modified workspace initialization parameters to ignore the session cache flag and force display the loader screen whenever a user explicitly submits the login passcode form, maintaining the progressive transition experience.
- **Smart Session Recovery Bypass**: Preserved loader-bypass cache on direct page reloads and state restoration, preventing repetitive and annoying animations for already-connected sessions.
- **Session Reset on Logout**: Added cleanup triggers that remove the `obm_portal_analyzed` session cache flag when users click logout, ensuring the loader starts fresh for new client sessions.

## [3.2.2] - 2026-08-06

### Changed — Contrast & Accessibility Upgrades (`photo-selection.php`, `photo-selection.js`, `photo-selection.css`)
- **Active UI Contrast Restorations**: Changed the text color on active switcher tabs and primary action buttons (Unlock My Gallery, Create Client Portal, Finalize Selections) from black to white to maximize readability against the theme accent color backgrounds.
- **Input Icon Focus Preservation**: Added `z-10` and `pointer-events-none` styling properties to input field absolute icons, preventing them from being visually obscured by opaque white backgrounds when inputs are focused.

## [3.2.1] - 2026-08-05

### Fixed — Client Photo Schema Mappings & Selections Saving (`get_client_photos.php`, `finalize_selections.php`)
- **JSON Field Schema Corrections**: Mapped the database columns `filename` and `thumb_url` to JS model properties `name` and `url` inside `get_client_photos.php`, resolving empty values that caused image loading issues.
- **Selection State Persistence**: Mapped selection queries to the correct database column `selection_status` (setting/checking values `'APPROVED'` and `'PENDING'`) instead of the non-existent `selected` column inside both API controllers, ensuring client selections save successfully.

## [3.2.0] - 2026-08-05

### Added
- **Debugging & Learning Journal**: Created [docs/DEBUGGING_JOURNAL.md](file:///var/www/html/obm-new-version/docs/DEBUGGING_JOURNAL.md) in the workspace directory to serve as a study resource, documenting CLI commands, curl options, and testing routines.

### Fixed — API Authorization Access & Client Login Issues (`api.php`)
- **Authorized Client APIs**: Updated the API middleware layer in `api.php` to enable active client sessions (with valid passcode validations) to access endpoints under the `photos` namespace, fixing the 401 Unauthorized block that broke client portal workspace lookups.

### Changed — Smart Accelerated Loading Screens (`photo-selection.js`)
- **First-Time Loader Acceleration**: Sped up the workspace initialization progress ticks 5x (running in ~400ms total) to load resources quickly without slowing down page navigation.
- **Session Analysis Bypass**: Implemented session storage cached status checks (`sessionStorage.getItem('obm_portal_analyzed')`) to completely skip loading animations on subsequent page reloads or navigations, rendering the workspace instantly.

## [3.1.3] - 2026-08-04

### Changed — Smooth Scrolling & Active State Observer Updates (`portfolio.js`)
- **Seamless Local Navigation**: Created custom anchor click interceptors inside `portfolio.js` that capture navigation clicks on local section anchors (Story, Services, Portfolio) and trigger smooth animation scroll transitions instead of loading page redirects.
- **Dynamic Scroll Spy Active Highlighting**: Rebuilt navigation scroll spy logic to resolve target selectors using partial attributes (`href*="#"`), fixing active visual highlights that were broken by clean routing URL configurations.
- **Page Load Section Transitions**: Added page load checking routines that smoothly scroll a visitor to targeted hashes after initial content painting, delivering high-end entrance animations when moving between separate subpages.

## [3.1.2] - 2026-08-04

### Fixed — Homepage Button Style Conflicts (`_templates/core/_head.php`)
- **Resolved Design System Override**: Excluded the `index.php` template from automatic dynamic stylesheet loading in `core/_head.php`. This prevents the legacy `assets/css/index.css` design system rules from implicitly overriding `.btn-primary` with orange rectangular colors, restoring the luxury round pill aesthetic and reactive theme accent properties on the homepage.

## [3.1.1] - 2026-08-04

### Changed — Dynamic Client Session UI Adaptations (`_templates/index.php`)
- **Dynamic Homepage CTA buttons**: Programmed PHP session checks inside `_templates/index.php` so that all call-to-action buttons (Hero, Services list, Footer) dynamically render as direct link targets (`Go to Selection Workspace`) if the client has already signed in, eliminating redundant login prompts.

## [3.1.0] - 2026-08-04

### Changed — Professional Extensionless Routing & Canonical Redirections (`.htaccess`, `portfolio.js`)
- **Canonical Address Bar Redirects**: Configured Apache `.htaccess` rewrite conditions using raw HTTP browser headers (`%{THE_REQUEST}`) to redirect requests with `.html` or `.php` extensions to their clean, extensionless counterparts (e.g., `/packages.html` -> `/packages`).
- **Clean Index Redirects**: Added rules to redirect `/index`, `/index.php`, and `/index.html` to the clean root path `/`.
- **Client redirection fix**: Updated client portal redirection inside `portfolio.js` to dispatch visitors to `/photo-selection` instead of the static `photo-selection.html` page.

### Removed
- **Static Homepage**: Deleted `index.html` in the document root to guarantee Apache defaults to processing the dynamic `index.php` controller.

## [3.0.0] - 2026-08-04

### Added — Private Administrator Authentication & Database Command Center (`admin.php`, `_templates/admin.php`, `admin-store.js`)
- **Session Authentication Security**: Secured the Studio Command Center using native Aether framework `Session::ensureLogin()`. Unauthorized access triggers automatic redirects to the standard admin login screen.
- **Dynamic Database Synchronization**: Integrated the admin panel metrics, package rates, live client directory list, and selection tracker tables to fetch data directly from MySQL database tables (`packages`, `client_portals`, `client_photos`, `live_event`) instead of client-side local mocks.
- **Secure Logout Routing**: Implemented administrative session destruction by processing request flags (`/admin?logout=1`) to invalidate session tokens.
- **Physical Upload Dispatcher**: Replaced Simulated Upload Queue with actual multi-part physical file uploading to `/api/admin/upload_photos`. File payloads are dynamically saved into the local `htdocs/uploads/` directory, and file path references are written to the `client_photos` database table.

### Changed — Client Portal Authentication & Persistence (`photo-selection.js`, `photo-selection.php`)
- **Interactive Database Authentication**: Connected client login forms to `/api/auth/client_login` API endpoints for real event code validations, populating $_SESSION client parameters on success.
- **Live Selections persistence**: Converted selections submission handler in `photo-selection.js` to dispatch selections transactionally to `/api/photos/finalize_selections` instead of `localStorage`.
- **Database Selection Loading**: Replaced mockup gallery lists with real photo entries returned by `/api/photos/get_client_photos`.

### Removed
- **Redundant Static Assets**: Removed static `.html` files (`admin.html`, `packages.html`, `photo-selection.html`, `digital-album.html`, `live-event.html`) to clean the workspace.

## [2.4.1] - 2026-08-02

### Changed — Footer signature customization (`index.html`)
- **Tribute Signature**: Custom-tailored the footer copyright note on the index page to display a family tribute: `"Crafted with ❤️ by their sons"`.

## [2.4.0] - 2026-07-31

### Changed — Client Portal Workspace Polish & Home Page Theme Adoption (`photo-selection.html`, `photo-selection.css`, `photo-selection.js`)
- **Homepage White/Light Theme Adoption**: Redesigned the client photo selection portal to inherit the studio's premium light/white theme by default. Linked the core `styles.css` design system stylesheet and adopted the official animated aurora background mesh (`aurora-mesh`) for perfect visual parity with the home and packages pages.
- **Removed Studio Nav Header Links**: Completely removed global site links (Home, Packages, Albums, Live, Admin) from the client view header to ensure absolute visual focus on photo selection.
- **Removed Studio Dashboard Manager**: Removed the local "Studio Dashboard" management section (Overview, Deleted detection, Client directory, Disk upload panels) from the client interface, keeping it lightweight and client-facing.
- **Dynamic Light Theme Stylesheet Overrides**: Appended high-contrast and soft gradient styling overrides inside `photo-selection.css` specifically for `html.theme-light`. Includes frosted white glass panels, shadow depth adjustments, and crisp text indicators. Forces all text, buttons, and icons inside the cinematic lightbox modal (`#lightboxModal`) and overlay cards to remain bright white, while transforming the custom hover zoom cursor (`#customCursor`) into a frosted white circle with a bright blue search icon.
- **Cleaned JS State Handlers**: Removed legacy tab navigation switches and stubs in `photo-selection.js` to prevent runtime console errors from removed DOM selectors.

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
