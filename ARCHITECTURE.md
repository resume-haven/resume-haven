# ResumeHaven – Architektur

Dieses Dokument beschreibt die technische Architektur des ResumeHaven‑MVP.

---

# 🧠 1. Überblick

ResumeHaven ist ein modular aufgebautes, regelbasiertes Analyse‑Tool.  
Die Architektur ist bewusst einfach gehalten, um:

- Wartbarkeit  
- Testbarkeit  
- Erweiterbarkeit  

zu gewährleisten.

---

# 🧩 2. Hauptkomponenten

## AnalysisEngine
Zentrale Steuereinheit der Analyse.  
Verantwortlich für:

- orchestrieren von Extraktion, Matching, Tagging  
- Erzeugen eines `AnalysisResult`  
- Bereitstellen der Methode `analyze(jobText, resumeText)`  

---

## JobExtractor
Extrahiert Anforderungen aus der Stellenausschreibung.

---

## ResumeExtractor
Extrahiert Erfahrungen und Skills aus dem Lebenslauf.

---

## Matcher
Findet Übereinstimmungen zwischen Anforderungen und Erfahrungen.

---

## Tagger
Ordnet Erfahrungen passenden Anforderungen zu.

---

## AnalysisResult
Strukturiertes Ergebnisobjekt:

- requirements  
- experiences  
- skills  
- matches  
- gaps  
- taggedExperiences  
- irrelevant  

---

# 🧭 3. Controller

## AnalysisController
- GET `/` → Formular  
- POST `/analyze` → Validierung + Engine  

Controller enthalten **keine Business‑Logik**.

---

# 🎨 4. Views

- Blade Templates  
- TailwindCSS  
- Minimalistisch  
- Panels für Ergebnisse  

---

# 🐳 5. Docker‑Architektur

Services:

- php-fpm (PHP 8.5)  
- nginx  
- node (Tailwind Build)  
- mailpit  

---

# 🚫 6. Nicht im MVP enthalten

- keine KI  
- keine Datenbank  
- keine Accounts  
- keine Speicherung von Nutzerdaten  
- kein E‑Mail‑Versand (nur Mailpit)  
- keine API  
- keine PDF‑Generierung  

---

# 📌 7. Ziel der Architektur

- Klarheit  
- Einfachheit  
- Erweiterbarkeit  
- Stabilität  
