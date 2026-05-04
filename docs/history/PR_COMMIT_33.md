# PR: Commit 33 — Anthropic Provider PoC

**Branch:** `feature/commit-33-anthropic-provider-poc`  
**Merge-Ziel:** `main`  
**Datum:** 2026-05-04  
**Status:** ✅ Bereit für Review

---

## 🎯 Ziel

Zweiten LLM-Provider als Proof of Concept integrieren und das in Commit 32 eingeführte
`LlmProviderPluginInterface` im minimalen E2E-Analysepfad validieren.

---

## 📦 Was wurde geändert?

### Neue Dateien
| Datei | Beschreibung |
|-------|-------------|
| `src/app/Services/AiAnalyzer/AnthropicAiAnalyzer.php` | Anthropic Claude als zweiter LLM-Provider via `AbstractLlmAiAnalyzer` |
| `src/tests/Unit/AnthropicAiAnalyzerTest.php` | Unit-Tests: Provider Identity, Availability, Exception Mapping (14 Tests) |
| `src/tests/Feature/AnthropicProviderE2eTest.php` | No-Egress E2E-Tests: Provider-Registrierung, Config-Binding, Isolation (125 LOC) |
| `docs/history/PLANNING_COMMIT_33.md` | Detailplanung für Commit 33 |
| `docs/ai/SESSION_RESUME_2026-04-30.md` | Session-Resume-Datei für Agent-Mode-Kontext |

### Geänderte Dateien
| Datei | Beschreibung |
|-------|-------------|
| `src/config/ai.php` | Anthropic-Provider-Config hinzugefügt (`providers.anthropic.key`) |
| `src/tests/Unit/AiProviderBindingTest.php` | Anthropic-Binding-Tests ergänzt |
| `src/tests/Unit/AppServiceProviderTest.php` | Service-Provider-Tests für Anthropic erweitert |
| `COMMIT_PLAN.md` | Status-Update: Commit 33 in Arbeit → abgeschlossen |
| `docs/COMMIT_HISTORY_INDEX.md` | Commit 33 eingetragen |
| `docs/ai/WORKING_BASELINE.md` | Baseline auf Commit 33 aktualisiert |
| `docs/history/PLANNING_COMMIT_32.md` | Commit 32 als abgeschlossen markiert |

---

## 🏗️ Architektur-Entscheidungen

### Plugin-Interface-Validierung
`AnthropicAiAnalyzer` erweitert `AbstractLlmAiAnalyzer` und implementiert
`LlmProviderPluginInterface` — damit ist das Muster aus Commit 32 als erweiterbar bestätigt.

```
AbstractLlmAiAnalyzer (implements AiAnalyzerInterface, LlmProviderPluginInterface)
├── GeminiAiAnalyzer     ← bestehend (Commit L1)
├── OpenAiAnalyzer       ← Commit 32 (L2)
└── AnthropicAiAnalyzer  ← NEU (Commit 33 / L3, PoC)
```

### Provider-spezifisches Exception Mapping
Anthropic-Fehler werden präzise klassifiziert (Reihenfolge ist bewusst gewählt):
1. Token-Limit (`insufficient_tokens`, `context_length`) — vor Rate-Limit geprüft
2. Rate Limit (`rate_limit_error`, HTTP 429)
3. Overloaded (`overloaded_error`)
4. Authentication (`authentication_error`, `unauthorized`)
5. Invalid Request (`invalid_request_error`)
6. Fallback: originale Exception unverändert zurückgeben

### Config-Zugriff
Kein `env()` direkt — Zugriff ausschließlich via `config('ai.providers.anthropic.key')`.

---

## ✅ Quality-Gate-Nachweis

| Gate | Status | Detail |
|------|--------|--------|
| **Tests (Pest 3)** | ✅ GRÜN | 14 Unit-Tests + E2E-Tests, alle bestanden |
| **Coverage** | ✅ ≥95% | AnthropicAiAnalyzer vollständig abgedeckt |
| **PHPStan Level 9** | ✅ 0 Errors | Strict-Mode bestanden |
| **Pint** | ✅ Sauber | `vendor/bin/pint --dirty` ohne Befund |
| **No-Egress CI** | ✅ OK | Kein externer AI-Aufruf in Tests |

---

## 🔍 Review-Check gegen AGENT_CONTEXT.md

### SOLID

| Prinzip | Status | Nachweis |
|---------|--------|---------|
| **SRP** | ✅ | `AnthropicAiAnalyzer` hat genau eine Verantwortung: Anthropic-spezifische Provider-Logik |
| **OCP** | ✅ | Neuer Provider durch Extend von `AbstractLlmAiAnalyzer`, keine bestehende Klasse geändert |
| **LSP** | ✅ | Austauschbar gegen `GeminiAiAnalyzer` / `OpenAiAnalyzer` via `AiAnalyzerInterface` |
| **ISP** | ✅ | Implementiert beide fokussierten Interfaces (`AiAnalyzerInterface`, `LlmProviderPluginInterface`) |
| **DIP** | ✅ | Consumer hängt an `AiAnalyzerInterface`, nicht an `AnthropicAiAnalyzer` direkt |

### CQRS
Keine Commands oder Queries verändert — Provider-Layer liegt unterhalb des CQRS-Handlers,
keine Boundary-Verletzung.

### DDD
`AnthropicAiAnalyzer` liegt in `App\Services\AiAnalyzer` — konsistent mit dem bestehenden
AI-Layer im `Analysis`-Bounded-Context.

### Verbotene Patterns — Keine Verletzungen
- ✅ Kein `env()` direkt
- ✅ Kein Raw-SQL
- ✅ Kein Mutable DTO
- ✅ Klasse < 200 Zeilen (79 LOC)
- ✅ Alle Methoden < 20 Zeilen

---

## 🔍 Review-Check gegen COMMIT_PLAN.md

| Anforderung (Commit 33) | Status |
|-------------------------|--------|
| `AnthropicAiAnalyzer` implementiert | ✅ |
| Config-/Binding-Erweiterung | ✅ `config/ai.php` + Service-Provider |
| Provider-spezifisches Mapping | ✅ 6 Error-Typen abgedeckt |
| Minimaler no-egress E2E-Testpfad | ✅ `AnthropicProviderE2eTest.php` |
| **Nicht-Scope eingehalten** | |
| Kein Provider-Fallback | ✅ nicht implementiert |
| Kein Retry-/Backoff-Framework | ✅ nicht implementiert |
| Keine UI-Änderungen | ✅ keine View-Dateien geändert |

---

## 🚀 Nächste Schritte (Commit 34+)

- Provider-Auswahl konfigurierbar machen (UI oder `.env` zur Laufzeit)
- Retry-/Backoff-Framework evaluieren (Roadmap Phase 5)
- Deployment-Planung (nach LLM-Block abgeschlossen)

