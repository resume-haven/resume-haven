---
name: resumehaven-quality-gate-planner
description: Produces a minimal, ordered ResumeHaven quality-gate execution plan (Pest, coverage relevance, PHPStan, Pint) for a given change.
---

# ResumeHaven Quality Gate Planner

Use this skill to generate the smallest safe verification plan for code changes.

## Trigger Names

- Primary Trigger: `quality_gate_planner`
- Alias: `resumehaven-quality-gate-planner`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Use this skill after implementation or before merge to verify quality-gate coverage.

- New feature changes
- Refactors touching business logic
- Risky bug fixes

## When Not to Use This Skill

- No-code tasks
- Exploratory discussion without concrete files or diff

## Input Expectations

Provide:

- Touched files or diff
- Whether behavior changed
- Time/risk constraints (fast check vs full gate)

## Output Contract

Respond with:

1. Ordered runlist (minimal first)
2. Why each command is required
3. Success criteria for each gate

## Checklist Focus

- Targeted Pest tests first
- Coverage implication and when full coverage run is needed
- PHPStan level-9 safety
- Pint formatting after code edits

## Example Prompts

- "Use `quality_gate_planner` and give me the smallest safe runlist for this change."
- "Run `resumehaven-quality-gate-planner` and tell me exactly which Pest/PHPStan/Pint steps are mandatory before merge."

