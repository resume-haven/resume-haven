# ResumeHaven – Commit-Plan

Dieser Commit‑Plan definiert die empfohlene Reihenfolge der ersten Commits im Projekt.  
Er sorgt für eine klare, nachvollziehbare Git‑History und erleichtert die Zusammenarbeit mit GitHub Copilot und anderen Entwicklern.

Jeder Commit ist klein, fokussiert und baut logisch auf dem vorherigen auf.

---

# 🧱 Commit 1 – Projektgrundstruktur

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

# 🐳 Commit 2 – docker-compose Grundgerüst

**Zweck:** Grundstruktur der Container definieren.

**Inhalt:**
- `docker-compose.yml` mit Service‑Platzhaltern  
- Services: php, nginx, node, mailpit  
- Noch keine Konfiguration, nur Struktur  

---

# 🧩 Commit 3 – Dockerfiles (Skeleton)

**Zweck:** Basis-Dockerfiles anlegen.

**Inhalt:**
- `docker/php/Dockerfile`  
- `docker/nginx/Dockerfile`  
- `docker/node/Dockerfile`  
- Alle noch ohne Inhalt, nur FROM‑Zeilen  

---

# 🌐 Commit 4 – Nginx-Konfiguration vorbereiten

**Zweck:** Webserver-Struktur vorbereiten.

**Inhalt:**
- `docker/nginx/default.conf` hinzugefügt  
- Minimaler Serverblock  
- Noch ohne Laravel‑Routing  

---

# 🐘 Commit 5 – Laravel installieren

**Zweck:** Laravel-Grundsystem einrichten.

**Inhalt:**
- Docker gestartet  
- Laravel in `/src` installiert  
- `.env` angelegt  
- `php artisan key:generate` ausgeführt  
- Basis-Laravel-Struktur committed  

---

# 🎨 Commit 6 – TailwindCSS einrichten

**Zweck:** Frontend-Build-Pipeline vorbereiten.

**Inhalt:**
- `package.json`  
- `tailwind.config.js`  
- `postcss.config.js`  
- `resources/css/app.css`  
- Build‑Pipeline vorbereitet  

---

# 🧭 Commit 7 – Basis-Views & Routing

**Zweck:** UI‑Grundgerüst erstellen.

**Inhalt:**
- `routes/web.php` mit GET `/` und POST `/analyze`  
- `resources/views/layout.blade.php`  
- `resources/views/analysis.blade.php`  
- Minimaler HTML‑Rahmen  

---

# 🧠 Commit 8 – AnalysisEngine Skeleton

**Zweck:** Kernarchitektur anlegen.

**Inhalt:**
- `app/Services/AnalysisEngine.php`  
- `app/Services/Extractors/JobExtractor.php`  
- `app/Services/Extractors/ResumeExtractor.php`  
- `app/Services/Matcher.php`  
- `app/Services/Tagger.php`  
- Leere Methoden, nur Struktur  

---

# 🛡️ Commit 9 – Validierung hinzufügen

**Zweck:** Eingaben absichern.

**Inhalt:**
- Validierung für `job_text` und `resume_text`  
- Entweder via FormRequest oder Controller  

---

# 📊 Commit 10 – Ergebnisdarstellung (UI)

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

# 📧 Commit 11 – Mailpit-Konfiguration

**Zweck:** Lokale Mailumgebung aktivieren.

**Inhalt:**
- `.env` angepasst  
- Mailpit‑Service in docker-compose aktiviert  

---

# 🧹 Commit 12 – Cleanup & Dokumentation

**Zweck:** Projekt abrunden.

**Inhalt:**
- Kommentare ergänzt  
- `ARCHITECTURE.md` verlinkt  
- `CONTRIBUTING.md` verlinkt  
- `ROADMAP.md` verlinkt  
- kleinere Aufräumarbeiten  

---

# 🎯 Ergebnis

Nach diesem Commit‑Plan hast du:

- eine saubere, nachvollziehbare Git‑History  
- ein strukturiertes Projekt  
- eine klare Grundlage für Copilot  
- eine perfekte Basis für spätere Erweiterungen  
