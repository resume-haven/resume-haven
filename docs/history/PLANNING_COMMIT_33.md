# Detailed planning Commit 33 — L3 Anthropic Provider PoC

**Branch:** `feature/commit-33-anthropic-provider-poc`
**Status:** Completed
**Created:** 2026-04-30
**Completed:** 2026-05-04

---

##Goal

Integrate a second LLM provider as a proof of concept to do this in commit 32
to validate formalized plugin interface under real provider differences.
Additionally, a minimal E2E analysis path (no-egress) is used for the provider selection path
secured.

---

## Decision log (planning session 2026-04-30)

| # | Question | decision |
|---|-------|--------------|
| 1 | Provider for L3 | Anthropic |
| 2 | E2E Test Depth | Minimum parse path |
| 3 | Status in plan | Commit 33 directly in progress |

---

##Scope

### Step 1 — Add Anthropic Analyzer

- New `AnthropicAiAnalyzer` under `Services/AiAnalyzer`
- Implements existing L2 hooks via `AbstractLlmAiAnalyzer`
- Provider-specific exception mapping for typical Anthropic error texts

### Step 2 — Expand Config & Binding

- `ai.analyzers.anthropic` to `config/ai.php`
- `ai.providers.anthropic.key` is used as an availability signal
- `AppServiceProvider` remains config-driven (no additional branch switch)

### Step 3 — Add unit tests

- `AnthropicAiAnalyzerTest` (provider name, availability, mapping)
- Binding tests for `AI_PROVIDER=anthropic`
- Guardrails of existing providers (`mock`, `gemini`, `openai`) remain green

### Step 4 — Minimum E2E analysis path

- Feature test that runs the analysis path with `AI_PROVIDER=anthropic`
- no-egress approach (controlled stub/test double, no external API call)
- Goal: End-to-end selection path + result flow remains stable

---

## Test catalog

###Unit

- `AnthropicAiAnalyzer`:
  - `getProviderName()`
  - `isAvailable()` with/empty key
  - `mapProviderException()` branch coverage
- `AppServiceProvider`:
  - Binding `anthropic`
  - existing guard exceptions unchanged

### Feature (minimum E2E)

- Analysis flow with `AI_PROVIDER=anthropic` provides valid answer structure
- The error path is mapped in a user-friendly manner, without external egress

### Regression

- Existing analyzer suites remain green
- No regression on the `AiAnalyzerInterface` contract

---

## Non scope in commit 33

- No provider fallback (e.g. Anthropic -> OpenAI)
- No retry/backoff framework
- No third additional provider
- No UI/UX changes
- No change to domain contracts

---

## Success criteria

- `AI_PROVIDER=anthropic` returns a valid `AiAnalyzerInterface` instance
- Anthropic-specific exception mapping is testable
- Minimum E2E analysis path is stable and no-egress
- Relevant tests, Pint and PHPStan remain green

---

## Risks / open points

- E2E path must not trigger external API calls
- Mapping logic must not worsen existing generic error messages
- Avoid scope creep towards fallback/retry

---

## References

- Active plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Previous detailed plan: `PLANNING_COMMIT_32.md`
- History index: `../COMMIT_HISTORY_INDEX.md`