# Commit 23 - GitHub Actions CI + Branch Protection

**Branch:** `feature/commit-23-github-actions-ci`  
**Status:** In Planung  
**Erstellt:** 2026-03-11

---

## Ziel

Quality Gates automatisch zwischen Feature-Branches und `main` absichern, ohne bereits Deployment-Themen vorzuziehen.

---

## Scope

### Enthalten
- GitHub Actions CI (CI-first)
- Jobs: `pint`, `phpstan`, `pest` + Coverage (`>=95%`)
- Trigger: `push`, `pull_request` auf `main`, `workflow_dispatch`
- Coverage-Artefakte als Build-Artifact (Retention: 7 Tage)
- Codecov-Upload fuer Coverage-Badge (public Repo)
- Status-Badges in `README.md`
- Dokumentation fuer Branch-Protection (`main`)

### Nicht enthalten
- Kein Deployment
- Keine Cloud-Infrastruktur
- Keine Release-Automation

---

## Technische Entscheidungen

1. CI-Stack: `shivammathur/setup-php` (PHP 8.5) statt Docker-build in CI
2. Coverage: bestehende `clover.xml` wird fuer Codecov genutzt
3. APP_KEY: wird zur Laufzeit im Workflow erzeugt (nicht im Repo)
4. AI im CI: `AI_PROVIDER=mock`, `GEMINI_API_KEY` als leerer Platzhalter
5. Protected Branch: `main` mit Required Checks (`pint`, `phpstan`, `pest`)

---

## Geplante Dateien

- `.github/workflows/ci.yml`
- `src/.env.ci`
- `README.md` (Badges)
- `docs/DEVELOPMENT.md` (Branch-Protection + Codecov-Setup)
- `COMMIT_PLAN.md` (Status/Scope)
- `docs/ROADMAP.md` (Status aktualisieren)

---

## Definition of Done

- CI startet fuer Push/PR/manuell und laeuft reproduzierbar
- `pint`, `phpstan`, `pest --coverage --min=95` sind aktiv
- Coverage-Artefakte werden hochgeladen (7 Tage)
- Codecov-Badge in `README.md` verfuegbar
- Branch-Protection fuer `main` dokumentiert
- Alle lokalen Quality Gates bleiben gruen

---

## Geklaerte Punkte

- Repo ist public -> Codecov ohne Token moeglich
- `clover.xml` existiert bereits
- Deployment bleibt fuer Commit 24+ eingeplant

