# Docker Setup für ResumeHaven mit FrankenPHP

## Überblick

Dieses Docker-Setup enthält:

- **FrankenPHP** (PHP 8.5 mit Caddy) - Web Server

## Voraussetzungen

- Docker & Docker Compose installiert
- Git

## Schnelleinstieg

### 1. Umgebungsvariablen vorbereiten

```bash
cp .env.docker .env
```

### 2. Docker-Umgebung starten

```bash
docker-compose up -d
```

Das erste Mal kann das Building länger dauern (~5-10 Minuten).

### 3. Applikation initialisieren

```bash
# SSH in den Container
docker-compose exec app bash

# Im Container (nur wenn DB konfiguriert ist):
php artisan migrate
```

Alternativ (ohne Shell zu betreten, nur mit DB-Konfiguration):

```bash
docker-compose exec app php artisan migrate
```

### 4. Assets builden (optional, für Vite)

```bash
docker-compose exec app npm run build
```

## Zugriff

| Service | URL | Credentials |
|---------|-----|-------------|
| **Web App** | http://localhost | - |

## Wichtige Befehle

```bash
# Logs anschauen
docker-compose logs -f app

# In den App-Container gehen
docker-compose exec app bash

# Artisan-Befehle
docker-compose exec app php artisan tinker
docker-compose exec app php artisan make:model User -m

# Tests ausführen
docker-compose exec app php artisan test

# Formatierung & Linting
docker-compose exec app composer lint
docker-compose exec app composer test:lint

# Container stoppen
docker-compose down

```

## Entwicklungs-Tipps

### Dateien ändern
- Der lokale Ordner ist in den Container gemountet → Änderungen sind sofort sichtbar
- PHP-Cache wird bei FrankenPHP automatisch aktualisiert

### Hot Module Replacement (Vite)
```bash
docker-compose exec app npm run dev
```

## Troubleshooting

### Port 80/443 bereits in Verwendung
Ändere in `docker-compose.yml`:
```yaml
ports:
  - "8080:80"    # Statt 80
  - "8443:443"   # Statt 443
```
Dann erreichbar unter http://localhost:8080

### composer install fehlgeschlagen
```bash
# Container löschen und neu starten
docker-compose down
docker-compose up -d
```

## Production Notes

⚠️ **Für Production:**
- APP_DEBUG=false setzen
- Secrets nicht in .env hardcoden
- APP_KEY vor Production generieren: `php artisan key:generate`
- Proper Logging konfigurieren

## Anpassungen

### Externe Datenbank nutzen
Setze in `.env.docker` (oder `.env`) die Variablen `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

### Custom Ports
Alle Ports sind in `docker-compose.yml` konfigurierbar.

---

**Fragen?** Siehe Laravel-Dokumentation: https://laravel.com/docs
