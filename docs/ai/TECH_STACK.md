# ⚙️ Tech Stack & Versionen

## 📦 PHP & Laravel

- **PHP:** 8.5.3
- **Laravel:** 12.x
- **Laravel AI:** v0.2.1 (Gemini-Provider aktiv)
- **Laravel Prompts:** v0
- **Laravel Pail:** v1.2.2
- **Laravel Pint:** v1.24
- **Laravel Boost:** v2.2
- **Laravel Tinker:** v2.10.1

---

## 🧪 Testing & Quality

### Testing Frameworks
- **Pest:** v3.8 (Primary Test Framework)
- **PHPUnit:** v11.5.3 (Underlying Framework)
- **Mockery:** v1.6 (Mocking)

### Code Quality
- **PHPStan:** 2.1.40 (Level 9 strict)
- **Laravel Pint:** v1.24 (PSR-12 + Laravel style)
- **Xdebug:** Optional (via `make debug-on`)

### Aktuelle Metriken
- **Tests:** 128 (100+ Unit, 20+ Feature)
- **Assertions:** 335+
- **Coverage:** 98.2% ✅
- **PHPStan Errors:** 0
- **Coverage-Minimum:** 95%

---

## 🎨 Frontend

### Build Tools
- **TailwindCSS:** v3
- **PostCSS:** Latest
- **Vite:** Laravel-integriert
- **Node:** Latest LTS (im Docker-Container)

### CSS Framework
- **Utility-First:** TailwindCSS
- **Responsive:** Mobile-First (geplant: Commit 20)
- **Dark-Mode:** Geplant (Commit 20a)

### Build-Kommandos
```bash
npm run dev        # Development-Modus (watch)
npm run build      # Production-Build (minified)
```

---

## 🐳 Docker-Services

### PHP-FPM
- **Image:** PHP 8.5-FPM Alpine
- **Port:** 9000 (intern)
- **Features:**
  - Composer 2.x
  - Xdebug (optional, build-time ARG)
  - SQLite Support
  - GD Extension
  - Intl Extension

**Volumes:**
- `./src:/var/www/html`
- `./docker/php/custom.ini:/usr/local/etc/php/conf.d/custom.ini`

**User:** `appuser:appgroup` (UID:GID dynamisch via `.env`)

---

### Nginx
- **Image:** nginx:alpine
- **Port:** 8080 → 80
- **Webroot:** `/var/www/html/public`
- **Config:** `./docker/nginx/default.conf`

**URL:** http://localhost:8080

---

### Node
- **Image:** node:lts-alpine
- **Zweck:** Tailwind Build Pipeline
- **Working Dir:** `/app`
- **Volume:** `./src:/app`

**Kommandos:**
```bash
docker exec resumehaven-node npm install
docker exec resumehaven-node npm run build
```

---

### Mailpit
- **Image:** axllent/mailpit:latest
- **SMTP Port:** 1025
- **Web UI Port:** 8025 → 8025

**URL:** http://localhost:8025

**Zweck:** Lokaler SMTP-Testserver für E-Mails

---

## 🗄️ Datenbank

### Entwicklung
- **Engine:** SQLite
- **File:** `src/database/database.sqlite`
- **Vorteil:** In-App, keine externe DB nötig

### Production (geplant)
- **Engine:** MySQL 8.x
- **Hoster:** IONOS Webspace
- **Config:** `.env` (`DB_CONNECTION=mysql`)

### Migrations
```bash
php artisan migrate              # Migrationen ausführen
php artisan migrate:fresh --seed # Reset + Seed
```

**Aktuelle Tabellen:**
- `analysis_cache` — Cache für Analyse-Ergebnisse

---

## 🔧 Make-Kommandos (wichtigste)

### Tests & Coverage
```bash
make test                   # Alle Tests (Pest)
make test-unit              # Nur Unit-Tests
make test-feature           # Nur Feature-Tests
make test-coverage          # Coverage-Check (min 95%)
make test-coverage-report   # HTML-Report erzeugen
make coverage-open          # Report im Browser
make coverage-clean         # Reports löschen
```

### Code-Qualität
```bash
make phpstan                # Static Analysis (Level 9)
make pint-fix               # Code-Formatting
make pint-analyse           # Nur Analyse (kein Fix)
```

### Debugging
```bash
make debug-on               # Xdebug aktivieren (rebuild)
make debug-off              # Xdebug deaktivieren
make debug-status           # Status prüfen
```

### Docker
```bash
make docker-up              # Container starten
make docker-down            # Container stoppen
make docker-restart         # Schneller Neustart
make docker-rebuild         # Neuer Build
make docker-clean           # Volumes löschen
```

### Shell-Zugriff
```bash
make php-shell              # Bash im PHP-Container
make node-shell             # Shell im Node-Container
make nginx-shell            # Shell im Nginx-Container
```

### Cache & DB
```bash
make php-cache-clear        # Laravel-Cache leeren
make db-migrate             # Migrationen ausführen
make db-seed                # Seeds laden
make cache-clear-analysis   # Analyse-Cache leeren
```

---

## 🌐 URLs

### Local Development
- **App:** http://localhost:8080
- **Mailpit UI:** http://localhost:8025

### Production (geplant)
- **Domain:** TBD (IONOS Webspace)
- **SSL:** Let's Encrypt (geplant)

---

## 📝 Konfiguration

### AI-Provider (`.env`)
```env
# Provider: mock (dev) oder gemini (prod)
AI_PROVIDER=gemini

# Gemini-Konfiguration
AI_GEMINI_API_KEY=xxx
AI_GEMINI_MODEL=gemini-2.5-flash

# Mock-Konfiguration (für Entwicklung ohne API-Kosten)
AI_MOCK_SCENARIO=realistic  # realistic | high_score | low_score | no_match
AI_MOCK_DELAY=500           # Simulierte API-Latenz in ms
```

### Xdebug (`.env`)
```env
# Xdebug-Modi (nur wenn INSTALL_XDEBUG=true)
XDEBUG_MODE=debug,coverage

# IDE-Key
XDEBUG_CONFIG="idekey=resumehaven"
```

### Docker-User (`.env`)
```env
# User-ID und Group-ID für PHP-Container
UID=1000
GID=1000
```

---

## 🔄 Update-Strategie

### Dependencies
```bash
# Composer
composer update

# NPM
npm update

# Laravel Boost (überschreibt src/AGENTS.md!)
composer update laravel/boost
```

### PHPStan-Baseline
Bei Major-Updates von PHPStan Baseline neu generieren:
```bash
vendor/bin/phpstan analyse --generate-baseline
```

### Docker-Images
Bei Dockerfile-Änderungen neu bauen:
```bash
make docker-rebuild
```

---

## 🧰 Entwicklungs-Tools

### IDE-Support
- **VSCode:** `.vscode/launch.json` (Xdebug)
- **PhpStorm:** Path-Mapping `/var/www/html` → `./src`

### Git-Workflow
- **Branch-Strategie:** Feature-Branches (`feature/commit-XX-name`)
- **Commit-Convention:** Siehe `COMMIT_PLAN.md`
- **PR-Template:** `.github/PULL_REQUEST_TEMPLATE.md` (inkl. SOLID-Gate)

### CI/CD (geplant)
- **GitHub Actions:** Geplant (Commit 23+)
- **Pre-Commit Hooks:** Geplant (Pint, PHPStan, Tests)

---

## 📊 Performance-Benchmarks

### Test-Execution
- **Ohne Xdebug:** ~60s (128 Tests)
- **Mit Xdebug:** ~80s (+33% Overhead)

### Coverage-Report-Generierung
- **Clover XML:** ~10s
- **HTML-Report:** ~15s

### Cache-Hit-Rate
- **Development:** ~80% (bei wiederholten Anfragen)
- **Production:** TBD

---

## 🔐 Security

### Input-Validierung
- **Max File Size:** 50KB pro Input
- **Pattern-Detection:** SQL, XSS, Event-Handler
- **Sanitization:** Null-Bytes, Whitespace, Line-Endings

### CSRF-Protection
- **Laravel CSRF:** Aktiv in allen POST-Formularen
- **Token:** `@csrf` in Blade-Templates

### SQL-Injection-Prävention
- **Repository Pattern:** Prepared Statements
- **Kein Raw-SQL:** Außer in Repositories

### Prompt-Injection-Schutz
- **Strikte System-Rules:** Im AI-Analyzer-Prompt
- **Input-Behandlung:** Als "UNVERTRAUTER INHALT"

---

## 📚 Dokumentations-Stack

### Markdown
- **Docs:** `/docs/*.md`
- **GitHub Pages:** Jekyll (Theme: Minimal)

### PHPDoc
- **Standard:** PHPStan-kompatibel
- **Array-Shapes:** Detaillierte Type-Hints

### Code-Kommentare
- **Komplexität:** Nur bei komplexer Logik
- **Regeln:** Beschreibe "Warum", nicht "Was"

---

## 🎯 Browser-Support (geplant)

### Desktop
- **Chrome:** Latest 2 Versionen
- **Firefox:** Latest 2 Versionen
- **Safari:** Latest 2 Versionen
- **Edge:** Latest 2 Versionen

### Mobile (Commit 20)
- **iOS Safari:** Latest 2 Versionen
- **Chrome Android:** Latest 2 Versionen

---

## 📚 Siehe auch

- **Projektüberblick:** `docs/ai/PROJECT_OVERVIEW.md`
- **Agent-Kontext:** `docs/ai/AGENT_CONTEXT.md`
- **Architektur:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Development Setup:** `docs/DEVELOPMENT.md`
- **Debugging:** `docs/DEBUGGING.md`

