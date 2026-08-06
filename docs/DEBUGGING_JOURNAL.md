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