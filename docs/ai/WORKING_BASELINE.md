# Working baseline

This file is the operational starting point for AI-powered sessions.
It serves as a "soft reset" and has priority for the daily context.

##Scope

- Applies to the current implementation phase (MVP, current branch).
- Repository status is the source of truth.
- In case of conflicts: system/tooling rules > this file > older chat contexts.

## Current work rules

1.architecture
   - DDD with Bounded Context `Analysis`.
   - CQRS in phases and strictly per use case.
   - SOLID as a mandatory gate for implementations and reviews.
   - Program to an interface (interface-based design, abstraction instead of concretization).

2. Controllers and use cases
   - Prefer single-action controllers (`__invoke()`), unless explicitly justified.
   - Business logic in Actions/UseCases/Services, not in the controller.
   - Small, testable methods with clear responsibilities.

3. Data modeling
   - DTO-first for input/output between layers.
   - DTOs immutable if possible (`readonly`).
   - Typing complete (PHPStan Level 9 compatible).

4. Quality gates
   - Tests required (Feature + Unit, Pest).
   - `phpstan` without errors.
   - `pint` on changed files.
   - Coverage Minimum value according to the project configuration (currently 95%).

5. AI and error robustness
   - Providers interchangeable (currently Mock, Gemini, OpenAI, Anthropic via interface binding).
   - Robustly handle API timeouts, empty/invalid responses, and parsing errors.
   - Keep cache behavior reproducible and testable.

## Session reset protocol

If the chat context has grown significantly, use this file as a reset basis:

- Only consider the current repo status + this baseline as binding.
- Ignore older chat details unless explicitly referenced.
- If you are unclear, pause briefly and ask questions.

**Current session summary:**
See `docs/ai/SESSION_RESUME_YYYY-MM-DD.md` for the latest status (if available).

**Current working mode:** Agent mode (branch-agnostic)
**Focus:** Commit 36 ​​(Roadmap Planning & Documentation Sync) with status alignment between `COMMIT_PLAN.md`, `docs/ROADMAP.md`, `docs/COMMIT_HISTORY_INDEX.md` and `docs/history/COMMIT_HISTORY_2026.md` — details in `docs/history/PLANNING_COMMIT_36.md`.

**Recommended reset order:**
1. This file (`WORKING_BASELINE.md`)
2. Current session resume (e.g. `SESSION_RESUME_2026-03-18.md`)
3. `COMMIT_PLAN.md` (Status overview)
4. `AGENT_CONTEXT.md` (Work Rules Details)

##Care

- Update this file when making architectural or process decisions.
- Document changes briefly (e.g. in the commit plan / changelog).

### Versioning

**Scheme:** `Major.Minor`

- **Major** (e.g. 1.0 → 2.0): Fundamental architectural or process changes
- **Minor** (e.g. 1.0 → 1.1): Additions, clarifications, editorial updates

**When to increase minor:**
- Added new work rules or quality gates
- Existing rules clarified or expanded
- Structural adjustments (e.g. new use cases, new bounded contexts)
- Editorial revisions with relevance to the content

**When to raise major:**
- Fundamental change in architectural principles (e.g. CQRS → Event Sourcing)
- New central patterns (e.g. introduction of Hexagonal Architecture)
- Breaking changes in the way of development

---

**Last updated**: 2026-05-11
**Version**: 1.7 (Commit 36 ​​​​focus with roadmap planning and documentation sync)