# 🔄 GitHub Actions Quick Reference

## Status Badges

```markdown
[![Code Quality](https://github.com/your-org/resume-haven/actions/workflows/code-quality.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/code-quality.yml)
[![CI](https://github.com/your-org/resume-haven/actions/workflows/ci.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/ci.yml)
[![Security](https://github.com/your-org/resume-haven/actions/workflows/security.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/security.yml)
```

## Workflows auf einen Blick

| Workflow | Trigger | Tools | Duration |
|----------|---------|-------|----------|
| **Code Quality** | Push, PR, Cron 2:00 UTC | Pint, PHPStan, Rector, Pest | ~8-10 min |
| **Security** | Push, PR, Cron 3:00 UTC | Pest (Arch), Composer, PHP | ~5-7 min |
| **CI** | Push, PR | Validation, Lint, Analysis, Tests | ~8-10 min |

## Lokale Tests

Um Tests lokal zu laufen (gleich wie im CI):

```bash
# Alle Tests
make quality

# Einzelne Checks
make lint-check      # Code Style
make phpstan         # Static Analysis
make rector          # Refactoring
make test-unit       # Unit Tests
make test-feature    # Feature Tests
make test-integration # Integration Tests
make test-architecture # Architecture Tests
```

## Fehlerhafte Workflows debuggen

1. Öffne das "Actions" Tab in GitHub
2. Wähle den fehlgeschlagenen Workflow
3. Klicke auf den fehlgeschlagenen Job
4. Expandiere die einzelnen Steps
5. Logs zeigen exakte Fehlerausgabe

## Cache-Issues

Cache wird automatisch invalidiert wenn:
- `composer.lock` ändert
- 7 Tage ohne Nutzung vergangen sind

## Weitere Dokumentation

Siehe [docs/GITHUB_ACTIONS.md](GITHUB_ACTIONS.md) für detaillierte Dokumentation.
