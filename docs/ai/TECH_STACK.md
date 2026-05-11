# ⚙️ Tech Stack & Versions

## 📦 PHP & Laravel

- **PHP:** 8.5.3
- **Laravel:** 12.x
- **Laravel AI:** v0.2.1 (Gemini provider active)
- **Laravel Prompts:** v0
- **Laravel Pail:** v1.2.2
- **Laravel Pint:** v1.24
- **Laravel Boost:** v2.2
- **Laravel Tinker:** v2.10.1

---

## 🧪 Testing & Quality

### Testing Frameworks
- **Plague:** v3.8 (Primary Test Framework)
- **PHPUnit:** v11.5.3 (Underlying Framework)
- **Mockery:** v1.6 (Mocking)

### Code Quality
- **PHPStan:** 2.1.40 (Level 9 strict)
- **Laravel Pint:** v1.24 (PSR-12 + Laravel style)
- **Xdebug:** Optional (via `make debug-on`)

### Current metrics
- **Tests:** 128 (100+ Unit, 20+ Feature)
- **Assertions:** 335+
- **Coverage:** 98.2% ✅
- **PHPStan Errors:** 0
- **Coverage minimum:** 95%

---

## 🎨 Frontend

### Build Tools
- **TailwindCSS:** v3
- **PostCSS:** Latest
- **Vite:** Laravel integrated
- **Node:** Latest LTS (in Docker container)

### CSS framework
- **Utility-First:** TailwindCSS
- **Responsive:** Mobile-First (planned: Commit 20)
- **Dark mode:** Planned (Commit 20a)

### Build commands
```bash
npm run dev        # Development-Modus (watch)
npm run build      # Production-Build (minified)
```

---

## 🐳 Docker Services

### PHP FPM
- **Image:** PHP 8.5-FPM Alpine
- **Port:** 9000 (internal)
- **Features:**
  - Composer 2.x
  - Xdebug (optional, build-time ARG)
  - SQLite support
  - DG Extension
  - Intl Extension

**Volume:**
- `./src:/var/www/html`
- `./docker/php/custom.ini:/usr/local/etc/php/conf.d/custom.ini`

**User:** `appuser:appgroup` (UID:GID dynamic via `.env`)

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
- **Purpose:** Tailwind Build Pipeline
- **Working Dir:** `/app`
- **Volume:** `./src:/app`

**Commands:**
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

**Purpose:** Local SMTP test server for email

---

## 🗄️Database

### Development
- **Engine:** SQLite
- **File:** `src/database/database.sqlite`
- **Advantage:** In-app, no external DB required

### Production (planned)
- **Engine:** MySQL 8.x
- **Hoster:** IONOS web space
- **Config:** `.env` (`DB_CONNECTION=mysql`)

### Migrations
```bash
php artisan migrate              # Migrationen ausführen
php artisan migrate:fresh --seed # Reset + Seed
```

**Current tables:**
- `analysis_cache` — Cache for analysis results

---

## 🔧 Make commands (most important)

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

### Code quality
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

### Shell access
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

### Production (planned)
- **Domain:** TBD (IONOS Webspace)
- **SSL:** Let's Encrypt (planned)

---

## 📝 Configuration

### AI Provider (`.env`)
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

### Docker user (`.env`)
```env
# User-ID und Group-ID für PHP-Container
UID=1000
GID=1000
```

---

## 🔄 Update strategy

### Dependencies
```bash
# Composer
composer update

# NPM
npm update

# Laravel Boost (überschreibt src/AGENTS.md!)
composer update laravel/boost
```

### PHPStan baseline
Regenerate baseline for major updates of PHPStan:
```bash
vendor/bin/phpstan analyse --generate-baseline
```

### Docker images
Rebuild for Dockerfile changes:
```bash
make docker-rebuild
```

---

## 🧰 Development tools

### IDE support
- **VSCode:** `.vscode/launch.json` (Xdebug)
- **PhpStorm:** Path mapping `/var/www/html` → `./src`

### Git workflow
- **Branch strategy:** Feature branches (`feature/commit-XX-name`)
- **Commit Convention:** See `COMMIT_PLAN.md`
- **PR template:** `.github/PULL_REQUEST_TEMPLATE.md` (incl. SOLID Gate)

### CI/CD (planned)
- **GitHub Actions:** Planned (Commit 23+)
- **Pre-Commit Hooks:** Planned (Pint, PHPStan, Tests)

---

## 📊 Performance benchmarks

### Test execution
- **Without Xdebug:** ~60s (128 tests)
- **With Xdebug:** ~80s (+33% overhead)

### Coverage report generation
- **Clover XML:** ~10s
- **HTML report:** ~15s

### Cache hit rate
- **Development:** ~80% (for repeated requests)
- **Production:** TBD

---

## 🔐 Security

### Input validation
- **Max File Size:** 50KB per input
- **Pattern detection:** SQL, XSS, event handler
- **Sanitization:** Zero bytes, whitespace, line endings

### CSRF protection
- **Laravel CSRF:** Active in all POST forms
- **Token:** `@csrf` in blade templates

### SQL injection prevention
- **Repository Pattern:** Prepared Statements
- **No Raw SQL:** Except in repositories

### Prompt injection protection
- **Strict system rules:** In the AI ​​Analyzer prompt
- **Input Treatment:** As "UNTRAVIDENT CONTENT"

---

## 📚 Documentation stack

### Markdown
- **Docs:** `/docs/*.md`
- **GitHub Pages:** Jekyll (Theme: Minimal)

### PHPDoc
- **Default:** PHPStan compatible
- **Array shapes:** Detailed type hints

### Code comments
- **Complexity:** Only for complex logic
- **Rules:** Describe “Why,” not “What”

---

## 🎯 Browser support (planned)

###Desktop
- **Chrome:** Latest 2 versions
- **Firefox:** Latest 2 versions
- **Safari:** Latest 2 versions
- **Edge:** Latest 2 versions

### Mobile (Commit 20)
- **iOS Safari:** Latest 2 versions
- **Chrome Android:** Latest 2 versions

---

## 📚 See then

- **Project overview:** `docs/ai/PROJECT_OVERVIEW.md`
- **Agent Context:** `docs/ai/AGENT_CONTEXT.md`
- **Architecture:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Development Setup:** `docs/DEVELOPMENT.md`
- **Debugging:** `docs/DEBUGGING.md`

---

**Last updated**: 2026-03-09
**Version**: 2.1 (Consolidated AI Documentation Context)