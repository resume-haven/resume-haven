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

Die Anwendung folgt einer klaren, modularen Struktur:

- **AnalysisEngine**  
- **JobExtractor**  
- **ResumeExtractor**  
- **Matcher**  
- **Tagger**  
- **Controller**  
- **Blade Views**  
- **TailwindCSS Build Pipeline**  

Eine vollständige Architekturdokumentation befindet sich im Repository  
👉 `resume-haven-ideas/`

---

# 🛠️ Installation (lokale Entwicklung)

## Voraussetzungen
- Docker & Docker Compose  
- Git  
- (optional) Node lokal, falls kein Docker genutzt wird  

## Projekt klonen
```bash
git clone https://github.com/<dein-user>/resume-haven.git
cd resume-haven
```

## Docker starten
```bash
docker-compose up --build
```

## Laravel installieren
```bash
docker exec -it php bash
composer install
cp .env.example .env
php artisan key:generate
```

## Tailwind starten
```bash
docker exec -it node bash
npm install
npm run dev
```

## Anwendung aufrufen
http://localhost:8080

---

# 🐳 Docker-Services

- **php-fpm** – PHP 8.5 + Composer  
- **nginx** – Webserver  
- **node** – Tailwind Build Pipeline  
- **mailpit** – lokaler SMTP‑Testserver  

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

# 🧪 Tests

```bash
docker exec -it php bash
php artisan test
```

---

# 📁 Dokumentation

Die vollständige technische und konzeptionelle Dokumentation befindet sich im Repository:

👉 `resume-haven-ideas/`

Dort findest du:
- Architektur  
- Datenstrukturen  
- UI‑Design  
- UX‑Texte  
- Docker‑Konzept  
- Validierungsregeln  
- Wireframes  

---

# 📜 Lizenz

Dieses Projekt ist aktuell **nicht öffentlich lizenziert**.  
Alle Rechte vorbehalten.

---

# 🤝 Mitwirken

Pull Requests sind willkommen, sobald das MVP stabil ist.  
Bis dahin dient dieses Repository der initialen Entwicklung.

