# Commit 25 - Analysis quality & explainability

**Branch:** `feature/commit-25-analysis-delta-explainability`
**Status:** In implementation
**Created:** 2026-03-16

---

##Goal

Results should be understandable for users:

- What has changed compared to the baseline?
- Why did the score increase/decrease?
- Which recommendations have changed in priority?

---

##Scope

###Contain
- Persistent baseline in the `Profile` context (new table)
- Fallback to session data if no persistent baseline is available
- Delta engine for comparison:
  - Score difference
  - Match/gap difference
  - Recommendation difference including priority change
- UI Impact representation in `result`:
  - Improvement -> Gruenton + `↑`
  - Consistent -> blue tone + `→`
  - Deterioration -> red tone + `↓`
- Mock data extension for comparison scenarios
- Unit and feature tests for Delta/Impact/Fallback

### Not included
- Prompt fine tuning (follows in commit 25a)
- User/Auth/AuthZ
- Cloud/deployment topics

---

## Technical guard rails

- Maintain DDD/CQRS in phases
- Single action controller, no new business logic in the controller
- Encapsulate comparison logic in Actions/UseCases
- Data transfer via immutable DTOs
- Interface-based dependencies (where interchangeable components exist)

---

## Planned implementation slices

### Slice 0 - UI stabilization
- Visually stable CTA “Create skills resume”.
- Check and fix asset pipeline/Tailwind build

### Slice 1 - Baseline + Delta Engine
- New persistence for baseline in the `Profile` context
- Implement comparison DTOs and comparison action

### Slice 2 - Explainable result UI
- Delta panel in `result` with key figures and impact information
- Clearly visualize priority changes in recommendations

### Slice 3 - mock data + tests + gates
- Expand mock scenarios (improvement, tie, deterioration)
- Add unit/feature tests
- `make test`, `make phpstan`, `make pint-analyse` green

---

## Success Criteria (DoD)

1. Comparison between baseline and current analysis is visible and understandable.
2. Recommendation changes including priority changes are displayed with impact (color + arrow).
3. Missing persistent baseline does not lead to errors (fallback active).
4. Mock data reproducibly covers the central delta cases.
5. Quality gates remain green.

---

## Risks & countermeasures

- **Risk:** Baseline is missing or inconsistent.
  **Measure:** Defined session fallback and defensive type guards.

- **Risk:** UI will be overloaded by new delta data.
  **Measure:** Compact delta panel with clear priority.

- **Risk:** Mock scenarios do not adequately reflect real cases.
  **Action:** Test at least three scenarios (improvement, constant, deterioration).