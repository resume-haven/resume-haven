# Commit 23 - GitHub Actions CI + Branch Protection

**Branch:** `feature/commit-23-github-actions-ci`
**Status:** Completed
**Created:** 2026-03-11
**Completed:** 2026-03-13

---

##Goal

Automatically secure quality gates between feature branches and `main` without already prioritizing deployment topics.

---

##Scope

###Contain
- GitHub Actions CI (CI-first)
- Jobs: `pint`, `phpstan`, `pest` + Coverage (`>=95%`)
- Trigger: `push`, `pull_request` on `main`, `workflow_dispatch`
- Coverage artifacts as build artifacts (retention: 7 days)
- Codecov upload for coverage badge (public repo)
- Status badges in `README.md`
- Documentation for branch protection (`main`)

### Not included
- No deployment
- No cloud infrastructure
- No release automation

---

## Technical decisions

1. CI stack: `shivammathur/setup-php` (PHP 8.5) instead of Docker build in CI
2. Coverage: existing `clover.xml` is used for Codecov
3. APP_KEY: is created at runtime in the workflow (not in the repo)
4. AI in CI: `AI_PROVIDER=mock`, `GEMINI_API_KEY` as empty placeholders
5. Protected Branch: `main` with Required Checks (`pint`, `phpstan`, `pest_coverage`)

---

## Scheduled files

- `.github/workflows/ci.yml`
- `src/.env.ci`
- `README.md` (Badges)
- `docs/DEVELOPMENT.md` (branch protection + codecov setup)
- `COMMIT_PLAN.md` (Status/Scope)
- `docs/ROADMAP.md` (update status)

---

## Definition of Done

- CI starts for push/PR/manually and runs reproducibly
- `pint`, `phpstan`, `pest --coverage --min=95` are active
- Coverage artifacts are uploaded (7 days)
- Codecov badge available in `README.md`
- Branch protection for `main` documented
- All local quality gates remain green

---

## Clarified points

- Repo is public -> Codecov possible without token
- `clover.xml` already exists
- Deployment remains scheduled for commit 24+