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
