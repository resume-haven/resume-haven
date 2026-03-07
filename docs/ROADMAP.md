# ResumeHaven – Roadmap

Diese Roadmap beschreibt die geplanten Schritte für das ResumeHaven‑MVP und mögliche Erweiterungen.

---

# 🚀 Phase 1 – MVP ✅ (abgeschlossen)

## 1. Projektinitialisierung ✅
- Docker‑Grundstruktur  
- php-fpm, nginx, node, mailpit  
- Laravel installieren  
- Tailwind einrichten  

## 2. Basis‑UI ✅
- Formularseite  
- Ergebnisseite  
- Layout + Panels  

## 3. AnalysisEngine (Skeleton) ✅
- Klassenstruktur  
- Interfaces  
- Dummy‑Implementationen  

## 4. Validierung ✅
- job_text  
- resume_text  

## 5. Ergebnisdarstellung ✅
- Anforderungen  
- Erfahrungen  
- Matches  
- Lücken  
- Zuordnungen  
- Irrelevante Punkte  

---

# 🧠 Phase 2 – Engine‑Verbesserungen (in Arbeit)

## 1. Verbesserte Extraktion
- robustere Erkennung von Anforderungen  
- bessere Segmentierung von Lebensläufen  

## 2. Matching‑Optimierung
- Synonym‑Regeln  
- Keyword‑Mapping  

## 3. Tagging‑Verbesserung
- kontextbasierte Tags  
- Mehrfachzuordnungen  

## 4. Engineering & Qualität ✅ (teilweise umgesetzt)
- GitHub Workflow etablieren (CI, PR‑Checks, Review‑Gate) → **geplant**
- **Git-Hooks** für Pre-Commit-Checks einführen → **geplant**
  - Changelog-Update-Check bei Feature-Commits
  - PHPStan + Pint vor Commit ausführen
  - Commit-Message-Conventions prüfen
- Dokumentationsstruktur nach **arc42** ausbauen → **geplant**
- Anforderungen strukturiert über **req42** pflegen → **geplant**
- Acceptance‑Tests für Kern-User‑Flows definieren und automatisieren → **geplant**
- **renovate.js** für automatisierte Dependency-Updates einführen → **geplant**
- **Mutation-Testing** als zusätzlicher Quality-Gate (z. B. Pest Mutate / Infection) → **geplant**
- **Architecture-Testing** für Layer-Regeln, Dependency-Rules und Namespace-Compliance → **geplant**
- **Security-Testing** (OWASP-orientierte Checks, Input-/Prompt-/Auth-Tests) → **✅ umgesetzt**
  - `make test-security` – OWASP-orientierte Security-Tests
  - `make test-security-strict` – Erweiterte Security-Tests mit stop-on-failure
  - `make test-security-gate` – Kombiniertes Gate (Security + PHPStan + Pint)
  - Security-Test-Template in `docs/DEVELOPMENT.md` dokumentiert
  - OWASP-Mapping-Tabelle in `docs/CODING_GUIDELINES.md`
- **Changelog/Release Notes** → **✅ umgesetzt**
  - `CHANGELOG.md` nach Keep a Changelog Standard
  - Semantic Versioning (MAJOR.MINOR.PATCH)
  - Verlinkt in `docs/DEVELOPMENT.md` und `docs/ai/AGENT_CONTEXT.md`  

---

# 🎨 Phase 3 – UI/UX‑Optimierung (geplant)

- Dark Mode → **geplant**
- bessere Panels → **geplant**
- mobile Optimierung → **geplant**
- Export der Analyse (ohne PDF‑Generierung) → **geplant**

## 1. Legal‑Seiten & Compliance (geplant für Commit 20b)
- Impressum erstellen  
- Datenschutzseite (DSGVO-konform) hinzufügen  
- Kontaktseite mit rechtlich sauberem Kontaktweg ergänzen (nur Formular, kein mailto-Fallback)
- Lizenzen / Third‑Party‑Notices transparent darstellen (automatisiert aus `composer.lock` + `package-lock.json`)
- Verlinkung der Legal‑Seiten im Footer jeder Seite  
- Feature-Tests für alle Legal-Routen
- CSRF-Schutz für Kontaktformular  

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

---

# 📊 Aktueller Stand (2026-03-08)

## ✅ Abgeschlossen
- Phase 1 (MVP): Komplett umgesetzt
- DDD-Architektur mit Commands/Handlers/UseCases/Actions
- CQRS-Pattern (Phase 1 abgeschlossen, Phase 2 in Arbeit)
- Code-Coverage: 98.2% (Minimum: 95%)
- PHPStan Level 9: 0 Errors
- OWASP-orientiertes Security-Testing implementiert
- Interface-based Design (AiAnalyzerInterface)
- Xdebug-Integration für Debugging + Coverage

## 🔄 In Arbeit
- Phase 2: Engine-Verbesserungen
- Security-Testing vollständig dokumentiert
- Legal-Seiten-Planung (Commit 20b)

## 📋 Geplant
- GitHub CI/CD Workflow
- arc42 Dokumentationsstruktur
- req42 Requirements Management
- Acceptance-Tests
- renovate.js
- Mutation-Testing
- Architecture-Testing

---

# 🛠️ Make-Kommandos (Übersicht)

```bash
# Tests
make test                   # Alle Tests
make test-security          # OWASP Security-Tests
make test-security-strict   # Security-Tests (strict mode)
make test-security-gate     # Security + PHPStan + Pint
make test-coverage          # Coverage-Check (≥95%)

# Code-Qualität
make phpstan                # PHPStan Level 9
make pint-fix               # Code-Formatierung
make pint-analyse           # Formatierungs-Check

# Docker
make docker-up              # Container starten
make docker-down            # Container stoppen
make php-shell              # PHP-Container Shell

# Debugging
make debug-on               # Xdebug aktivieren
make debug-off              # Xdebug deaktivieren
make debug-status           # Xdebug-Status prüfen
```

Vollständige Kommando-Liste: `make help`
