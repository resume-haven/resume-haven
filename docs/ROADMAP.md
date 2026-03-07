# ResumeHaven – Roadmap

Diese Roadmap beschreibt die geplanten Schritte für das ResumeHaven‑MVP und mögliche Erweiterungen.

---

# 🚀 Phase 1 – MVP (aktuell)

## 1. Projektinitialisierung
- Docker‑Grundstruktur  
- php-fpm, nginx, node, mailpit  
- Laravel installieren  
- Tailwind einrichten  

## 2. Basis‑UI
- Formularseite  
- Ergebnisseite  
- Layout + Panels  

## 3. AnalysisEngine (Skeleton)
- Klassenstruktur  
- Interfaces  
- Dummy‑Implementationen  

## 4. Validierung
- job_text  
- resume_text  

## 5. Ergebnisdarstellung
- Anforderungen  
- Erfahrungen  
- Matches  
- Lücken  
- Zuordnungen  
- Irrelevante Punkte  

---

# 🧠 Phase 2 – Engine‑Verbesserungen

## 1. Verbesserte Extraktion
- robustere Erkennung von Anforderungen  
- bessere Segmentierung von Lebensläufen  

## 2. Matching‑Optimierung
- Synonym‑Regeln  
- Keyword‑Mapping  

## 3. Tagging‑Verbesserung
- kontextbasierte Tags  
- Mehrfachzuordnungen  

## 4. Engineering & Qualität
- GitHub Workflow etablieren (CI, PR‑Checks, Review‑Gate)  
- Dokumentationsstruktur nach **arc42** ausbauen  
- Anforderungen strukturiert über **req42** pflegen  
- Acceptance‑Tests für Kern-User‑Flows definieren und automatisieren  
- **renovate.js** für automatisierte Dependency-Updates einführen  
- **Mutation-Testing** als zusätzlicher Quality-Gate (z. B. Pest Mutate / Infection)  
- **Architecture-Testing** für Layer-Regeln, Dependency-Rules und Namespace-Compliance  
- **Security-Testing** (OWASP-orientierte Checks, Input-/Prompt-/Auth-Tests)  

---

# 🎨 Phase 3 – UI/UX‑Optimierung

- Dark Mode  
- bessere Panels  
- mobile Optimierung  
- Export der Analyse (ohne PDF‑Generierung)  

## 1. Legal‑Seiten & Compliance
- Impressum erstellen  
- Datenschutzseite (DSGVO-konform) hinzufügen  
- Kontaktseite mit rechtlich sauberem Kontaktweg ergänzen  
- Lizenzen / Third‑Party‑Notices transparent darstellen  
- Verlinkung der Legal‑Seiten im Footer jeder Seite  

---

# 🌐 Phase 4 – Erweiterungen (optional)

- PDF‑Export  
- API‑Version  
- Benutzerkonten  
- Speicherung von Analysen  
- KI‑gestützte Analyse (nur wenn gewünscht)  

---

# 🚫 Nicht geplant (Stand MVP)

- Datenbank  
- KI‑Features  
- User‑Accounts  
- Tracking  
- Speicherung von Nutzerdaten  

---

# 📌 Hinweis

Diese Roadmap ist flexibel und wird bei Bedarf angepasst.
