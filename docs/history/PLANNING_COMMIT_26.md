# Commit 26 - Profile expansion without auth

**Branch:** `feature/commit-26-profile-expansion-no-auth`
**Status:** Completed
**Created:** 2026-03-18

### Progress (2026-04-16)
- [x] Slice 0: UX flow for save/load/feedback stabilized
- [x] Slice 1: MVP retention technically implemented (expiry when loading + cleanup path)
- [x] Slice 2: UI notes on data storage/lifespan visibly added
- [x] Slice 3: CI guardrails explicitly hardened (`AI_PROVIDER=mock`, empty `GEMINI_API_KEY`)
- [x] Slice 4: Feature testing + PHPStan + Pint validated

---

##Goal

Further expand profile functions locally without user auth, with a focus on UX and robust usability.

Key questions for commit 26:

- How does the profile flow become clearer and more error-tolerant for users?
- How do we pragmatically implement retention in the MVP without prejudging productive platform details?
- How do we reliably prevent external data leakage in CI?

---

##Scope

###Contain
- UX-first expansion in the `Profile` flow (save/load/feedback)
- Consistent error messages and clear indications of success
- MVP pragmatic retention mechanics
- Additional UI notes on data storage and lifespan
- CI guardrails as required:
  - `AI_PROVIDER=mock`
  - No external AI secrets in CI
  - No-egress for AI paths with “allow internal services only” approach
- Unit/feature testing for new/customized flows

### Not included
- User/Auth/AuthZ
- Productive, platform-specific retention end architecture
- External LLM provider integration into CI

---

## Technical guard rails

- Adhere to DDD/CQRS/SOLID unchanged
- Maintain single action controller
- Business logic in Actions/UseCases/Services
- DTO-first and complete typing (PHPStan Level 9)
- Local development without external data leakage

---

## Planned implementation slices

### Slice 0 - Sharpen UX flow (profiles)
- Make saving/loading profiles clearer
- Unify user feedback for success/failure
- Edge cases (invalid tokens, missing session data, defective payloads) communicate cleanly on the UX side

### Slice 1 - MVP retention technical
- Implement pragmatic retention rules (without platform coupling)
- Defensively secure existing data paths
- Provide technical deletion paths for local use

### Slice 2 - UI Notes Retention
- Visible information in the affected profile views
- Explanation of local data storage and lifespan
- Clearly indicate the MVP status

### Slice 3 - CI guardrails (required)
- CI strictly on `AI_PROVIDER=mock`
- Treat external AI secrets as invalid in CI
- No-egress for AI paths with general approach "allow internal services only"

### Slice 4 - Tests + Quality Gates
- Targeted unit/feature tests for profile flow and retention
- `php artisan test --compact` (affected areas, then entire run)
- `make phpstan`
- `vendor/bin/pint --dirty --format agent`

---

## Success Criteria (DoD)

1. Profile flow without auth is robust, consistent and traceable.
2. Retention is technically implemented effectively in the MVP.
3. Additional UI notes on data retention/lifespan are visible.
4. CI guardrails are required and prevent external AI egress.
5. Tests, PHPStan and Pint remain green.

---

## Risks & countermeasures

- **Risk:** MVP retention is mixed with final product logic.
  **Measure:** Clear separation, mark open points as technical debt.

- **Risk:** CI guardrails unintentionally block legitimate internal paths.
  **Measure:** Clearly document "allow internal services only" and make it testable.

- **Risk:** UX cues are inconsistent or too hidden.
  **Measure:** Central placement in affected profile flows and consistent choice of words.

---

## Technical Debt (platform dependent, separate items)

### TD-26-01 - Finalize storage strategy
**Description:** Set final storage strategy for target platform (local/dev vs. productive).
**Definition of Ready:**
- The target platform and operating model have been decided.
- Requirements for data storage and access patterns are documented.

**Definition of Done:**
- Final storage design is implemented and documented.
- Migration/adaptation of existing data paths is completed.
- Relevant tests are available and green.

### TD-26-02 - Finalize retention lifecycle
**Description:** Implement final retention/deletion lifecycle on target platform.
**Definition of Ready:**
- Platform-related retention requirements have been clarified.
- Triggers for deletion (time or event based) are defined.

**Definition of Done:**
- Final lifecycle is implemented (including cleanup mechanics).
- Monitoring/transparency for retention processes is available.
- Acceptance tests for lifecycle scenarios are green.

### TD-26-03 - Productive compliance hardening
**Description:** Complete compliance/security hardening for production operations.
**Definition of Ready:**
- The target platform and compliance framework are binding.
- Specific requirements (e.g. logging, auditing, secrets handling) are documented.

**Definition of Done:**
- Productive compliance measures are implemented.
- Documentation for operations and audits is updated.
- Security/regression tests are green.

---

##Note on the roadmap

Update in `docs/ROADMAP.md` (commit 26 from planned -> in progress) has been done.