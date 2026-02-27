# ResumeHaven – Commit-Plan

Dieser Commit‑Plan definiert die empfohlene Reihenfolge der ersten Commits im Projekt.  
Er sorgt für eine klare, nachvollziehbare Git‑History und erleichtert die Zusammenarbeit mit GitHub Copilot und anderen Entwicklern.

Jeder Commit ist klein, fokussiert und baut logisch auf dem vorherigen auf.

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

## Commit 16 – Score‑Berechnung & visuelle Bewertung (Prozent, Balken, Farbcodierung)

### Ziel

Die Analyse‑Ergebnisse werden um eine numerische Bewertung ergänzt.
Nutzer sehen nun auf einen Blick, wie gut ihr Profil zur Stellenausschreibung passt.

---

### Änderungen

#### 1. Score‑Berechnung

- Neue Formel implementiert:
  - Score = Matches / (Matches + Gaps) * 100
- Ergebnis wird als Prozentwert gerundet
- Grundlage für spätere Gewichtungen geschaffen

---

#### 2. Farbskala

- Score‑abhängige Farbcodierung:
  - 0–40 % → Rot (`#dc2626`)
  - 40–70 % → Gelb (`#f59e0b`)
  - 70–100 % → Grün (`#16a34a`)
- Farben sind vollständig markenkonsistent

---

#### 3. Fortschrittsbalken

- Horizontaler Balken zeigt den Score visuell an
- Farbe abhängig vom Score
- Balken animiert (optional)

---

#### 4. Zusammenfassungspanel

- Neues Panel am Anfang der Ergebnis‑Seite:
  - großer Score‑Wert (z. B. „72 % Match“)
  - farbiger Fortschrittsbalken
  - kurze Bewertung („Gute Übereinstimmung“ etc.)
  - Anzahl Matches und Gaps
- Panel nutzt bestehende UI‑Tokens (Panels, Farben, Typografie)

---

#### 5. Integration in die Ergebnis‑UI

- Score‑Panel wird oberhalb der bisherigen vier Panels angezeigt
- Reihenfolge der Ergebnis‑Darstellung optimiert
- UI wirkt klarer, professioneller und nutzerfreundlicher

---

### Ergebnis

ResumeHaven bietet jetzt eine vollständige visuelle Bewertung:

- Prozent‑Score
- Farbkodierung
- Fortschrittsbalken
- Zusammenfassung der Stärken und Lücken

Die Analyse wirkt dadurch deutlich verständlicher und professioneller.

---

## Commit 17 – Empfehlungen & Verbesserungsvorschläge (KI‑gestützt)

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
## 🎯 Ergebnis

Nach diesem Commit‑Plan hast du:

- eine saubere, nachvollziehbare Git‑History
- ein strukturiertes Projekt
- eine klare Grundlage für Copilot
- eine perfekte Basis für spätere Erweiterungen

