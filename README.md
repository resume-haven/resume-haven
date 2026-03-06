# ResumeHaven – MVP

ResumeHaven ist ein leichtgewichtiges, textbasiertes Analyse‑Tool, das Stellenausschreibungen und Lebensläufe miteinander vergleicht und strukturiert auswertet.  
Das Projekt basiert auf Laravel 12, TailwindCSS und einer modularen Analyse‑Engine.

Dieses Repository enthält den **Produktivcode** des MVP.

---

# 🚀 Features (MVP)

- Textbasierte Analyse ohne KI  
- Extraktion von Anforderungen aus Stellenausschreibungen  
- Extraktion von Erfahrungen aus Lebensläufen  
- Matching zwischen Anforderungen und Erfahrungen  
- Identifikation von Lücken  
- Zuordnung relevanter Erfahrungen  
- Minimalistische UI (TailwindCSS)  
- Keine Speicherung von Nutzerdaten  

---

# 📐 Architektur

Die Anwendung folgt einer **Domain-driven, Command/Query-orientierten Architektur**:

- **Domain-Driven Design** (modulare Geschäftsbereiche)
- **CQRS-Light** (Command/Handler Pattern)
- **Single Action Controllers** (Controller sind dünn, ~34 Zeilen)
- **Repository Pattern** (Persistence-Abstraktion)
- **UseCase Pattern** (Business-Logic-Orchestrierung)

### Struktur

```
app/Domains/Analysis/
├── Commands/        # Request-Objekte
├── Handlers/        # Command-Handler
├── UseCases/        # Business-Logic
│   ├── ExtractDataUseCase/
│   ├── MatchingUseCase/
│   └── GapAnalysisUseCase/
├── Cache/           # Cache-Layer
│   ├── Actions/
│   └── Repositories/
└── Dto/             # Data Transfer Objects
```

Eine vollständige Architekturdokumentation befindet sich hier:  
👉 [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)

Coding Guidelines und Best Practices:  
👉 [`docs/CODING_GUIDELINES.md`](docs/CODING_GUIDELINES.md)

Alle Dokumentationen:  
👉 [`docs/index.md`](docs/index.md)

---

# 🛠️ Installation (lokale Entwicklung)

## Voraussetzungen
- Docker & Docker Compose  
- Git  
- Make (zum Ausführen von `make` Kommandos)
- (optional) Node lokal, falls kein Docker genutzt wird  

## Projekt klonen
```bash
git clone https://github.com/<dein-user>/resume-haven.git
cd resume-haven
```

## Docker starten (empfohlen)
```bash
make docker-up
# oder manuell:
docker compose up -d
```

## Laravel Setup
```bash
make setup
# oder manuell:
docker exec -it resumehaven-php composer install
docker exec -it resumehaven-php cp .env.example .env
docker exec -it resumehaven-php php artisan key:generate
docker exec -it resumehaven-php php artisan migrate
```

## Anwendung aufrufen
http://localhost:8080

---

## 🐛 Debugging aktivieren (optional)

Für lokales Debugging mit Xdebug:

```bash
make debug-on       # Xdebug aktivieren
# Dann Breakpoint setzen und debuggen
make debug-off      # Xdebug deaktivieren (schneller Mode)
```

Siehe [Debugging Guide](./docs/DEBUGGING.md) für Details.

---

## 📚 Häufige Kommandos

```bash
make help                 # Alle Kommandos anzeigen
make test                 # Tests ausführen
make test-coverage        # Coverage-Check in Konsole (min 80%)
make test-coverage-report # Coverage-Dateien unter src/coverage-report/
make pint-fix             # Code formatieren
make phpstan              # Statische Analyse
make php-shell            # Bash im PHP-Container
```

Vollständige Übersicht: [Development Setup](./docs/DEVELOPMENT.md)

---

# 🐳 Docker-Services

- **php-fpm** – PHP 8.5 + Composer (+ optional Xdebug)
- **nginx** – Webserver  
- **node** – Tailwind Build Pipeline  
- **mailpit** – lokaler SMTP-Testserver  

Mailpit UI:  
http://localhost:8025

---

# 📦 Deployment (IONOS Webspace)

IONOS unterstützt kein Docker.  
Für das MVP wird ein klassisches Laravel‑Deployment genutzt:

1. `public/` als Webroot konfigurieren  
2. `vendor/` hochladen  
3. Tailwind Build lokal erzeugen und hochladen  
4. `.htaccess` für Laravel Routing verwenden  
5. `.env` für Produktionsumgebung anpassen  

---

# 💾 Datenbank-Setup

## Automatisches Setup (Docker - empfohlen)

Die Datenbank-Migrationen werden beim Container-Start automatisch ausgeführt:

1. **Beim ersten Start**: `entrypoint.sh` erstellt die SQLite-Datei und führt Migrationen durch
2. **Bei jedem Neustart**: Migrationen werden erneut aufgerufen (idempotent - keine Duplikate)
3. **Seeding** (optional): Kann manuell mit `php artisan db:seed` ausgeführt werden

## Migrationen manuell ausführen

```bash
# Status der Migrationen prüfen
docker exec -it resumehaven-php php artisan migrate:status

# Migrationen ausführen
docker exec -it resumehaven-php php artisan migrate

# Migrationen rückgängig machen
docker exec -it resumehaven-php php artisan migrate:rollback

# Alle Tabellen löschen und neu migrieren
docker exec -it resumehaven-php php artisan migrate:refresh
```

## Datenbank-Strategie

- **Entwicklung**: SQLite (in-app, keine externe DB nötig)
- **Production**: MySQL/PostgreSQL (via `.env`)
- **Migrationen**: Versioniert, automatisch idempotent
- **Seeds**: Optional, manuell auslösbar

---

# 🧪 Tests

```bash
docker exec -it php bash
php artisan test
```

---

# 🔧 Artisan Commands

## Cache Management

### Analysis Cache leeren

```bash
# Alle Cache-Einträge löschen
php artisan cache:clear-analysis

# Nur Einträge älter als N Tage löschen
php artisan cache:clear-analysis --older-than=30
```

**Verwendung mit Makefile**:
```bash
make cache-clear-analysis
```

**Cronjob-Integration** (optional, für Production):
```php
// In app/Console/Kernel.php
$schedule->command('cache:clear-analysis --older-than=30')
    ->dailyAt('03:00');
```

**Optionen**:
- `--older-than=N` – Löscht nur Einträge, die älter als N Tage sind (basierend auf `updated_at`)

**Beispiele**:
- `✓ Cleared 42 cache entries.` (alle gelöscht)
- `✓ Deleted 15 cache entries older than 30 days.` (alte gelöscht)
- `✓ Cache table is already empty.` (kein Cache vorhanden)

---

# 📁 Dokumentation

Die vollständige technische Dokumentation befindet sich im `docs/` Verzeichnis:

👉 **[Dokumentations-Index](docs/index.md)** (oder online: [GitHub Pages](https://username.github.io/resume-haven/))

Wichtige Dokumente:
- **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** – Technische Architektur & Design Patterns
- **[CODING_GUIDELINES.md](docs/CODING_GUIDELINES.md)** – Best Practices & Standards
- **[REFACTORING_SUMMARY.md](docs/REFACTORING_SUMMARY.md)** – Architektur-Refactoring Zusammenfassung
- **[AGENTS.md](docs/AGENTS.md)** – AI-Agenten Dokumentation
- **[ROADMAP.md](docs/ROADMAP.md)** – Feature-Roadmap & Vision
- **[CONTRIBUTING.md](docs/CONTRIBUTING.md)** – Contribution Guidelines

Entwicklungsplan:
- **[COMMIT_PLAN.md](COMMIT_PLAN.md)** – Detaillierter Commit-by-Commit Plan

---

# 📜 Lizenz

Dieses Projekt ist aktuell **nicht öffentlich lizenziert**.  
Alle Rechte vorbehalten.

---

# 🤝 Mitwirken

Pull Requests sind willkommen, sobald das MVP stabil ist.  
Bitte lies die **[Contribution Guidelines](docs/CONTRIBUTING.md)** vor dem Beitragen.  

Bis dahin dient dieses Repository der initialen Entwicklung.

