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

## Commit 19 – Empfehlungen & Verbesserungsvorschläge (KI‑gestützt)

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
## 🔒 Commit 19 – Security Härtung (MVP-Abschluss Phase 1)

**Zweck:** Anwendung gegen gängige Sicherheitsrisiken hardenen

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

## 🎨 Commit 20 – Responsive Layout & Mobile-First Design (MVP-Abschluss Phase 2a)

**Zweck:** UI für alle Geräte optimieren

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

## 🌙 Commit 20a – Dark-Mode Support (MVP-Abschluss Phase 2b)

**Zweck:** Dunkle Benutzeroberfläche für Augen-Komfort

### Added

#### 1. Tailwind Dark-Mode aktivieren
- `dark:` Prefix für Dark-Mode Styles in Blade-Templates
- System-Preference respektieren (`prefers-color-scheme`)
- Toggle-Button im Header (optional)

#### 2. Farb-Anpassungen
- Hintergrund: `white` → `dark:bg-slate-900`
- Text: `black` → `dark:text-slate-100`
- Panels: `bg-white` → `dark:bg-slate-800`
- Borders: `border-gray-200` → `dark:border-slate-700`

#### 3. Storage-Preference
- LocalStorage für User-Preference speichern
- Script im Head prüft Preference vor Page-Paint

### Tests Added
- CSS-Audit für Dark-Mode Contrast
- Manual Testing auf verschiedenen Systemen

### Result
Benutzer mit Dark-Mode-Preference bekommen passende UI.

---

## 🏆 Commit 21 – Code-Qualität Level 9 (MVP-Abschluss Phase 3a)

**Zweck:** Maximale Robustheit und Wartbarkeit durch SOLID-Prinzipien, PHPStan Level 9 und >90% Test-Coverage

**Datum:** 05.03.2026 (geplant)  
**Geschätzter Aufwand:** 4-6 Stunden  
**Status:** 🔄 In Planung

---

### 📊 IST-Analyse (aktueller Stand)

#### ✅ Bereits SOLID-konform:
- ✅ Domain-Driven Architecture (Commands, Handlers, UseCases, Actions)
- ✅ Single Responsibility in Actions
- ✅ DTOs sind immutable
- ✅ Repository Pattern (kein Raw SQL)
- ✅ Dependency Injection überall

#### ⚠️ Verbesserungspotenzial:

**1. AnalyzeController (78 Zeilen)**
- **Problem:** `analyze()` Methode mit 50+ Zeilen, mehrere Responsibilities
- **SRP-Verletzung:** Validation + Dispatching + View-Building in einer Methode
- **Lösung:** Single-Action-Controller mit `__invoke()` + private Helper-Methoden

**2. ValidateInputAction (144 Zeilen)**
- **Problem:** `execute()` mit 40+ Zeilen, Pattern-Detection und Sanitization gemischt
- **Lösung:** Zwei separate Services (`PatternDetectorService`, `InputSanitizerService`)

**3. GeminiAiAnalyzer (160 Zeilen)**
- **Problem:** `analyze()` mit 50+ Zeilen, viele Responsibilities
- **Lösung:** Response-Parsing und Validation in separate Actions extrahieren

**4. Fehlende Type-Hints**
- **Problem:** Einige `@param`/`@return` ohne strikte Type-Hints
- **Lösung:** PHPStan Level 9 = vollständige Typisierung

**5. Test-Coverage Gaps**
- **Problem:** Edge-Cases nicht getestet (Unicode, sehr große Inputs)
- **Lösung:** >90% Coverage mit zusätzlichen Tests

---

### 🚀 Umsetzungsplan (6 Phasen)

#### Phase 1: Controller-Refactoring (Single-Action-Pattern)

**Ziel:** AnalyzeController zu Single-Action-Controller umbauen

**Änderungen:**
```php
// VORHER (78 Zeilen, analyze() mit 50+ Zeilen)
class AnalyzeController extends Controller
{
    public function analyze(Request $request): View
    {
        // Validation, Dispatching, Score, View-Building...
    }
}

// NACHHER (60 Zeilen, __invoke() mit ~15 Zeilen)
class AnalyzeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $dto = $this->validateAndSanitizeInput($request);
        $result = $this->dispatchAnalysis($dto);
        $score = $this->calculateScore($result);
        return $this->buildView($result, $score);
    }
    
    private function validateAndSanitizeInput(Request $request): AnalyzeRequestDto { }
    private function dispatchAnalysis(AnalyzeRequestDto $dto): AnalyzeResultDto { }
    private function calculateScore(AnalyzeResultDto $result): ?ScoreResultDto { }
    private function buildView(AnalyzeResultDto $result, ?ScoreResultDto $score): View { }
}
```

**Route-Änderung:**
```php
// routes/web.php
Route::post('/analyze', AnalyzeController::class); // statt @analyze
```

**Erwartetes Ergebnis:**
- `__invoke()`: ~15 Zeilen (Orchestrierung only)
- Jede private Methode: < 15 Zeilen
- Cyclomatic Complexity: < 5

---

#### Phase 2: ValidateInputAction Refactoring

**Ziel:** Pattern-Detection und Input-Sanitization in separate Services extrahieren

**Neue Services:**

1. **PatternDetectorService**
```php
namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

class PatternDetectorService
{
    public function detect(string $input): array
    {
        $detected = [];
        if ($this->isSqlPattern($input)) $detected[] = 'SQL Keywords';
        if ($this->isXssPattern($input)) $detected[] = 'Script Tags';
        if ($this->isEventHandlerPattern($input)) $detected[] = 'Event Handlers';
        return $detected;
    }
    
    private function isSqlPattern(string $input): bool { }
    private function isXssPattern(string $input): bool { }
    private function isEventHandlerPattern(string $input): bool { }
}
```

2. **InputSanitizerService**
```php
namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

class InputSanitizerService
{
    public function sanitize(string $input): string
    {
        return $this->normalizeLineEndings(
            $this->trimWhitespace(
                $this->removeNullBytes($input)
            )
        );
    }
    
    private function removeNullBytes(string $input): string { }
    private function trimWhitespace(string $input): string { }
    private function normalizeLineEndings(string $input): string { }
}
```

**Vereinfachte ValidateInputAction:**
```php
class ValidateInputAction
{
    public function __construct(
        private PatternDetectorService $patternDetector,
        private InputSanitizerService $sanitizer,
    ) {}
    
    public function execute(string $input, string $fieldName = 'input'): ValidatedInputDto
    {
        $this->validateLength($input, $fieldName);
        $patterns = $this->patternDetector->detect($input);
        $sanitized = $this->sanitizer->sanitize($input);
        $this->validateNotEmpty($sanitized);
        
        return new ValidatedInputDto($input, $sanitized, strlen($sanitized), !empty($patterns), $patterns);
    }
    
    private function validateLength(string $input, string $fieldName): void { }
    private function validateNotEmpty(string $sanitized): void { }
}
```

**Erwartetes Ergebnis:**
- `execute()`: ~20 Zeilen (statt 40+)
- Zwei neue Services mit je < 50 Zeilen
- Cyclomatic Complexity < 5 pro Methode

---

#### Phase 3: GeminiAiAnalyzer Refactoring

**Ziel:** Response-Parsing und Validation in separate Actions extrahieren

**Neue Actions:**

1. **ValidateAiResponseAction**
```php
namespace App\Services\AiAnalyzer\Actions;

class ValidateAiResponseAction
{
    public function execute(string $rawResponse): array
    {
        $this->validateLength($rawResponse);
        $this->validateJsonStructure($rawResponse);
        $this->validateSecurity($rawResponse);
        
        return json_decode($rawResponse, true);
    }
}
```

2. **ParseAiResponseAction**
```php
namespace App\Services\AiAnalyzer\Actions;

class ParseAiResponseAction
{
    public function execute(array $data, AnalyzeRequestDto $request): AnalyzeResultDto
    {
        $this->validateStructure($data);
        
        $requirements = $this->extractRequirements($data);
        $experiences = $this->extractExperiences($data);
        $matches = $this->extractMatches($data);
        $gaps = $this->extractGaps($data);
        $tags = $this->extractTags($data);
        
        return new AnalyzeResultDto(
            $request->jobText(),
            $request->cvText(),
            $requirements,
            $experiences,
            $matches,
            $gaps,
            null,
            $tags
        );
    }
}
```

**Vereinfachter GeminiAiAnalyzer:**
```php
class GeminiAiAnalyzer implements AiAnalyzerInterface
{
    public function __construct(
        private ParseAiResponseAction $parseResponse,
        private ValidateAiResponseAction $validateResponse,
    ) {}
    
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
        try {
            $sanitized = $this->sanitizeInput($request);
            $response = $this->callApi($sanitized);
            $validated = $this->validateResponse->execute($response);
            return $this->parseResponse->execute($validated, $request);
        } catch (\Throwable $e) {
            $this->logError($e, $request);
            return $this->buildErrorDto($request, $e);
        }
    }
}
```

**Erwartetes Ergebnis:**
- `analyze()`: ~15 Zeilen (statt 50+)
- Zwei neue Actions mit klaren Responsibilities
- Bessere Testbarkeit durch Separation

---

#### Phase 4: PHPStan Level 9

**Aktuelle Aufgaben:**

1. **Type-Hints vervollständigen:**
```php
// VORHER
public function getByHash(string $hash): ?array

// NACHHER
/**
 * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, tags?: array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}, error?: string|null}|null
 */
public function getByHash(string $hash): ?array
```

2. **Union-Types korrekt annotieren:**
```php
// VORHER
public function handle($command)

// NACHHER
public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto
```

3. **Nullable-Types korrekt:**
```php
// VORHER
public $score;

// NACHHER
public readonly ?ScoreResultDto $score;
```

4. **PHPStan-Konfiguration verschärfen:**
```yaml
# phpstan.neon
parameters:
    level: 9
    paths:
        - app
        - tests
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
```

**Erwartetes Ergebnis:**
- PHPStan Level 9: 0 Errors
- Alle Properties, Parameter und Return-Types vollständig typisiert

---

#### Phase 5: Test-Coverage > 90%

**Fehlende Tests identifizieren:**

1. **ValidateInputAction - Edge-Cases:**
   - Unicode-Zeichen (Emoji: 😀, Umlaute: äöü)
   - Sehr lange Strings (49KB, 50KB, 51KB)
   - Mehrfache Patterns gleichzeitig
   - Null-Bytes in verschiedenen Positionen

2. **GeminiAiAnalyzer - Error-Paths:**
   - Timeout-Simulation
   - Ungültiges JSON
   - Fehlende Response-Felder
   - API-Rate-Limit

3. **AnalyzeController - Validation-Failures:**
   - Zu kurze Inputs (<30 chars)
   - Zu lange Inputs (>50KB)
   - XSS/SQL-Injection in Inputs

4. **PatternDetectorService (neu):**
   - Alle Patterns einzeln testen
   - Kombinationen von Patterns
   - Case-Insensitivity

5. **InputSanitizerService (neu):**
   - Alle Sanitization-Schritte isoliert
   - Kombinationen von problematischen Zeichen

**Neue Tests (15+):**
- `PatternDetectorServiceTest.php` (8 Tests)
- `InputSanitizerServiceTest.php` (6 Tests)
- `ValidateAiResponseActionTest.php` (5 Tests)
- `ParseAiResponseActionTest.php` (5 Tests)
- Erweiterte Edge-Case-Tests in bestehenden Test-Suites

**Coverage-Ziel:**
```bash
make test -- --coverage
# Ziel: >90% Coverage (aktuell ~85%)
```

**Mutation-Testing (optional):**
```bash
vendor/bin/pest --mutate
# Ziel: >80% Mutation-Score
```

---

#### Phase 6: Final Validation & Performance-Audit

**1. Test-Validierung:**
```bash
make test              # Alle Tests grün (100+ Tests)
make phpstan           # Level 9, 0 Errors
make pint              # Code-Formatting clean
```

**2. Performance-Audit:**

**N+1 Query Detection:**
- `AnalysisCacheRepository::getByHash()` → Single Query ✓
- Eloquent-Relations → Eager Loading checken

**Caching-Effektivität:**
- Cache-Hit-Rate messen
- Durchschnittliche Response-Zeit mit/ohne Cache

**Code-Duplication:**
```bash
vendor/bin/phpcpd app/
# Ziel: < 5% Duplikation
```

**3. Dokumentation aktualisieren:**
- `docs/CODING_GUIDELINES.md` mit neuen Services ergänzen
- `docs/ARCHITECTURE.md` mit Refactoring-Entscheidungen
- `COMMIT_PLAN.md` mit Ergebnissen

---

### Tests Added

**Neue Test-Dateien:**
- `PatternDetectorServiceTest.php` (8 Unit-Tests)
- `InputSanitizerServiceTest.php` (6 Unit-Tests)
- `ValidateAiResponseActionTest.php` (5 Unit-Tests)
- `ParseAiResponseActionTest.php` (5 Unit-Tests)
- Erweiterte Tests in bestehenden Suites (10+ Tests)

**Test-Metriken:**
- **Total Tests:** 100+ (vorher: 85)
- **Assertions:** 320+ (vorher: 265)
- **Coverage:** >90% (vorher: ~85%)

---

### 📊 Erwartete Metriken nach Commit 21

| Metrik | Vor Commit 21 | Nach Commit 21 | Verbesserung |
|--------|---------------|----------------|--------------|
| **Tests** | 85 | 100+ | +15+ Tests |
| **Assertions** | 265 | 320+ | +55+ |
| **PHPStan Level** | 7-8 | 9 | +1-2 Levels |
| **Test-Coverage** | ~85% | >90% | +5% |
| **Controller Zeilen** | 78 | ~60 | -18 Zeilen |
| **Längste Methode** | 50 Zeilen | <20 Zeilen | -60% |
| **Cyclomatic Complexity** | 8-10 | <5 | -50% |

---

### Result

Code ist Production-Ready mit höchster Quality:

- ✅ **SOLID-Prinzipien:** Alle Klassen < 200 Zeilen, Methoden < 20 Zeilen
- ✅ **Single-Action-Controller:** `AnalyzeController::__invoke()`
- ✅ **Kleine Methoden:** Cyclomatic Complexity < 5
- ✅ **PHPStan Level 9:** 0 Errors, vollständige Typisierung
- ✅ **Test-Coverage:** > 90%
- ✅ **Performance:** N+1 Queries vermieden, Caching optimiert
- ✅ **Wartbarkeit:** Code-Duplication < 5%

---

### 🎯 Definition of Done

Commit 21 gilt als **complete**, wenn:

1. ✅ **PHPStan Level 9**: 0 Errors
2. ✅ **Test-Coverage**: > 90%
3. ✅ **Tests**: Alle grün (100+ Tests, 320+ Assertions)
4. ✅ **SOLID**: Alle Klassen < 200 Zeilen, Methoden < 20 Zeilen
5. ✅ **Single-Action-Controller**: `AnalyzeController::__invoke()`
6. ✅ **Kleine Methoden**: Cyclomatic Complexity < 5
7. ✅ **Pint**: Code-Formatting clean
8. ✅ **Dokumentation**: CODING_GUIDELINES.md und ARCHITECTURE.md aktualisiert

---

### ✅ Checkliste für Umsetzung (05.03.2026)

#### Vor Start:
- [ ] Branch `feature/commit-21-code-quality` erstellen
- [ ] Tests ausführen (Baseline: 85 Tests, alle grün)
- [ ] PHPStan Level aktuell prüfen (`make phpstan`)

#### Phase 1: Controller-Refactoring
- [ ] AnalyzeController zu Single-Action umbauen
- [ ] Private Methoden extrahieren (4 Methoden)
- [ ] Route anpassen (`::class` statt `@analyze`)
- [ ] Tests aktualisieren
- [ ] PHPStan: 0 Errors

#### Phase 2: ValidateInputAction
- [ ] PatternDetectorService erstellen + Tests (8 Tests)
- [ ] InputSanitizerService erstellen + Tests (6 Tests)
- [ ] ValidateInputAction refactoren
- [ ] Tests aktualisieren (18+ Tests total)
- [ ] PHPStan: 0 Errors

#### Phase 3: GeminiAiAnalyzer
- [ ] ValidateAiResponseAction erstellen + Tests (5 Tests)
- [ ] ParseAiResponseAction erstellen + Tests (5 Tests)
- [ ] GeminiAiAnalyzer refactoren
- [ ] Tests aktualisieren
- [ ] PHPStan: 0 Errors

#### Phase 4: PHPStan Level 9
- [ ] `make phpstan` ausführen
- [ ] Alle Type-Hints ergänzen
- [ ] Nullability korrekt annotieren
- [ ] PHPStan Level 9: 0 Errors

#### Phase 5: Test-Coverage > 90%
- [ ] Coverage-Report generieren (`make test -- --coverage`)
- [ ] Fehlende Tests identifizieren
- [ ] Edge-Case-Tests schreiben (15+ neue Tests)
- [ ] Coverage > 90% erreichen

#### Phase 6: Final Validation
- [ ] `make test`: Alle Tests grün (100+ Tests)
- [ ] `make phpstan`: Level 9, 0 Errors
- [ ] `make pint`: Code-Formatting clean
- [ ] Performance-Audit durchführen
- [ ] CODING_GUIDELINES.md aktualisieren
- [ ] ARCHITECTURE.md aktualisieren

---

## 📝 Commit 21a – Umfassende Test-Suite & Dokumentation (MVP-Abschluss Phase 3b)

**Zweck:** Dokumentation aller kritischen Schnittstellen

### Added

#### 1. Zusätzliche Pest Tests
- Architecture Tests (Namespace-Struktur, Dependency-Rules)
- Integration Tests für alle UseCases
- Acceptance Tests für kritische User-Flows

#### 2. Dokumentation
- PHPDoc Comments für alle Public Methods
- Architecture Decision Records (ADRs) für wichtige Entscheidungen
- Setup-Anleitung für neue Entwickler

#### 3. Test-Beispiele im `tests/README.md`
- Wie schreibe ich Unit Tests?
- Wie schreibe ich Feature Tests?
- Wie nutze ich Mocks/Stubs?

### Tests Added
- 20+ weitere Pest Tests
- Minimal 90% Coverage

### Result
Codebase ist selbst-dokumentiert und einfach zu erweitern.

---

## 💾 Commit 22 – CV-Speicherung (DB-basiert, anonym)

**Zweck:** Lebensläufe speichern & wiederverwenden (GDPR-konform, ohne Auth)

### Added

#### 1. Database Migration
```
app/Domains/Resume/
├── Models/
│   └── StoredResume.php
├── Repositories/
│   └── StoredResumeRepository.php
├── Actions/
│   ├── GetOrCreateResumeAction.php
│   └── DeleteResumeAction.php
└── Dto/
    └── StoredResumeDto.php
```

Migration `create_stored_resumes_table`:
- `id` (UUID)
- `content_hash` (SHA256 des CV-Texts)
- `resume_text` (longText)
- `session_id` (für Tracking, optional)
- `created_at`, `updated_at`

#### 2. Controller-Erweiterung
- CV-Form: Checkbox "Gespeicherten CV verwenden?"
- GET `/` pre-fills CV-Textarea wenn gespeichert
- POST `/analyze`: Speichert CV wenn neu/geändert

#### 3. Actions
- `GetOrCreateResumeAction`: Prüft Hash, gibt bestehenden CV zurück
- `DeleteResumeAction`: Löscht CV (anonym, ohne Auth)

#### 4. UI
- Checkbox im Form
- "Gespeicherte CVs ansehen/löschen"-Link im Footer
- Einfacher "Delete"-Button neben jedem CV

#### 5. Privacy & GDPR
- Keine PII speichern (nur Text)
- Delete-Link für User (selbst-Löschung)
- Keine Tracking / Email / Namen gespeichert

### Tests Added
- `StoredResumeRepositoryTest.php` (Unit)
- `GetOrCreateResumeActionTest.php` (Unit)
- `ResumeStorageFeatureTest.php` (Feature)

### Result
Nutzer können ihre CVs speichern & wiederverwenden, ohne sich anzumelden.

---

## 🧹 Commit 22a – Resume Cleanup-Cronjob (Optional)

**Zweck:** Alte CVs automatisch löschen (Speicherplatz, Datenschutz)

### Added

#### 1. Artisan Command
```bash
php artisan resume:cleanup --older-than=90
```

- Löscht CVs älter als N Tage (default: 90)
- Output: "Deleted 42 stored resumes."

#### 2. Cronjob-Integration
```php
// In app/Console/Kernel.php
$schedule->command('resume:cleanup --older-than=90')
    ->dailyAt('03:00');
```

#### 3. Logging
- Log in `storage/logs/resume-cleanup.log`
- Anzahl gelöschter Einträge
- Timestamp

### Tests Added
- `ResumeCleanupCommandTest.php` (Feature)

### Result
Alte CVs werden automatisch gelöscht (Data Minimization GDPR).

---

## 🎯 MVP-Abschluss-Checkliste

Nach Commit 22a ist das **MVP complete**:

- ✅ **Funktionalität:** Analysen, Tags, Caching, CV-Speicherung funktionieren
- ✅ **Sicherheit:** Prompt-Injection, Input-Validation, Error-Handling (Commit 19)
- ✅ **UX:** Responsive, Dark-Mode, Accessibility (Commits 20–20a)
- ✅ **Qualität:** PHPStan Level 9, >90% Coverage (Commits 21–21a)
- ✅ **Dokumentation:** Vollständig, selbst-dokumentierter Code
- ✅ **Privacy:** GDPR-konform, Daten-Minimization, Delete-Option
- ✅ **Deployment:** Lauffähig auf IONOS Webspace

**Next:** v1.0.0 Release + Englische Dokumentation (Phase 2)

