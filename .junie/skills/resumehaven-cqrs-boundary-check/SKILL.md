---
name: resumehaven-cqrs-boundary-check
description: Checks ResumeHaven changes for CQRS boundary violations and suggests minimal refactor steps with test impact.
---

# ResumeHaven CQRS Boundary Check

Use this skill to validate strict command/query separation in ResumeHaven code changes.

## Trigger Names

- Primary Trigger: `cqrs_boundary_check`
- Alias: `resumehaven-cqrs-boundary-check`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Use this skill when code touches commands, queries, handlers, orchestration flows, or caching behavior.

- New command or query handlers
- Refactors in `app/Domains/*/Handlers`
- Flow changes that may mix read/write behavior

## When Not to Use This Skill

- Pure UI or styling updates
- Documentation-only changes
- Changes outside business-flow boundaries

## Input Expectations

Provide:

- Diff or touched files
- Intended command/query behavior
- Expected side effects and read paths

## Output Contract

Respond with:

1. CQRS findings with file/path and line reference
2. Minimal refactor plan to restore boundaries
3. Required tests or test updates

## Checklist Focus

- Commands avoid read orchestration leakage
- Queries do not mutate state
- Handler naming and folder placement match intent
- Cache reads/writes align with CQRS phase strategy

## Example Prompts

- "Run `cqrs_boundary_check` on this diff and list CQRS boundary violations with file and line references."
- "Use `resumehaven-cqrs-boundary-check` and propose minimal refactors to separate read and write responsibilities."

