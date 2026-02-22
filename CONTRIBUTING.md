# Contributing to ResumeHaven

Vielen Dank für dein Interesse an ResumeHaven!  
Dieses Dokument beschreibt die Regeln und Erwartungen für Beiträge zum Projekt.

---

# 🎯 Projektphilosophie

ResumeHaven ist ein bewusst **minimalistisches**, **regelbasiertes** Analyse‑Tool.  
Das MVP soll:

- klar strukturiert  
- leicht verständlich  
- ohne unnötige Komplexität  
- ohne KI  
- ohne Datenbank  
- ohne User‑Accounts  

sein.

Bitte halte dich bei Beiträgen an diese Prinzipien.

---

# 🧱 Architekturgrundsätze

- Die Analyse erfolgt über die **AnalysisEngine**.  
- Die Engine besteht aus:  
  - JobExtractor  
  - ResumeExtractor  
  - Matcher  
  - Tagger  
- Die Engine liefert ein `AnalysisResult`.  
- Controller sind dünn und enthalten keine Logik.  
- Views sind minimalistisch und nutzen TailwindCSS.  
- Keine Speicherung von Nutzerdaten.

---

# 🛠️ Entwicklungsumgebung

ResumeHaven nutzt Docker:

- php-fpm (PHP 8.5)  
- nginx  
- node (Tailwind Build)  
- mailpit  

Starte die Umgebung:

```bash
docker-compose up --build
```

Laravel installieren:

```bash
docker exec -it php bash
composer install
cp .env.example .env
php artisan key:generate
```

Tailwind starten:

```bash
docker exec -it node bash
npm install
npm run dev
```

---

# 🧪 Tests

Bitte stelle sicher, dass alle Tests erfolgreich laufen:

```bash
php artisan test
```

Neue Features müssen mit Tests abgedeckt werden.

---

# 📦 Pull Requests

Bitte beachte:

- PRs müssen klein und fokussiert sein  
- Commit‑Nachrichten klar und beschreibend  
- Keine neuen Abhängigkeiten ohne Diskussion  
- Keine KI‑Features ohne explizite Freigabe  
- Keine Datenbankeinführung  

---

# 📚 Dokumentation

Alle Architektur‑ und Konzeptdokumente befinden sich im Repo:

`resume-haven-ideas/`

Bitte halte die Dokumentation aktuell, wenn
