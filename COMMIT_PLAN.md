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

## 🛡️ Commit 9 – Integrate TailwindCSS into the Laravel application

### Added

- Initialized Node environment inside the `resumehaven-node` container:
  - `npm init -y`
  - Installed TailwindCSS, PostCSS, and Autoprefixer as dev dependencies:

    ```bash
    npm install -D tailwindcss postcss autoprefixer
    ```

  - Generated Tailwind and PostCSS configuration files via:

    ```bash
    npx tailwindcss init -p
    ```

- Created Tailwind entrypoint at:
  - `resources/css/app.css` containing:

    ```bash
    @tailwind base;
    @tailwind components;
    @tailwind utilities;
    ```

- Added build directory:
  - `public/build/`

### Updated

- Configured `tailwind.config.js` to scan Laravel view and resource files:

  ```js
  content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
  ]
  ```

- Added npm scripts to `package.json`:

  ```json
  "scripts": {
      "dev": "tailwindcss -i ./resources/css/app.css -o ./public/build/app.css --watch",
      "build": "tailwindcss -i ./resources/css/app.css -o ./public/build/app.css --minify"
  }
  ```

- Updated Laravel layout (or `welcome.blade.php`) to load the generated stylesheet:

  ```html
  <link rel="stylesheet" href="/build/app.css">
  ```

### Result

TailwindCSS is now fully integrated

---

## 📊 Commit 10 – Ergebnisdarstellung (UI)

**Zweck:** Analyseergebnisse visuell darstellen.

**Inhalt:**

- Panels für:  
  - Anforderungen  
  - Erfahrungen  
  - Matches  
  - Lücken  
  - Zuordnungen  
  - Irrelevante Punkte  
- Tailwind‑Styling  

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
