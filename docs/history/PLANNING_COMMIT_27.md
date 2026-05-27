# Commit 27 - Acceptance testing core flows

**Branch:** `feature/commit-27-acceptance-core-flows`
**Status:** Completed
**Created:** 2026-04-13

### Progress (2026-04-13)
- [x] Slice 0: Current status/gap analysis completed
- [x] Slice 1: Analysis flow + validation edge case secured in acceptance
- [x] Slice 2: Skills resume flow and delta session fallback secured
- [x] Slice 3: Profile flow including token edge case secured
- [x] Slice 4: CI/Command fine-tuning implemented (Composer/Makefile/CI job)
- [x] Slice 5: Quality gates validated (Acceptance + phpstan + pint)

---

##Goal

The core user flows of the MVP are securely secured via a dedicated acceptance suite.

Key questions for commit 27:

- Which end-to-end flows are release-critical and must be present as regression guard?
- Which edge cases must be covered in the acceptance suite?
- How do we keep the suite in Pest fast, deterministic and CI stable?

---

##Scope

###Contain
- Dedicated acceptance suite for core flows in the `src/tests/Acceptance/` directory
- Core flows:
  - Analysis flow (Job + CV -> Result)
  - Skills Resume Flow (Create, Display, Reuse)
  - Delta/comparison flow (baseline/fallback visible)
  - Profile flow without auth (save/load/feedback)
- Edge case coverage for these flows:
  - Validation errors (empty/invalid entries)
  - Missing session/profile data
  - Incorrect or invalid tokens/IDs
  - Defensive behavior in case of analyzer errors/timeout results
- Pest-compliant test database strategy including documentation
- Integration into Makefile/Composer/CI and project documentation

### Not included
- New product features outside of existing core flows
- Auth/AuthZ flow
- External provider integration into CI
- UI redesign without direct test reference

---

## Test database strategy (Pest/Laravel)

### Default (preferred)
- `RefreshDatabase` for isolated tests (clean condition per test)
- `sqlite` in-memory as a fast default in the test environment
- Migrations are controlled via Laravel test bootstrap

### Fallback/option
- Separate testing DB only if in-memory is not sufficient for individual scenarios
- Switching transparently via `.env.testing` and CI variables

###Guardrails
- No shared side effects between tests
- Deterministic data about factories/seeders/fixtures
- No dependency on external AI communication in CI (`AI_PROVIDER=mock`)

---

## Technical guard rails

- Adhere to DDD/CQRS/SOLID unchanged
- Respect single-action controllers and action/use case boundaries
- Do not duplicate technical logic in acceptance tests
- Focus assertions on visible behavior and contract boundaries
- Given-When-Then style test names for readability

---

## Planned implementation slices

### Slice 0 - Current status and gap analysis
- Inventory existing acceptance tests
- Create core flow matrix (flow x scenario).
- Record missing edge cases as concrete test cases

### Slice 1 - Analysis and results flow
- Secure happy path for analysis end-to-end
- Edge cases: validation, incomplete payload, analyzer error output
- Check result presentation for stable key content

### Slice 2 - Skills CV and Delta Flow
- Secure creation/display/reuse of competency data
- Test delta fallback (without persistent baseline) as an acceptance case
- Edge cases: missing baseline, inconsistent comparison data

### Slice 3 - Profile flow without auth
- Secure save/load/feedback as core path
- Edge cases: missing session data, invalid references, defensive redirects
- Review retention cues and expected UX feedback

### Slice 4 - Stabilization and CI integration
- Consolidate reusable test helpers
- Include suite in make/composer commands and CI
- Optimize runtime/stability (sequence, isolation, data structure)

### Slice 5 - Quality Gates
- Acceptance suite completely green run
- Validate `make test` or targeted acceptance targets
- `make phpstan` and `vendor/bin/pint --dirty --format agent` without regression

---

## Success Criteria (DoD)

1. All defined core flows are covered by acceptance tests.
2. Defined edge cases are secured with explicit test cases.
3. Test database strategy is documented and reproducibly configurable.
4. Acceptance Suite runs stably locally and in CI with `AI_PROVIDER=mock`.
5. Tests, PHPStan and Pint remain green.

---

## Risks & countermeasures

- **Risk:** Flaky testing through shared state or non-deterministic data.
  **Action:** `RefreshDatabase`, clear fixtures, no external calls.

- **Risk:** Tight assertions on markup details produce fragile tests.
  **Action:** Check behavior and relevant UI texts instead of CSS/HTML details.

- **Risk:** Acceptance Suite becomes too slow.
  **Action:** in-memory DB as standard, reduce redundant setup steps.

- **Risk:** Edge cases are covered inconsistently.
  **Measure:** central scenario matrix and review against DoD.

---

## Definition of Ready

- Core flow matrix is ​​aligned.
- Existing acceptance suite has been analyzed.
- Test database strategy for local/CI is defined.
- No-egress guardrails for AI paths are clearly documented.

## Definition of Done

- All planned slices have been implemented.
- Core and edge case scenarios are green.
- CI executes the acceptance suite reproducibly.
- Documentation and references in `COMMIT_PLAN.md` are updated.

---

## References

- Activity plan: `COMMIT_PLAN.md`
- Previous detailed plan: `docs/history/PLANNING_COMMIT_26.md`
- History: `docs/history/COMMIT_HISTORY_2026.md`
- Roadmap: `docs/ROADMAP.md`
- Agent context: `docs/ai/AGENT_CONTEXT.md`