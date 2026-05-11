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
# oder mit Make:
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
make setup          # Projekt initialisieren (Composer, NPM, Migrations)
make docker-up      # Container starten
docker compose logs # Logs anzeigen
```

### **Tests**

```bash
make test                   # Alle Tests (Pest)
make test-unit              # Unit-Tests nur
make test-feature           # Feature-Tests nur
make test-acceptance        # Acceptance-Tests nur (Kernflows)
make test-acceptance-gate   # Pint + PHPStan + Acceptance-Tests
make test-security          # OWASP-orientierte Security-Tests
make test-security-strict   # Erweiterte Security-Tests (stop-on-failure)
make test-security-gate     # Security-Tests + PHPStan + Pint-Analyse
make test-coverage          # Tests mit Coverage (benötigt Xdebug, min 95%)
make test-coverage-report   # Coverage-Dateien (clover+xml/html)
make coverage-open          # Öffnet HTML-Coverage-Report im Browser
make coverage-clean         # Löscht alte Coverage-Reports
```

**Code Coverage Requirements:**
- **Minimum:** 95% total coverage
- **GeminiAiAnalyzer.php:** ≥80%
- **Current status:** 98.2% Total ✅

### **Code Quality**

```bash
make pint-fix       # Code automatisch formatieren
make pint-analyse   # Nur Analyse (kein Fix)
make phpstan        # Statische Analyse
```

### **Shells & Containers**

```bash
make php-shell      # Bash im PHP-Container
make node-shell     # Shell im Node-Container
make nginx-shell    # Shell im Nginx-Container
```

### **Database**

```bash
make db-migrate     # Migrationen ausführen
make db-seed        # Seeds laden
make db-migrate-refresh  # Reset + Re-Migrate + Seed
```

### **Cache & Services**

```bash
make php-cache-clear    # Laravel-Cache leeren
make docker-logs        # Docker-Logs folgen
make docker-restart     # Container neu starten (schnell)
make docker-rebuild     # Neuer Build (nach Docker-Änderungen)
```

---

## 🐛 Debugging with Xdebug

See **[Debugging Guide](./DEBUGGING.md)** for complete instructions.

**Quick Start:**
```bash
make debug-on       # Xdebug aktivieren (debug + coverage)
make debug-status   # Status prüfen
make php-shell      # Shell (XDEBUG_CONFIG ist bereits gesetzt)
```

**Coverage reports:**
```bash
make debug-on               # Xdebug aktivieren
make test-coverage          # Coverage-Check (min 95%)
make test-coverage-report   # Coverage-Dateien unter src/coverage-report/
make coverage-open          # HTML-Report im Browser öffnen
make coverage-clean         # Alte Reports löschen
```

Then set the breakpoint to port 9003 in the IDE and run the script!

---

## 📊 Workflow for development

### **Typical developer tag:**

```bash
# Morgens
make docker-up          # Container starten
make php-shell          # In Container gehen
composer install        # Falls nötig
php artisan migrate     # Migrations ausführen

# Während Entwicklung
make test               # Tests nach jeder Änderung
make pint-fix           # Code formatieren
make phpstan            # Analyse vor Commit

# Debugging nötig?
make debug-on           # Xdebug an
make php-shell          # Debuggen
make debug-off          # Xdebug aus (schneller)

# Feierabend
make docker-down        # Container stoppen
```

---

## 🐚 PHP Shell Tips

```bash
make php-shell

# Im Container:
php artisan tinker              # PHP REPL
vendor/bin/pest                 # Tests
php artisan make:migration xyz  # Neue Migration
php artisan route:list          # Routes anzeigen
composer install                # Abhängigkeiten
```

---

## 🔄 Docker troubleshooting

### **Container not reachable?**

```bash
docker ps                   # Laufen alle Container?
docker compose logs         # Fehler in den Logs?
make docker-restart         # Schnell neu starten
make docker-rebuild         # Komplett neu bauen
```

### **Port already occupied?**

```bash
sudo lsof -i :8080         # Wer nutzt Port 8080?
make docker-down            # Container stoppen
```

### **Clear cache/data?**

```bash
make docker-clean           # Container + Volumes löschen
make docker-up              # Neu starten (frisch!)
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
# Basis-Qualitätsgates
make test
make phpstan
make pint-analyse

# gezielte Security-Tests
make test-security

# optional: strikter Lauf
make test-security-strict

# empfohlen vor Merge
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