# Debugging & Learning Journal
## OBM Studio Catalyst Suite

This journal acts as a growing ledger of engineering problems solved, diagnostic tools used, command flags explained, and design patterns learned. It serves as a study guide and code verification handbook.

---

## [ENTRY 1] - 2026-08-05
### Concept: Isolating PHP Execution and API Verification via CLI & Curl

When testing web applications, manually logging in via a browser is slow. We can automate testing using **PHP CLI execution** (to test database functions in isolation) and **Advanced Curl Commands** (to test HTTP requests, session state, and SSL/SNI routing).

---

### Part A: Isolated PHP Code Execution (CLI)

#### Command
```bash
php -r "require 'htdocs/libs/load.php'; var_dump(\App\ClientPortal::findByCode('DEMO2026'));"
```

#### Breakdown of Options
- `php`: Invokes the Command Line Interface (CLI) version of PHP.
- `-r <code>`: Runs the specified PHP `code` directly in the terminal without needing to create a temporary `.php` file.
- `require 'htdocs/libs/load.php'`: Loads the project's autoloader, environment configuration, database bootstrapper, and global classes.
- `var_dump(...)`: Outputs the type and structure of the returned object in a readable format.

#### Why we used it
We used it to confirm that our MySQL database connection parameters were valid and that the `ClientPortal` model's query logic successfully returned records for passcode `DEMO2026` in isolation, proving the backend query logic was correct.

---

### Part B: Local HTTPS Verification with Host SNI (`curl --resolve`)

#### Command
```bash
curl --resolve obmstudio.in:443:127.0.0.1 -k -i -X POST -H "Content-Type: application/json" -d '{"code":"DEMO2026"}' https://obmstudio.in/api/auth/client_login
```

#### Breakdown of Options
* `--resolve <host>:<port>:<ip>`: **Force Name Resolution**. Tells curl to skip DNS lookup and map requests for `obmstudio.in` on port `443` directly to `127.0.0.1` (localhost). This is critical on servers because:
  1. We are testing local changes before they go live on public DNS.
  2. It forces the local Apache SSL/TLS engine to receive the correct **Server Name Indication (SNI)**, which prevents `421 Misdirected Request` errors.
* `-k` (or `--insecure`): Tells curl to allow connection even if the SSL certificate is self-signed or has a domain name mismatch (common on local development environments).
* `-i` (or `--include`): Includes the HTTP response headers in the terminal output. Crucial for checking response statuses (e.g. `200 OK`, `401 Unauthorized`) and cookies.
* `-X <METHOD>`: Specifies the HTTP request method (e.g. `POST`, `GET`, `PUT`, `DELETE`).
* `-H "<Header-Name>: <Value>"`: Adds an HTTP request header (e.g., telling the server we are sending JSON data).
* `-d '<JSON>'`: Sends the raw POST data payload.

#### Why we used it
We used it to verify that our Apache `.htaccess` rewrite rules and our `client_login.php` controller correctly received and processed incoming JSON payloads under active virtual hosts, returning a successful `200 OK` JSON response.

---

### Part C: Session Cookie Persistence Testing (`curl -c` and `-b`)

#### Command Sequence
```bash
# Step 1: Login and save session cookie
curl --resolve obmstudio.in:443:127.0.0.1 -k -c /tmp/cookies.txt -i -X POST -H "Content-Type: application/json" -d '{"code":"DEMO2026"}' https://obmstudio.in/api/auth/client_login

# Step 2: Request photo gallery using the saved session cookie
curl --resolve obmstudio.in:443:127.0.0.1 -k -b /tmp/cookies.txt -i https://obmstudio.in/api/photos/get_client_photos
```

#### Breakdown of Options
- `-c <file>` (or `--cookie-jar`): Writes all cookies received from the server response (like the PHP session ID `PHPSESSID`) into `<file>`.
- `-b <file>` (or `--cookie`): Reads cookies from the specified `<file>` and sends them back in the `Cookie:` HTTP request header of the next request.

#### Why we used it
PHP sessions rely on the browser storing a session ID in a cookie and sending it along on every subsequent API call. By writing the cookie from the login response to `/tmp/cookies.txt` and reading it during the photos fetch call, we simulated a browser session. This revealed that the API gateway was throwing a `401 Unauthorized` block on client session lookups, allowing us to patch `api.php`.

---

## [ENTRY 2] - 2026-08-05
### Concept: Schema Mappings in API Integration (Unmatched JSON/DB Keys)

#### The Problem
After logging in, the client workspace loaded, but all client images returned as `null` or failed to load. The console showed that properties like `name` and `url` inside the fetched `photos` array were `null` or missing, even though photos exist in the database.

#### Diagnostic Steps
1. **DB Column Audit**: Ran a MySQL select query directly inside the workspace database:
   ```bash
   mysql -u happysb -pSBk@55376 -e "SELECT * FROM \`obm-new-version\`.client_photos WHERE portal_id = 1"
   ```
   This showed columns: `id`, `portal_id`, `filename`, `category`, `thumb_url`, `selection_status`.
2. **API Logic Verification**: Audited the PHP mapper inside `get_client_photos.php`.
   We observed that the mapper was looking for:
   - `$p['name']` (instead of `$p['filename']`)
   - `$p['url']` (instead of `$p['thumb_url']`)
   - `$p['selected']` (instead of checking `selection_status === 'APPROVED'`)

#### Why it occurred (Root Cause)
The API gateway templates were written assuming standard object property keys (like `name`, `url`, `selected`), whereas the custom database schema used specialized enterprise column names (`filename`, `thumb_url`, `selection_status`). This mismatch returned empty fields to the frontend.
Similarly, `finalize_selections.php` attempted to write selections to a non-existent column named `selected` instead of `selection_status`.

#### The Fix
1. **API Mapping Translation**: Updated [get_client_photos.php](file:///var/www/html/obm-new-version/htdocs/libs/api/photos/get_client_photos.php) to translate database columns to frontend model keys:
   ```php
   'name' => $p['filename'] ?? '',
   'url' => $p['thumb_url'] ?? '',
   'selected' => (strtoupper($p['selection_status'] ?? '') === 'APPROVED')
   ```
2. **Database Update Corrections**: Updated [finalize_selections.php](file:///var/www/html/obm-new-version/htdocs/libs/api/photos/finalize_selections.php) to save choices back to the correct column:
   ```php
   $stmt = $db->prepare("UPDATE `client_photos` SET `selection_status` = 'APPROVED' WHERE `portal_id` = ? AND `id` IN ($placeholders)");
   ```

---

## [ENTRY 3] - 2026-08-06
### Concept: CSS Visual Stacking Contexts and Contrast accessibility (z-index & text colors)

#### The Problem
1. **Vanishing Icons**: When clicking or focusing on any of the input fields (Name, Email, Passcode) inside the Client Portal authentication screen, the absolute positioned icons (user, mail, key) completely vanished.
2. **Low Text Contrast**: In light mode, active tabs and action buttons rendered with black text on orange/amber backgrounds, reducing the professional design aesthetic.

#### Diagnostic Steps
1. **Visual Inspections (Stacking Order)**: By inspecting the input elements in Developer Tools, we noticed that focusing on `.glass-input` in light mode changed its background opacity from semi-transparent (`rgba(0, 0, 0, 0.03)`) to almost fully opaque white (`rgba(255, 255, 255, 0.9)`).
2. **HTML DOM Evaluation**: Audited the DOM structure in `photo-selection.php`:
   ```html
   <i class="absolute left-3.5 top-1/2 -translate-y-1/2 ..."></i>
   <input class="glass-input ...">
   ```
   Because the `<i>` tag is placed before `<input>` in the DOM and both use default z-indexes, focusing or painting the input covers the preceding icon under opaque backgrounds.

#### Why it occurred (Root Cause)
- **Vanishing Icons**: The opaque background color of the focused input covers up the icon positioned beneath it.
- Low Contrast / Specificity Override: We found that `photo-selection.css` has a global light mode override:
  ```css
  html.theme-light .text-white {
      color: #0f172a !important;
  }
  ```
  This is used to map standard dark mode text layers into dark slate (`#0f172a`) in light mode. However, this rule overrides utility classes like `text-white` on button and active tab backgrounds, forcing them to render black.

#### The Fix
1. **Adding Stacking Contexts**: Added `z-10` and `pointer-events-none` to all preceding input absolute icons:
   ```html
   <i class="absolute left-3.5 top-1/2 -translate-y-1/2 ... z-10 pointer-events-none"></i>
   ```
   `z-10` forces the icon to stay on top of the input's white focused background. `pointer-events-none` ensures clicks pass through the icon to focus the input text block.
2. **Restoring Contrast with Specificity Force**: Defined a new styling rule `.text-white-force` inside `photo-selection.css` that bypasses the light-mode override:
   ```css
   .text-white-force {
       color: #ffffff !important;
   }
   ```
   We replaced `text-white` with `text-white-force` on active tab states and primary submit buttons to guarantee white text on both themes.
   Additionally, we mapped this specificity-preserving class onto both the gallery workspace header "Finalize" counter button and the lightbox selection toolbar "Finalize" button, ensuring their text and icons remain crisp white in light mode templates.

---

## [ENTRY 4] - 2026-08-06
### Concept: Conditional Session Cache & Routing (Smart Loader Bypassing vs. Explicit Action Flags)

#### The Problem
After implementing sessionStorage cache bypass (`obm_portal_analyzed = 'true'`) to prevent the loading animation from running on page reloads, the loader screen was skipped *all* the time—even when users manually logged out and logged back in with a passcode. The screen never showed up again, which made form submissions feel abrupt.

#### Diagnostic Steps
1. **Application State Verification**: Checked the login submission handler (`handleAuth`) and observed that it called `loadClientWorkspace(email, username)`.
2. **Flag Evaluation**: Inspected the loader function and saw it checked `sessionStorage.getItem('obm_portal_analyzed') === 'true'` unconditionally, bypassing the animation regardless of the trigger event (explicit form submission vs. automatic session restore on page load).

#### Why it occurred (Root Cause)
The session storage flag `obm_portal_analyzed` persists throughout the entire browser tab session. Since the logout routine did not clear this flag, and the login routine did not differentiate between a page load reload and a user clicking "Unlock My Gallery", the loader animation remained locked in bypass mode.

#### The Fix
1. **Differentiating Routing Triggers**: Modified `loadClientWorkspace` to accept a boolean parameter `forceShowLoader`:
   ```javascript
   function loadClientWorkspace(email, username, forceShowLoader = false)
   ```
2. **Conditional Evaluation**: Updated the bypass check to skip the animation *only* if the session is cached AND we are not forcing the loader:
   ```javascript
   if (alreadyAnalyzed && !forceShowLoader) { // skip loader }
   ```
3. **Trigger Alignments**:
   - For auto-login on page load: Called `loadClientWorkspace(email, username)` (uses default `forceShowLoader = false`, keeping it smart).
   - For form submissions in `handleAuth`: Called `loadClientWorkspace(email, username, true)` (ignores cache, displaying the animation).
4. **Session Reset**: Added `sessionStorage.removeItem('obm_portal_analyzed')` inside the `logout` function to clear state parameters when logging out.

---

## [ENTRY 5] - 2026-08-06
### Concept: Dynamic State Toggles & Viewport Space Optimization (Navbar Replacement)

#### The Problem
When the client is active in the selection workspace gallery, both the main website navigation bar and the client portal workspace header bar render stacked on top of each other. This creates significant visual clutter, duplicates logos/controls, and reduces the active vertical grid viewport area for photo grids.

#### Diagnostic Steps
1. **Nav Distribution Inspection**: Inspected `_master.php` and verified that the website's primary floating navbar `_nav.php` is injected globally outside of individual controller views.
2. **Overlap Evaluation**: Observed that the workspace header is sticky to the top of `#galleryView`. When `#galleryView` is displayed, both headers occupy the same top screen area.

#### Why it occurred (Root Cause)
The master layout always renders the global header `floating-nav-container`, while the individual client workspace also defines a portal-specific header, causing them to stack on top of each other.

#### The Fix
1. **Dynamic HTML State Flag**: Modified `loadClientWorkspace` inside `photo-selection.js` to append a CSS hook to the document element when the portal is active, and remove it inside `logout`:
   ```javascript
   document.documentElement.classList.add('portal-active');
   ```
2. **Conditional CSS Suppression**: Wrote a targeting display rule inside `photo-selection.css` to hide the main floating header when the workspace state flag is active:
   ```css
   html.portal-active .floating-nav-container {
       display: none !important;
   }
   ```
3. **Workspace Portal Home Navigation Shortcut**: Added a home link shortcut icon to the client portal header in `photo-selection.php` mapped to `Session::url('index')` so users can easily return to the main site portfolio pages:
   ```html
   <a href="<?= Session::url('index') ?>" class="..." title="Return to Website">
       <i data-lucide="home" class="w-5 h-5"></i>
   </a>
   ```

---

## [ENTRY 6] - 2026-08-07
### Concept: Aspect Ratio Restrictions in Fluid CSS Layouts (Viewport-Relative Dimensions)

#### The Problem
High-resolution photos displayed in the lightbox selection slider were appearing very small on widescreen/desktop monitors. Landscape-oriented photos left substantial empty spaces at the margins.

#### Diagnostic Steps
1. **Dimension Inspections**: Checked the layout classes on `#lightboxTrack` and `#lightboxImage`.
2. **Constraint Check**: Observed that `#lightboxTrack` was capped at `max-w-4xl` (896px). Since the images maintain aspect ratios dynamically, a strict width restriction causes the height to scale down proportionally, rendering the image as a small block in the middle of a large viewport.

#### Why it occurred (Root Cause)
Using pixel-based max-width constraints (like `max-w-4xl`) limits fluid scaling on large viewports, preventing wide landscape photos from utilizing available screen width.

#### The Fix
1. **Viewport Relative Styling**: Replaced static max-width boundaries with viewport-relative units (`vw`) and expanded the vertical boundaries (`vh`) inside [photo-selection.php](file:///var/www/html/obm-new-version/htdocs/_templates/photo-selection.php):
   - **Track**: `max-w-4xl max-h-[75vh]` -> `max-w-[85vw] max-h-[80vh]`
   - **Image**: `max-w-full max-h-[75vh]` -> `w-full max-w-full max-h-[80vh]` (Added `w-full` to force the tag to stretch to fill the horizontal track).
2. **Resolution Upgrades**: Created a `getHighResUrl(url)` utility inside `photo-selection.js` to swap the Unsplash query width parameter `w=500` (thumbnail) with `w=1600` (high resolution) on the fly before setting the image source. This ensures that landscape photographs have enough resolution to scale up and look crisp rather than remaining constrained to thumbnail dimensions.

---

## [ENTRY 7] - 2026-08-07
### Concept: Dynamic Route Dispatching & Asynchronous Toast Notifications (State-Driven Client Feedback)

#### The Problem
The Client Portal authentication screen had two tabs: "Client Login" and "New Account" (Signup). However:
1. Submitting either form executed the exact same login routing sequence (calling `/api/auth/client_login`), meaning signups were stubs that failed to initialize new client portal workspace records.
2. Clicking "Unlock My Gallery" or "Create Client Portal" provided no interactive visual states during processing, leaving users in the dark on connection states or server response details.

#### Diagnostic Steps
1. **Script Validation**: Audited the form submit handler `handleAuth` inside `photo-selection.js`. We observed that it collected inputs and hardcoded a POST call to `client_login` unconditionally.
2. **Directory Architecture Audit**: Checked `htdocs/libs/api/auth/` and verified that no public client registration controller existed.

#### Why it occurred (Root Cause)
The client self-registration flow was not completed on the API gateway controller layers or in the frontend dispatch logic, resulting in duplicated behavior on both form views.

#### The Fix
1. **Public Registration API Controller**: Created the endpoint [client_signup.php](file:///var/www/html/obm-new-version/htdocs/libs/api/auth/client_signup.php) inside the public auth namespace directory. This controller validates inputs, runs passcode uniqueness checks via `ClientPortal::findByCode`, and registers new workspace slots in the MySQL database.
2. **State-Driven Routing**: Updated `handleAuth` inside [photo-selection.js](file:///var/www/html/obm-new-version/htdocs/photo-selection.js) to inspect `currentAuthMode` and dynamically route requests:
   - **`login`**: Shows an information toast ("Connecting..."), queries `/api/auth/client_login`, and triggers success/error notifications accordingly.
   - **`signup`**: Shows an information toast ("Submitting..."), queries `/api/auth/client_signup`, displays success toast, resets the inputs, and automatically switches the view back to the login tab.

---

## [ENTRY 8] - 2026-08-07
### Concept: UI Cleanups & Authorization Constraint Alignment (Feature Disabling)

#### The Problem
The client requested the removal of the client portal self-registration workflow ("New Account" tab) from the entry page, because client portals and select allocations must be configured strictly by administrators.

#### Diagnostic Steps
1. **Scope Verification**: Audited `photo-selection.php` layout and confirmed that self-registration switches and signup fields occupied visual real estate on the entry layout.
2. **Access Control Alignment**: Confirmed that self-creation of portals bypasses admin review and allows unmonitored database insertions.

#### Why it occurred (Root Cause)
The client portal self-signup flow is deprecated by business design constraints; registration is strictly administrative.

#### The Fix
1. **Template Cleanups**: Removed the tab switcher container and the hidden name input field from `photo-selection.php`, displaying only the email and passcode input fields on the entry card.
2. **Script Simplification**: Deleted the unused variables and handlers (`currentAuthMode`, `switchAuthTab`) from `photo-selection.js`, routing form submissions directly to `client_login` validation.
3. **Endpoint Disabling**: Deleted the public controller `client_signup.php` to prevent unauthenticated POST requests from inserting new portals in the database.

---

## [ENTRY 9] - 2026-08-07
### Concept: CSS Selector Specificity Mismatches in Theme Systems

#### The Problem
In the light theme, the background container of the custom confirmation dialog ("Reset Entire Workspace?") remained dark charcoal/black, while its text turned dark slate, making it illegible.

#### Diagnostic Steps
1. **HTML Inspection**: Checked the markup inside `photo-selection.php` around the confirmation modal container card. The container element had the class `obm-modal-container`.
2. **CSS Inspection**: Searched the light-theme overrides in `photo-selection.css`. Found that the style rule for changing the modal background in light theme was targeting `.obm-modal-card` instead of `.obm-modal-container`:
   ```css
   html.theme-light .obm-modal-card { ... }
   ```

#### Why it occurred (Root Cause)
The selector class name `.obm-modal-card` inside the light theme stylesheet did not match the class name `.obm-modal-container` in the HTML markup.

#### The Fix
Replaced the selector `.obm-modal-card` with `.obm-modal-container` in [photo-selection.css](file:///var/www/html/obm-new-version/htdocs/photo-selection.css):
```css
html.theme-light .obm-modal-container {
    background: rgba(255, 255, 255, 0.95) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.15) !important;
}
```
This allows the dialog container to correctly render as white/translucent in light mode.

---

## [ENTRY 10] - 2026-08-07
### Concept: UI-API Data Discrepancy & Validation Consistency (Mismatched Inputs)

#### The Problem
The Client Portal login form required both an email address and a passcode. However, the backend validation script only checked the passcode against the database, completely ignoring the email input value. This meant clients could type any arbitrary email alongside a valid passcode and still unlock access, creating a security gap.

#### Diagnostic Steps
1. **API Parameter Audit**: Inspected `/api/auth/client_login` request handler. Observed that it only read `code` and called `ClientPortal::findByCode($code)`.
2. **Payload Verification**: Checked the fetch payload in `photo-selection.js` and confirmed that only `code` was sent in the body.

#### Why it occurred (Root Cause)
The email verification check was not integrated into the authentication API controller or sent in the client-side payload, resulting in a mismatch between UI input expectations and backend validation rules.

#### The Fix
1. **API Validation Extension**: Modified [client_login.php](file:///var/www/html/obm-new-version/htdocs/libs/api/auth/client_login.php) to extract the email from the payload and run a case-insensitive match against the registered client portal email:
   ```php
   if (strcasecmp(trim($portal->getEmail()), trim($email)) !== 0) {
       // Return 401 error
   }
   ```
2. **Frontend Payload Update**: Updated the authentication fetch call in [photo-selection.js](file:///var/www/html/obm-new-version/htdocs/photo-selection.js) to pass both the email and passcode parameters:
   ```javascript
   body: JSON.stringify({ email: emailInput, code: codeInput })
   ```
3. **Homepage Modal Alignment**:
   - Refactored [_footer.php](file:///var/www/html/obm-new-version/htdocs/_templates/core/_footer.php) to insert an "Email Address" input field before the passcode input.
   - Updated the submission logic in [_master.php](file:///var/www/html/obm-new-version/htdocs/_templates/_master.php) to extract the email input value and transmit it along with the passcode.
   - Cleaned up [portfolio.js](file:///var/www/html/obm-new-version/htdocs/portfolio.js) by removing its redundant mock form submission handler and updated its testing auto-fill buttons to populate both the test email and passcode.

---

## [ENTRY 11] - 2026-08-07
### Concept: Infinite Loop/Stall Conditions inside State Verification Listeners

#### The Problem
After logging in with a new client portal that contains zero uploaded photos, refreshing the page with F5 causes the page to render completely blank, with only the background gradient and footer navigation bar visible.

#### Diagnostic Steps
1. **Verification Checks**: Inspected page load recovery triggers inside `photo-selection.js` (`bypassLoginForTesting` -> `loadClientWorkspace`).
2. **Interval Audits**: Observed that when `alreadyAnalyzed` session storage flag is active, the initialization routine uses an interval check to verify if the photo retrieval API call is complete before fading out the loader. The condition was checking for `apiPhotos.length > 0`:
   ```javascript
   if (apiPhotos.length > 0 || loadError) { ... }
   ```

#### Why it occurred (Root Cause)
If a portal database record contains exactly 0 mapped photos, `apiPhotos.length` is `0`, and `loadError` is `null` (since the fetch successfully resolved an empty dataset). The bypass interval loops indefinitely, preventing state initialization and leaving the container views hidden.

#### The Fix
Replaced the array-length check with an explicit request status completion boolean flag (`fetchDone`):
1. **API Callback Flagging**: Modified the `get_client_photos` API fetch promise handlers to toggle `fetchDone = true` on both success and error responses.
2. **Safe Bypassing Checks**: Updated the interval check to verify `fetchDone` instead of array elements:
   ```javascript
   if (fetchDone) {
       clearInterval(checkDone);
       ...
   }
   ```
This guarantees the page initializes and displays the selection dashboard correctly even if the database has no photos allocated yet.

---

## [ENTRY 12] - 2026-08-07
### Concept: UI Cleanups & Dynamic Page Layout Placeholders (Workspace Empty States)

#### The Problem
When a client portal database record has no photos assigned yet, displaying an empty selection dashboard (with categories, search bars, slideshow structures, and selection toolbars) looks cluttered, broken, and confusing.

#### Diagnostic Steps
1. **Layout Verification**: Evaluated the UI when `photoDatabase` is empty. The page layout displayed all headers, slide controls, and search items, but had an empty image grid, which feels unpolished.
2. **Behavior Verification**: Checked if clients need custom contact or logout shortcuts when their portal workspace is still preparing.

#### Why it occurred (Root Cause)
The selection workspace did not differentiate between a populated photo catalogue and a newly created, uninitialized portal, displaying empty versions of all controls by default.

#### The Fix
1. **Empty State Placeholder Container**: Inserted a premium `#emptyWorkspaceState` card inside [photo-selection.php](file:///var/www/html/obm-new-version/htdocs/_templates/photo-selection.php) that contains an informative message advising that OBM Studio is currently preparing their photo list, alongside contact and logout shortcuts.
2. **Dynamic UI Toggling**: Added the `checkWorkspaceEmpty()` checker function inside [photo-selection.js](file:///var/www/html/obm-new-version/htdocs/photo-selection.js) and hooked it into `refreshGallery()`. If `photoDatabase.length === 0`:
   - It displays the `#emptyWorkspaceState` card.
   - It hides the slideshow carousel, filter bar, categories selection, meta headers, and main grid.

---

## [ENTRY 13] - 2026-08-07
### Concept: UI Polishing and Luxury Styling Refinement

#### The Problem
The default empty workspace placeholder state card looked flat and basic, lacking interactive components, background styling depth, and premium animations.

#### The Fix
Restyled the empty workspace view with glassmorphism overlays and ambient glows, added animated ping and bounce icons, and integrated an interactive status timeline depicting secure handshake progress.

---

## [ENTRY 14] - 2026-08-07
### Concept: Light Theme CSS Contrast Mappings

#### The Problem
In Light Theme mode, the newly added workspace lifecycle timeline card background (styled with `.bg-black/40` overlay class) remained dark gray, causing the remapped dark charcoal text elements inside it to have extremely poor, illegible color contrast.

#### Diagnostic Steps
1. **Theme Verification**: Inspected the rendered DOM when active theme is set to `white`. Verified that `html` is assigned class `html.theme-light`.
2. **CSS Inspection**: Confirmed that `.bg-black/40` and `.text-gray-500` were not overridden under `html.theme-light`, leaving the background dark and text faded.

#### The Fix
Added target remapping rules under `html.theme-light` in [photo-selection.css](file:///var/www/html/obm-new-version/htdocs/photo-selection.css):
```css
html.theme-light .bg-black\/40 {
    background: rgba(0, 0, 0, 0.03) !important;
}
html.theme-light .bg-black\/50 {
    background: rgba(0, 0, 0, 0.04) !important;
}
html.theme-light .text-gray-500 {
    color: #64748b !important;
}
```
This forces the timeline card container to render as a soft light-gray translucent box, creating optimal readability for dark text.

---

## [ENTRY 15] - 2026-08-07
### Concept: Layout Alignment Centering & Local Style Scope Remappings

#### The Problem
In the Digital Album view, the bottom toolbar layout had two major issues:
1. **Misaligned Centering**: The navigation block (Prev button, dots list, Next button) was forced to the far left of the page layout, rendering it out-of-balance on screen.
2. **Invisible Indicators**: Under Light Mode, the inactive progress dots (`.spread-dot`) and keyboard key badge labels (`.kbd`) were rendered in dark mode styling (translucent white borders/text on top of a white card container), making them completely invisible/unreadable. The action buttons kept their dark background (`bg-slate-800/80`), which looked out-of-place.

#### Diagnostic Steps
1. **Layout Audit**: Analyzed the flex directions inside `digital-album.php`. Confirmed that the row items used `justify-between` and only had two children, pushing navigation left and actions right.
2. **Contrast Checks**: Inspected the local CSS block. Verified that `.kbd` and `.spread-dot` definitions did not contain light theme selectors.

#### The Fix
1. **Center-Balanced Flex Row**: Replaced the controls container row with a three-column layout (Left: brand identifier, Center: centered navigation controls, Right: action buttons). Assigned `flex-1` to the left and right containers on desktop to keep the center navigation perfectly centered.
2. **Light Theme Local Overrides**: Added CSS overrides inside the local `<style>` block in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php):
   - Overrode inactive `.spread-dot` elements to soft gray overlays in light mode.
   - Remapped `.kbd` key labels to light bordered indicators.
   - Restyled `.bg-slate-800/80` buttons to light-glassmorphic blocks.

---

## [ENTRY 16] - 2026-08-07
### Concept: CSS Grid Centering and Force Color Overlays

#### The Problem
1. **Misaligned Centering**: The navigation controls block was still slightly offset on desktop/tablet views because the action buttons container on the right was naturally wider than the branding text on the left, shifting the center layout.
2. **Invisible Image Text overlays**: In Light Mode, the text descriptions overlaying the album spread page photographs (such as "Sacred Fire Rituals" and "Aerial Venue View") turned dark slate (due to `html.theme-light .text-white` rules). Since the background behind the text remained dark (the image spread shadow backdrop), this made the text illegible.

#### Diagnostic Steps
1. **Grid Audits**: Evaluated flex configurations. Confirmed that unequal column widths broke the center balance of the row items.
2. **Color Inspection**: Found that page titles used `.text-white` which was remapped to `#0f172a` in Light Mode.

#### The Fix
1. **True CSS Grid Centering**: Refactored the flex controls row in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php) into a three-column CSS Grid (`grid grid-cols-1 md:grid-cols-3`). This forces each column to occupy exactly 33.33% of the desktop toolbar container, centering the middle navigation block mathematically.
2. **Title Overlay Color Override**: Replaced the `.text-white` utility classes on `#page-title-left` and `#page-title-right` elements with `.text-white-force` so they remain bright white on top of the dark image overlays in all theme modes.

---

## [ENTRY 17] - 2026-08-07
### Concept: CSS Specificity Precedence (Override Tags vs Classes)

#### The Problem
Even after applying `.text-white-force` (`color: #fff !important`), the page title text overlays on top of the album photographs still rendered as black in Light Mode.

#### Diagnostic Steps
Inspected style calculations in the browser. Observed that the global light mode override selector `html.theme-light h3` (specificity `021`) took precedence over the single class selector `.text-white-force` (specificity `010`).

#### The Fix
Added high-specificity selectors to target `.text-white-force` when assigned to heading elements under the light theme class:
```css
.text-white-force,
html.theme-light .text-white-force,
html.theme-light h3.text-white-force {
    color: #ffffff !important;
}
```
This increases the override specificity to `031`, ensuring it takes precedence and keeps the text white against dark photo overlays.

---

## [ENTRY 18] - 2026-08-07
### Concept: CSS Selector Exclusion Filters (Pseudoclass chain specificity)

#### The Problem
In Light Mode, overlay titles inside the album spread still rendered as black (#0f172a) despite high-specificity override declarations (`html.theme-light h3.text-white-force` - specificity `031`).

#### Diagnostic Steps
Inspected the active CSS selectors in the browser's developer console. Found that a rule in [styles.css](file:///var/www/html/obm-new-version/htdocs/styles.css) at line 549 contained a chained list of exclusions:
```css
html.theme-light h3:not(.grad-cyan):not(.grad-gold):not(.grad-aurora):not(.grad-story) { ... }
```
Because CSS pseudo-classes `:not()` accumulate the specificity of their arguments, the selector's total specificity resolved to `051` (5 classes + 1 element), allowing it to take precedence over the specificity of the override class (`031`).

#### The Fix
Appended `:not(.text-white-force)` to the exclusions list in the global light mode typography rules inside [styles.css](file:///var/www/html/obm-new-version/htdocs/styles.css):
```css
html.theme-light h3:not(.grad-cyan):not(.grad-gold):not(.grad-aurora):not(.grad-story):not(.text-white-force) {
    color: #0f172a !important;
}
```
This forces the global remapping rules to completely ignore any heading elements assigned the `.text-white-force` class, allowing them to render in clean white (`#ffffff !important`) as designed.

---

## [ENTRY 19] - 2026-08-07
### Concept: Theme-Adaptive UI Frames (Bimodal CSS selectors)

#### The Problem
The `.album-book` main container background was hardcoded to dark blue (`#0a0e18`) with dark border outlines. When switching back and forth between light mode and dark mode, the container backdrop remained dark, which clashed with the clean, white light theme background styling.

#### Diagnostic Steps
1. **Rule Audit**: Found the static `#0a0e18` background declaration in the default `.album-book` definition class.
2. **Contrast check**: Verified that the container frame needed to adapt color structures dynamically (dark in dark theme, light in light theme) instead of staying static.

#### The Fix
Added adaptive theme overrides in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php):
- Default (Dark theme): Renders with background `#0a0e18` and dark transparent borders/shadows.
- Override (`html.theme-light .album-book`): Remaps to pure white background (`#ffffff`), a soft gray border (`rgba(0, 0, 0, 0.08)`), and lighter, clean shadows. This guarantees clean contrast behavior in both themes.

---

## [ENTRY 20] - 2026-08-07
### Concept: Color Contrast & Border Visibility in Light Interfaces

#### The Problem
In Light Mode, the card container outline border around the `.album-book` container was practically invisible. The original override only mapped the `border-color` to a very faint gray `rgba(0,0,0,0.08)`, which blended too closely into the light background body (`#f8fafc`).

#### Diagnostic Steps
1. **Visual Contrast Audit**: Verified that the border was not defined using full shorthand variables in the override, leaving the default dark transparency rules to compete.
2. **Contrast Ratio Check**: Confirmed that `rgba(0,0,0,0.08)` on `#f8fafc` has low contrast visibility.

#### The Fix
Replaced the `border-color` override on the `html.theme-light .album-book` selector in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php) with an explicit, high-contrast shorthand:
```css
border: 2px solid rgba(0, 0, 0, 0.12) !important;
```
This forces a distinct, clean, 2-pixel wide soft-charcoal border frame around the book spread, cleanly separating the white album card from the surrounding page body background.

---

## [ENTRY 21] - 2026-08-07
### Concept: HTML Stacking Context & Border Clipping

#### The Problem
Even after setting a 2px solid border explicitly in the light theme overrides, no border was visible around the book frame. 

#### Diagnostic Steps
Inspected the DOM. Observed that the child pages (`.album-page` elements and their images) have `position: relative` and stretch to cover 100% width/height of the container. Because of the CSS Stacking Context, these positioned children render on top of the parent container's backgrounds and borders, completely masking the border.

#### The Fix
1. **Removed Parent Border**: Removed the border property from the parent `.album-book` styles to avoid rendering issues.
2. **Created Border Overlay**: Inserted an absolute overlay `<div class="album-border-overlay"></div>` inside `.album-book` at the top level of the children hierarchy.
3. **Layer Ordering**: Styled `.album-border-overlay` to sit on top of the images:
   ```css
   .album-border-overlay {
     position: absolute;
     inset: 0;
     border-radius: 20px;
     border: 2px solid rgba(255, 255, 255, 0.08);
     pointer-events: none;
     z-index: 25;
   }
   ```
   Setting `pointer-events: none` ensures mouse clicks pass directly through to zoom buttons/images underneath. In light mode, this overlay's border is overridden to `rgba(0, 0, 0, 0.12)`.

---

## [ENTRY 22] - 2026-08-07
### Concept: Bimodal Overlay Backdrops

#### The Problem
In Light Mode, when entering Fullscreen Showcase view, the overlay backdrop was hardcoded to solid black (`rgba(3, 4, 7, 0.98)`). The exit and navigation buttons inside it also rendered as white translucent boxes, clashing with the light theme palette.

#### Diagnostic Steps
1. **Backdrop Inspection**: Found the static `background` style in `.album-fullscreen` definition block.
2. **Button Stacking Check**: Verified that the control buttons inside the fullscreen layer used the dark theme `.bg-white/10` and `.text-white` classes.

#### The Fix
Added adaptive theme overrides in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php):
- **Backdrop Overrides**: Remapped `.album-fullscreen` in Light Mode to a frosted, soft-slate white backdrop (`rgba(248, 250, 252, 0.98) !important`).
- **Button Controls Overrides**: Remapped the `.bg-white/10` controls inside the fullscreen box to soft-slate dark overlays with charcoal text:
  ```css
  html.theme-light .album-fullscreen .bg-white\/10 {
    background: rgba(0, 0, 0, 0.05) !important;
    color: #0f172a !important;
  }
  ```

---

## [ENTRY 23] - 2026-08-07
### Concept: HTML Inline Event Scope & Browser API Name Collisions

#### The Problem
In the Fullscreen Album Showcase layout, clicking the "Exit Fullscreen" minimize button threw the console exception:
`Uncaught (in promise) TypeError: Not in fullscreen mode`
Additionally, the overlay remained stuck on screen, preventing the user from closing it.

#### Diagnostic Steps
1. **Scope Chain Review**: Evaluated inline event scopes. HTML elements using inline triggers like `onclick="exitFullscreen()"` search the element itself, then the parent form, then the `document` object, and finally the global `window` object.
2. **API Collision Verification**: Confirmed that `document` has a native method called `exitFullscreen()`. The browser resolved the inline `exitFullscreen()` call to `document.exitFullscreen()` instead of our custom window function, triggering a native API check which failed because the browser was not in native fullscreen.

#### The Fix
1. **Collision-free Prefixes**: Renamed the custom functions in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php) from `enterFullscreen` and `exitFullscreen` to `obmEnterFullscreen` and `obmExitFullscreen`.
2. **Backdrop Exit Trigger**: Added a backdrop click handler to `#album-fullscreen` so clicking outside the images closes the overlay:
   ```javascript
   fsOverlay.addEventListener('click', (e) => {
     if (e.target.id === 'album-fullscreen') {
       obmExitFullscreen();
     }
   });
   ```

---

## [ENTRY 24] - 2026-08-07
### Concept: Premium Cinematic Overlays & Bimodal Glassmorphism

#### The Problem
The navigation controls and exit button inside the Fullscreen Album Showcase layout were plain, flat boxes positioned at the bottom center and top right. They lacked premium aesthetics, visual hover cues, and animations.

#### Diagnostic Steps
1. **UX Design Review**: Evaluated typical premium photo showcases (e.g. Pixieset). Conformed that placing large floating arrows on the left/right screen edges provides a much more immersive, theatrical desktop browsing experience than grouping buttons together at the bottom.
2. **Theme Testing**: Audited how dark-glass components looked on top of the Light Mode fullscreen white background. Verified that bimodal remapping was required to shift the dark glass components to white glass controls.

#### The Fix
1. **Cinematic Floating Navigation**: Replaced the bottom navigation buttons in [digital-album.php](file:///var/www/html/obm-new-version/htdocs/_templates/digital-album.php) with large, circular glass buttons absolute-positioned at the center-left and center-right screen boundaries. Added subtle translation micro-animations on hover (`group-hover:-translate-x-0.5` and `group-hover:translate-x-0.5`).
2. **Rotating Close Button**: Upgraded the top-right close control to a circular frosted close button with a 90-degree hover rotation transition on the `X` icon.
3. **Capsule Dot Indicator**: Enclosed the bottom dots inside a floating glass capsule wrapper.
4. **Light Mode CSS Overrides**: Remapped these control badges to white frosted glass panels with charcoal indicators in Light Mode:
   ```css
   html.theme-light .album-fullscreen .bg-slate-900\/40 {
     background: rgba(255, 255, 255, 0.75) !important;
     border-color: rgba(0, 0, 0, 0.08) !important;
     color: #0f172a !important;
   }
   ```

---

## [ENTRY 25] - 2026-08-07
### Concept: Toast Engines, Parameter Polymorphism & Layout Displacements

#### The Problem
Clicking the Favorite or Note buttons inside the Digital Album view failed to show any visible toast popups. Additionally, clicking them created an empty visual gap at the bottom of the viewport, pushing the footer layout down.

#### Diagnostic Steps
1. **Container Alignment Check**: Found that the container element inside [_toastv3.php](file:///var/www/html/obm-new-version/htdocs/_templates/core/_toastv3.php) was assigned the class `.toast-panel` instead of `.toast-container`. Since `.toast-panel` has no CSS styling, the container defaulted to `position: static` at the bottom of the body.
2. **Markup Audit**: Audited [toastv3.js](file:///var/www/html/obm-new-version/htdocs/assets/js/toastv3.js). Observed that it generated legacy `.toast` elements instead of matching the modern `.toast-body` and `.toast-content` structure expected by [styles.css](file:///var/www/html/obm-new-version/htdocs/styles.css).
3. **Transition Analysis**: Verified that `.toast-item` has a default opacity of `0`. Since the script did not add the `.toast-show` class on append, the items remained invisible.
4. **Signature Discrepancy**: Noticed that the layout templates called `showToast(title, message, type)` but the script expected `showToast(type, title, message)`. This broke theme mapping and text placements.

#### The Fix
1. **Toast Container Layout**: Updated `_toastv3.php` to use classes `.toast-container` and `.toast-pos-bottom-right`. This positions the container as a fixed floating box, preventing layout displacement.
2. **Polymorphic Parameter Normalizer**: Integrated an argument-reordering detector in [toastv3.js](file:///var/www/html/obm-new-version/htdocs/assets/js/toastv3.js) that checks if the first parameter matches a list of known style keys. If not, it automatically swaps variables to handle legacy layouts.
3. **Engine Upgrade**: Refactored the DOM builder inside the script to create correct modern elements, trigger transition classes (`.toast-show` / `.toast-hide`), render Lucide icons, and manage the auto-dismiss timer.