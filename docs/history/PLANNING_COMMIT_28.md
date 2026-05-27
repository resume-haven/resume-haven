# Commit 28 – Architecture testing & engineering hardening

**Branch:** `feature/commit-28-architecture-tests`
**Status:** Completed
**Created:** 2026-04-17
**Completed:** 2026-04-20

---

##Goal

Automated architecture tests (Pest Arch) ensure existing layer rules, DDD/CQRS boundaries and SOLID principles. In addition, engineering hardening measures (mutation testing preparation, Git hooks, extended quality gates) are implemented.

### Graduation (2026-04-20)

- ✅ Architecture suite implemented (`DddArchTest`, `CqrsArchTest`, `SolidArchTest`)
- ✅ Composer/Makefile expanded to include `test:pest-arch`, `quality:arch-gate`, `test-mutation`, `hooks-install`
- ✅ Mutation testing prepared (optional, separate run)
- ✅ Git hooks prepared (`.githooks/pre-commit`, manual activation)
- ✅ Documentation in `COMMIT_PLAN.md` and `docs/ROADMAP.md` brought to completion

### Key questions for commit 28

- Which layer and boundary rules need to be secured automatically?
- How is mutation testing prepared as an optional, isolated tool?
- How are Git hooks integrated sensibly and optionally?

---

##Scope

###Contain

- **Architecture Test Suite** in `src/tests/Architecture/` with Pest 3 `arch()` API:
  - `DddArchTest.php`: Bounded Context Boundaries (Profile ↔ Analysis)
  - `CqrsArchTest.php`: Command/Query segregation
  - `SolidArchTest.php`: Single-action controller, interface-based design, read-only DTOs
- **Full Scope:** All `app/` namespaces (Domains, Http, Services, Ai, Dto)
- **Mutation testing preparation** with `pestphp/pest-plugin-mutate`:
  - Added composer dev dependency
  - `make test-mutation` as its own target (Scope: `app/Domains`)
  - Not included in the standard CI (separate workflow possible)
  - Open detailed questions documented in `docs/ROADMAP.md`
- **Git hooks** in `.githooks/pre-commit`:
  - `pint --dirty`, `phpstan analyse --no-progress`, commit message convention check
  - Can be activated manually via `make hooks-install` (no auto-install)
- **Makefile & Composer scripts extended:**
  - `make test-arch` – Architecture tests only
  - `make test-arch-gate` – Arch + PHPStan + Pint
  - `make test-mutation` – Mutation tests (domains, dry-run)
  - `make hooks-install` – Enable Git hooks manually
  - Composer script `quality:arch-gate`
- **CI integration:**
  - Added Architecture Suite in `phpunit.xml`
  - CI job `pest_architecture` in `.github/workflows/ci.yml` (optional: separate mutation workflow)
- **Documentation:**
  - Detailed plan outsourced to `docs/history/PLANNING_COMMIT_28.md`
  - `docs/ARCHITECTURE.md` updated (architecture testing status)
  - `docs/ROADMAP.md` expanded (mutation testing open questions)
  - `COMMIT_PLAN.md` updated (status and next commits)

### Not included

- New product features
- Mutation testing in standard CI (optional/workflow dispatch only)
- Automatic Git Hook installation via `composer install`
- Other engineering tools (Deptrac, PhpMetrics, etc.)

---

## Technical guard rails

- **Pest Arch vs. Deptrac:** Pest 3 is sufficient for layer rules; Deptrac would add too much complexity
- **Architecture Tests:** Write tests in given-style (concise statements), not as details
- **Mutation testing:** Scope limited to `app/Domains` for runtime optimization
- **Git Hooks:** Shell script (POSIX compatible), works on WSL + macOS + Linux
- **SOLID-Enforcement:** Checking interface implementations, readonly classes, controller patterns

---

## Planned implementation slices

### Slice 0 – Architecture Analysis & Preparation
- Architecture test suite plan structure (DDD, CQRS, SOLID)
- Inventory existing layer rules
- Document Pest Arch syntax and Pest plugin mutates

### Slice 1 – Implement DDD testing
- `DddArchTest.php`: Bounded Context Boundaries (Profile ↔ Analysis)
- Validate coupling at Action/UseCase level
- Prevent model access (Eloquent).

### Slice 2 – Implement CQRS testing
- `CqrsArchTest.php`: Command/Query segregation
- Validate namespace conventions (commands, queries, handlers)
- No command-in-query usage, no query-in-command usage

### Slice 3 – Implement SOLID testing
- `SolidArchTest.php`: Single-action controller, interface-based design, read-only DTOs
- Check controller `__invoke` method
- Validate DTOs immutable
- Check service interface usage

### Slice 4 – Prepare mutation testing
- Add `pestphp/pest-plugin-mutate` in `composer.json` as a dev dependency
- `test:pest-mutation` Implement composer script (Scope: `app/Domains`)
- `make test-mutation` Add target
- Document open questions (MSI threshold, slow test strategy) in the roadmap

### Slice 5 – Setting up Git hooks
- `.githooks/pre-commit` Write script (Pint, PHPStan, commit message check)
- `make hooks-install` Implement target
- Validate hook execution locally

### Slice 6 – Extend Makefile & Composer
- Add `make test-arch`, `make test-arch-gate`, `make test-mutation`, `make hooks-install`
- `quality:arch-gate` Define composer script
- `.PHONY` Update targets

### Slice 7 – CI integration & quality gates
- `phpunit.xml`: Register Architecture Suite as a test suite
- `pest/Pest.php`: Include architecture tests
- New CI job `pest_architecture` in `.github/workflows/ci.yml` (or separate `mutation.yml`)
- Validate `make test` and quality gates

### Slice 8 – Complete documentation
- Outsource detailed plan to `docs/history/PLANNING_COMMIT_28.md`
- `docs/ARCHITECTURE.md`: Update architecture testing status
- `docs/ROADMAP.md`: Mutation testing document open questions
- `COMMIT_PLAN.md`: Set status to commit 28, list next commits
- Update changelog in the unreleased block

---

## Success Criteria (DoD)

1. Architecture Suite is completely green (all three test files passed).
2. DDD limits are checked automatically (Profile ↔ Analysis is not linked at the command/handler level).
3. CQRS segregation is validated (Commands → `void`, Queries → read-only).
4. SOLID rules are tested (single action controller, readonly DTOs, interface usage).
5. Mutation testing is prepared (dev dependency, script, target available; open questions documented).
6. Git hooks are installable and functional (`make hooks-install`).
7. Makefile and Composer scripts are extended (test-arch, test-mutation, hooks-install).
8. CI is customized (architecture suite runs in standard CI).
9. All standard gates remain green (Pint, PHPStan, test suites).
10. Documentation is updated and linked.

---

## Risks & countermeasures

- **Risk:** Arch tests too strict/fragmented → too many false positives.
  **Measure:** Step-by-step implementation, review after Slice 3.

- **Risk:** Mutation testing becomes too slow.
  **Measure:** Scope limited to `app/Domains`; Prepare option for parallel execution.

- **Risk:** Git hooks do not work on all platforms.
  **Action:** POSIX-compatible shell script, WSL/macOS/Linux tested.

- **Risk:** CI complexity grows.
  **Measure:** Architecture tests in the standard CI, mutation in a separate workflow (optional).

---

## Definition of Ready

- Pest Arch syntax is documented.
- Layer rules are specifically listed.
- Mutation testing framework is selected (pest-plugin-mutate).
- Git hook requirements are clear (Pint, PHPStan, commit message).

## Definition of Done

- All planned slices have been implemented.
- Architecture Suite runs locally and in CI green.
- Mutation testing is prepared (dev dependency, script, open questions documented).
- Git hooks can be activated manually and are functional.
- `make test-arch-gate`, `make test-mutation`, `make hooks-install` work.
- Documentation is updated and linked.
- Changelog is maintained in the Unreleased block.

---

## References

- Activity plan: `COMMIT_PLAN.md`
- Previous detailed plan: `docs/history/PLANNING_COMMIT_27.md`
- Roadmap: `docs/ROADMAP.md`
- Architecture documentation: `docs/ARCHITECTURE.md`
- Agent context: `docs/ai/AGENT_CONTEXT.md`