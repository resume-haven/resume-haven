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
| **Debugging** | [docs/DEBUGGING.md](./DEBUGGING.md) |
| **Architektur** | [docs/ARCHITECTURE.md](./ARCHITECTURE.md) |
| **Coding Guidelines** | [docs/CODING_GUIDELINES.md](./CODING_GUIDELINES.md) |
| **Contributing** | [docs/CONTRIBUTING.md](./CONTRIBUTING.md) |

---

## ✅ Checkliste zum Starten

- [ ] Docker & Docker Compose installiert
- [ ] `docker compose up -d` ausgeführt
- [ ] http://localhost:8080 öffnet die Seite
- [ ] `make php-shell` funktioniert
- [ ] `make test` laufen die Tests grün

**Viel Erfolg beim Entwickeln!** 🚀



