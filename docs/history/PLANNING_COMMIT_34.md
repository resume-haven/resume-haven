# Detailed planning Commit 34 — L4 Retry-PoC + Error Hardening

**Branch:** `feature/commit-34-ai-retry-poc`
**Status:** Completed
**Created:** 2026-05-04
**Completed:** 2026-05-07

---

##Goal

Making the provider-agnostic AI layer more robust against transient errors without in a
to derive a full-fledged retry/backoff framework or provider fallback. Commit 34 should
document the minimum retry PoC as a small, testable stability commit and
implementable.

---

## Decision log (planning session 2026-05-04)

| # | Question | decision |
|---|-------|--------------|
| 1 | Retry configuration | Named config default via `AI_RETRY_BACKOFF_MS=150` |
| 2 | Back-off behavior | Constant, no exponential backoff in PoC |
| 3 | Rollback | Explicit rollback plan via `retry.enabled=false` |
| 4 | Logging | Extended context including retry metadata |
| 5 | Transient detection | Provider specific plus global fallback |

---

##Scope

### Step 1 — Define retry configuration

- New `retry` block in `src/config/ai.php`
- Named defaults for:
  - `retry.enabled`
  - `retry.max_attempts`
  - `retry.backoff_ms`
- `retry.backoff_ms` is made configurable via `AI_RETRY_BACKOFF_MS`

### Step 2 — Retry Orchestration in Abstract Analyzer

- Retry loop in `callAi()` path of `AbstractLlmAiAnalyzer`
- Constant backoff for the PoC
- Immediate termination in the event of non-transient errors
- No changes to domain contracts (`AiAnalyzerInterface`, DTOs, commands/queries)

### Step 3 — Provider-specific transient heuristics

- `GeminiAiAnalyzer`, `OpenAiAnalyzer`, `AnthropicAiAnalyzer` classify known transient errors
- Global fallback covers at least:
  - `timeout`
  - `429`
  - `overloaded`
  - `connection`
  - `network`
- Provider-specific exception mapping is retained

### Step 4 — Sharpen logging and error image

- Advanced logging fields in the error path:
  - `retry_attempt`
  - `max_attempts`
  - `transient_classifier`
  - `retry_exhausted`
- User-facing error messages remain stable and understandable

### Step 5 — Tests & Gates

- Unit tests for retry success after transient initial error
- Unit tests for immediate termination in the event of non-transient errors
- Unit tests for logging context and `retry_exhausted`
- Regression to existing provider tests

---

## Test catalog

### Unit testing

- `AbstractLlmAiAnalyzer`:
  - retry exactly up to `max_attempts` in the event of a transient error
  - stops immediately in the event of a non-transient error
  - uses constant backoff default from config
  - logs retry metadata correctly
- `GeminiAiAnalyzer`:
  - transient classification for `timeout`, `rate limit`, `connection/network`
- `OpenAiAnalyzer`:
  - transient classification for `429`, `rate limit`, `timeout`
- `AnthropicAiAnalyzer`:
  - transient classification for `rate_limit`, `429`, `overloaded`, `timeout`

### Regression

- Existing analyzer suites remain green
- No regression effect on `AiAnalyzerInterface`
- Existing error mappings remain usable and testable

---

## Non scope in commit 34

- No provider fallback (e.g. Anthropic -> OpenAI)
- Not a full-fledged retry/backoff framework
- No UI/UX changes
- No deployment reordering
- No queue/job swapping for AI retries
- No changes to domain contracts

---

## Success criteria

- Retry behavior can be activated/deactivated via Config
- Constant backoff default is named and documented
- Transient errors are detected provider-specifically plus global fallback
- Retry metadata appears traceable in logging
- Relevant tests, Pint and PHPStan remain green

---

## Risks / open points

- Retry must not produce flakiness in tests
- Provider-specific mapping must not be overridden by global fallback heuristics
- PoC must remain small and must not tip over into fallback/framework scope

---

## Rollback plan

If the retry PoC shows unexpected side effects, the behavior will be rolled back without code
initially deactivated via configuration:

- `retry.enabled=false`
- Relapse to single-attempt behavior
- Logging fields are allowed to remain even if retry is disabled

A complete code rollback is only necessary if pure config deactivation does that
Misconduct is not adequately limited.

---

## References

- Active plan: `../../COMMIT_PLAN.md`
- Working Baseline: `../ai/WORKING_BASELINE.md`
- Roadmap: `../ROADMAP.md`
- Previous detailed plan: `PLANNING_COMMIT_33.md`
- Commit-33-PR-Summary: `PR_COMMIT_33.md`
- History index: `../COMMIT_HISTORY_INDEX.md`