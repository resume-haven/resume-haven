yes, # Detailed Planning Commit 35 - Auth/Claim UX Polish

**Branch:** `feature/commit-35-auth-claim-ux-polish`
**Status:** Completed
**Created:** 2026-05-07
**Completed:** 2026-05-11

---

##Goal

Make the existing auth/claim flow consistent from the user's perspective, thus creating an already created one
Analysis result is displayed cleanly again after login/registration and the claim status is clear
communicated - without introducing new domain features.

---

## Decisions (Planning Session 2026-05-07)

| # | Question | decision |
|---|-------|--------------|
| 1 | Redirect without result data | Claim-specific redirect with reference to re-analysis |
| 2 | Session behavior result data | Back button friendly, data is retained in the session |
| 3 | Scope limit | Commit strictly limited to UX/flow polish |
| 4 | Session key | Explicit: `analysis_result_view_data` |

---

##Scope

### Step 1 - Result route for session restore

- `GET /result` with route name `result.show`
- Single-action controller for rendering `result.blade.php` from session data
- Redirect to `analyze` if `analysis_result_view_data` is missing

### Step 2 - Session persistence in the analysis flow

- In `AnalyzeController`, persist the view data under `analysis_result_view_data`
- When carrying out a new analysis, consciously overwrite existing data
- No aggressive session cleanup (back button friendly)

### Step 3 - Align auth redirects with result flow

- Login/Register redirect with `resume_token` in session on `result.show`
- Default destination `analyze` continues without token
- Existing `intended` behavior is retained

### Step 4 - Sharpen claim feedback and microcopy

- Claim-specific information if the result status is missing
- Clearer CTA texts in `result.blade.php` as well as contextual notes in Login/Register
- Success feedback for Auto-Claim visible and consistent

### Step 5 - Tests and quality gates

- Feature tests for `result.show` (with/without session data)
- Feature testing for auth redirect behavior with session tokens
- Expand relevant listener/result tests for claim feedback

---

## Non scope in commit 35

- No new claim domain logic
- No dashboard expansion, no new product features
- No deployment reordering
- No provider/AI layer refactoring

---

## Success criteria

- The result remains available to the user in a comprehensible manner after login/registration
- Missing result data leads to claim-specific and clear `analyze`
- Session behavior is back-button friendly and regression-free
- Tests, PHPStan and Pint remain green

---

## References

- Active plan: `../../COMMIT_PLAN.md`
- Working Baseline: `../ai/WORKING_BASELINE.md`
- History index: `../COMMIT_HISTORY_INDEX.md`
- Previous detailed plan: `PLANNING_COMMIT_34.md`
- Roadmap: `../ROADMAP.md`