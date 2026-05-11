# 🚀 Local Development Setup

Guide to local development with Docker.

---

## 📦 Requirements

- Docker & Docker Compose
- WSL2 (Windows) or directly Linux/Mac
- Make (for `make` commands)
- Git

---

## 🏗️ Start containers

```bash
docker compose up -d
# or with Make:
make docker-up
```

This starts:
- **PHP-FPM** (Port 9000)
- **Nginx** (Port 8080)
- **Node** (for assets)
- **Mailpit** (test mailbox, port 8025)

Access: **http://localhost:8080**

---

## 🔨 Common commands

### **Setup & Installation**

```bash
make setup          # Initialize project (Composer, NPM, Migrations)
make docker-up      # Start containers
docker compose logs # Show logs
```

### **Tests**

```bash
make test                   # All tests (Pest)
make test-unit              # Unit tests only
make test-feature           # Feature tests only
make test-acceptance        # Acceptance tests only (core flows)
make test-acceptance-gate   # Pint + PHPStan + Acceptance tests
make test-security          # OWASP-oriented security tests
make test-security-strict   # Extended security tests (stop-on-failure)
make test-security-gate     # Security tests + PHPStan + Pint analysis
make test-coverage          # Tests with coverage (requires Xdebug, min 95%)
make test-coverage-report   # Coverage files (clover+xml/html)
make coverage-open          # Opens HTML coverage report in browser
make coverage-clean         # Deletes old coverage reports
```

**Code Coverage Requirements:**
- **Minimum:** 95% total coverage
- **GeminiAiAnalyzer.php:** ≥80%
- **Current status:** 98.2% Total ✅

### **Code Quality**

```bash
make pint-fix       # Format code automatically
make pint-analyse   # Analysis only (no fix)
make phpstan        # Static analysis
```

### **Shells & Containers**

```bash
make php-shell      # Bash in PHP container
make node-shell     # Shell in Node container
make nginx-shell    # Shell in Nginx container
```

### **Database**

```bash
make db-migrate     # Run migrations
make db-seed        # Load seeds
make db-migrate-refresh  # Reset + re-migrate + seed
```

### **Cache & Services**

```bash
make php-cache-clear    # Clear Laravel cache
make docker-logs        # Follow Docker logs
make docker-restart     # Restart containers (fast)
make docker-rebuild     # New build (after Docker changes)
```

---

## 🐛 Debugging with Xdebug

See **[Debugging Guide](./DEBUGGING.md)** for complete instructions.

**Quick Start:**
```bash
make debug-on       # Enable Xdebug (debug + coverage)
make debug-status   # Check status
make php-shell      # Shell (XDEBUG_CONFIG is already set)
```

**Coverage reports:**
```bash
make debug-on               # Enable Xdebug
make test-coverage          # Coverage check (min 95%)
make test-coverage-report   # Coverage files under src/coverage-report/
make coverage-open          # Open HTML report in browser
make coverage-clean         # Delete old reports
```

Then set the breakpoint to port 9003 in the IDE and run the script!

---

## 📊 Workflow for development

### **Typical developer day:**

```bash
# Morning
make docker-up          # Start containers
make php-shell          # Enter container
composer install        # If needed
php artisan migrate     # Run migrations

# During development
make test               # Tests after every change
make pint-fix           # Format code
make phpstan            # Analysis before commit

# Need debugging?
make debug-on           # Enable Xdebug
make php-shell          # Debug
make debug-off          # Disable Xdebug (faster)

# End of day
make docker-down        # Stop containers
```

---

## 🐚 PHP Shell Tips

```bash
make php-shell

# Inside container:
php artisan tinker              # PHP REPL
vendor/bin/pest                 # Tests
php artisan make:migration xyz  # New migration
php artisan route:list          # Show routes
composer install                # Dependencies
```

---

## 🔄 Docker troubleshooting

### **Container not reachable?**

```bash
docker ps                   # Are all containers running?
docker compose logs         # Errors in the logs?
make docker-restart         # Quick restart
make docker-rebuild         # Full rebuild
```

### **Port already occupied?**

```bash
sudo lsof -i :8080         # Who is using port 8080?
make docker-down            # Stop containers
```

### **Clear cache/data?**

```bash
make docker-clean           # Delete containers + volumes
make docker-up              # Restart (fresh!)
```

---

## 🔁 GitHub CI & Branch Protection (Commit 23)

### CI workflow

The workflow lies in:
- `.github/workflows/ci.yml`

Active jobs:
- `pint`
- `phpstan`
- `pest_acceptance` (core flows + edge cases, `AI_PROVIDER=mock`)
- `pest_coverage` (incl. coverage gate `>=95%`)

Triggers:
- `push` (all branches except `main`)
- `pull_request` to `main`
- `workflow_dispatch` (manual)

Coverage artifacts:
- Upload as GitHub Artifact `coverage-report`
- Retention: 7 days

### CI environment file

- `src/.env.ci` contains the CI-specific default values
- `AI_PROVIDER=mock` prevents external API dependencies
- `GEMINI_API_KEY` remains as an empty placeholder
- `APP_KEY` is created in the workflow at runtime

### Codecov Setup (public repository)

1. Sign in to `codecov.io` using GitHub
2. Activate repository
3. No tokens necessary (public repo)
4. Workflow uploads `src/coverage-report/clover.xml`

### Status Badges

Three badges are provided in the `README.md`:
- CI (GitHub Actions)
- Coverage (Codecov)
- PHPStan Level 9

Note: In the badge URLs, replace `<owner>/<repo>` with the real GitHub path.

### Branch protection for `main`

Status:
- **Done (in the repo):** CI checks exist as jobs `pint`, `phpstan`, `pest_acceptance`, `pest_coverage`
- **To-do (set manually in GitHub Settings):** Activate branch protection rule including required checks

GitHub Settings (To-do):
1. `Settings -> Branches -> Add rule`
2. Branch pattern: `main`
3. Activate:
   - `Require a pull request before merging`
   - `Require status checks to pass before merging`
   - `Require branches to be up to date before merging`
   - `Do not allow bypassing the above settings`
4. Select required checks:
   - `pint`
   - `phpstan`
   - `pest_acceptance`
   - `pest_coverage`

Branch protection checklist:
- [ ] Rule created for `main`
- [ ] `Require a pull request before merging` active
- [ ] `Require status checks to pass before merging` active
- [ ] Required checks set: `pint`, `phpstan`, `pest_acceptance`, `pest_coverage`

---

## 📚 More documentation

| Topic | Link |
|-------|------|
| **Roadmap** | [docs/ROADMAP.md](./docs/ROADMAP.md) |
| **Changelog** | [CHANGELOG.md](./CHANGELOG.md) |
| **Debugging** | [docs/DEBUGGING.md](./docs/DEBUGGING.md) |
| **Architecture** | [docs/ARCHITECTURE.md](./docs/ARCHITECTURE.md) |
| **Coding Guidelines** | [docs/CODING_GUIDELINES.md](./docs/CODING_GUIDELINES.md) |
| **Contributing** | [docs/CONTRIBUTING.md](./docs/CONTRIBUTING.md) |

---

## ✅ Starting checklist

- [ ] Docker & Docker Compose installed
- [ ] `docker compose up -d` executed
- [ ] http://localhost:8080 opens the page
- [ ] `make php-shell` works
- [ ] `make test` the tests run green

**Good luck developing!** 🚀

## 🛡️ Security test template (OWASP-oriented)

Use this template for security-relevant changes (input validation, auth, external requests, prompting).

### 1) Minimum security test run

```bash
# Basic quality gates
make test
make phpstan
make pint-analyse

# Targeted security tests
make test-security

# Optional: strict run
make test-security-strict

# Recommended before merge
make test-security-gate
```

### 2) PR checklist security

- [ ] Inputs treated as untrusted (validation + sanitization)
- [ ] Output contextually escaped/encoded (no unescaped HTML/JS)
- [ ] CSRF present for all POST forms (`@csrf`)
- [ ] No secrets in the code (only Config/Env)
- [ ] External requests secured with timeouts/allowlist
- [ ] Prompt/input injection taken into account
- [ ] Security regression tests added/updated

###3) OWASP mapping quick check

- **A01 Access Control**: Unauthorized access tests present
- **A03 Injection**: SQL/XSS/Prompt injection tests available
- **A05 Misconfiguration**: Safe defaults checked
- **A06 Vulnerable Components**: Dependency update/CVE check taken into account
- **A09 Logging/Monitoring**: Security-relevant errors are logged without secrets

### 4) Recommended test file names

- `tests/Feature/SecurityPromptInjectionTest.php`
- `tests/Feature/InputValidationTest.php`
- `tests/Feature/ApiErrorHandlingTest.php`
- `tests/Feature/SecurityAccessControlTest.php`

---
