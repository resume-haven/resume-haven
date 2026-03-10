# ResumeHaven – Commit-Plan

Dieser Commit‑Plan definiert die empfohlene Reihenfolge der ersten Commits im Projekt.  
Er sorgt für eine klare, nachvollziehbare Git‑History und erleichtert die Zusammenarbeit mit GitHub Copilot und anderen Entwicklern.

Jeder Commit ist klein, fokussiert und baut logisch auf dem vorherigen auf.

**Letzte Aktualisierung:** 2026-03-10  
**Aktueller Stand:** Commit 21a abgeschlossen, Commit 22 implementiert (Basis-Flow) und in Verifikation/Dokumentation

---

## 📊 Status-Überblick

### ✅ Abgeschlossen (Commits 1-21a)
- **Commit 1-11:** Docker-Setup, Laravel-Installation, Basis-Konfiguration
- **Commit 12:** KI-Integration (Gemini), Analyse-Engine, Validierung, Tests
- **Commit 13:** KI-Prompt-Engineering & Error-Handling
- **Commit 14:** UI-Verbesserungen (Score-Panel, Tags, Layout)
- **Commit 15:** Analyse-Cache (Hash-basiert, DB-gestützt)
- **Commit 15a:** Domain-Architektur-Refactoring (DDD, CQRS, SOLID)
- **Commit 16:** Input-Validierung & Security (OWASP)
- **Commit 16a:** AI-Provider-Abstraktion (Mock/Gemini via Interface)
- **Commit 16b:** AI-Response-Tags (Match-Tags, Gap-Tags mit Fallback)
- **Commit 16c:** Prompt-Injection-Schutz (Strikte System-Rules)
- **Commit 17:** KI-Empfehlungen (RecommendationDto, Priority-Badges, View)
- **Commit 18:** Cache-Management (Command zum Leeren)
- **Commit 18a:** Security-Härtung & Kontext-Dokumentation (WORKING_BASELINE)
- **Commit 20:** Quality-Gates (Coverage 98.2%, PHPStan Level 9, Xdebug)
- **Commit 20a:** Code-Qualität (Architektur-Cleanup, Test-Erweiterungen)
- **Commit 20b:** Legal-Seiten (Impressum, Datenschutz, Kontakt, Lizenzen)
- **Commit 21:** Responsive Layout & Mobile-First (Alpine.js, Touch-Optimierungen)
- **Commit 21a:** Dark-Mode Support (System-Präferenz, Toggle, Persistierung)

**Hinweis:** Commit 19 wurde übersprungen (Nummerierungslücke in der historischen Entwicklung)

### 🔄 In Umsetzung (Commit 22)
- **Commit 22:** CV-Speicherung (Profile Context, verschlüsselt, token-basiert)
  - Branch: `feature/commit-22-profile-cv-storage`
  - Status: Basis-Implementierung abgeschlossen, Tests/PHPStan/Pint grün
  - Detailplan: `docs/PLANNING_COMMIT_22.md`
  - Implementierungsleitfaden: `docs/COMMIT_22_IMPLEMENTATION_GUIDE.md`

### ⏳ Zukünftig (Commits 23+)
- **Commit 23+:** CI/CD, Deployment, weitere Features

---

## 🧱 Commit 1 – Projektgrundstruktur

**Zweck:** Repository initialisieren und Basisordner anlegen.

**Inhalt:**

- Leeres Repository initialisiert  
- Ordnerstruktur angelegt:  
  - `/docker/php`  
  - `/docker/nginx`  
  - `/docker/node`  
  - `/src`  
- `.gitignore` hinzugefügt  
- `README.md` hinzugefügt  

---

## 🐳 Commit 2 – docker-compose Grundgerüst

**Zweck:** Grundstruktur der Container definieren.

**Inhalt:**

- `docker-compose.yml` mit Service‑Platzhaltern  
- Services: php, nginx, node, mailpit  
- Noch keine Konfiguration, nur Struktur  

---

## 🧩 Commit 3 – Dockerfiles (Skeleton)

**Zweck:** Basis-Dockerfiles anlegen.

**Inhalt:**

- `docker/php/Dockerfile`  
- `docker/nginx/Dockerfile`  
- `docker/node/Dockerfile`  
- Alle noch ohne Inhalt, nur FROM‑Zeilen  

---

## 🌐 Commit 4 – Nginx-Konfiguration vorbereiten

**Zweck:** Webserver-Struktur vorbereiten.

**Inhalt:**

- `docker/nginx/default.conf` hinzugefügt  
- Minimaler Serverblock  
- Noch ohne Laravel‑Routing  

---

## 🐘 Commit 5 – Finalize PHP container for Laravel

### Added

- Completed PHP 8.5 Dockerfile with all required Laravel extensions:
  - pdo_mysql  
  - mbstring  
  - xml  
  - zip  
  - intl  
  - gd  
  - bcmath  
- Installed required system packages:
  - git  
  - unzip  
  - libzip-dev  
  - libpng-dev  
  - libonig-dev  
  - libxml2-dev  
  - libicu-dev  
  - libcurl4-openssl-dev  
- Added Composer to the PHP container (copied from composer:2 image)

### Updated

- Ensured tokenizer, curl, and pdo are **not** installed manually (already built into PHP 8.5)
- Cleaned up Dockerfile to avoid unnecessary layers and reduce image size

### Result

The PHP container is now fully Laravel‑ready and supports:

- Composer installation
- Laravel framework installation
- All required PHP extensions for Laravel 10/11/12
- Clean and reproducible builds

---

## 🎨 Commit 6 – Switch Node container to secure Alpine base image

### Added

- Updated Node Dockerfile to use the lightweight and security‑focused `node:20-alpine` base image.
- Set `/var/www/html` as the working directory for all Node/Tailwind build operations.

### Updated

- Replaced previous Node image (`node:20`) due to multiple known CVEs in the Debian-based variant.
- Ensured the Node container remains minimal, containing only what is required for TailwindCSS builds.
- No global npm packages or additional system dependencies installed to keep the attack surface minimal.

### Result

The Node container is now:

- significantly smaller,
- more secure (Alpine base),
- fully compatible with npm and npx,
- ready for TailwindCSS setup in the upcoming commit.

No changes were made to docker-compose.yml in this commit.

---

## 🧭 Commit 7 – Finalize docker-compose configuration for all services

### Added

- Completed `docker-compose.yml` with fully defined services:
  - **php** (Laravel backend, built from `docker/php`)
  - **nginx** (webserver, built from `docker/nginx`)
  - **node** (Tailwind build environment, built from `docker/node`)
  - **mailpit** (local mail testing environment)

### Updated

- Mounted project source directory (`./src`) into all relevant containers at `/var/www/html`.
- Added consistent container names:
  - `resumehaven-php`
  - `resumehaven-nginx`
  - `resumehaven-node`
  - `resumehaven-mailpit`
- Configured nginx to depend on php for correct startup order.
- Exposed required ports:
  - `8080:80` for nginx
  - `8025:8025` and `1025:1025` for Mailpit

### Result

The complete development environment can now be started with:

```bash
docker-compose up --build
```

All containers start correctly and interact as intended:

- nginx forwards requests to php-fpm  
- php has access to the application code  
- node is ready for Tailwind builds  
- Mailpit is available for email testing  

Laravel installation can proceed in the next commit.

---

## 🧠 Commit 8 – Install Laravel application into /src

### Added

- Installed a fresh Laravel application into the `/src` directory using:

  ```bash
  composer create-project laravel/laravel .
  ```

- Generated a new `.env` file based on `.env.example`.
- Executed `php artisan key:generate` to create a valid application key.

### Updated

- Ensured the `/src` directory was empty before installation.
- Verified that the PHP container (from previous commits) supports all required Laravel extensions.
- Confirmed that nginx correctly serves the Laravel `public` directory.

### Not Committed

- `.env` (kept local, excluded via `.gitignore`)
- `vendor/` (excluded via `.gitignore`)

### Result

Laravel is now fully installed and operational inside the Docker environment.
The application is accessible via nginx at:

```bash
http://localhost:8080
```

This completes the framework setup and prepares the project for TailwindCSS integration in the next commit.

---

## 🛡️ Commit 9 – TailwindCSS installieren & Node‑Container lauffähig machen

### 1. Node‑Container dauerhaft lauffähig machen

docker-compose.yml (Ausschnitt):

```yml
    node:
      build:
        context: ./docker/node
      volumes:
        - ./src:/var/www/html
      container_name: resumehaven-node
      command: ["tail", "-f", "/dev/null"]
```

---

### 2. Node‑Dockerfile minimal halten

Datei: docker/node/Dockerfile

```bash
    FROM node:20-alpine
    WORKDIR /var/www/html
```

---

### 3. In den Node‑Container einsteigen

```bash
    docker exec -it resumehaven-node sh
```

---

### 4. package.json erzeugen

```bash
    npm init -y
```

---

### 5. TailwindCSS + PostCSS + Autoprefixer installieren

```bash
    npm install -D tailwindcss postcss autoprefixer
```

---

### 6. Tailwind initialisieren

```bash
    npx tailwindcss init -p
```

Erzeugt:

- tailwind.config.js
- postcss.config.js

---

### 7. Tailwind konfigurieren

Datei: tailwind.config.js

```js
    export default {
        content: [
            "./resources/**/*.blade.php",
            "./resources/**/*.js",
            "./resources/**/*.vue",
        ],
        theme: {
            extend: {},
        },
        plugins: [],
    }
```

---

### 8. Tailwind‑Entry CSS erstellen

Datei: resources/css/app.css

```css
    @tailwind base;
    @tailwind components;
    @tailwind utilities;
```

---

### 9. npm‑Scripts ergänzen

Datei: package.json

```json
    "scripts": {
      "dev": "tailwindcss -i ./resources/css/app.css -o ./public/build/app.css --watch",
      "build": "tailwindcss -i ./resources/css/app.css -o ./public/build/app.css --minify"
    }
```

---

### 10. Build‑Ordner anlegen

```bash
    mkdir -p public/build
```

---

### 11. Ersten Tailwind‑Build ausführen

```bash
    npm run build
```

Ergebnis:

```bash
    public/build/app.css
```

---

### 12. Tailwind in Laravel einbinden

Datei: resources/views/layouts/app.blade.php oder welcome.blade.php

```html
    <link rel="stylesheet" href="/build/app.css">
```

---

### 13. Testen

In einer Blade‑Datei:

```html
    <h1 class="text-3xl font-bold text-blue-600">
        Tailwind läuft!
    </h1>
```

Browser:

    http://localhost:8080

---

### 14. Rechteprobleme vermeiden (SQLite & Storage)

Falls Laravel Fehler wirft:

```bash
    docker exec -it resumehaven-php bash

    chown -R www-data:www-data storage bootstrap/cache database
    chmod -R 775 storage bootstrap/cache database

    # SQLite Datei beschreibbar machen
    chmod 666 database/database.sqlite
```

---

### Ergebnis

- Node‑Container läuft dauerhaft
- Tailwind ist installiert
- Tailwind‑Build funktioniert
- CSS wird korrekt ausgeliefert
- Laravel lädt Tailwind sauber
- Rechteprobleme sind behoben

---

## 📊 Commit 10 – Basis‑Layout & Routing

### 1. Layout‑Ordner anlegen

    resources/views/layouts/app.blade.php

---

### 2. Globales Layout erstellen

Datei: resources/views/layouts/app.blade.php

```html
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'ResumeHaven')</title>

        <link rel="stylesheet" href="/build/app.css">
    </head>
    <body class="bg-gray-100 text-gray-900">

        <header class="bg-white shadow">
            <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-blue-600">ResumeHaven</h1>

                <nav class="space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-600">Home</a>
                    <a href="/about" class="text-gray-700 hover:text-blue-600">About</a>
                </nav>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 py-8">
            @yield('content')
        </main>

        <footer class="bg-white border-t mt-12">
            <div class="max-w-5xl mx-auto px-4 py-4 text-sm text-gray-500">
                © {{ date('Y') }} ResumeHaven – Alle Rechte vorbehalten.
            </div>
        </footer>

    </body>
    </html>
```

---

### 3. Routing anlegen

Datei: routes/web.php

```php
    <?php

    use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('home');
    });

    Route::get('/about', function () {
        return view('about');
    });
```

---

### 4. Beispiel‑Views erstellen

Datei: resources/views/home.blade.php

```html
    @extends('layouts.app')

    @section('title', 'Home')

    @section('content')
        <h2 class="text-3xl font-bold mb-4">Willkommen bei ResumeHaven</h2>
        <p class="text-gray-700">
            Deine Plattform für professionelle Lebensläufe und Bewerbungsunterlagen.
        </p>
    @endsection
```

---

Datei: resources/views/about.blade.php

```html
    @extends('layouts.app')

    @section('title', 'About')

    @section('content')
        <h2 class="text-3xl font-bold mb-4">Über ResumeHaven</h2>
        <p class="text-gray-700">
            ResumeHaven hilft dir, moderne und professionelle Lebensläufe zu erstellen.
        </p>
    @endsection
```

---

### 5. Tailwind neu bauen

```bash
    npm run build
```

---

### 6. Testen

Browser öffnen:

    http://localhost:8080

Du solltest das neue Layout sehen.

---

## 📧 Commit 11 – Eingabemaske & Logo‑Integration

### Ziel

Die Startseite bleibt als Landing Page bestehen.  
Die eigentliche Analyse‑Maske wird unter `/analyze` bereitgestellt.  
Zusätzlich wurde das ResumeHaven‑Logo (Light & Dark Mode) in das Layout integriert.

---

## Änderungen

### 1. Routing

- `/` zeigt weiterhin auf `home.blade.php` (Landing Page)
- Neue Route `/analyze` für die Eingabemaske
- POST‑Route `/analyze` für spätere Analyse‑Logik

---

### 2. Layout-Anpassungen

- Logo in den Header integriert (Light‑ und Dark‑Mode‑Varianten)
- Wortmarke „ResumeHaven“ bleibt bestehen
- Navigation erweitert: Home · Analyse · About
- Farben und Typografie an das definierte Branding angepasst

---

### 3. Landing Page (Home)

- Text überarbeitet, um den Nutzen von ResumeHaven klar zu kommunizieren
- Primärer CTA „Analyse starten“ als Button gestaltet
- Button führt zu `/analyze`

---

### 4. Analyse-Seite

- Neue View `analyze.blade.php`
- Zwei große Textfelder:
  - Stellenausschreibung
  - Lebenslauf
- CTA‑Button „Analysieren“
- Styling gemäß Branding (Farben, Abstände, Dark Mode)

---

### 5. Branding & Design

- Logo‑Farben und UI‑Farben gemäß definiertem Konzept:
  - primary: `#2D6CDF`
  - primary-dark: `#1e40af`
  - neutral-light: `#f3f4f6`
  - neutral-dark: `#2B2B2B`
- Dark‑Mode‑Unterstützung vorbereitet (`dark:`‑Klassen)
- Einheitliche Typografie und Abstände

---

## Ergebnis

- ResumeHaven hat jetzt eine professionelle Landing Page
- Die Analyse‑Maske ist klar strukturiert und nutzerfreundlich
- Das Logo ist integriert und unterstützt Light & Dark Mode
- Die UI wirkt schlicht, modern und markenkonsistent

---

## 🧹 Commit 12 – Analyse‑Controller & Request‑Validation

### Ziel

Die Analyse‑Eingaben (Stellenausschreibung & Lebenslauf) sollen serverseitig validiert und verarbeitet werden.  
Die spätere KI‑Analyse wird vorbereitet, aber noch nicht implementiert.

---

### Änderungen

#### 1. Neuer Controller: `AnalyzeController`

- Methode `analyze()` erstellt
- Validierung der Felder:
  - `job_text`: required, min:30
  - `cv_text`: required, min:30
- Bei Validierungsfehlern: Redirect zurück zur Eingabemaske
- Bei Erfolg: Weiterleitung auf eine neue Ergebnis‑View
- Platzhalter für spätere KI‑Analyse eingefügt

---

#### 2. Routing

- POST‑Route `/analyze` zeigt jetzt auf `AnalyzeController@analyze`

---

#### 3. Analyse‑View (`analyze.blade.php`)

- Validierungsfehler werden angezeigt
- Alte Eingaben werden wiederhergestellt (`old()`)

---

#### 4. Neue Ergebnis‑View (`result.blade.php`)

- Platzhalter‑Seite für spätere Analyse‑Ergebnisse
- Wird vom Controller nach erfolgreicher Validierung geladen

---

### Ergebnis

- Die Analyse‑Eingaben werden jetzt korrekt validiert
- Fehler werden sauber an die UI zurückgegeben
- Die Struktur für die spätere KI‑Analyse ist vorbereitet
- ResumeHaven hat nun einen vollständigen Analyse‑Flow (ohne KI‑Logik)

---

## Commit 13 – KI‑Analyse (OpenAI‑Integration, Parsing, Matching‑Logik)

### Ziel

Die Analyse‑Logik wird implementiert.  
ResumeHaven kann jetzt Stellenausschreibung und Lebenslauf an OpenAI senden, strukturierte Daten zurückerhalten und diese in einer Ergebnis‑View darstellen.

---

### Änderungen

#### 1. OpenAI‑Integration

- Installation des offiziellen OpenAI‑PHP‑Clients
- API‑Key in `.env` hinterlegt
- OpenAI‑Client im `AnalyzeController` verwendet

---

#### 2. Prompt‑Engineering

- KI erhält eine klar definierte Aufgabe:
  - Anforderungen aus der Stellenausschreibung extrahieren
  - Erfahrungen aus dem Lebenslauf extrahieren
  - Matches (Anforderung ↔ Erfahrung) identifizieren
  - Gaps (fehlende Anforderungen) identifizieren
- KI muss **ausschließlich JSON** zurückgeben
- JSON‑Schema definiert:
  - `requirements`
  - `experiences`
  - `matches`
  - `gaps`

---

#### 3. Parsing & Fehlerbehandlung

- KI‑Antwort wird als JSON geparst
- Falls Parsing fehlschlägt:
  - Fehlermeldung an Nutzer
  - Eingaben bleiben erhalten
- Strukturierte Daten werden an die Ergebnis‑View übergeben

---

#### 4. Ergebnis‑View (`result.blade.php`)

- Erste Darstellung der Analyse:
  - Anforderungen
  - Erfahrungen
  - Matches
  - Gaps
- Noch ohne visuelle Hervorhebung (folgt in Commit 14)

---

### Ergebnis

ResumeHaven kann jetzt:

- Stellenausschreibung + Lebenslauf an die KI senden
- Anforderungen und Erfahrungen extrahieren
- Matches und Gaps identifizieren
- Ergebnisse strukturiert anzeigen

Die Grundlage für die spätere visuelle Darstellung (Commit 14) ist geschaffen.

---

## Commit 14 – Ergebnis‑UI (Matches, Gaps, Tags, Farben, Panels)

### Ziel

Die rohe KI‑Analyse aus Commit 13 wird in eine klare, visuell strukturierte Ergebnis‑UI überführt.  
Nutzer erkennen sofort, welche Anforderungen erfüllt sind (Matches) und welche fehlen (Gaps).

---

### Änderungen

#### 1. Ergebnis‑View (`result.blade.php`)

- Vier Panels implementiert:
  - **Anforderungen** (requirements)
  - **Erfahrungen** (experiences)
  - **Matches** (Anforderung ↔ Erfahrung)
  - **Gaps** (fehlende Anforderungen)
- Panels mit einheitlichem Styling:
  - abgerundete Ecken
  - dezenter Schatten
  - Light/Dark‑Mode‑Unterstützung
  - großzügige Abstände
  - Hintergrund der Boxen ist weiß
  - Abstände zwischen den Boxen sind definiert
  - Sortierung der Bereiche ist angepasst

---

#### 2. Tag‑Design (Verschoben & Erweiterungsvorschlag)

- Die Darstellung als Tags/Badges wird als späterer Punkt behandelt.
- Geplant: Separate, kompakte Bereiche für Match-Tags und Gap-Tags, die die bisherigen Panels ersetzen können.
- Details zu einzelnen Matches/Gaps werden erst bei Bedarf eingeblendet (z.B. per Klick oder Button).
- Ziel: Übersichtliche, moderne UI mit reduzierter visueller Überladung und nutzerfreundlicher Interaktion.
- Analog für Requirements und Experiences: Tag-Darstellung mit optionalen Details.
- Die Panel-Struktur bleibt erhalten, Tags/Badges werden später als interaktive Bereiche ergänzt.

---

#### 3. Farb- und Layout‑Integration

- Nutzung der definierten Design‑Tokens:
  - primary, primary-dark
  - neutral-light, neutral-dark
  - text-light, text-dark
- Panels und Tags passen sich automatisch an Light/Dark‑Mode an

---

#### 4. Datenbindung

- Die vom Controller gelieferten Arrays (`requirements`, `experiences`, `matches`, `gaps`) werden in der UI ausgegeben
- Saubere Schleifenstruktur
- Keine Logik in der View (MVP‑Konformität)

---

### Ergebnis

ResumeHaven zeigt jetzt eine professionelle, klar strukturierte Analyse‑Ansicht:

- Anforderungen und Erfahrungen sind übersichtlich dargestellt
- Matches und Gaps sind farblich hervorgehoben
- Die UI ist markenkonsistent und modern
- Grundlage für spätere Erweiterungen (Score, Empfehlungen, Tagging) ist geschaffen
- Die Anordnung und das Layout der Bereiche sind optimiert
- Tag/Badge-Darstellung wird später ergänzt

---

## Commit 15 – Analyseergebnis-Caching in der Datenbank (Entwicklung)

### Ziel

Während der Entwicklung sollen Analyseergebnisse gecacht werden, um Kosten und Rechenzeit für KI-Requests zu sparen. Da auf dem Webspace keine In-Memory-Caches wie Valkey oder Memcache zur Verfügung stehen, wird das Caching in der MySQL-Datenbank realisiert.

---

### Änderungen

#### 1. Migration für Cache-Tabelle

- Neue Tabelle `analysis_cache` mit Feldern:
  - id (PK)
  - job_text (Text, ggf. Hash)
  - cv_text (Text, ggf. Hash)
  - result (JSON)
  - created_at, updated_at

#### 2. Service/Repository für Cache-Logik

- Prüft vor jedem KI-Request, ob ein passender Eintrag existiert
- Gibt bei Treffer das gespeicherte Ergebnis zurück
- Führt bei Miss einen KI-Request aus und speichert das Ergebnis

#### 3. Controller-Anpassung

- Vor dem Aufruf der KI wird der Cache geprüft
- Nach erfolgreichem KI-Request wird das Ergebnis gespeichert
- Während der Entwicklung werden so Tokens und Rechenzeit gespart

#### 4. Hinweise

- Die Lösung ist nicht hochperformant, aber für Einzelentwickler und Entwicklung ausreichend
- Später kann das Caching auf Redis oder andere Systeme umgestellt werden
- Die Datenbanklösung simuliert das spätere Produktionsverhalten besser als Session-Caching

---

### Ergebnis

- Analyseergebnisse werden während der Entwicklung effizient wiederverwendet
- Kosten und Wartezeiten werden reduziert
- Die Lösung ist kompatibel mit dem späteren Hosting (MySQL)

---

## ✅ Commit 16 – Score‑Berechnung & visuelle Bewertung (ABGESCHLOSSEN)

### Ziel

Die Analyse‑Ergebnisse werden um eine numerische Bewertung ergänzt.
Nutzer sehen nun auf einen Blick, wie gut ihr Profil zur Stellenausschreibung passt.

---

### Änderungen

#### 1. Score‑Berechnung ✅

- Neue Formel implementiert:
  - Score = Matches / (Matches + Gaps) * 100
- Ergebnis wird als Prozentwert gerundet
- Grundlage für spätere Gewichtungen geschaffen
- **Implementiert**: `ScoreResultDto`, `CalculateScoreAction`, `ScoringUseCase`

---

#### 2. Farbskala ✅

- Score‑abhängige Farbcodierung:
  - 0–40 % → Rot (`#dc2626`)
  - 40–70 % → Gelb (`#f59e0b`)
  - 70–100 % → Grün (`#16a34a`)
- Farben sind vollständig markenkonsistent

---

#### 3. Fortschrittsbalken ✅

- Horizontaler Balken zeigt den Score visuell an
- Farbe abhängig vom Score
- Balken animiert (Transition: 500ms)
- **BONUS**: SVG-Kreisindikator hinzugefügt

---

#### 4. Zusammenfassungspanel ✅

- Neues Panel am Anfang der Ergebnis‑Seite:
  - großer Score‑Wert (z. B. „72 % Match")
  - farbiger Fortschrittsbalken
  - kurze Bewertung („Hohe/Mittlere/Geringe Übereinstimmung")
  - Anzahl Matches und Gaps
- Panel nutzt bestehende UI‑Tokens (Panels, Farben, Typografie)

---

#### 5. Integration in die Ergebnis‑UI ✅

- Score‑Panel wird oberhalb der bisherigen vier Panels angezeigt
- Reihenfolge der Ergebnis‑Darstellung optimiert
- UI wirkt klarer, professioneller und nutzerfreundlicher
- Error-Handling: Score wird nur berechnet wenn Analyse erfolgreich

---

#### 6. Tests & Code Quality ✅

- 5 neue Unit-Tests für `CalculateScoreAction`
- PHPStan Level 9: 0 Errors
- Pint Code-Style: sauber
- Feature-Tests angepasst

---

#### 7. Docker-Fix (BONUS) ✅

- PHP-FPM `www.conf`: Listen auf allen Interfaces (`9000`)
- Explizites Docker-Netzwerk hinzugefügt
- Makefile erweitert: `docker-restart`, `docker-rebuild`
- `restart-containers.bat` erstellt
- 502 Bad Gateway Problem behoben

---

### Ergebnis

ResumeHaven bietet jetzt eine vollständige visuelle Bewertung:

- ✅ Prozent‑Score mit intelligenter Berechnung
- ✅ Farbkodierung (Rot/Gelb/Grün)
- ✅ Fortschrittsbalken (horizontal + SVG-Kreis)
- ✅ Zusammenfassung der Stärken und Lücken
- ✅ Responsive Design
- ✅ Error-Handling
- ✅ Vollständige Test-Abdeckung

Die Analyse wirkt dadurch deutlich verständlicher und professioneller.

**Domain-Architektur**: Controller von 94 → 34 Zeilen reduziert (63% kleiner)

---

## Commit 16a – AI Mock Strategy Pattern (für Entwicklung ohne API-Limits)

### Ziel

Während der Entwicklung sollen AI-Analysen ohne API-Kosten möglich sein. Dazu wird ein sauberes **Strategy Pattern** implementiert, das zwischen Production (Gemini) und Development (Mock) umschalten kann - gesteuert über Config/Env.

**Problem gelöst**: Entwickler hat API-Limits erreicht und kann nicht mehr entwickeln/testen.

---

### Änderungen

#### 1. Interface für AI-Analyzer

**Neue Datei**: `app/Services/AiAnalyzer/Contracts/AiAnalyzerInterface.php`

```php
interface AiAnalyzerInterface {
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto;
    public function isAvailable(): bool;
    public function getProviderName(): string;
}
```

- Definiert Contract für alle AI-Provider
- Ermöglicht einfachen Wechsel zwischen Implementierungen
- Basis für Dependency Injection

---

#### 2. Gemini Implementation (Refactored)

**Neue Datei**: `app/Services/AiAnalyzer/GeminiAiAnalyzer.php`

- Bestehender Code aus `AnalyzeApplicationService` extrahiert
- Implementiert `AiAnalyzerInterface`
- Verwendet Laravel AI Package
- Production-ready

---

#### 3. Mock Implementation (NEU)

**Neue Datei**: `app/Services/AiAnalyzer/MockAiAnalyzer.php`

- Implementiert `AiAnalyzerInterface`
- Gibt vordefinierte, realistische Test-Daten zurück
- **Verschiedene Szenarien**:
  - `realistic`: Ausgeglichenes Ergebnis (60% Score)
  - `high_score`: Sehr gute Übereinstimmung (90% Score)
  - `low_score`: Geringe Übereinstimmung (25% Score)
  - `no_match`: Keine Übereinstimmungen (0% Score)
- Konfigurierbar über `.env`
- Simuliert API-Delay (konfigurierbar)

**Mock-Response Beispiel** (realistic scenario):
```json
{
  "requirements": ["PHP 8+", "Laravel", "RESTful API", "MySQL", "Git"],
  "experiences": ["5 Jahre PHP", "Laravel Projekte", "API Entwicklung"],
  "matches": [
    {"requirement": "PHP 8+", "experience": "5 Jahre PHP"},
    {"requirement": "Laravel", "experience": "Laravel Projekte"}
  ],
  "gaps": ["MySQL", "Git"]
}
```

---

#### 4. Config-Erweiterung

**Update**: `config/ai.php`

```php
return [
    'provider' => env('AI_PROVIDER', 'mock'), // mock | gemini
    
    'mock' => [
        'scenario' => env('AI_MOCK_SCENARIO', 'realistic'),
        'delay' => env('AI_MOCK_DELAY', 500), // Simuliere API-Delay
    ],
    
    'gemini' => [
        // ...existing config...
    ],
];
```

---

#### 5. Service Provider Binding

**Update**: `app/Providers/AppServiceProvider.php`

```php
public function register(): void
{
    $this->app->bind(AiAnalyzerInterface::class, function ($app) {
        $provider = config('ai.provider', 'mock');
        
        return match($provider) {
            'gemini' => $app->make(GeminiAiAnalyzer::class),
            'mock' => $app->make(MockAiAnalyzer::class),
            default => throw new \InvalidArgumentException("Unknown AI provider: {$provider}"),
        };
    });
}
```

---

#### 6. AnalyzeApplicationService Refactoring

**Update**: `app/Services/AnalyzeApplicationService.php`

- Constructor-Injection: `AiAnalyzerInterface`
- Delegiert an injizierte Implementation
- **Keine Änderung der Public API**
- Bestehender Code funktioniert weiter

---

#### 7. Environment Configuration

**Update**: `.env` und `.env.example`

```env
# AI Provider Configuration
AI_PROVIDER=mock
AI_MOCK_SCENARIO=realistic
AI_MOCK_DELAY=500

# Gemini (für Production)
GEMINI_API_KEY=your-key-here
GEMINI_API_MODEL=gemini-2.5-flash
```

---

#### 8. Tests

- Unit-Tests für `MockAiAnalyzer`
- Tests für verschiedene Szenarien
- Tests für Provider-Switching
- Integration-Tests

---

### Vorteile

✅ **Entwicklung ohne API-Kosten**: Mock-Daten für lokale Entwicklung
✅ **Schnelles Feedback**: Keine Wartezeit auf API-Responses
✅ **Testbarkeit**: Verschiedene Szenarien einfach testbar
✅ **Saubere Architektur**: Strategy Pattern, SOLID Principles
✅ **Flexibilität**: Einfach neue Provider hinzufügen (OpenAI, Claude, etc.)
✅ **Production-Ready**: Einfaches Umschalten via `.env`

---

### Ergebnis

Entwickler können jetzt:

- ✅ Ohne API-Limits entwickeln und testen
- ✅ Verschiedene Szenarien durchspielen (high/low/no match)
- ✅ Schneller iterieren (kein API-Delay)
- ✅ Mit einem Env-Switch auf Production umschalten
- ✅ Neue AI-Provider einfach integrieren

Die Architektur ist sauber, erweiterbar und production-ready.

---

## Commit 16b – AI Response Format erweitern (Tags-Struktur)

### Ziel

Die AI-Response wird um eine strukturierte **Tags-Section** erweitert. Tags ermöglichen eine bessere Gruppierung und Darstellung von Matches und Gaps für die spätere UI-Komponenten-Implementierung (Commit 16c).

**Key Insight**: Während `matches` und `gaps` Details bleiben, bieten `tags` eine elegantere, gruppierte Ansicht für die UI.

---

### Änderungen

#### 1. DTOs erweitern

**Update**: `app/Dto/AnalyzeResultDto.php`

- Füge optionales `tags` Field hinzu:
  ```php
  public readonly ?array $tags = null;
  ```
- Tags-Struktur:
  ```php
  [
    'matches' => [ // Gruppierte Matches
      ['requirement' => '...', 'experience' => ['...', '...']]
    ],
    'gaps' => ['...', '...'] // Array von Strings
  ]
  ```

**Neue Datei**: `app/Domains/Analysis/Dto/TagMatchDto.php`
- `requirement: string` (z.B. "Frontend")
- `experience: array<string>` (z.B. ["React", "Vue"])
- Immutable mit PHPDoc

---

#### 2. AI Response Parsing anpassen

**Update**: `app/Services/AiAnalyzer/GeminiAiAnalyzer.php`

- Parse `tags` Section aus AI-Response (falls vorhanden)
- Type-Safe Array-Handling mit PHPDoc-Assertions
- Fallback zu `null` wenn Tags nicht vorhanden

**Update**: `app/Services/AiAnalyzer/MockAiAnalyzer.php`

- Alle 4 Szenarien mit `tags` Section erweitert
- Nutze scratch_4.json als Vorlage für realistische Daten
- Mock-Response mit vollständiger Tags-Struktur

---

#### 3. Tag-Generierungs-Service (Fallback)

**Neue Datei**: `app/Domains/Analysis/UseCases/GenerateTagsUseCase/GenerateTagsAction.php`

- **Fallback**: Falls AI keine Tags liefert, generiere sie programmatisch
- Logik:
  1. Grupiere `matches` nach Requirement
  2. Sammle alle Experiences für jedes Requirement
  3. Verwende Gaps 1:1 (sind ohnehin Strings)
- Keine Extra-AI-Anfrage nötig (Algorithmic Fallback)

---

#### 4. Handler erweitern

**Update**: `app/Domains/Analysis/Handlers/AnalyzeJobAndResumeHandler.php`

- Nach Analyse: Prüfe ob `tags` im Response vorhanden
- Falls nicht: Rufe `GenerateTagsAction` auf (Fallback)
- Speichere `tags` in DTO

---

#### 5. Controller anpassen

**Update**: `app/Http/Controllers/AnalyzeController.php`

- Übergebe `tags` an View via `->with('tags', $result->tags)`
- Tags werden als `$tags` in result.blade.php verfügbar (noch nicht gerendert)

---

#### 6. Tests

**Neue Datei**: `tests/Unit/GenerateTagsActionTest.php`
- Test für Gruppierung von Matches
- Test für Gaps 1:1 Übernahme
- Test für leere Arrays
- Test für Edge Cases

**Update**: `tests/Unit/MockAiAnalyzerTest.php`
- Prüfe dass Tags in Mock-Response vorhanden sind
- Prüfe dass Tags-Struktur korrekt ist (requirement + experience[])

---

### Mock-Daten Struktur (aus scratch_4.json)

#### **Realistic Scenario**:
```json
{
  "tags": {
    "matches": [
      {"requirement": "Softwareentwicklung", "experience": ["20 Jahre Erfahrung"]},
      {"requirement": "Frontend", "experience": ["React"]},
      {"requirement": "Webtechnologien", "experience": ["JavaScript", "HTML", "CSS"]},
      {"requirement": "Agile Methoden", "experience": ["Scrum", "Kanban"]}
    ],
    "gaps": [
      "Lead", "Java", "Spring Boot", "Angular", "Vue",
      "AI‑Tools", "Microservices", "APIs", "Docker", "Cloud"
    ]
  }
}
```

#### **High Score, Low Score, No Match**: Analog angepasst

---

### Backward Compatibility

✅ **Alte Fields bleiben bestehen**:
- `matches` Array: Bleibt unverändert (1:1 Mapping)
- `gaps` Array: Bleibt unverändert (einfache Strings)
- `tags`: Neue optionale Section (falls vorhanden)

✅ **Fallback-Logik**:
- Wenn AI keine Tags liefert: `tags` wird programmatisch generiert
- Garantiert dass `tags` immer vorhanden sind
- View kann sicher auf `$tags` zugreifen

---

### Ergebnis

ResumeHaven hat jetzt:

- ✅ Erweiterte AI-Response mit strukturierten Tags
- ✅ Backward Compatibility (alte Fields + neue Tags)
- ✅ Intelligente Fallback-Generierung ohne Extra-Requests
- ✅ Gruppierte Ansicht für elegante UI-Darstellung
- ✅ Vorbereitung für Commit 16c (Tag/Badge UI)
- ✅ Vollständige Test-Abdeckung
- ✅ PHPStan Level 9 clean

Die Architektur ist bereit für die UI-Komponenten in Commit 16c.

---

## Commit 16c – Tag/Badge UI Implementation

### Ziel

Tags aus Commit 16b werden in der UI als Badges dargestellt. Match-Tags (grün) und Gap-Tags (rot) für moderne Darstellung.

---

### Ergebnis

- ✅ Tag/Badge-UI ist implementiert
- ✅ Match-Tags zeigen gruppierte Experiences
- ✅ Gap-Tags sind rot/prominent
- ✅ Fallback zu Detail-Listen vorhanden
- ✅ Tests grün

---

## Security Hardening – Analyzer Prompt

### Ziel

Der Analyzer-Prompt wird gehärtet gegen Prompt-Injection.

---

### Ergebnis

- ✅ Sicherheitsregeln im Prompt integriert
- ✅ Schema ist korrekt typisiert
- ✅ Tests validieren Security

---

## Commit 18 – Analysis Cache Management Command

### Ziel

Artisan-Command `cache:clear-analysis` zum Leeren der Analyse-Cache-Tabelle. MVP-Implementierung mit optionalem Altersfilter für zukünftige Cronjob-Integration.

---

### Änderungen

#### 1. Neues Artisan Command

**Neue Datei**: `app/Console/Commands/ClearAnalysisCacheCommand.php`

**Signature**: `cache:clear-analysis {--older-than= : Clear cache entries older than N days}`

**Funktionen**:
- `php artisan cache:clear-analysis` → leert **alle** Cache-Einträge
- `php artisan cache:clear-analysis --older-than=7` → löscht nur Einträge älter als 7 Tage
- Intelligente Output-Meldungen:
  - Singular/Plural-Erkennung ("entry" vs "entries")
  - Kein Fehler wenn Cache leer ist
  - Klare Bestätigungsmeldungen
- Exit-Codes:
  - `0` (SUCCESS) bei erfolgreicher Ausführung
  - `1` (FAILURE) bei ungültigen Parametern (z.B. negative Zahlen)

**Beispiele**:
```bash
# Alle Einträge löschen (MVP)
php artisan cache:clear-analysis
# ✓ Cleared 42 cache entries.

# Nur alte Einträge löschen (Cronjob-ready)
php artisan cache:clear-analysis --older-than=30
# ✓ Deleted 15 cache entries older than 30 days.

# Cache ist leer
php artisan cache:clear-analysis
# ✓ Cache table is already empty.
```

#### 2. Makefile-Target

**Neue Targets**:
```makefile
cache-clear-analysis: ## Analysis Cache leeren
	docker exec -it resumehaven-php php artisan cache:clear-analysis
```

**Verwendung**:
```bash
make cache-clear-analysis
```

#### 3. Feature-Tests

**Neue Datei**: `tests/Feature/ClearAnalysisCacheCommandTest.php`

**5 Tests**:
1. `cache:clear-analysis` leert alle Einträge
2. Gibt Nachricht aus wenn Cache bereits leer
3. `--older-than=7` löscht nur Einträge älter als 7 Tage (basierend auf `updated_at`)
4. Lehnt negative `--older-than` Werte ab (Exit-Code 1)
5. Behandelt fehlende alte Einträge sauber (kein Fehler)

#### 4. Code Quality

- **Pint**: Automatische Code-Formatierung
- **PHPStan Level 9**: Keine Fehler
- **Singular/Plural Logic**: Grammatikalisch korrekte Ausgaben
- **Type-Safe**: Alle Parameter validiert

---

### Struktur für Zukunft

**Cronjob-Integration (geplant)**:
```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Alte Cache-Einträge täglich um 3 Uhr nachts löschen
    $schedule->command('cache:clear-analysis --older-than=30')
        ->dailyAt('03:00')
        ->timezone('Europe/Berlin');
}
```

---

### Ergebnis

- ✅ Command funktioniert zuverlässig und robust
- ✅ MVP (alles löschen) + Bonus (Altersfilter)
- ✅ Vollständige Test-Abdeckung (5 Tests)
- ✅ Makefile-Integration für einfache Nutzung
- ✅ Cronjob-ready für automatische Bereinigung
- ✅ Benutzerfreundliche, grammatikalisch korrekte Ausgaben
- ✅ Error-Handling für Edge-Cases

**Dateien**:
- `app/Console/Commands/ClearAnalysisCacheCommand.php` (65 Zeilen)
- `tests/Feature/ClearAnalysisCacheCommandTest.php` (99 Zeilen)
- `Makefile` (+ 1 Target)

---

## Commit 17 – Empfehlungen & Verbesserungsvorschläge (KI‑gestützt)

**Status:** ✅ **Vollständig umgesetzt & committed (2026-03-08)**

### Implementierte Features

#### Backend
- ✅ `RecommendationDto` (immutable, typed mit `priority: 'high'|'medium'|'low'`)
- ✅ AI-Prompt erweitert um `recommendations`-Feld mit strukturiertem Output
- ✅ JSON-Schema-Validierung für recommendations
- ✅ `ParseAiResponseAction`: Parst recommendations mit Type-Guards und priority-Validierung
- ✅ `AnalyzeResultDto`: Erweitert um `recommendations`-Array
- ✅ `AnalyzeJobAndResumeHandler`: Leitet recommendations von AI-Analyse durch
- ✅ `MockAiAnalyzer`: Alle 4 Szenarien mit realistischen Empfehlungen
  - Realistic: 2 Empfehlungen (high, medium)
  - High Score: 1 Empfehlung (low)
  - Low Score: 6 Empfehlungen (3× high, 2× medium, 1× low)
  - No Match: 5 Empfehlungen (4× high, 1× medium)
- ✅ Cache-Integration: `GetCachedAnalysisAction` rekonstruiert recommendations als DTOs
- ✅ Cache-Typisierung: PHPDoc in `AnalysisCacheRepository` und `AnalysisCache` Model

#### Frontend
- ✅ `result.blade.php`: Neues Panel "💡 Empfehlungen & Verbesserungsvorschläge"
- ✅ Prioritäts-Badges (farbcodiert: high=rot, medium=gelb, low=grün)
- ✅ Gap-Namen, Verbesserungsvorschläge, Beispiel-Formulierungen
- ✅ Robustes Rendering (akzeptiert DTO und Array-Formate)
- ✅ Responsive Design + Dark-Mode-Support

#### Tests
- ✅ `RecommendationDtoTest.php` (6 Unit-Tests)
- ✅ `ParseAiResponseActionTest.php` (+3 Tests für recommendations)
- ✅ `RecommendationsUiTest.php` (5 Feature-Tests)

#### Quality Gates
- ✅ PHPStan Level 9: 0 Errors
- ✅ Pint: Code-Style konform
- ✅ Tests: 176 passed (496 assertions)

### Ziel

Die Analyse wird um konkrete, KI‑gestützte Verbesserungsvorschläge erweitert.
Nutzer erhalten jetzt klare Hinweise, wie sie ihren Lebenslauf optimieren können, um besser zur Stellenausschreibung zu passen.

---

### Änderungen

#### 1. Erweiterung des KI‑Prompts

- Neuer Abschnitt im Prompt:
  - Empfehlungen zu fehlenden Anforderungen
  - Priorisierung (hoch, mittel, niedrig)
  - Beispiel‑Formulierungen für den Lebenslauf
- KI muss die Empfehlungen als strukturiertes JSON zurückgeben

---

#### 2. Controller‑Erweiterung

- Parsing der neuen Felder:
  - `recommendations`
- Fehlerbehandlung für unvollständige KI‑Antworten
- Weitergabe der Daten an die Ergebnis‑View

---

#### 3. Ergebnis‑UI

- Neues Panel „Empfehlungen & Verbesserungsvorschläge“
- Darstellung pro Empfehlung:
  - Gap‑Titel
  - Empfehlungstext
  - Priorität (farblich codiert)
  - Beispiel‑Formulierung in einem eigenen Kasten
- Farben gemäß Branding:
  - hoch → Rot
  - mittel → Gelb
  - niedrig → Grün

---

#### 4. UX‑Verbesserungen

- Einheitliche Panels
- Tags und Farben im ResumeHaven‑Stil
- Klare Typografie und Abstände
- Light/Dark‑Mode‑Unterstützung

---

### Ergebnis

ResumeHaven bietet jetzt nicht nur eine Analyse, sondern auch konkrete, umsetzbare Empfehlungen.
Nutzer sehen:

- welche Anforderungen fehlen
- wie sie diese Lücken schließen können
- welche Formulierungen sie verwenden können
- welche Punkte besonders wichtig sind

Damit wird ResumeHaven zu einem echten Karriere‑Coach.

---
## 🔒 Commit 18a – Security Härtung (MVP-Abschluss Phase 1)

**Zweck:** Anwendung gegen gängige Sicherheitsrisiken hardenen

**Status:** ✅ **Vollständig umgesetzt & committed (2026-03-08)**

### Added

#### 1. Prompt-Injection-Schutz im AI-Analyzer
- Strikte Prompt-Struktur mit Separatoren (###)
- Input-Bereinigung vor KI-Anfrage
- Output-Validierung nach KI-Response
- Unit-Tests für malicious inputs

#### 2. Input-Validierung (Preliminary)
- Maximale Längen: Job-Text (50KB), CV-Text (50KB)
- Erlaubte Zeichen: ASCII + UTF-8 Umlaute/Sonderzeichen
- Warnung/Rejection für verdächtige Patterns (z.B. SQL-Befehle, Script-Tags)

#### 3. Error-Handling für API-Failures
- Graceful Degradation bei KI-Timeout
- Sprechende Error-Messages (user-facing)
- Logging von Fehlern für Debugging

#### 4. CSRF-Protection Review
- Validierung, dass `@csrf` in allen POST-Forms vorhanden ist
- Token-Refresh-Logik überprüfen

#### 5. SQL-Injection-Prävention
- Audit: Repository-Pattern nutzt bereits Prepared Statements
- Keine Raw-Queries in Domain-Logic

### Tests Added
- `SecurityPromptInjectionTest.php` (Pest Feature Test)
- `InputValidationTest.php` (Pest Feature Test)
- `ApiErrorHandlingTest.php` (Pest Feature Test)

### Result
Anwendung ist resistent gegen bekannte Top-10-Sicherheitsrisiken (OWASP).

---

## 🎨 Commit 21 – Responsive Layout & Mobile-First Design (MVP-Abschluss Phase 2a)

**Zweck:** UI für alle Geräte optimieren

**Status:** ✅ Abgeschlossen (2026-03-09)

**Durchgeführt:**
- Alpine.js via CDN integriert für Mobile-Menu-Toggle
- Responsive Header mit Hamburger-Menu (< md zeigt Mobile-Menu)
- Responsive Footer (Stack vertikal Mobile → horizontal Desktop)
- Analyze-Form: Grid-Layout (1 Column Mobile → 2 Columns Desktop)
- Touch-optimierte Inputs (min-h-[48px], text-base >= 16px für iOS)
- Result-View: Responsive Score-Panel (5xl → 6xl → 7xl)
- Touch-Optimierungen in app.css (WCAG 44px Touch-Targets, Focus-States)
- 6 Feature-Tests in ResponsiveLayoutTest.php
- PHPStan Level 9: 0 Errors
- Pint: Code-Style konform
- Alle Tests grün (182 passed)

### Updated

#### 1. Tailwind-Breakpoints aktivieren
- `sm:` (640px) für Tablets
- `md:` (768px) für große Tablets
- `lg:` (1024px) für Desktops
- `xl:` (1280px) für große Desktops

#### 2. Layout-Anpassungen
- Input-Form: Single-Column auf Mobile, 2-Column auf Desktop
- Result-Panels: Stack vertikal < 768px, 2-Column > 768px
- Navigation/Header: Hamburger-Menu auf Mobile

#### 3. Typography-Verbesserungen
- Responsive Font-Sizes (`text-sm:`, `text-base:`, `text-lg:`)
- Bessere Line-Heights auf Mobile
- Improved Spacing (padding/margin Responsiveness)

#### 4. Accessibility (WCAG 2.1 AA Preparation)
- Color Contrast Ratio überprüft
- Focus States für alle interaktiven Elemente
- Semantic HTML (`<main>`, `<section>`, `<nav>`)

### Tests Added
- Visual Regression Tests (optional mit Percy/Chromatic)
- Manual Mobile Device Testing (Checklist in Docs)

### Result
UI ist usable auf Phones (320px) bis 4K Desktops (2560px).

---

## 🌙 Commit 21a – Dark-Mode Support (MVP-Abschluss Phase 2b)

**Zweck:** Vollständige Dark-Mode-Unterstützung mit System-Präferenz-Detection, Toggle-Button und persistenter User-Präferenz

**Status:** ✅ Abgeschlossen (2026-03-09)

**Durchgeführt:**
- Tailwind `darkMode: 'class'` in `tailwind.config.js` aktiviert
- `DarkModeManager` JavaScript-Modul erstellt (`resources/js/dark-mode.js`)
- System-Präferenz-Detection (`prefers-color-scheme: dark`)
- LocalStorage-Persistierung für User-Präferenz
- Toggle-Button im Header mit Sun/Moon Icons
- Dark-Mode CSS für alle Komponenten (HTML, Header, Footer, Navigation)
- 10 Feature-Tests in `DarkModeTest.php`
- PHPStan Level 9: 0 Errors
- Pint: Code-Style konform
- Alle Tests grün (194 passed, 1499 assertions)

### Features

#### 1. Tailwind Dark-Mode Konfiguration
- `darkMode: 'class'` aktiviert (class-based, nicht media-query)
- Erlaubt manuellen Toggle per JavaScript
- Alle `dark:` Varianten funktionieren

#### 2. JavaScript Dark-Mode Manager
- **Auto-Init:** Initialisiert automatisch beim Page-Load
- **System-Präferenz:** Respektiert `prefers-color-scheme` als Fallback
- **LocalStorage:** Persistiert User-Präferenz (`darkMode: 'true'|'false'`)
- **Toggle-Funktion:** `DarkModeManager.toggle()` für Button
- **Event-Dispatch:** Custom Events für UI-Updates
- **Watcher:** Live-Erkennung von System-Präferenz-Änderungen

#### 3. UI-Integration
- **Toggle-Button im Header:**
  - Sun Icon (🌞) für Light Mode
  - Moon Icon (🌙) für Dark Mode
  - Aria-Labels für Accessibility
  - Responsive Design (funktioniert mit Mobile Menu)

#### 4. Dark-Mode CSS
- **HTML/Body:** `dark:bg-neutral-dark dark:text-text-dark`
- **Header:** `dark:bg-neutral-dark dark:border-gray-700`
- **Footer:** `dark:bg-neutral-dark dark:border-gray-700`
- **Navigation:** `dark:text-gray-400 dark:hover:text-primary`
- **Buttons:** `dark:hover:bg-gray-800`
- **Alle Views:** Bereits mit `dark:` Varianten ausgestattet

#### 5. Feature-Tests (10 Tests)
1. ✅ Dark-Mode Toggle Button im Header vorhanden
2. ✅ Sun Icon für Light Mode sichtbar
3. ✅ Moon Icon für Dark Mode sichtbar
4. ✅ Dark-Mode Klassen auf HTML Element
5. ✅ Dark-Mode JavaScript geladen
6. ✅ Header hat Dark-Mode Support
7. ✅ Footer hat Dark-Mode Support
8. ✅ Tailwind darkMode Config aktiviert
9. ✅ Alle Standard-Seiten haben Dark-Mode Support
10. ✅ Mobile Menu Button hat Dark-Mode Support

### Quality-Gates
- ✅ Tests: 194 passed (1499 assertions)
- ✅ PHPStan Level 9: 0 Errors
- ✅ Pint: Code-Style konform
- ✅ Assets: Neu gebaut mit Tailwind Dark-Mode Support

### Dateien
- **Neu:** `resources/js/dark-mode.js`, `tests/Feature/DarkModeTest.php`
- **Aktualisiert:** `tailwind.config.js`, `resources/js/app.js`, `resources/views/layouts/app.blade.php`

### Dokumentation
- `docs/COMMIT_21a_IMPLEMENTATION_GUIDE.md` (vollständige Implementierungsdokumentation)

### Result
Benutzer können Dark-Mode manuell aktivieren oder automatisch basierend auf System-Präferenz nutzen. Präferenz wird persistent gespeichert.

---

## ⚖️ Commit 20b – Legal-Seiten & Vertrauen (MVP-Abschluss Phase 2c)

**Zweck:** Rechtliche Mindestanforderungen für den MVP erfüllen und Vertrauen durch transparente Informationen stärken.

**Status:** ✅ Abgeschlossen (2026-03-09)

**Durchgeführt:**
- Routes für Legal-Pages und Contact hinzugefügt
- `LegalController` mit named methods für statische Seiten
- `ContactController` für Kontaktformular (show/submit)
- Views für Impressum, Datenschutz, Kontakt, Lizenzen erstellt
- Footer-Navigation mit Legal-Links erweitert
- `GenerateLicenseDataCommand` implementiert (parst composer.lock & package-lock.json)
- Composer-Script & Makefile-Target für `licenses:generate` hinzugefügt

**Nachbesserungen (2026-03-09):**
- Legal-Views waren initial nicht vorhanden (nur Controller/Routes)
- Alle 4 Legal-Blade-Views nachträglich erstellt (impressum, datenschutz, kontakt, lizenzen)
- Responsive Design + Dark-Mode Support
- Escaped Quotes (\" → ") korrigiert
- Blade-Syntax-Fehler behoben (doppeltes @endsection in kontakt.blade.php)
- `GenerateLicenseDataCommand` (`licenses:generate`) vollständig implementiert
- Datenmodell für Lizenz-Export um optionales Feld `homepage` erweitert
- `lizenzen.blade.php` zeigt Paketnamen als Link, wenn `homepage` vorhanden ist
- Neue Feature-Tests für den Lizenzgenerator ergänzt
- Alle Tests grün, PHPStan Level 9: 0 Errors

**Quality-Gates:**
- ✅ Tests: 184 passed (1471 assertions)
- ✅ PHPStan Level 9: 0 Errors
- ✅ Pint: Code-Style konform
- ✅ Legal-Routes funktionieren: /impressum, /datenschutz, /kontakt, /lizenzen
- ✅ `make licenses-generate` erstellt licenses.json (78 PHP + 203 Node Packages)
- ✅ Feature-Tests für Legal-Pages, Contact-Form, Footer, Licenses
- ✅ Lizenzen-Seite zeigt alle Pakete mit Links (wenn homepage vorhanden)

---

### 📋 Implementierungsplan

#### Phase 1: Routes & Controller-Struktur

**1.1 Routes definieren** (`routes/web.php`)
```php
// Legal Pages (GET only)
Route::get('/impressum', [LegalController::class, 'impressum'])->name('legal.impressum');
Route::get('/datenschutz', [LegalController::class, 'datenschutz'])->name('legal.datenschutz');
Route::get('/lizenzen', [LegalController::class, 'lizenzen'])->name('legal.lizenzen');

// Contact (GET + POST)
Route::get('/kontakt', [ContactController::class, 'show'])->name('contact.show');
Route::post('/kontakt', [ContactController::class, 'submit'])->name('contact.submit');
```

**1.2 Controller erstellen**
- `app/Http/Controllers/LegalController.php` (Single-Action aufteilen in named methods für Legal)
- `app/Http/Controllers/ContactController.php` (show + submit)

**Architektur-Entscheidung:**
- **LegalController**: Named Methods (nicht Single-Action), da statische Content-Seiten
- **ContactController**: 2 Methods (show/submit), da CRUD-Pattern
- **Begründung**: Single-Action-Prinzip gilt primär für Business-Logic, nicht für simple View-Returns

---

#### Phase 2: Views erstellen

**2.1 Legal-Seiten** (`resources/views/legal/`)
- `impressum.blade.php` - Anbieterkennzeichnung
- `datenschutz.blade.php` - DSGVO-konform
- `kontakt.blade.php` - Kontaktformular
- `lizenzen.blade.php` - Automatisch generiert

**2.2 Layout erweitern** (`resources/views/layouts/app.blade.php`)
- Footer um Legal-Links erweitern
- Responsive Footer (Stack vertikal < 768px, Horizontal > 768px)

**Content-Struktur Legal-Seiten:**
```blade
@extends('layouts.app')
@section('title', 'Impressum')
@section('content')
    <div class="prose dark:prose-invert max-w-3xl">
        <h1>Impressum</h1>
        <!-- Platzhalter-Content für MVP -->
    </div>
@endsection
```

---

#### Phase 3: Kontaktformular

**3.1 Backend-Struktur**

**DTO:**
```php
// app/Dto/ContactRequestDto.php
readonly class ContactRequestDto {
    public function __construct(
        public string $name,
        public string $email,
        public string $message,
    ) {}
}
```

**Validation:**
```php
// app/Http/Requests/ContactRequest.php (Form Request)
- name: required, string, min:2, max:100
- email: required, email, max:255
- message: required, string, min:10, max:5000
```

**UseCase (optional):**
```php
// app/Domains/Contact/UseCases/SendContactMessageAction.php
- Validiert Input
- Speichert in DB (ContactMessage Model) ODER
- Sendet E-Mail (MVP: nur Logging)
- Returns Success/Error
```

**3.2 ContactController**
```php
public function show(): View
{
    return view('legal.kontakt');
}

public function submit(ContactRequest $request): RedirectResponse
{
    // Validierung via ContactRequest
    // UseCase aufrufen
    // Redirect mit Success-Message
    return redirect()->route('contact.show')
        ->with('success', 'Vielen Dank für Ihre Nachricht!');
}
```

**3.3 View mit Validierungs-Feedback**
```blade
@if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert-error">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('contact.submit') }}">
    @csrf
    <!-- Formular-Felder -->
</form>
```

---

#### Phase 4: Lizenzen-Seite (automatisiert)

**4.1 Artisan Command erstellen**
```bash
php artisan make:command GenerateLicenseData
```

**4.2 Command-Logik** (`app/Console/Commands/GenerateLicenseDataCommand.php`)
```php
class GenerateLicenseDataCommand extends Command
{
    protected $signature = 'licenses:generate';
    protected $description = 'Generiert Lizenzdaten aus composer.lock und package-lock.json';

    public function handle(): int
    {
        $phpLicenses = $this->parseComposerLock();
        $nodeLicenses = $this->parsePackageLock();
        
        $data = [
            'generated_at' => now()->toIso8601String(),
            'php' => $phpLicenses,
            'node' => $nodeLicenses,
        ];
        
        Storage::put('licenses.json', json_encode($data, JSON_PRETTY_PRINT));
        
        $this->info('Lizenzen erfolgreich generiert!');
        return 0;
    }
}
```

**4.3 LegalController::lizenzen()**
```php
public function lizenzen(): View
{
    $licenses = json_decode(Storage::get('licenses.json'), true);
    
    return view('legal.lizenzen', [
        'php' => $licenses['php'] ?? [],
        'node' => $licenses['node'] ?? [],
        'generated_at' => $licenses['generated_at'] ?? null,
    ]);
}
```

**4.4 View** (`resources/views/legal/lizenzen.blade.php`)
- Tabelle mit: Paket, Version, Lizenz
- Trennung PHP/Node
- Hinweis auf Generierungszeitpunkt

**4.5 Composer-Integration**
```json
// composer.json - scripts
"scripts": {
    // ...existing scripts...
    "licenses:generate": "@php artisan licenses:generate"
}
```

**4.6 Makefile-Integration**
```makefile
# Makefile
licenses: ## Lizenzen neu generieren
    docker exec -it resumehaven-php composer run licenses:generate
```

**4.7 Build-Integration (Post-Update)**
```json
// composer.json - scripts
"post-update-cmd": [
    "@php artisan vendor:publish --tag=laravel-assets --ansi --force",
    "@php artisan licenses:generate"
]
```

---

#### Phase 5: Footer-Navigation

**5.1 Layout-Update** (`resources/views/layouts/app.blade.php`)
```blade
<footer class="bg-white dark:bg-neutral-dark border-t mt-16">
    <div class="max-w-5xl mx-auto px-6 py-6">
        <!-- Footer-Links -->
        <nav class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
            <a href="{{ route('legal.impressum') }}" class="hover:text-primary">Impressum</a>
            <span class="text-gray-300">•</span>
            <a href="{{ route('legal.datenschutz') }}" class="hover:text-primary">Datenschutz</a>
            <span class="text-gray-300">•</span>
            <a href="{{ route('contact.show') }}" class="hover:text-primary">Kontakt</a>
            <span class="text-gray-300">•</span>
            <a href="{{ route('legal.lizenzen') }}" class="hover:text-primary">Lizenzen</a>
        </nav>
        
        <!-- Copyright -->
        <div class="text-sm text-gray-500 dark:text-gray-500">
            © {{ date('Y') }} ResumeHaven — Bewerbungsanalyse leicht gemacht.
        </div>
    </div>
</footer>
```

---

#### Phase 6: Tests

**6.1 Feature-Tests** (`tests/Feature/LegalPagesTest.php`)
```php
test('impressum ist erreichbar', function () {
    $response = $this->get(route('legal.impressum'));
    $response->assertStatus(200);
    $response->assertSee('Impressum');
});

// Analog für datenschutz, lizenzen
```

**6.2 Contact-Form-Tests** (`tests/Feature/ContactFormTest.php`)
```php
test('kontakt-formular zeigt seite', function () {
    $response = $this->get(route('contact.show'));
    $response->assertStatus(200);
    $response->assertSee('Kontaktformular');
});

test('kontakt-formular validiert pflichtfelder', function () {
    $response = $this->post(route('contact.submit'), []);
    $response->assertSessionHasErrors(['name', 'email', 'message']);
});

test('kontakt-formular akzeptiert valide eingabe', function () {
    $response = $this->post(route('contact.submit'), [
        'name' => 'Max Mustermann',
        'email' => 'max@example.com',
        'message' => 'Test-Nachricht mit mindestens 10 Zeichen',
    ]);
    
    $response->assertRedirect(route('contact.show'));
    $response->assertSessionHas('success');
});

test('kontakt-formular erfordert csrf-token', function () {
    $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->post(route('contact.submit'), []);
    // Test für CSRF-Fehler
});
```

**6.3 Footer-Test** (`tests/Feature/FooterNavigationTest.php`)
```php
test('footer enthält alle legal-links', function () {
    $response = $this->get('/');
    
    $response->assertSee('Impressum');
    $response->assertSee('Datenschutz');
    $response->assertSee('Kontakt');
    $response->assertSee('Lizenzen');
});
```

**6.4 Licenses-Test** (`tests/Feature/LicensesPageTest.php`)
```php
test('lizenzen-seite zeigt php-pakete', function () {
    Artisan::call('licenses:generate');
    
    $response = $this->get(route('legal.lizenzen'));
    $response->assertStatus(200);
    $response->assertSee('laravel/framework');
});
```

---

### 📦 Dateien-Struktur

**Neu zu erstellen:**
```
src/
├── app/
│   ├── Console/Commands/
│   │   └── GenerateLicenseDataCommand.php
│   ├── Dto/
│   │   └── ContactRequestDto.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ContactController.php
│   │   │   └── LegalController.php
│   │   └── Requests/
│   │       └── ContactRequest.php
│   └── Domains/Contact/
│       └── UseCases/
│           └── SendContactMessageAction.php (optional)
├── resources/views/legal/
│   ├── impressum.blade.php
│   ├── datenschutz.blade.php
│   ├── kontakt.blade.php
│   └── lizenzen.blade.php
├── tests/Feature/
│   ├── LegalPagesTest.php
│   ├── ContactFormTest.php
│   ├── FooterNavigationTest.php
│   └── LicensesPageTest.php
└── storage/app/
    └── licenses.json (generiert)
```

**Zu ändern:**
```
src/
├── routes/web.php
├── resources/views/layouts/app.blade.php
├── composer.json
└── Makefile (Root-Verzeichnis)
```

---

### ✅ Akzeptanzkriterien

**Funktional:**
- [ ] Alle vier Seiten (Impressum, Datenschutz, Kontakt, Lizenzen) sind erreichbar
- [ ] Footer-Links funktionieren auf allen Seiten
- [ ] Kontaktformular validiert serverseitig
- [ ] Lizenzen werden automatisch generiert
- [ ] Erfolgs-/Fehlermeldungen werden angezeigt

**Technisch:**
- [ ] Alle Tests grün (Pest)
- [ ] PHPStan Level 9: 0 Errors
- [ ] Pint: Code-Style konform
- [ ] CSRF-Protection aktiv
- [ ] Responsive Design (Mobile + Desktop)
- [ ] Dark-Mode funktioniert

**Content:**
- [ ] Platzhalter-Texte für Impressum/Datenschutz (für MVP ausreichend)
- [ ] Klarer Hinweis: "Muster-Content, vor Produktivbetrieb anpassen"

---

### 🎯 MVP-Entscheidungen

**Was wird NICHT implementiert (für später):**
- ❌ Echtes E-Mail-Versenden (nur Logging/DB-Speicherung)
- ❌ Datenschutz-Cookie-Banner (kommt mit Analytics)
- ❌ Multi-Language-Support
- ❌ Admin-Interface für Kontakt-Messages
- ❌ Rate-Limiting für Kontaktformular (später mit Redis)

**Was wird als Platzhalter implementiert:**
- ⚠️ Impressum-Content: "Muster-Impressum – bitte anpassen"
- ⚠️ Datenschutz-Content: "Muster-Datenschutzerklärung – DSGVO-Vorlage"
- ⚠️ Kontaktformular speichert nur in Log (kein E-Mail-Versand)

---

### 🚀 Implementierungsreihenfolge

1. **Routes + LegalController** (statische Seiten)
2. **Views für Legal-Seiten** (mit Platzhalter-Content)
3. **Footer-Navigation** (Layout-Update)
4. **Tests für statische Seiten + Footer**
5. **ContactController + ContactRequest** (Formular-Backend)
6. **Contact-View + Validation-UI**
7. **Contact-Tests**
8. **GenerateLicenseDataCommand** (Lizenzen-Generator)
9. **Lizenzen-View**
10. **Lizenzen-Tests**
11. **PHPStan + Pint + Final Testing**

---

### ⏱️ Geschätzter Aufwand

- **Phase 1-4 (Statische Seiten + Footer):** ~1h
- **Phase 5-7 (Kontaktformular):** ~1.5h
- **Phase 8-10 (Lizenzen):** ~1h
- **Tests + Quality Gates:** ~0.5h
- **Gesamt:** ~4h

---

## 💾 Commit 22 – Anonyme CV-Speicherung (Profile Context)

**Branch:** `feature/commit-22-profile-cv-storage`  
**Status:** 🔄 Basis implementiert, verifiziert und dokumentiert  
**Zweck:** Implementierung eines neuen Bounded Context `Profile` für anonyme CV-Speicherung und -Wiederherstellung über URL-Token

**Detaillierte Planung:** `docs/PLANNING_COMMIT_22.md`  
**Implementierungsstand:** `docs/COMMIT_22_IMPLEMENTATION_GUIDE.md`

---

### ✅ Bereits umgesetzt

#### Domain & Persistence
- ✅ Neuer Bounded Context `Profile` unter `app/Domains/Profile/`
- ✅ Migration `stored_resumes` mit `token`, `encrypted_cv`, `last_accessed_at`
- ✅ Model `StoredResume`
- ✅ Repository `ProfileRepository`

#### CQRS & DTOs
- ✅ `StoreResumeCommand`
- ✅ `GetResumeByTokenQuery`
- ✅ `StoreResumeHandler`
- ✅ `GetResumeByTokenHandler`
- ✅ Immutable DTOs: `StoreResumeDto`, `ResumeTokenDto`, `LoadedResumeDto`

#### Actions & Security
- ✅ `GenerateTokenAction` mit URL-safe Base64-Token
- ✅ `EncryptResumeAction` / `DecryptResumeAction`
- ✅ AES-256-GCM mit aus dem Token abgeleitetem Secret (MVP-Kompromiss)
- ✅ Fehlerbehandlung für ungültige Tokens und defekte Payloads

#### HTTP & UI
- ✅ Single-Action-Controller: `StoreResumeController`, `LoadResumeController`
- ✅ `StoreResumeRequest` für serverseitige Validierung
- ✅ Neue Routes: `POST /profile/store`, `GET /profile/load/{token}`
- ✅ `analyze.blade.php` um Speichern/Laden, Statusmeldungen und Link-Ausgabe erweitert

#### Tests & Quality Gates
- ✅ Feature-Tests für Speichern, Laden, Fehlerfälle, UI-Präsenz
- ✅ Unit-Tests für Token-Generierung und Krypto-Actions
- ✅ `make test-feature` grün
- ✅ `make test-unit` grün
- ✅ `make phpstan` grün
- ✅ `make pint-analyse` grün

---

### 🔄 Noch offen / optionaler Ausbau

- ⏳ Komfortfunktion zum Kopieren des Speicher-Links in der UI
- ⏳ Detail-Dokumentation in weiteren Übersichtsseiten nach Bedarf
- ⏳ Spätere Migration auf User-basierte Verschlüsselung vor Auth-Features

---

### 🎯 Ergebnis

ResumeHaven kann nun einen Lebenslauf anonym speichern, verschlüsselt persistieren und über einen nicht erratbaren Token-Link wieder laden, ohne den `Analysis`-Context direkt zu koppeln. Die Umsetzung folgt DDD, CQRS, Single-Action-Controller, immutable DTOs und wurde durch Feature-/Unit-Tests sowie PHPStan und Pint abgesichert.

---

