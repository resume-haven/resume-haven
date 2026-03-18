---
name: resumehaven-controller-thinness-check
description: Reviews controllers for ResumeHaven standards: single-action style, form requests, and strict delegation of business logic.
---

# ResumeHaven Controller Thinness Check

Use this skill to detect and fix business-logic leakage in controllers.

## Trigger Names

- Primary Trigger: `controller_thinness_check`
- Alias: `resumehaven-controller-thinness-check`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Use this skill when controller files are added or modified.

- New endpoint implementation
- Legacy controller refactor
- Validation and orchestration rewrites

## When Not to Use This Skill

- Domain/action-only changes with no controller touch
- Pure front-end tasks

## Input Expectations

Provide:

- Controller files or diff
- Expected request/response behavior
- Related Action/UseCase/Service contracts

## Output Contract

Respond with:

1. Violations with file/path and line reference
2. Target architecture shape (what belongs where)
3. Minimal extraction plan into FormRequest/Action/UseCase/Service

## Checklist Focus

- Single-action controller (`__invoke`) usage
- Form Request usage over inline validation
- Controller orchestration only, no business logic
- Proper delegation to domain/application services

## Example Prompts

- "Run `controller_thinness_check` on this controller and list logic leaks with file and line references."
- "Use `resumehaven-controller-thinness-check` and propose minimal extraction steps to Actions/UseCases."

