# ResumeHaven – Roadmap

Diese Roadmap beschreibt die geplanten Schritte für das ResumeHaven‑MVP und mögliche Erweiterungen.

---

# 🚀 Aktueller Stand (Stand: Commit 36)

- **Phase 1-4:** Kern-Features (Analyse, UI, Auth, Multi-CV) sind weitgehend stabil und umgesetzt.
- **Phase 5 (LLM-Layer):** Meilensteine L1 bis L4 sind erfolgreich abgeschlossen (Provider-Abstraktion, OpenAI/Anthropic Integration, Retry-Logik).
- **UX/Auth:** Commit 35 hat den Auth/Claim-Flow poliert (Ergebnis-Restore).
- **Nächster Fokus:** Konsolidierung und Vorbereitung auf Deployment vs. weitere Feature-Härtung.

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

# 🧠 Phase 2 – Bewerbungs-Management & Dokument-Workflow (Neu fokussiert)

## 1. Dokument-Handling & Paperdoc-Integration (Commit 39)
- [ ] **Import:** Automatisches Auslesen von PDF/DOCX via Paperdoc.dev.
- [ ] **Validierung:** Prüfung auf Dateigröße und Formate (mimes:pdf,docx).
- [ ] **Fallbacks:** Nahtloser Wechsel zwischen File-Upload und Copy-Paste.

## 2. Persistenz & Bewerbungs-Lifecycle (Commit 41)
- [ ] **Application-Model:** Einführung einer `Application`-Entity (Job + CV + Status).
- [ ] **Bewerbungshistorie:** Tracking von Events (Abgeschickt, Interview, Zusage, Absage).
- [ ] **Metadaten:** Erfassung von Gehaltswunsch, Notizen und Bewerbungsdatum.

## 3. Generierung & Interaktives Editing (Commit 42)
- [ ] **Rohdaten-Modus:** KI generiert optimierten CV und Anschreiben als editierbare Rohdaten (Markdown/JSON).
- [ ] **Inline-Editor:** UI zum Anpassen der Vorschläge vor der Finalisierung.
- [ ] **Versionierung:** Speicherung der Rohdaten + PDFs pro Iteration.
- [ ] **Export:** Finaler PDF-Download via Paperdoc.

## 4. Dashboard & Analytics (Commit 43)
- [ ] **Übersicht:** Status-Board aller aktiven und vergangenen Bewerbungen.
- [ ] **Analytics:** Auswertung von Erfolgsquoten (z. B. "Interview-Rate").
- [ ] **Suche & Filter:** Schneller Zugriff auf spezifische Stellenausschreibungen.

---

# 🧠 Phase 3 – Engine‑Verbesserungen (fortlaufend)

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

# 🎨 Phase 3 – UI/UX‑Optimierung (in Arbeit)

- Dark Mode → **✅ umgesetzt**
- bessere Panels → **teilweise umgesetzt, weitere Polishes geplant**
- mobile Optimierung → **✅ Basis umgesetzt**
- Export der Analyse (ohne PDF‑Generierung) → **geplant**
- Auth/Claim UX Polish (CTA, Microcopy, Status-Hinweise) → **✅ abgeschlossen in Commit 35**

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

# 🔌 Phase 5 – Provider-agnostischer LLM-Layer ✅ (abgeschlossen)

## Ziel

Den AI-Analyzer von der konkreten Gemini-Implementierung lösen, sodass beliebige LLM-Provider
über ein einheitliches Plugin-Interface eingebunden werden können — ohne Änderungen am
Domain-Code.

## Status Quo (nach Commit 34)

- **L1:** `AbstractLlmAiAnalyzer` extrahiert, `GeminiAiAnalyzer` umgestellt. ✅
- **L2:** Plugin-Interface `LlmProviderPluginInterface` formalisiert, `AI_PROVIDER=openai` verfügbar. ✅
- **L3:** `AnthropicAiAnalyzer` als zweiten Provider (PoC) integriert. ✅
- **L4:** Konfigurierbare Retry-Logik + Error-Hardening im AI-Layer umgesetzt. ✅

## Erreichte Architektur

```
AiAnalyzerInterface                    ← unverändert, bleibt Vertragsgrundlage
    │
    ├── AbstractLlmAiAnalyzer          ← generischer Basis-Analyzer (Sanitization, Retry, Logging)
    │       uses LlmProviderPluginInterface
    │
    ├── GeminiAiAnalyzer               ← implementiert LlmProviderPluginInterface
    ├── OpenAiAnalyzer                 ← implementiert LlmProviderPluginInterface
    ├── AnthropicAiAnalyzer            ← implementiert LlmProviderPluginInterface
    └── MockAiAnalyzer                 ← bleibt unverändert
```

## Plugin-Konzept für LLM-Eigenheiten

Jeder Provider implementiert das `LlmProviderPluginInterface`, um provider-spezifische Eigenheiten abzubilden:

| Aspekt | Umsetzung |
|---|---|
| **Prompt-Format** | Über `getPromptPayload()` |
| **Structured Output** | Über `getPromptPayload()` (JSON-Struktur) |
| **Fehler-Mapping** | Über `mapToTransientException()` (Retry-Steuerung) |

## Mutation-Testing-Vorbereitung (Commit 28)

Mutation-Testing wird als optionales Engineering-Tool vorbereitet (nicht im Standard-CI).

**Status:** Vorbereitet in Commit 28
- ✅ `pestphp/pest-plugin-mutate` als dev-dependency
- ✅ `make test-mutation` Target (Scope: `app/Domains`)
- ✅ Composer-Script `test:pest-mutation`

**Offene Fragen für Detailplanung (später):**

- [ ] **MSI-Schwellwert:** Welcher Mutation-Score-Index ist akzeptabel? (z. B. 80 %, 85 %)
- [ ] **Slow-Test-Strategie:** Sollen lange laufende Tests ausgeschlossen werden? (z. B. Integration Tests)
- [ ] **Parallelisierung:** Soll `--parallel` im Mutation-Workflow aktiviert werden?
- [ ] **CI-Integration:** Separater `mutation.yml` Workflow (workflow_dispatch) oder Subset im Standard-CI?
- [ ] **Coverage-Mindestwert:** Nur Tests mit Coverage >= 95 % mutieren?

## Umsetzungsschritte (grob)

1. **`AbstractLlmAiAnalyzer` extrahieren**
   - Gemeinsamen Code aus `GeminiAiAnalyzer` hochziehen
   - `GeminiAiAnalyzer` auf `extends AbstractLlmAiAnalyzer` umstellen
   - Alle bestehenden Tests bleiben grün

2. **Plugin-Interface formalisieren**
   - Optional: `LlmProviderPluginInterface` für provider-spezifische Hooks
     (`buildPromptPayload()`, `normalizeResponse()`, `mapProviderException()`)

3. **Provider-Config erweitern**
   - `AI_PROVIDER=openai` / `AI_PROVIDER=anthropic` im AppServiceProvider ergänzen
   - Konfigurationsstruktur in `config/ai.php` provider-generisch gestalten

4. **Ersten Zweit-Provider implementieren** (Proof of Concept)
   - z. B. `OpenAiAnalyzer` als Validierung der Abstraktion

5. **Tests & Quality Gates**
   - AbstractLlmAiAnalyzer: Unit-Tests für gemeinsame Logik
   - Pro Plugin: minimale Integrations-Tests für abweichende Pfade

## Abhängigkeiten & Voraussetzungen

- Commit 28 (Architecture-Tests) sollte abgeschlossen sein, damit Layer-Regeln
  den neuen `AbstractLlmAiAnalyzer` korrekt einordnen
- `laravel/ai` SDK muss den gewünschten Provider unterstützen (aktuelle Version prüfen)
- Kein User-Auth erforderlich

## Offene Fragen für die Detailplanung

- [ ] Soll `LlmProviderPluginInterface` als eigenes Interface existieren oder reicht
      Template-Method im Abstract-Analyzer?
- [ ] Welcher Zweit-Provider wird als Proof of Concept implementiert (OpenAI / Anthropic)?
- [ ] Soll Provider-Fallback (z. B. Gemini → OpenAI bei Timeout) in Phase 5 oder später?
- [ ] Konfiguration pro Analyse-Typ (z. B. Gemini für Matching, OpenAI für Empfehlungen)?

---

# 🚫 Nicht geplant (Stand MVP)

- Tracking / Analytics  
- Mandanten-/Teamverwaltung  
- Produktive Cloud-/Deployment-Strategie vor Abschluss des User-/LLM-Blocks  
- Lokales LLM-Hosting im MVP  

---

# 📌 Hinweis

Diese Roadmap ist flexibel und wird bei Bedarf angepasst.

---

# 📊 Current status (2026-05-27)

## ✅ Completed
- **Phase 1 (MVP):** Komplett umgesetzt
- **Phase 2 (BMS & Dokumente):** Integration von Paperdoc, Bewerbungshistorie und Dashboard (Neu fokussiert)
- DDD-Architektur mit Commands/Handlers/UseCases/Actions
- CQRS-Pattern (Phase 1 abgeschlossen, Phase 2 in Arbeit)
- Code-Coverage: >=95% abgesichert
- PHPStan Level 9: 0 Errors
- OWASP-orientiertes Security-Testing implementiert
- Interface-based Design (AiAnalyzerInterface + Strategy-Pattern)
- Xdebug-Integration für Debugging + Coverage
- Commit 22: Profile-Context (anonyme, verschlüsselte CV-Speicherung)
- Commit 23: GitHub Actions CI + Branch Protection
- Commit 24: Kompetenzlebensläufe I (MVP-light)
- Commit 25: Analysequalität & Erklärbarkeit (abgeschlossen)
- Commit 26: Profile-Ausbau ohne Auth (abgeschlossen)
- Commit 27: Acceptance-Tests Kernflows (abgeschlossen)
- Commit 28: Architecture-Tests & Engineering-Härtung (abgeschlossen)
- Commit 29: Auth + Rollen + Claim-Flow (abgeschlossen)
- Commit 30: CV-Verwaltung (Multi-CV CRUD) (abgeschlossen)
- Commit 31: Delete/AuthZ-Gate + provider-generische AI-Basis (abgeschlossen)
- Commit 32: L2 Plugin-Interface + OpenAI auswählbar + provider-spezifisches Exception-Mapping (abgeschlossen)
- Commit 33: L3 Anthropic Provider PoC + minimaler E2E-Analyse-Pfad (abgeschlossen)
- Commit 34: L4 Retry-PoC + Error-Hardening im AI-Layer (abgeschlossen)
- Commit 35: Auth/Claim UX polish (result restore, token-based redirects, claim feedback) (completed)

## 🔄 In progress
- Commit 36: Roadmap planning & documentation sync ✅
  - Status alignment between `COMMIT_PLAN.md`, `docs/ROADMAP.md`, `docs/COMMIT_HISTORY_INDEX.md`, `docs/ai/WORKING_BASELINE.md`, and `docs/history/COMMIT_HISTORY_2026.md`
  - Re-prioritize: Swap Commit 37 and 38 (CV management before deployment basis)

## 📋 Planned
- Commit 37: **CV-Verwaltung Ausbau**
  - Ziel: Filter, Suche und Paginierung für gespeicherte Lebensläufe im Dashboard.
  - Fokus: UX-Verbesserung für Nutzer mit vielen Dokumenten.
- Commit 38: **Paperdoc-Integration & Dokument-Parsing**
  - Ziel: PDF/DOCX Import via Paperdoc.dev.
  - Fokus: Automatisierung des Inputs für CV und Job.
- Commit 39: **Bewerbungs-Lifecycle & Persistenz**
  - Ziel: Speicherung von Bewerbungen inkl. Historie und Events.
  - Fokus: Datenbank-Schema für ApplicationManagement Domain.
- Commit 40: **Interaktives Editing & Generierung**
  - Ziel: Editierbare Rohdaten für CV/Anschreiben vor PDF-Export.
  - Fokus: UI-Editor und Versionierung.
- Commit 41: **Dashboard & Analytics**
  - Ziel: Zentrales Board für Bewerbungsverfolgung.
  - Fokus: KPI-Visualisierung und Status-Tracking.
- Commit 42: **Deployment-Basis & Infrastructure Härtung**
  - Ziel: Projekt "production-ready" machen (Config, Logging, Env-Validation).
  - Fokus: Sicherheits-Header, Logging-Profile, Environment-Checks.
- Commit 43: **Engineering-Exzellenz & Dokumentation**
  - Ziel: Formale Dokumentation und fortgeschrittene Qualitätssicherung.
  - Fokus: arc42 (Architektur), req42 (Anforderungen), Mutation Testing (Infection/Pest Mutate).
- **Phase 5: provider-agnostic LLM layer (Erweiterung)**
  - Open decisions around plugin interface, fallback, and provider configuration
  - Follow-up questions are listed in the Phase 5 section above
- GitHub CI/CD workflow (Commit 23 ✅)
- arc42 documentation structure
- req42 requirements management
- Acceptance tests (Commit 27 ✅)
- renovate.js
- Mutation testing (Commit 28: preparation ✅, detailed planning later)
- Architecture testing (Commit 28 ✅)

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
