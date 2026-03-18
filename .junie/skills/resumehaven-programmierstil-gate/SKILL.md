---
name: resumehaven-programmierstil-gate
description: Enforces ResumeHaven coding standards for implementation tasks (DDD, phased CQRS, SOLID, single-action controllers, immutable DTOs, strict typing, and mandatory quality gates).
---

# ResumeHaven Programmierstil Gate

Use this skill to apply the default ResumeHaven engineering baseline automatically during coding tasks.

## Trigger Names

- Primary Trigger: `programmierstil_gate`
- Alias: `resumehaven-programmierstil-gate`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Use this skill when the user asks to implement or refactor code in this repository and wants standards applied without repeating them in each prompt.

- New feature implementation
- Refactoring existing classes
- Editing controllers, actions, use cases, DTOs, services, repositories
- Preparing changes for PR-quality output

## When Not to Use This Skill

Do not use this skill as the primary workflow for purely non-code tasks.

- Plain documentation-only requests
- Design brainstorming without code changes
- Non-ResumeHaven repositories

## Input Expectations

Provide:

- Target files or feature scope
- Expected behavior and constraints
- Whether changes are command-side, query-side, or both (CQRS context)

## Output Contract

Always produce:

1. A short architecture placement note (DDD/CQRS fit)
2. A SOLID check summary for touched code
3. Required/updated tests
4. Quality-gate run plan (Pest, coverage, PHPStan, Pint)

## Guardrails

- Keep command and query responsibilities separated
- Use single-action controllers (`__invoke`) where applicable
- Keep business logic out of controllers
- Prefer immutable DTO patterns and strict typing
- Do not bypass repository boundaries for persistence concerns

## Example Prompts

- "Apply `programmierstil_gate` and implement this feature in the existing Analysis domain flow."
- "Use `resumehaven-programmierstil-gate` and refactor this service with architecture placement, SOLID check, tests, and quality gates."


