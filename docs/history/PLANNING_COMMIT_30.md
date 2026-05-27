# Detailed planning Commit 30 — CV management (Multi-CV CRUD)

**Branch:** `feature/commit-30-multi-cv-crud`
**Status:** Completed
**Created:** 2026-04-23
**Completed:** 2026-04-24

---

##Goal

The previous profile function from a single, token-based CV flow to one
Expand robust CV management per user. Users should have multiple saved CVs
View, edit, delete and reuse for analysis.

---

## Decision log (from planning session 2026-04-23)

| # | Question | decision |
|---|-------|--------------|
| 1 | Remaining work from commit 29 | Not as a `29a`, but as a roadmap follow-up |
| 2 | Starting scope commit 30 | Directly complete CRUD scope |
| 3 | Migration `resume_token` → `resume_tokens[]` | Pragmatic Cutover without Backward Compat Guarantee |
| 4 | Pagination | Yes, initially fixed page size `10` |
| 5 | Test depth | Complete test catalog including edge cases |

---

##Scope

### Step 1 — Dashboard / CV overview

- New user view for saved CVs
- Pagination with fixed page size `10`
- Sorting: most recently updated CVs first
- Only your own CVs visible; Admin is allowed access within the scope of the policy

### Step 2 — Save Multi CV

- Saving creates additional CV entries instead of updating the implicit single flow
- For logged in users, `user_id` is set directly
- Session brings tokens into `resume_tokens[]`
- Clearly define duplicate handling for session tokens

### Step 3 — Edit CV

- Owner can update existing CVs
- Admin can have moderating access
- Token/owner linkage remains stable
- Analysis reuse with updated CV remains possible

### Step 4 — Delete CV

- Owner can delete his own CVs
- Admin can delete
- Delete cleanly removes tokens from `resume_tokens[]`
- CVs that no longer exist cannot be reloaded

### Step 5 — Ownership / Policy / Routing

- `ProfilePolicy` is consistently applied to dashboard/CRUD flows
- No repository bypasses at controller boundaries
- Routes and use cases clearly separate read flows and write paths

### Step 6 — Secure regressions

- Existing flows for claim, load, retention and analysis reuse remain green
- `resume_token` is no longer required as a primary contract
- Empty or missing `resume_tokens[]` session is handled robustly

---

## Test catalog

### Feature testing

- Users only see their own CVs in a paginated list
- Pagination provides a maximum of 10 entries per page
- Owner can create, edit, delete CV
- External users are not allowed to edit or delete
- Admin is allowed to access shared administrative operations
- `resume_tokens[]` is expanded when saved and cleaned up when deleted
- Existing claim/load flows remain functional

### Unit testing

- Repository sorting/pagination contract
- Session token helper for `resume_tokens[]`
- Policy decisions (owner / stranger / admin)
- Action/DTO borders for update/delete/cutover

### Edge cases

- Empty CV list
- Invalid or no longer existing token
- Duplicate tokens in the session
- Deleting an already deleted data record
- Update with invalid content/validation error

---

## Non scope in commit 30

- ❌ No team/client management
- ❌ No user-based key rotation for saved CVs
- ❌ No admin UI other than necessary preparations
- ❌ No configurable pagination in the MVP
- ❌ No search / filter / tags in the CV list (later expansion)

---

## Success criteria

- Users see their CVs paginated and sorted by topicality
- CRUD works for Owner/Admin according to policy
- `resume_tokens[]` robustly replaces the old single token flow
- Claim, load and retention flows remain regression-free
- PHPStan Level 9: 0 Errors
- Pint: clean
- Coverage target remains met

---

## Risks / open points

- Pragmatic cutover on `resume_tokens[]` requires clean session migration within the running flow
- Delete/Update actions must not make existing analysis or claim flows inconsistent
- The dashboard UI should remain clear, although no later convenience functions (search/filter) are available

---

## References

- Active plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Previous detailed plan: `PLANNING_COMMIT_29.md`
- History index: `../COMMIT_HISTORY_INDEX.md`

---

## Closing note

- Commit 30 was successfully completed and merged.
- The next implementation order for commit 31 has been set: **3, 1, 2**.