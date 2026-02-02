declare(strict_types=1);

# Code Quality Guide

ResumeHaven implementiert ein umfassendes Code Quality System mit vier spezialisierten Tools:
- **Pint** - Code Formatting
- **PHPStan + Larastan** - Static Analysis
- **Pest** - Testing Framework
- **Rector** - Automated Refactoring

## Tools

### 1. Laravel Pint (Code Formatter)
**Automatische Formatierung** von PHP-Code nach PSR-12 Standard.

```bash
# Format code
make lint

# Check without fixing
make lint-check
```

**Was es tut:**
- Indentation korrekt setzen
- Import-Statements organisieren
- Code-Stil vereinheitlichen
- Automatische Fixes anwenden

**Konfiguration:** `pint.json`

---

### 2. PHPStan with Larastan (Static Analysis)
**Findet Fehler** bevor der Code läuft - Types, Logik, Sicherheit.

```bash
# Run analysis
make phpstan

# Generate baseline for known issues
make phpstan-baseline
```

**Was es tut:**
- Type-Fehler finden
- Uninitialisierte Variablen
- Null-Pointer Probleme
- Logische Fehler
- Laravel-spezifische Issues

**Konfiguration:** `phpstan.neon.dist`  
**Baseline:** `phpstan-baseline.neon` (für akzeptierte Fehler)

**Level Erklärung:**
- **0:** Nur offensichtliche Fehler
- **5:** Empfohlenes Level (Balanced)
- **9:** Maximum strikt (Zeit-intensiv)

---

### 3. Pest (Testing Framework)
**Testet** deine Anwendung mit ausdrucksstarker Syntax und erweiterten Funktionen.

```bash
# Run all tests
make test

# Run with coverage report
make test-coverage

# Run specific test
docker-compose exec app ./vendor/bin/pest tests/Feature/ResumeTest.php
```

**Features:**
- Elegant API mit fluent interface
- Parallel test execution
- Coverage reports
- Artisan commands testing
- Mocking & expectations
- Laravel integration plugin

**Konfiguration:** `pest.xml`

**Beispiel Test (Pest):**
```php
<?php

use App\Models\Resume;

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
**Automatisiert** Code-Refactoring und Modernisierung.

```bash
# See refactoring suggestions (dry-run)
make rector

# Apply refactoring
make rector-fix
```

**Was es tut:**
- PHP Version Upgrades (PHP 8.5 Zielversion)
- Laravel Version Upgrades (Laravel 12.0)
- Code Modernisierung
- Deprecated Code ersetzen
- Property Promotion
- Readonly Properties
- Type Declarations

**Konfiguration:** `rector.php`

**Aktuelle Setup (Stand Feb 2026):**
- **PHP-Zielversion:** 8.5
- **Laravel-Zielversion:** 12.0
- **Performance:** 8 parallele Prozesse
- **Caching:** Aktiviert für schnellere Läufe
- **Laravel-Rulesets:** 14 spezialisierte Sets für Code Quality, Collections, Type Declarations, etc.

**Beispiele von Rector:**
```php
// Vorher
public function __construct(
    private string $name,
    private string $email
) {}

// Nachher (Rector macht das automatisch!)
public function __construct(
    private string $name,
    private string $email,
) {}
```

**Laravel-spezifische Refactorings:**
- Facade Aliases → vollständig qualifizierte Namen
- Collection helpers → Method calls
- Static Calls → Dependency Injection
- Legacy Factories → Factory Klassen
- Magic Methods → Query Builder
- Array/String Functions → Static Calls

---

## Quick Quality Checks

### Einen Check laufen lassen
```bash
make lint           # Format code mit Pint
make lint-check     # Pint validieren
make phpstan        # Static analysis
make test           # Pest tests ausführen
make test-coverage  # Tests mit Coverage
make rector          # Refactoring vorschlagen
```

### Alle Checks automatisch fixen
```bash
make quality-fix
```

Das kombiniert:
- `make lint` (Pint - auto-fix)
- `make rector-fix` (Rector - Code modernisieren)

### Alle Checks validieren (ohne zu ändern)
```bash
make quality
```

Das kombiniert:
- `make lint-check` (Pint validate)
- `make phpstan` (Static analysis)
- `make test` (Pest tests)

---

## Code Quality in CI/CD

### GitHub Actions
Die Pipeline läuft automatisch bei jedem **Push** und **Pull Request**.

**Siehe:** `.github/workflows/code-quality.yml`

**Was die Pipeline tut:**
1. ✓ Composer Dependencies validieren
2. ✓ Code Style mit Pint prüfen
3. ✓ Static Analysis mit PHPStan
4. ✓ Pest Tests
5. ✓ Coverage Report

**Status:** Check den Status auf Pull Requests!

---

## Best Practices

### 1. Vor jedem Commit
```bash
make quality-fix  # Auto-fix issues (Pint + Rector)
make test         # Ensure tests pass
```

### 2. Vor Push
```bash
git add .
make quality      # Validate all checks
git push
```

### 3. Im Team
- Code Reviews überprüfen auch Quality Checks
- Merge Requests können nur mit grünem Status merged werden
- Baseline nur mit Team-Approval ändern

### 4. Neue Features (TDD Workflow)
```bash
# 1. Test schreiben (Red)
docker-compose exec app ./vendor/bin/pest tests/Feature/NewFeatureTest.php

# 2. Implementieren (Green)
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
Wenn PHPStan Fehler findet, die du akzeptieren möchtest:

```bash
make phpstan-baseline
git add phpstan-baseline.neon
git commit -m "chore: update phpstan baseline"
```

**Wichtig:** Baseline nur mit guten Gründen ändern!

---

## Troubleshooting

### Pint/Lint schlägt fehl
```bash
# Meist einfach auto-fix
make lint

# Dann überprüfen
make lint-check
```

### PHPStan braucht zu lange
```bash
# Memory limit erhöhen
docker-compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G
```

### Tests schlagen fehl
```bash
# Datenbank reset
docker-compose exec app php artisan migrate:fresh
make test
```

### Rector Refactoring zu aggressiv
```bash
# Nur bestimmte Rules anwenden
docker-compose exec app ./vendor/bin/rector process app --set=laravel-12-0
```

---

## Tools installieren

Falls Tools fehlen:

```bash
docker-compose exec app composer update
```

Die Tools sind in `composer.json` unter `require-dev` definiert:
- `larastan/larastan` - PHPStan für Laravel
- `pestphp/pest` - Testing Framework
- `pestphp/pest-plugin-laravel` - Laravel Plugin für Pest
- `rector/rector` - Automated Refactoring

---

## Performance

| Tool | Zeit | Frequenz |
|------|------|----------|
| Pint | ~1s | Vor jedem Commit |
| PHPStan | ~10s | Vor Pull Request |
| Pest | ~5-30s | Vor jedem Push |
| Rector | ~5s | Nach Version Upgrades |

**Tipp:** Lokal `make quality-fix` vor Push, dann braucht CI/CD weniger Zeit.

---

## Weitere Resources

- [Laravel Pint Docs](https://laravel.com/docs/pint)
- [PHPStan Docs](https://phpstan.org/)
- [Larastan](https://larastan.com/)
- [Pest Docs](https://pestphp.com/)
- [Rector Docs](https://getrector.com/)
- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)

## Tools

### 1. Laravel Pint (Code Formatter)
**Automatische Formatierung** von PHP-Code nach PSR-12 Standard.

```bash
# Format code
make lint

# Check without fixing
make lint-check
```

**Was es tut:**
- Indentation korrekt setzen
- Import-Statements organisieren
- Code-Stil vereinheitlichen
- Automatische Fixes anwenden

**Konfiguration:** `pint.json` (wird von Laravel verwaltet)

---

### 2. PHP_CodeSniffer (PSR-12 Compliance)
**Überprüft** den Code gegen den PSR-12 Standard.

```bash
# Check code style
make phpcs

# Auto-fix issues
make phpcs-fix
```

**Was es tut:**
- Überprüft PSR-12 Einhaltung
- Findet Style-Verletzungen
- Kann viele automatisch beheben
- Detaillierte Fehlerberichte

**Konfiguration:** `phpcs.xml.dist`

---

### 3. PHPStan with Larastan (Static Analysis)
**Findet Fehler** bevor der Code läuft - Types, Logik, Sicherheit.

```bash
# Run analysis
make phpstan

# Generate baseline for known issues
make phpstan-baseline
```

**Was es tut:**
- Type-Fehler finden
- Uninitialisierte Variablen
- Null-Pointer Probleme
- Logische Fehler
- Laravel-spezifische Issues

**Konfiguration:** `phpstan.neon.dist`  
**Baseline:** `phpstan-baseline.neon` (für akzeptierte Fehler)

**Level Erklärung:**
- **0:** Nur offensichtliche Fehler
- **5:** Empfohlenes Level (Balanced)
- **9:** Maximum strikt (Zeit-intensiv)

---

### 4. PHPUnit (Testing)
**Testet** deine Anwendung auf Bugs und Regressions.

```bash
# Run tests
make test

# Run with coverage
make test --coverage
```

**Konfiguration:** `phpunit.xml`

---

## Quick Quality Checks

### Einen Check laufen lassen
```bash
make lint           # Format code
make phpcs          # PSR-12 check
make phpstan        # Static analysis
make test           # Run tests
```

### Alle Checks automatisch fixen
```bash
make quality-fix
```

Das kombiniert:
- `make lint` (Pint - auto-fix)
- `make phpcs-fix` (CodeSniffer - auto-fix)

### Alle Checks validieren (ohne zu ändern)
```bash
make quality
```

Das kombiniert:
- `make lint-check` (Pint validate)
- `make phpcs` (CodeSniffer validate)
- `make phpstan` (Static analysis)

---

## Code Quality in CI/CD

### GitHub Actions
Die Pipeline läuft automatisch bei jedem **Push** und **Pull Request**.

**Siehe:** `.github/workflows/code-quality.yml`

**Was die Pipeline tut:**
1. ✓ Composer Dependencies validieren
2. ✓ Code Style mit Pint prüfen
3. ✓ PSR-12 mit CodeSniffer prüfen
4. ✓ Static Analysis mit PHPStan
5. ✓ Unit & Feature Tests
6. ✓ Coverage Report

**Status:** Check den Status auf Pull Requests!

---

## Best Practices

### 1. Vor jedem Commit
```bash
make quality-fix  # Auto-fix issues
make test         # Ensure tests pass
```

### 2. Vor Push
```bash
git add .
make quality      # Validate all checks
git push
```

### 3. Im Team
- Code Reviews überprüfen auch Quality Checks
- Merge Requests können nur mit grünem Status merged werden
- Baseline nur mit Team-Approval ändern

### 4. Neue Features
```bash
# 1. Implementieren
# ... code changes ...

# 2. Tests schreiben
docker-compose exec app php artisan make:test FeatureName

# 3. Quality checks
make quality-fix
make test

# 4. Commit & Push
git add .
git commit -m "feat: add feature description"
git push
```

---

## Known Issues & Baseline

### PHPStan Baseline
Wenn PHPStan Fehler findet, die du akzeptieren möchtest:

```bash
make phpstan-baseline
git add phpstan-baseline.neon
git commit -m "chore: update phpstan baseline"
```

**Wichtig:** Baseline nur mit guten Gründen ändern!

---

## Troubleshooting

### Pint/Lint schlägt fehl
```bash
# Meist einfach auto-fix
make lint

# Dann überprüfen
make lint-check
```

### PHPStan braucht zu lange
```bash
# Memory limit erhöhen
docker-compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G
```

### Tests schlagen fehl
```bash
# Datenbank reset
docker-compose exec app php artisan migrate:fresh
make test
```

---

## Tools installieren

Falls Tools fehlen:

```bash
docker-compose exec app composer install
docker-compose exec app composer update
```

Die Tools sind in `composer.json` unter `require-dev` definiert.

---

## Performance

| Tool | Zeit | Frequenz |
|------|------|----------|
| Pint | ~1s | Vor jedem Commit |
| CodeSniffer | ~2s | Vor jedem Commit |
| PHPStan | ~10s | Vor Pull Request |
| Tests | ~5-30s | Vor jedem Push |

**Tipp:** Lokal `make quality-fix` vor Push, dann braucht CI/CD weniger Zeit.

---

## Weitere Resources

- [Laravel Pint Docs](https://laravel.com/docs/pint)
- [PHPStan Docs](https://phpstan.org/)
- [Larastan](https://larastan.com/)
- [PSR-12 Standard](https://www.php-fig.org/psr/psr-12/)
- [PHPUnit Docs](https://phpunit.de/)
