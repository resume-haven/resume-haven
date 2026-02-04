declare(strict_types=1);

# Code Quality Guide

ResumeHaven implements a comprehensive code quality system with four specialized tools:
- **Pint** - Code Formatting
- **PHPStan + Larastan** - Static Analysis
- **Pest** - Testing Framework
- **Rector** - Automated Refactoring

## Tools

### 1. Laravel Pint (Code Formatter)
**Automatic formatting** of PHP code according to PSR-12 standard.

```bash
# Format code
make lint

# Check without fixing
make lint-check
```

**What it does:**
- Set correct indentation
- Organize import statements
- Unify code style
- Apply automatic fixes

**Configuration:** `pint.json`

---

### 2. PHPStan with Larastan (Static Analysis)
**Find errors** before code runs - Types, Logic, Security.

```bash
# Run analysis
make phpstan

# Generate baseline for known issues
make phpstan-baseline
```

**What it does:**
- Find type errors
- Uninitialized variables
- Null-pointer problems
- Logical errors
- Laravel-specific issues

**Configuration:** `phpstan.neon`  
**Baseline:** `phpstan-baseline.neon` (for accepted errors)

**Level Explanation:**
- **0:** Only obvious errors
- **5:** Recommended level (Balanced)
- **9:** Maximum strict (Time-intensive)

---

### 3. Pest (Testing Framework)
**Tests** your application with expressive syntax and advanced features.

```bash
# Run all tests
make test

# Run specific test suites
make test-unit           # Unit tests only
make test-feature        # Feature tests only
make test-integration    # Integration tests only
make test-architecture   # Architecture tests only

# Run specific test file
docker-compose exec app ./vendor/bin/pest tests/Feature/ExampleTest.php
```

**Features:**
- Elegant API with fluent interface
- Parallel test execution
- Coverage reports
- Artisan commands testing
- Mocking & expectations
- Laravel integration plugin
- Architecture Tests
- Dataset Support

**Configuration:** `phpunit.xml` (Pest uses PHPUnit configuration)

**Test Structure:**
- `tests/Unit/` - Unit tests for isolated logic
- `tests/Feature/` - Feature tests for HTTP & User workflows
- `tests/Integration/` - Integration tests for connected components
- `tests/Architecture/` - Architecture tests for code structure & rules

**Example Test (Pest):**
```php
<?php

test('can create resume', function () {
    $resume = Resume::factory()->create();
    expect($resume)->toBeInstanceOf(Resume::class);
});

test('resume has required fields', function () {
    $resume = Resume::factory()->create();
    expect($resume->name)->toBeString()
        ->and($resume->email)->toBeString();
});
```

---

### 4. Rector (Automated Refactoring)
**Automates** code refactoring and modernization.

```bash
# See refactoring suggestions (dry-run)
make rector

# Apply refactoring
make rector-fix
```

**What it does:**
- PHP Version Upgrades (PHP 8.5 target)
- Laravel Version Upgrades (Laravel 12.0)
- Code modernization
- Replace deprecated code
- Property Promotion
- Readonly Properties
- Type Declarations

**Configuration:** `rector.php`

**Current Setup (as of Feb 2026):**
- **PHP Target Version:** 8.5
- **Laravel Target Version:** 12.0
- **Performance:** 8 parallel processes
- **Caching:** Enabled for faster runs
- **Laravel Rulesets:** 14 specialized sets for Code Quality, Collections, Type Declarations, etc.

**Rector Examples:**
```php
// Before
public function __construct(
    private string $name,
    private string $email
) {}

// After (Rector does this automatically!)
public function __construct(
    private string $name,
    private string $email,
) {}
```

**Laravel-specific Refactorings:**
- Facade Aliases → Fully qualified names
- Collection helpers → Method calls
- Static Calls → Dependency Injection
- Legacy Factories → Factory classes
- Magic Methods → Query Builder
- Array/String Functions → Static Calls

---

## Quick Quality Checks

### Run a single check
```bash
make lint           # Format code with Pint
make lint-check     # Validate Pint
make phpstan        # Static analysis
make rector         # Suggest refactoring
make test           # Run all tests
make test-unit      # Unit tests only
make test-feature   # Feature tests only
make test-integration  # Integration tests only
make test-architecture # Architecture tests only
```

### Auto-fix all checks
```bash
make quality-fix
```

This combines:
- `make lint` (Pint - auto-fix)
- `make rector-fix` (Rector - modernize code)

### Validate all checks (without changes)
```bash
make quality
```

This combines:
- `make lint-check` (Pint validate)
- `make phpstan` (Static analysis)
- `make test` (Pest tests)

---

## Code Quality in CI/CD

### GitHub Actions
The pipeline runs automatically on every **Push** and **Pull Request**.

**See:** `.github/workflows/code-quality.yml`

**What the pipeline does:**
1. ✓ Validate Composer dependencies
2. ✓ Check code style with Pint
3. ✓ Static Analysis with PHPStan
4. ✓ Pest Tests
5. ✓ Coverage Report

**Status:** Check the status on Pull Requests!

---

## Best Practices

### 1. Before each commit
```bash
make quality-fix  # Auto-fix issues (Pint + Rector)
make test         # Ensure tests pass
```

### 2. Before push
```bash
git add .
make quality      # Validate all checks
git push
```

### 3. In teams
- Code reviews also check quality checks
- Merge requests can only be merged with green status
- Change baseline only with team approval

### 4. New Features (TDD Workflow)
```bash
# 1. Write test (Red)
docker-compose exec app ./vendor/bin/pest tests/Feature/NewFeatureTest.php

# 2. Implement (Green)
# ... code changes ...

# 3. Refactor (Refactor)
make rector-fix
make quality-fix

# 4. Tests & Quality
make test
make quality

# 5. Commit
git add .
git commit -m "feat: add new feature"
git push
```

---

## Known Issues & Baseline

### PHPStan Baseline
If PHPStan finds errors you want to accept:

```bash
make phpstan-baseline
git add phpstan-baseline.neon
git commit -m "chore: update phpstan baseline"
```

**Important:** Only change baseline with good reasons!

---

## Troubleshooting

### Pint/Lint fails
```bash
# Usually just auto-fix
make lint

# Then verify
make lint-check
```

### PHPStan takes too long
```bash
# Increase memory limit
docker-compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G
```

### Tests fail
```bash
# Reset database
docker-compose exec app php artisan migrate:fresh
make test
```

### Rector refactoring too aggressive
```bash
# Apply only specific rules
docker-compose exec app ./vendor/bin/rector process app --set=laravel-12-0
```

---

## Install Tools

If tools are missing:

```bash
docker-compose exec app composer update
```

Tools are defined in `composer.json` under `require-dev`:
- `larastan/larastan` - PHPStan for Laravel
- `pestphp/pest` - Testing Framework
- `pestphp/pest-plugin-laravel` - Laravel Plugin for Pest
- `rector/rector` - Automated Refactoring

---

## Performance

| Tool | Time | Frequency |
|------|------|----------|
| Pint | ~1s | Before each commit |
| PHPStan | ~10s | Before pull request |
| Pest | ~5-30s | Before each push |
| Rector | ~5s | After version upgrades |

**Tip:** Run `make quality-fix` locally before push, then CI/CD needs less time.

---

## Further Resources

- [Laravel Pint Docs](https://laravel.com/docs/pint)
- [PHPStan Docs](https://phpstan.org/)
- [Larastan](https://larastan.com/)
- [Pest Docs](https://pestphp.com/)
- [Rector Docs](https://getrector.com/)
- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)
