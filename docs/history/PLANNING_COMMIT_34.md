# Detailplanung Commit 34 — L4 Retry-PoC + Error-Hardening

**Branch:** `feature/commit-34-ai-retry-poc`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-05-04  
**Abgeschlossen:** 2026-05-07

---

## Ziel

Den provider-agnostischen AI-Layer gegen transiente Fehler robuster machen, ohne in ein
vollwertiges Retry-/Backoff-Framework oder Provider-Fallback abzugleiten. Commit 34 soll
als kleiner, testbarer Stabilitaets-Commit den minimalen Retry-PoC dokumentieren und
implementierbar zuschneiden.

---

## Entscheidungslog (Planungs-Session 2026-05-04)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Retry-Konfiguration | Benannter Config-Default via `AI_RETRY_BACKOFF_MS=150` |
| 2 | Backoff-Verhalten | Konstant, kein exponentieller Backoff im PoC |
| 3 | Rollback | Expliziter Rollback-Plan ueber `retry.enabled=false` |
| 4 | Logging | Erweiterter Kontext inkl. Retry-Metadaten |
| 5 | Transient-Erkennung | Provider-spezifisch plus globaler Fallback |

---

## Scope

### Schritt 1 — Retry-Konfiguration definieren

- Neuer `retry`-Block in `src/config/ai.php`
- Benannte Defaults fuer:
  - `retry.enabled`
  - `retry.max_attempts`
  - `retry.backoff_ms`
- `retry.backoff_ms` wird ueber `AI_RETRY_BACKOFF_MS` konfigurierbar gemacht

### Schritt 2 — Retry-Orchestrierung im Abstract-Analyzer

- Retry-Loop im `callAi()`-Pfad von `AbstractLlmAiAnalyzer`
- Konstanter Backoff fuer den PoC
- Sofortiger Abbruch bei nicht-transienten Fehlern
- Keine Aenderung an Domain-Vertraegen (`AiAnalyzerInterface`, DTOs, Commands/Queries)

### Schritt 3 — Provider-spezifische Transient-Heuristik

- `GeminiAiAnalyzer`, `OpenAiAnalyzer`, `AnthropicAiAnalyzer` klassifizieren bekannte transiente Fehler
- Globaler Fallback deckt mindestens ab:
  - `timeout`
  - `429`
  - `overloaded`
  - `connection`
  - `network`
- Provider-spezifisches Exception-Mapping bleibt erhalten

### Schritt 4 — Logging und Fehlerbild schaerfen

- Erweiterte Logging-Felder im Fehlerpfad:
  - `retry_attempt`
  - `max_attempts`
  - `transient_classifier`
  - `retry_exhausted`
- User-facing Fehlermeldungen bleiben stabil und nachvollziehbar

### Schritt 5 — Tests & Gates

- Unit-Tests fuer Retry-Erfolg nach transientem Erstfehler
- Unit-Tests fuer sofortigen Abbruch bei nicht-transienten Fehlern
- Unit-Tests fuer Logging-Kontext und `retry_exhausted`
- Regression auf bestehende Provider-Tests

---

## Testkatalog

### Unit-Tests

- `AbstractLlmAiAnalyzer`:
  - retryt bei transientem Fehler genau bis `max_attempts`
  - stoppt sofort bei nicht-transientem Fehler
  - verwendet konstanten Backoff-Default aus Config
  - loggt Retry-Metadaten korrekt
- `GeminiAiAnalyzer`:
  - transiente Klassifikation fuer `timeout`, `rate limit`, `connection/network`
- `OpenAiAnalyzer`:
  - transiente Klassifikation fuer `429`, `rate limit`, `timeout`
- `AnthropicAiAnalyzer`:
  - transiente Klassifikation fuer `rate_limit`, `429`, `overloaded`, `timeout`

### Regression

- Bestehende Analyzer-Suites bleiben gruen
- Kein Regressionseffekt auf `AiAnalyzerInterface`
- Bestehende Fehler-Mappings bleiben nutzbar und testbar

---

## Nicht-Scope in Commit 34

- Kein Provider-Fallback (z. B. Anthropic -> OpenAI)
- Kein vollwertiges Retry-/Backoff-Framework
- Keine UI-/UX-Aenderungen
- Keine Deployment-Neueinordnung
- Keine Queue-/Job-Auslagerung fuer AI-Retries
- Keine Aenderung an Domain-Vertraegen

---

## Erfolgskriterien

- Retry-Verhalten ist ueber Config aktivierbar/deaktivierbar
- Konstanter Backoff-Default ist benannt und dokumentiert
- Transiente Fehler werden provider-spezifisch plus globalem Fallback erkannt
- Retry-Metadaten erscheinen im Logging nachvollziehbar
- Relevante Tests, Pint und PHPStan bleiben gruen

---

## Risiken / offene Punkte

- Retry darf keine Flakiness in Tests erzeugen
- Provider-spezifisches Mapping darf nicht durch globale Fallback-Heuristik uebersteuert werden
- PoC muss klein bleiben und darf nicht in Fallback-/Framework-Scope kippen

---

## Rollback-Plan

Falls der Retry-PoC unerwartete Nebeneffekte zeigt, wird das Verhalten ohne Code-Rollback
zunaechst ueber Konfiguration deaktiviert:

- `retry.enabled=false`
- Rueckfall auf Single-Attempt-Verhalten
- Logging-Felder duerfen bestehen bleiben, auch wenn Retry deaktiviert ist

Ein vollstaendiger Code-Rollback ist nur noetig, wenn die reine Config-Deaktivierung das
Fehlverhalten nicht ausreichend eingrenzt.

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Working Baseline: `../ai/WORKING_BASELINE.md`
- Roadmap: `../ROADMAP.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_33.md`
- Commit-33-PR-Zusammenfassung: `PR_COMMIT_33.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`


