# 🔄 GitHub Actions Quick Reference

## Status Badges

```markdown
[![Code Quality](https://github.com/your-org/resume-haven/actions/workflows/code-quality.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/code-quality.yml)
[![CI](https://github.com/your-org/resume-haven/actions/workflows/ci.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/ci.yml)
[![Security](https://github.com/your-org/resume-haven/actions/workflows/security.yml/badge.svg)](https://github.com/your-org/resume-haven/actions/workflows/security.yml)
```

## Workflows at a Glance

| Workflow | Trigger | Tools | Duration |
|----------|---------|-------|----------|
| **Code Quality** | Push, PR, Cron 2:00 UTC | Pint, PHPStan, Rector, Pest | ~8-10 min |
| **Security** | Push, PR, Cron 3:00 UTC | Pest (Arch), Composer, PHP | ~5-7 min |
| **CI** | Push, PR | Validation, Lint, Analysis, Tests | ~8-10 min |

## Local Tests

Run tests locally (same as in CI):

```bash
# All tests
make quality

# Individual checks
make lint-check      # Code Style
make phpstan         # Static Analysis
make rector          # Refactoring
make test-unit       # Unit Tests
make test-feature    # Feature Tests
make test-integration # Integration Tests
make test-architecture # Architecture Tests
```

## Debug Failed Workflows

1. Open the "Actions" tab in GitHub
2. Select the failed workflow
3. Click on the failed job
4. Expand individual steps
5. Logs show exact error output

## Cache Issues

Cache is automatically invalidated when:
- `composer.lock` changes
- 7 days pass without use

## More Documentation

See [docs/GITHUB_ACTIONS.md](GITHUB_ACTIONS.md) for detailed documentation.
