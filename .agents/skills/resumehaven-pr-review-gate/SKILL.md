---
name: resumehaven-pr-review-gate
description: Performs ResumeHaven-style PR reviews focused on high-risk findings first, including DDD/CQRS/SOLID compliance, regressions, security, and missing tests.
---

# ResumeHaven PR Review Gate

Use this skill to run a strict, risk-first code review with ResumeHaven quality expectations.

## Trigger Names

- Primary Trigger: `pr_review_gate`
- Alias: `resumehaven-pr-review-gate`
- Source of truth for team trigger names: `.github/skills/copilot-skills.yaml`

## When to Use This Skill

Use this skill when the user asks for a review, PR check, risk assessment, or release-readiness validation.

- Reviewing a feature branch
- Pre-merge quality review
- Regression and risk analysis
- Security and maintainability check

## When Not to Use This Skill

Do not use this skill for implementation-first requests where no review is requested.

- "Build feature X" without review ask
- Pure setup/support requests without changed code

## Input Expectations

Provide:

- Changed files or diff
- Relevant requirements or acceptance criteria
- Known constraints (performance, security, deadlines)

## Output Contract

Respond in this order:

1. Findings by severity (critical -> high -> medium -> low), each with file/path and line reference
2. Open questions or assumptions
3. Brief change summary
4. Recommended next steps

## Review Checklist Focus

- Behavioral bugs and regressions
- Security risks and unsafe patterns
- DDD/CQRS/SOLID violations
- Missing or weak tests
- Quality-gate risks (Pest, coverage >= 95, PHPStan, Pint)

## Example Prompts

- "Run `pr_review_gate` on this diff and prioritize findings with file and line references."
- "Use `resumehaven-pr-review-gate` and review these changes in ResumeHaven style with DDD/CQRS/SOLID and test-gap focus."


