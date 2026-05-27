# Detailed planning Commit 32 — L2 plugin interface + OpenAI selection

**Branch:** `feature/commit-32-llm-provider-plugin-interface`
**Status:** Completed
**Created:** 2026-04-28
**Completed:** 2026-04-30

---

##Goal

Extend the provider-agnostic LLM layer from L1 so that provider-specific
Deviations can be formally controlled via a plugin interface and `AI_PROVIDER=openai`
works as a concrete selection without domain changes.

---

## Decision log (planning session 2026-04-28)

| # | Question | decision |
|---|-------|--------------|
| 1 | Just prepare OpenAI in L2 or make it actively selectable | Actively selectable in commit 32 |
| 2 | Exception mapping centrally vs. provider-specific | Provider-specific via plugin hook |
| 3 | Implementation plan | One commit, not split |

---

##Scope

### Step 1 — Formalize plugin contract

- New `LlmProviderPluginInterface` under `Services/AiAnalyzer/Contracts`
- Hooks for provider-specific deviations:
  - `buildPromptPayload()`
  - `normalizeResponse()`
  - `mapProviderException()`

### Step 2 — Switch Abstract Flow to Plugin Hooks

- `AbstractLlmAiAnalyzer` uses the plugin contract in the `analyze()`/`callAi()` path
- Shared orchestration remains central (sanitizing, validating, parsing, logging)
- Deviations are outsourced via hook points

### Step 3 — Move Gemini to new contract

- `GeminiAiAnalyzer` implements provider-specific hook methods
- Existing runtime behavior remains functionally similar
- Provider-specific error mapping becomes explicit

### Step 4 — OpenAI as a selectable provider

- New `OpenAiAnalyzer` as L2 implementation
- `isAvailable()` over `ai.providers.openai.key`
- Admission to `ai.analyzers` and selection via `AI_PROVIDER=openai`

### Step 5 — Secure Binding/Config

- `AppServiceProvider` remains config-driven
- Registry/type guards remain active
- Error messages for invalid configurations remain deterministic

### Step 6 — Tests & Gates

- Unit tests for plugin hooks and provider-specific exception mapping
- Binding tests for `openai` selection path
- Relevant plague tests, pint, PHPStan

---

## Test catalog

### Unit testing

- `AbstractLlmAiAnalyzer`:
  - Hook pipeline for prompt/response
  - provider-specific exception mapping path
- `GeminiAiAnalyzer`:
  - Hook implementation and availability check
- `OpenAiAnalyzer`:
  - Provider name, availability, basic hook behavior
- `AppServiceProvider`:
  - Binding for `ai.provider=openai`
  - Registry guardrails remain green

### Regression

- Existing analyzer tests for `mock`/`gemini` remain green
- No regression for existing analysis use cases

---

## Non scope in commit 32

- No provider fallback (e.g. Gemini -> OpenAI on timeout)
- No third productive provider
- No UI changes
- No retry/backoff framework
- No change to the domain contracts (Commands/Queries/DTOs)

---

## Success criteria

- `AI_PROVIDER=openai` returns a valid `AiAnalyzerInterface` instance
- Provider-specific deviations occur via the plugin interface
- Exception mapping can be tested on a provider-specific basis
- PHPStan Level 9: 0 Errors
- Pint: clean
- Relevant tests: green

---

## Risks / open points

- Hook signatures must remain small to avoid over-engineering
- OpenAI integration in L2 should deliberately remain minimal (no scope creep)
- From a UX perspective, error texts must not appear inconsistent between providers

---

## References

- Active plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Previous detailed plan: `PLANNING_COMMIT_30.md`
- History index: `../COMMIT_HISTORY_INDEX.md`