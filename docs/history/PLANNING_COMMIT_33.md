# Detailplanung Commit 33 — L3 Anthropic Provider PoC

**Branch:** `feature/commit-33-anthropic-provider-poc`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-04-30  
**Abgeschlossen:** 2026-05-04

---

## Ziel

Einen zweiten LLM-Provider als Proof of Concept integrieren, um das in Commit 32
formalisierte Plugin-Interface unter realen Provider-Unterschieden zu validieren.
Zusätzlich wird ein minimaler E2E-Analysepfad (no-egress) für den Provider-Auswahlpfad
abgesichert.

---

## Entscheidungslog (Planungs-Session 2026-04-30)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Provider für L3 | Anthropic |
| 2 | E2E-Testtiefe | Minimaler Analysepfad |
| 3 | Status im Plan | Commit 33 direkt in Arbeit |

---

## Scope

### Schritt 1 — Anthropic Analyzer hinzufügen

- Neuer `AnthropicAiAnalyzer` unter `Services/AiAnalyzer`
- Implementiert bestehende L2-Hooks über `AbstractLlmAiAnalyzer`
- Provider-spezifisches Exception-Mapping für typische Anthropic-Fehlertexte

### Schritt 2 — Config & Binding erweitern

- `ai.analyzers.anthropic` in `config/ai.php`
- `ai.providers.anthropic.key` wird als Availability-Signal verwendet
- `AppServiceProvider` bleibt config-driven (kein zusätzlicher Branch-Switch)

### Schritt 3 — Unit-Tests ergänzen

- `AnthropicAiAnalyzerTest` (Providername, Availability, Mapping)
- Binding-Tests für `AI_PROVIDER=anthropic`
- Guardrails bestehender Provider (`mock`, `gemini`, `openai`) bleiben grün

### Schritt 4 — Minimaler E2E-Analysepfad

- Feature-Test, der den Analysepfad mit `AI_PROVIDER=anthropic` ausführt
- no-egress Ansatz (kontrollierter Stub/Test-Double, kein externer API-Call)
- Ziel: End-to-End-Auswahlpfad + Ergebnis-Flow bleibt stabil

---

## Testkatalog

### Unit

- `AnthropicAiAnalyzer`:
  - `getProviderName()`
  - `isAvailable()` mit/leerem Key
  - `mapProviderException()` branch coverage
- `AppServiceProvider`:
  - Binding `anthropic`
  - bestehende Guard-Exceptions unverändert

### Feature (minimal E2E)

- Analyse-Flow mit `AI_PROVIDER=anthropic` liefert valide Antwortstruktur
- Fehlerpfad wird user-friendly gemappt, ohne externen Egress

### Regression

- Bestehende Analyzer-Suites bleiben grün
- Keine Regression am `AiAnalyzerInterface`-Vertrag

---

## Nicht-Scope in Commit 33

- Kein Provider-Fallback (z. B. Anthropic -> OpenAI)
- Kein Retry-/Backoff-Framework
- Kein dritter zusätzlicher Provider
- Keine UI-/UX-Änderungen
- Keine Änderung an Domain-Verträgen

---

## Erfolgskriterien

- `AI_PROVIDER=anthropic` liefert eine gültige `AiAnalyzerInterface`-Instanz
- Anthropic-spezifisches Exception-Mapping ist testbar
- Minimaler E2E-Analysepfad ist stabil und no-egress
- Relevante Tests, Pint und PHPStan bleiben grün

---

## Risiken / offene Punkte

- E2E-Pfad darf keine externen API-Calls triggern
- Mapping-Logik darf bestehende generische Fehlermeldungen nicht verschlechtern
- Scope-Creep in Richtung Fallback/Retry vermeiden

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_32.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`


