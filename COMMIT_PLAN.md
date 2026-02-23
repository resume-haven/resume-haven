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

## 📧 Commit 11 – Mailpit-Konfiguration

**Zweck:** Lokale Mailumgebung aktivieren.

**Inhalt:**

- `.env` angepasst  
- Mailpit‑Service in docker-compose aktiviert  

---

## 🧹 Commit 12 – Cleanup & Dokumentation

**Zweck:** Projekt abrunden.

**Inhalt:**

- Kommentare ergänzt  
- `ARCHITECTURE.md` verlinkt  
- `CONTRIBUTING.md` verlinkt  
- `ROADMAP.md` verlinkt  
- kleinere Aufräumarbeiten  

---

## 🎯 Ergebnis

Nach diesem Commit‑Plan hast du:

- eine saubere, nachvollziehbare Git‑History  
- ein strukturiertes Projekt  
- eine klare Grundlage für Copilot  
- eine perfekte Basis für spätere Erweiterungen  
