# Detailplanung Commit 32 — L2 Plugin-Interface + OpenAI Auswahl

**Branch:** `feature/commit-32-llm-provider-plugin-interface`  
**Status:** In Arbeit  
**Erstellt:** 2026-04-28

---

## Ziel

Den provider-agnostischen LLM-Layer aus L1 so erweitern, dass provider-spezifische
Abweichungen formal ueber ein Plugin-Interface steuerbar sind und `AI_PROVIDER=openai`
als konkrete Auswahl ohne Domain-Aenderungen funktioniert.

---

## Entscheidungslog (Planungs-Session 2026-04-28)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | OpenAI in L2 nur vorbereiten oder aktiv auswählbar machen | Aktiv auswählbar in Commit 32 |
| 2 | Exception-Mapping zentral vs. provider-spezifisch | Provider-spezifisch ueber Plugin-Hook |
| 3 | Umsetzungszuschnitt | Ein Commit, nicht gesplittet |

---

## Scope

### Schritt 1 — Plugin-Vertrag formalisieren

- Neues `LlmProviderPluginInterface` unter `Services/AiAnalyzer/Contracts`
- Hooks fuer provider-spezifische Abweichungen:
  - `buildPromptPayload()`
  - `normalizeResponse()`
  - `mapProviderException()`

### Schritt 2 — Abstract-Flow auf Plugin-Hooks umstellen

- `AbstractLlmAiAnalyzer` verwendet den Plugin-Vertrag im `analyze()`-/`callAi()`-Pfad
- Gemeinsame Orchestrierung bleibt zentral (Sanitizing, Validate, Parse, Logging)
- Abweichungen werden ueber Hook-Punkte ausgelagert

### Schritt 3 — Gemini auf neuen Vertrag ziehen

- `GeminiAiAnalyzer` implementiert provider-spezifische Hook-Methoden
- Bestehendes Laufzeitverhalten bleibt funktional aehnlich
- Provider-spezifisches Error-Mapping wird explizit

### Schritt 4 — OpenAI als auswählbarer Provider

- Neuer `OpenAiAnalyzer` als L2-Implementierung
- `isAvailable()` ueber `ai.providers.openai.key`
- Aufnahme in `ai.analyzers` und Auswahl per `AI_PROVIDER=openai`

### Schritt 5 — Binding/Config absichern

- `AppServiceProvider` bleibt config-driven
- Registry-/Typ-Guards bleiben aktiv
- Fehlermeldungen fuer ungueltige Konfigurationen bleiben deterministic

### Schritt 6 — Tests & Gates

- Unit-Tests fuer Plugin-Hooks und provider-spezifisches Exception-Mapping
- Binding-Tests fuer `openai`-Auswahlpfad
- Relevante Pest-Tests, Pint, PHPStan

---

## Testkatalog

### Unit-Tests

- `AbstractLlmAiAnalyzer`:
  - Hook-Pipeline fuer Prompt/Response
  - provider-spezifischer Exception-Mapping-Pfad
- `GeminiAiAnalyzer`:
  - Hook-Implementierung und Availability-Check
- `OpenAiAnalyzer`:
  - Provider-Name, Availability, Basis-Hook-Verhalten
- `AppServiceProvider`:
  - Binding fuer `ai.provider=openai`
  - Registry-Guardrails bleiben gruen

### Regression

- Bestehende Analyzer-Tests fuer `mock`/`gemini` bleiben gruen
- Keine Regression fuer bestehende Analyse-Use-Cases

---

## Nicht-Scope in Commit 32

- Kein Provider-Fallback (z. B. Gemini -> OpenAI bei Timeout)
- Kein dritter produktiver Provider
- Keine UI-Aenderungen
- Kein Retry-/Backoff-Framework
- Keine Aenderung der Domain-Vertraege (Commands/Queries/DTOs)

---

## Erfolgskriterien

- `AI_PROVIDER=openai` liefert eine gueltige `AiAnalyzerInterface`-Instanz
- Provider-spezifische Abweichungen laufen ueber das Plugin-Interface
- Exception-Mapping ist provider-spezifisch testbar
- PHPStan Level 9: 0 Errors
- Pint: sauber
- Relevante Tests: gruen

---

## Risiken / offene Punkte

- Hook-Signaturen muessen klein bleiben, um Over-Engineering zu vermeiden
- OpenAI-Integration in L2 soll bewusst minimal bleiben (kein Scope-Creep)
- Fehlertexte duerfen UX-seitig nicht inkonsistent zwischen Providern wirken

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_30.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`

