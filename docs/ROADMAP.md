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

- Dark Mode → **✅ umgesetzt**
- bessere Panels → **teilweise umgesetzt, weitere Polishes geplant**
- mobile Optimierung → **✅ Basis umgesetzt**
- Export der Analyse (ohne PDF‑Generierung) → **geplant**
- Auth/Claim UX Polish (CTA, Microcopy, Status-Hinweise) → **geplant als Follow-up nach Commit 29**

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

# 🔌 Phase 5 – Provider-agnostischer LLM-Layer (geplant)

## Ziel

Den AI-Analyzer von der konkreten Gemini-Implementierung lösen, sodass beliebige LLM-Provider
über ein einheitliches Plugin-Interface eingebunden werden können — ohne Änderungen am
Domain-Code.

## Ausgangssituation

Die aktuelle Architektur ist bereits gut vorbereitet:

- `AiAnalyzerInterface` → provideragnostische Abstraktion ✅
- `AppServiceProvider` → Strategy-Pattern-Binding über `AI_PROVIDER`-Config ✅
- `GeminiAiAnalyzer` → konkrete Gemini-Implementierung (korrekt benannt) ✅
- `MockAiAnalyzer` → Test-/Dev-Implementierung ✅

Was fehlt: ein generischer Basisanalyzer sowie ein formalisiertes Plugin-Konzept für
provider-spezifische Eigenheiten.

## Geplante Architektur

```
AiAnalyzerInterface                    ← unverändert, bleibt Vertragsgrundlage
    │
    ├── AbstractLlmAiAnalyzer          ← neu: generischer Basis-Analyzer
    │       gemeinsame Logik:          (Sanitization, Error-Handling, Logging,
    │                                   JSON-Encoding, Response-Validierung)
    │
    ├── GeminiAiAnalyzer               ← bleibt, extends AbstractLlmAiAnalyzer
    ├── OpenAiAnalyzer                 ← zukünftig
    ├── AnthropicAiAnalyzer            ← zukünftig
    └── MockAiAnalyzer                 ← bleibt unverändert
```

## Plugin-Konzept für LLM-Eigenheiten

Jeder Provider kann in seinem Analyzer von der Basis abweichen, wo nötig:

| Aspekt | Beispiel für provider-spezifische Abweichung |
|---|---|
| **Prompt-Format** | Gemini: JSON-Objekt; OpenAI: System+User-Message-Struktur |
| **Structured Output** | Unterschiedliche Schema-Übergabe pro SDK |
| **Token-Limits** | Provider-spezifische Max-Token-Konfiguration |
| **Fehler-Codes** | HTTP-429 (Rate Limit), API-spezifische Exception-Typen |
| **Retry-Strategie** | Exponential Backoff je nach Provider-Verhalten |
| **Response-Normalisierung** | Unterschiedliche `toArray()`-Strukturen der SDK-Responses |

Jeder Analyzer überschreibt nur die Methoden, bei denen echte Abweichungen bestehen
(Template-Method-Pattern). Gemeinsame Logik bleibt in `AbstractLlmAiAnalyzer`.

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

# 📊 Aktueller Stand (2026-04-24)

## ✅ Abgeschlossen
- Phase 1 (MVP): Komplett umgesetzt
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

## 🔄 In Arbeit
- Commit 31: Reihenfolge **3 → 1 → 2**
  - 3) Acceptance-Gate fuer Multi-CV ausbauen
  - 1) Delete-Flow (Owner/Admin) mit Session-Cleanup
  - 2) Session-Token-Handling vereinheitlichen

## 📋 Geplant
- Commit 29 Follow-up: Auth/Claim UX Polish (CTA, Microcopy, Status-Hinweise)
- Commit 30+: Ausbau der CV-Verwaltung nach MVP-Cut (z. B. Filter, Suche, Pagination-Konfigurierbarkeit)
- **Phase 5: Provider-agnostischer LLM-Layer**
  - AbstractLlmAiAnalyzer als gemeinsame Basis
  - Plugin-Konzept für provider-spezifische Eigenheiten
  - Erster Zweit-Provider als Proof of Concept
  - Offene Fragen: siehe Phase-5-Abschnitt oben
- GitHub CI/CD Workflow (Commit 23 ✅)
- arc42 Dokumentationsstruktur
- req42 Requirements Management
- Acceptance-Tests (Commit 27 ✅)
- renovate.js
- Mutation-Testing (Commit 28: Vorbereitung ✅, Detailplanung später)
- Architecture-Testing (Commit 28 ✅)

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
