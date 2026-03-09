# 🚀 Local Development Setup

Anleitung für lokale Entwicklung mit Docker.

---

## 📦 Voraussetzungen

- Docker & Docker Compose
- WSL2 (Windows) oder direkt Linux/Mac
- Make (für `make` Kommandos)
- Git

---

## 🏗️ Container starten

```bash
docker compose up -d
# oder mit Make:
make docker-up
```

Das startet:
- **PHP-FPM** (Port 9000)
- **Nginx** (Port 8080)
- **Node** (für Assets)
- **Mailpit** (Test-Mailbox, Port 8025)

Zugang: **http://localhost:8080**

---

## 🔨 Häufige Kommandos

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
make test-security          # OWASP-orientierte Security-Tests
make test-security-strict   # Erweiterte Security-Tests (stop-on-failure)
make test-security-gate     # Security-Tests + PHPStan + Pint-Analyse
make test-coverage          # Tests mit Coverage (benötigt Xdebug, min 95%)
make test-coverage-report   # Coverage-Dateien (clover+xml/html)
make coverage-open          # Öffnet HTML-Coverage-Report im Browser
make coverage-clean         # Löscht alte Coverage-Reports
```

**Code Coverage Anforderungen:**
- **Minimum:** 95% Total Coverage
- **GeminiAiAnalyzer.php:** ≥80%
- **Aktueller Stand:** 98.2% Total ✅

### **Code-Qualität**

```bash
make pint-fix       # Code automatisch formatieren
make pint-analyse   # Nur Analyse (kein Fix)
make phpstan        # Statische Analyse
```

### **Shells & Container**

```bash
make php-shell      # Bash im PHP-Container
make node-shell     # Shell im Node-Container
make nginx-shell    # Shell im Nginx-Container
```

### **Datenbank**

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

## 🐛 Debugging mit Xdebug

Siehe **[Debugging Guide](./DEBUGGING.md)** für vollständige Anleitung.

**Quick Start:**
```bash
make debug-on       # Xdebug aktivieren (debug + coverage)
make debug-status   # Status prüfen
make php-shell      # Shell (XDEBUG_CONFIG ist bereits gesetzt)
```

**Coverage-Reports:**
```bash
make debug-on               # Xdebug aktivieren
make test-coverage          # Coverage-Check (min 95%)
make test-coverage-report   # Coverage-Dateien unter src/coverage-report/
make coverage-open          # HTML-Report im Browser öffnen
make coverage-clean         # Alte Reports löschen
```

Dann in IDE auf Port 9003 Breakpoint setzen und Script ausführen!

---

## 📊 Workflow für Entwicklung

### **Typischer Developer-Tag:**

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

## 🐚 PHP-Shell Tipps

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

### **Container nicht erreichbar?**

```bash
docker ps                   # Laufen alle Container?
docker compose logs         # Fehler in den Logs?
make docker-restart         # Schnell neu starten
make docker-rebuild         # Komplett neu bauen
```

### **Port schon belegt?**

```bash
sudo lsof -i :8080         # Wer nutzt Port 8080?
make docker-down            # Container stoppen
```

### **Cache/Daten löschen?**

```bash
make docker-clean           # Container + Volumes löschen
make docker-up              # Neu starten (frisch!)
```

---

## 📚 Weitere Dokumentation

| Thema | Link |
|-------|------|
| **Roadmap** | [docs/ROADMAP.md](./docs/ROADMAP.md) |
| **Changelog** | [CHANGELOG.md](./CHANGELOG.md) |
| **Debugging** | [docs/DEBUGGING.md](./docs/DEBUGGING.md) |
| **Architektur** | [docs/ARCHITECTURE.md](./docs/ARCHITECTURE.md) |
| **Coding Guidelines** | [docs/CODING_GUIDELINES.md](./docs/CODING_GUIDELINES.md) |
| **Contributing** | [docs/CONTRIBUTING.md](./docs/CONTRIBUTING.md) |

---

## ✅ Checkliste zum Starten

- [ ] Docker & Docker Compose installiert
- [ ] `docker compose up -d` ausgeführt
- [ ] http://localhost:8080 öffnet die Seite
- [ ] `make php-shell` funktioniert
- [ ] `make test` laufen die Tests grün

**Viel Erfolg beim Entwickeln!** 🚀

## 🛡️ Security-Test-Template (OWASP-orientiert)

Nutze dieses Template bei sicherheitsrelevanten Änderungen (Input-Validierung, Auth, externe Requests, Prompting).

### 1) Minimaler Sicherheits-Testlauf

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

### 2) PR-Checkliste Security

- [ ] Eingaben als untrusted behandelt (Validation + Sanitization)
- [ ] Output kontextgerecht escaped/encoded (kein unescaped HTML/JS)
- [ ] CSRF für alle POST-Formulare vorhanden (`@csrf`)
- [ ] Keine Secrets im Code (nur Config/Env)
- [ ] Externe Requests mit Timeouts/Allowlist abgesichert
- [ ] Prompt-/Input-Injection berücksichtigt
- [ ] Security-Regression-Tests ergänzt/aktualisiert

### 3) OWASP-Mapping Kurzcheck

- **A01 Access Control**: Unauthorized Access Tests vorhanden
- **A03 Injection**: SQL/XSS/Prompt-Injection Tests vorhanden
- **A05 Misconfiguration**: Sichere Defaults geprüft
- **A06 Vulnerable Components**: Dependency-Update/CVE-Check berücksichtigt
- **A09 Logging/Monitoring**: Sicherheitsrelevante Fehler werden ohne Secrets geloggt

### 4) Empfohlene Testdatei-Namen

- `tests/Feature/SecurityPromptInjectionTest.php`
- `tests/Feature/InputValidationTest.php`
- `tests/Feature/ApiErrorHandlingTest.php`
- `tests/Feature/SecurityAccessControlTest.php`

---
