# Commit 24 - Competency CVs I (MVP-light)

**Branch (planned):** `feature/commit-24-competence-resume`
**Status:** Completed (PR/Merge finalization)
**Created:** 2026-03-13

---

##Goal

A competency resume should be created and displayed as a new product artifact.
The focus is on immediate user value: structured skills instead of just running text.

---

##Scope

###Contain
- Derive competency profile from existing CV data
- Show competency resume in UI
- Create a basis for re-analysis with improved CV
- Render competency resume as a deterministic analysis artifact
- Use the competency CV specifically as a source of analysis
- Expand test coverage for new core flows
- Explicitly consider data protection/retention aspects in the planning

### Not included
- No user login/auth in commit 24
- No migration of existing test data to users (not necessary)
- No deployment/cloud setup
- No local LLM deployment

---

## Technical guidelines

- `Kompetenzlebenslauf` is not just input, but explicitly an artifact that can be created.
- The flow should be product-related: create -> display -> reuse for analysis.
- The existing analysis flow remains compatible.

---

## Success criteria (DoD-related)

1. Competency CV can be created and displayed.
2. Re-analysis with corrected/optimized CV is possible.
3. Measurable quality criteria prepared:
   - If the job text is identical, `Score_neu > Score_alt` typically applies
   - and/or `Gaps_neu < Gaps_alt`
4. New/adapted tests are green.
5. PHPStan/Pint/Tests remain green.

---

## Implementation status (Commit 24)

- Build Flow creates competency resume and saves preview + analysis artifact in session.
- Analyze UI shows preview, artifact and explicit use button for the analysis source.
- Reuse-Flow is implemented as its own single-action controller.
- Error path for missing artifact is covered (session error).
- Relevant feature/unit tests, PHPStan and Pint are green.

---

## Data protection/retention (planning requirement)

In commit 24 the following must be documented/taken into account:
- which CV data is stored and for how long
- how test/development data is handled
- which deletion paths are intended for stored artifacts

Note: final user-based security/retention follows in the later user block.

---

## Test strategy (mandatory)

- Feature testing for the new skills resume flow
- Unit tests for derivation logic/transformations
- Preparation for later acceptance tests (Commit 27)

Motivation: as complexity increases, test expansion is essential to avoid regressions.

---

## Placement in the new order

- Commit 24: Competency CVs I (A)
- Commit 25: Analysis quality & explainability (B)
- Commit 26: Profile expansion without Auth (D)
- Commit 27: Acceptance testing core flows (C)
- Commit 28: Architecture testing & engineering hardening (E)
- Commit 29+: User/Auth/AuthZ + rudimentary user management

Deployment then remains to be repositioned.