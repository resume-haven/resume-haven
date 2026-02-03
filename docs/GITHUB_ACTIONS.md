# GitHub Actions Workflows

This project uses GitHub Actions for automated code quality and security checks.

## 📋 Workflows

### 1. **Code Quality** (`code-quality.yml`)
Automatic code quality checks on every push and pull request.

**Jobs:**
- ✅ **Lint** - Code Style with Pint
- ✅ **Static Analysis** - PHPStan with Larastan
- ✅ **Refactoring Check** - Rector dry-run
- ✅ **Tests** - Unit, Feature, Integration, Architecture Tests

**Triggers:**
- Push to `main`, `develop`
- Pull Requests against `main`, `develop`
- Daily at 2:00 UTC

**Features:**
- Parallel job execution (faster)
- Composer Dependency Caching
- SQLite In-Memory Database
- Failed jobs don't stop others (fail-fast: false)

### 2. **Security** (`security.yml`)
Focused security checks and dependency validation.

**Jobs:**
- ✅ **Architecture Security Tests** - Pest Architecture Preset
- ✅ **Composer Validation** - Validate `composer.json` and `composer.lock`
- ✅ **PHP Syntax Check** - Detect syntax errors

**Triggers:**
- Push and Pull Requests
- Daily at 3:00 UTC

### 3. **CI Pipeline** (`ci.yml`)
Complete CI pipeline with dependent jobs.

**Job Dependencies:**
```
validation
    ├── lint
    ├── analysis
    └── tests
         └── status (final check)
```

**Triggers:**
- Push to `main`, `develop`
- Pull Requests against `main`, `develop`

**Features:**
- Dependent jobs (only run if previous succeeded)
- Faster feedback on validation errors
- Parallel execution of lint, analysis, tests

## 🚀 Features

### Free
✅ All workflows use **free GitHub Actions**
- Ubuntu Latest Runner (free)
- Standard Actions (free)
- No third-party paid tools

### Performance
✅ **Composer Dependency Caching**
- Cache based on `composer.lock`
- Saves ~30-60 seconds per run

✅ **Parallel Job Execution**
- Lint, Analysis, Tests run in parallel
- Total Runtime: ~5-10 minutes

✅ **Fail-Fast for Tests**
- Architecture Tests stop on first error (--bail)
- Faster feedback

### Reliability
✅ **Concurrency Control**
- Only one workflow per branch
- Older runs are cancelled

✅ **Scheduled Runs**
- Security checks daily
- Detects dependency problems early

## 📊 Tools Used

| Tool | Job | Action |
|------|-----|--------|
| **Pint** | Lint | `composer test:lint` |
| **PHPStan** | Analysis | `composer test:phpstan` |
| **Rector** | Analysis | `composer test:rector` |
| **Pest** | Tests | `composer test:*` |

All tools are defined as scripts in `composer.json`.

## 🔄 Workflow Status Badges

Add these badges to your `README.md`:

```markdown
[![Code Quality](https://github.com/username/resume-haven/actions/workflows/code-quality.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/code-quality.yml)
[![CI](https://github.com/username/resume-haven/actions/workflows/ci.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/ci.yml)
[![Security](https://github.com/username/resume-haven/actions/workflows/security.yml/badge.svg)](https://github.com/username/resume-haven/actions/workflows/security.yml)
```

## 📝 Customization

### Change Branches
Update `branches: [ main, develop ]` in workflows to use different branches.

### Change PHP Version
Update `php-version: '8.5'` in workflows for other PHP versions.

### Change Schedule
Cron syntax for scheduled runs:
```yaml
schedule:
  - cron: '0 2 * * *'  # daily at 02:00 UTC
  - cron: '0 */6 * * *'  # every 6 hours
  - cron: '0 0 * * 0'  # every Sunday at 00:00 UTC
```

## 🎯 Best Practices

1. **Protect Branches**: Allow merges only when all checks pass
   - GitHub Settings → Branches → Add Rule
   - Enable "Require status checks to pass"

2. **Notifications**: Configure GitHub Notifications for failed runs

3. **Artifacts**: Logs are available for 90 days

4. **Secrets**: No secrets in workflows (not used in this project)

## 🐛 Troubleshooting

### Workflows not executing
- Check `.github/workflows/` files are committed
- Branch must be defined in `on.push.branches`
- Check "Actions" tab for errors

### Cache not being used
- Cache is shared only between runs
- Different `composer.lock` = different cache keys

### Tests fail in CI but pass locally
- Different environment (PHP version, extensions)
- Missing `.env` setup - see workflow `Create .env file`
- SQLite errors - workflow creates `storage/database.sqlite`

## 📚 Further Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [shivammathur/setup-php](https://github.com/shivammathur/setup-php)
- [actions/cache](https://github.com/actions/cache)
