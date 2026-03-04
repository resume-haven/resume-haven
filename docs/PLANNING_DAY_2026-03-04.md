# 🎯 Planungstag – 04.03.2026
**Datum:** 04.03.2026  
**Status:** MVP-Phase – Fokus-Entscheidungen und Priorisierungen  
**Autor:** Guido & GitHub Copilot  
---
## 📌 Produktvision (Nord-Stern)
ResumeHaven unterstützt Menschen dabei, hochwertige und passgenaue Bewerbungsunterlagen zu erstellen, indem es Stellenausschreibungen und persönliche Profile intelligent analysiert.
**Aktueller Stand (nach Commit 16c):**
- ✅ Textbasierte Analyse mit KI (Gemini)
- ✅ Extraktion Anforderungen/Erfahrungen
- ✅ Matching & Gap-Analyse
- ✅ Strukturierte UI mit Panels
- ✅ Analysis Caching mit DB
- ✅ Mock-Modus für Entwicklung
- ✅ Tag-basierte Darstellung (Matches/Gaps)
- ✅ Domain-Driven Architektur
- ✅ Hohe Code-Qualität (PHPStan, Pint, Pest)
---
## 🎯 Fokus-Entscheidungen (04.03.2026)
### 1️⃣ Priorisierung: C → B → A
**C – Security Härtung (Commit 19):**
- Prompt-Injection-Härtung
- Input-Validierung
- Error-Handling für API-Timeouts
- CSRF-Protection review
- SQL-Injection-Prävention
**B – Layout/UX (Commit 20):**
- Responsive Design (Mobile-First)
- Dark-Mode Support
- Accessibility (WCAG 2.1 AA)
- Verbesserte Spacing & Typografie
**A – Code-Qualität (Commit 21):**
- PHPStan Level 9
- Zusätzliche Pest-Tests
- Dokumentation & Refactoring
### 2️⃣ CV-Speicherung: Option B
- DB-gespeichert (SQLite/MySQL)
- Keine User-Authentifizierung im MVP
- Session-/Hash-basiert (anonym)
- Cleanup nach 90 Tagen (optional)
### 3️⃣ Dokumentation: Option C
- Deutsche Doku bleibt aktuell
- Englische Übersetzung nach MVP-Abschluss
- Code-Kommentare in English
---
## 📊 Commit-Planung
| Commit | Thema | Phase | Status |
|--------|-------|-------|--------|
| 19 | Security Härtung | C | 🚀 Nächst |
| 19a | Prompt-Injection-Tests | C | Geplant |
| 19b | Input-Validierung | C | Geplant |
| 20 | Responsive Layout | B | Geplant |
| 20a | Mobile-First CSS | B | Geplant |
| 20b | Dark-Mode | B | Geplant |
| 21 | Code-Qualität (L9) | A | Geplant |
| 21a | Unit-Tests | A | Geplant |
| 22 | CV-Speicherung | Feature | Geplant |
| 22a | Cleanup-Cronjob | Feature | Optional |
---
## ✅ MVP-Abschluss definiert durch:
1. ✅ Funktional: Alle Commits 1–22 umgesetzt
2. ✅ Sicherheit: Keine bekannten Lücken
3. ✅ Qualität: PHPStan L9, >90% Coverage
4. ✅ UX: Responsive, Dark-Mode, WCAG 2.1 AA
5. ✅ Dokumentation: README, ARCHITECTURE, GUIDELINES komplett
6. ✅ Deployment: IONOS Webspace ready
---
**Gültig ab:** 04.03.2026  
**Nächste Review:** Nach Commit 21
