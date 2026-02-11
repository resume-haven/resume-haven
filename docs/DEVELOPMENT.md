# Development Guide

## 🚀 Quick Start

**See also:** [GitHub Actions](GITHUB_ACTIONS_QUICK.md) - Automated CI/CD Pipeline


### Local Mail (Mailpit)

Development emails are routed to Mailpit instead of real recipients.

- Web UI: http://localhost:8025
- SMTP: mailpit:1025

If you use a local `.env`, ensure these values are set.

## Code Standards

### Strict Types

All PHP files must declare strict types at the top:

```php
<?php
declare(strict_types=1);

namespace App\Domain\Entities;

class Resume
{
    // Code here...
}
```

**Why Strict Types?**
- Prevents type coercion bugs
- Catches type errors at runtime
- Improves code reliability
- Makes intentions explicit

### Enforcing Strict Types

**All PHP files in these directories have strict types enabled:**
- `app/` - Application code
- `bootstrap/` - Bootstrap files
- `database/` - Migrations, factories, seeders
- `routes/` - Route definitions
- `tests/` - Test files

**Total: 47 files with `declare(strict_types=1)`**

## Code Style Guide

### Naming Conventions

**Classes** - PascalCase
```php
class ResumeBuilder { }
class ExperienceEntry { }
class SkillAssessment { }
```

**Methods/Functions** - camelCase
```php
public function buildResume() { }
private function validateInput() { }
function calculateScore() { }
```

**Constants** - UPPER_SNAKE_CASE
```php
const MAX_FILE_SIZE = 5242880; // 5MB
const RESUME_FORMAT_PDF = 'pdf';
```

**Variables** - camelCase
```php
$resumeData = [];
$isValid = true;
$maxAttempts = 3;
```

### Code Structure

**Class Structure**
```php
<?php
declare(strict_types=1);

namespace App\Domain\Entities;

class Resume
{
    // Constants first
    const EXPORT_FORMAT = 'pdf';
    
    // Properties
    private string $name;
    private array $sections = [];
    
    // Constructor
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    // Public methods
    public function build(): string
    {
        return $this->render();
    }
    
    // Protected/private methods
    private function render(): string
    {
        return 'rendered content';
    }
}
```

## PHP Best Practices

### Type Hints

Always use type hints for parameters and return values:

```php
// ❌ Bad: No type hints
public function processData($input) {
    return $result;
}

// ✅ Good: Full type hints
public function processData(array $input): string {
    return json_encode($input);
}
```

### Null Safety

Use typed properties to prevent null issues:

```php
// ❌ Bad: Could be null
private $data;

// ✅ Good: Explicit nullability
private ?array $data = null;

// ✅ Best: Non-nullable with initialization
private array $data = [];
```

### Error Handling

Always handle exceptions properly:

```php
try {
    $resume = $this->buildResume($data);
} catch (\InvalidArgumentException $e) {
    $this->logger->error('Invalid resume data', ['error' => $e->getMessage()]);
    throw $e;
} catch (\Exception $e) {
    $this->logger->critical('Unexpected error building resume', ['error' => $e]);
    throw new \RuntimeException('Failed to build resume', 0, $e);
}
```

### Documentation

Use PHPDoc for complex logic:

```php
/**
 * Generates a resume in the specified format.
 *
 * @param array $resumeData The resume data structure
 * @param string $format The export format (pdf, docx, html)
 * @return string The generated resume content
 * @throws \InvalidArgumentException If format is unsupported
 */
public function generate(array $resumeData, string $format): string
{
    if (!in_array($format, self::SUPPORTED_FORMATS, true)) {
        throw new \InvalidArgumentException("Format {$format} not supported");
    }
    
    return $this->{'generate' . ucfirst($format)}($resumeData);
}

## HTTP & Routing

### API vs Web Routes

- `routes/api.php` is stateless and does not use CSRF protection.
- `routes/web.php` is stateful and CSRF-protected.

Controllers for JSON APIs should be registered in `api.php` to avoid CSRF errors in tests.

### API Endpoints

**Resumes**
- `GET /api/resumes/{id}` - Fetch resume read model
- `POST /api/resumes` - Create resume

**Users**
- `GET /api/users/{id}` - Fetch user read model
- `POST /api/users` - Create user

## Factories

When models live under `App\Infrastructure\Persistence`, Laravel expects factory classes in the
matching namespace `Database\Factories\Infrastructure\Persistence` and files under
`database/factories/Infrastructure/Persistence/`.
```

## Git Workflow

### Conventional Commits

This project uses **Conventional Commits** as specified in [conventionalcommits.org](https://www.conventionalcommits.org/en/v1.0.0/).

**Format:**
```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

**Types:**
- `feat` - A new feature
- `fix` - A bug fix
- `docs` - Documentation only changes
- `style` - Changes that don't affect code meaning (formatting, semicolons, etc.)
- `refactor` - Code changes that neither fix bugs nor add features
- `perf` - Code changes that improve performance
- `test` - Adding or updating tests
- `chore` - Changes to build process, dependencies, tools, CI/CD
- `ci` - CI/CD configuration changes

**Examples:**

```bash
# Simple feature
git commit -m "feat: add resume PDF export functionality"

# Bug fix with scope
git commit -m "fix(validation): prevent null pointer in email validator"

# Breaking change
git commit -m "feat!: redesign resume builder API

BREAKING CHANGE: ResumeBuilder constructor now requires Template parameter"

# Multiple commits
git commit -m "feat(export): add PDF export

- Integrate PDFlib library
- Implement PDF template rendering
- Add format validation"

# Documentation
git commit -m "docs: update deployment guide for Kubernetes"

# Test addition
git commit -m "test(models): add tests for Resume class"

# Dependencies
git commit -m "chore(deps): upgrade PHP to 8.5.2"
```

**Benefits:**
- Automated changelog generation
- Clear commit history
- Easy to identify breaking changes
- Semantic versioning alignment
- Better code review context

**Commit Checklist:**
- [ ] Type is one of: feat, fix, docs, style, refactor, perf, test, chore, ci
- [ ] Description is in imperative mood ("add", not "added")
- [ ] Description starts with lowercase (except for acronyms)
- [ ] No period at end of description
- [ ] Scope is optional but recommended
- [ ] Breaking changes use `!` after type
- [ ] Body explains why, not what (what is shown in diff)

## Makefile Reference

This project includes a **Makefile** that automates common tasks. The help is automatically generated from comments in the Makefile.

### View All Commands

```bash
make help
# or just
make
```

### Common Commands

```bash
# Docker Management
make up              # Start containers
make down            # Stop containers
make logs            # View logs
make shell           # Open container shell

# Application
make install         # Install dependencies
make test            # Run tests
make lint            # Check code style

# Development Setup
make dev             # Complete setup (up + install)
make init            # Initialize fresh project
```

### How It Works

The Makefile uses a **self-documenting system**:
- Each target has a comment: `## GROUP: target - Description`
- The `help` target parses these comments automatically
- Documentation stays in sync with code

**See [docs/MAKEFILE.md](MAKEFILE.md) for detailed documentation.**

## Development Workflow

### Setting Up Your Environment

With Makefile (recommended):

```bash
# One command setup
make dev

# Then develop
make shell        # Open container shell
make logs         # View logs
make test         # Run tests
```

Or manually:

```bash
# 1. Start containers
docker-compose up -d

# 2. Enter container
docker-compose exec app bash

# 3. Install dependencies (if needed)
composer install

# 4. Set up IDE debugging (F5 in VS Code)
```

#### Mailpit Configuration

If you use a local `.env`, ensure Mailpit is configured:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

#### Environment Variables

Common local settings:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### Making Code Changes

1. **Edit code locally** in VS Code
2. **Container automatically detects changes** (mounted volume)
3. **Refresh browser** to test changes
4. **Use Xdebug** to debug (F5, set breakpoints)

### Testing Code

We use **Pest** for testing - a modern, elegant testing framework for Laravel.

Query handlers return read models (from read repositories), while command handlers work with domain entities.

```bash
# Run all tests
make test

# Run tests with coverage report
make test-coverage

# Run specific test file
docker-compose exec app ./vendor/bin/pest tests/Feature/ExampleTest.php

# Run tests matching pattern
docker-compose exec app ./vendor/bin/pest --filter=ModelTest

# Run tests in parallel
docker-compose exec app ./vendor/bin/pest --parallel
```

**Example Pest Test:**

```php
<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Infrastructure\Persistence\ResumeModel;

describe('Resume API', function () {
    it('creates a resume', function () {
        $data = [
            'title' => 'John Doe',
            'summary' => 'Experienced developer',
        ];

        $response = $this->postJson('/api/resumes', $data);
        
        $response->assertCreated()
            ->assertJsonPath('data.title', 'John Doe');
    });

    it('returns 404 for missing resume', function () {
        $this->getJson('/api/resumes/999')
            ->assertNotFound();
    });
});
```

See [Code Quality Guide](CODE_QUALITY.md#pest-testing-framework) for more Pest documentation.

## File Organization

### Adding New Files

When creating new PHP files:

1. **Use appropriate namespace**
   ```php
   <?php
   declare(strict_types=1);
   
    namespace App\Application\Services;
   ```

2. **Add strict types**
   - Always second line after `<?php`

3. **Follow PSR-12** code style
   - Consistent indentation (4 spaces)
   - One blank line between methods

4. **Document public APIs**
   - PHPDoc blocks for classes and public methods

### Directory Purpose

| Directory | Purpose |
|-----------|---------|
| `app/` | Core application code |
| `public/` | Web server entry point and assets |
| `bootstrap/` | Application initialization |
| `storage/` | Temporary files, logs, cache |
| `database/` | Migrations, factories, seeders |
| `routes/` | Route definitions |
| `tests/` | Test files |
| `docker/` | Docker configurations |

## Debugging Tips

### Using Xdebug Effectively

1. **Set breakpoints** on suspect code lines
2. **Inspect variables** in Variables panel
3. **Watch expressions** for complex logic
4. **Step through code** methodically
5. **Check call stack** for function flow

See [docs/XDEBUG.md](XDEBUG.md) for detailed debugging guide.

### Quick Debugging

For quick inspection without IDE:

```bash
# Add to code
var_dump($variable);
die();

# Or in containers
docker-compose exec app php -r "var_dump(\$data);"
```

## Performance Considerations

### Memory Usage

For strict types enforcement:
- ~0.5% overhead per file
- Negligible in modern PHP 8.5
- Benefit in error prevention far outweighs cost

### Loading Time

Type checking happens at:
- **Parse time** (minimal impact)
- **Runtime** (only on function calls)

### Optimization

Declare strict types doesn't slow down code because:
1. Type hints compiled at parse time
2. No reflection overhead
3. PHP 8.5 optimizations handle native types

## Security Best Practices

### Input Validation

Always validate and sanitize input:

```php
public function createResume(string $name, array $sections): Resume
{
    // Validate name
    if (strlen($name) < 1 || strlen($name) > 255) {
        throw new \InvalidArgumentException('Invalid resume name');
    }
    
    // Validate sections
    foreach ($sections as $section) {
        if (!is_array($section) || empty($section['title'])) {
            throw new \InvalidArgumentException('Invalid section structure');
        }
    }
    
    return new Resume($name, $sections);
}
```

### SQL Injection Prevention

Direct database access is not allowed. Use repositories and Eloquent models instead of raw SQL.

```php
// ❌ Bad: direct SQL access
$query = "SELECT * FROM users WHERE name = ?";
$result = $db->query($query, [$name]);

// ✅ Good: repository + ORM
$user = $this->users->findByName($name);
```

## Common Patterns

### Builder Pattern

```php
<?php
declare(strict_types=1);

class ResumeBuilder
{
    private Resume $resume;
    
    public function __construct(string $name)
    {
        $this->resume = new Resume($name);
    }
    
    public function addExperience(Experience $exp): self
    {
        $this->resume->addSection('experience', $exp);
        return $this;
    }
    
    public function build(): Resume
    {
        return $this->resume;
    }
}

// Usage
$resume = (new ResumeBuilder('John Doe'))
    ->addExperience($exp1)
    ->addExperience($exp2)
    ->build();
```

### Repository Pattern

```php
<?php
declare(strict_types=1);

interface ResumeRepository
{
    public function findById(string $id): ?Resume;
    public function save(Resume $resume): void;
}

class SqliteResumeRepository implements ResumeRepository
{
    public function findById(string $id): ?Resume
    {
        // Database query
    }
    
    public function save(Resume $resume): void
    {
        // Database insert/update
    }
}
```

## Continuous Improvement

### Code Review Checklist

When reviewing code:
- [ ] Strict types declared
- [ ] Type hints on all methods
- [ ] Error handling present
- [ ] Documentation complete
- [ ] No obvious security issues
- [ ] Follows naming conventions
- [ ] Appropriate use of design patterns

### Refactoring Guide

**Automatisiertes Refactoring mit Rector:**

```bash
# Zeige Refactoring-Vorschläge (ohne Änderungen)
make rector

# Wende Refactoring automatisch an
make rector-fix
```

**Rector-Konfiguration (Feb 2026):**
- **PHP 8.5** - Zielversion mit moderner Syntax
- **Laravel 12.0** - Neueste Laravel-Features
- **14 Laravel-Rulesets** für Code Quality und Best Practices
- **Parallelisierung** - 8 Prozesse für schnelle Verarbeitung
- **Caching** - Beschleunigung bei wiederholten Läufen

**Rector automatisiert:**
- Property Promotion
- Readonly Properties
- Type Declarations
- Facade Aliases → vollständig qualifizierte Namen
- Collection Helpers → Method Calls
- Static Calls → Dependency Injection

**Manuelles Refactoring (wenn nötig):**
1. Ensure tests pass before changes
2. Make small, focused changes
3. Update type hints if signatures change
4. Add documentation if behavior changes
5. Test thoroughly after refactoring
6. Run `make rector-fix` zur Automatisierung

## Laravel Development

### Application Structure

ResumeHaven uses Laravel 12 with the following key features:

- **Strict Types**: All PHP files include `declare(strict_types=1)`
- **Database**: SQLite (default) with support for PostgreSQL/MySQL
- **Testing**: Pest for unit and feature tests
- **Code Style**: PSR-12 via Laravel Pint

### Common Laravel Commands

```bash
# Run migrations
make migrate

# Create a new migration
docker-compose exec app php artisan make:migration create_table_name

# Create a new model
docker-compose exec app php artisan make:model ModelName

# Create a controller
docker-compose exec app php artisan make:controller ControllerName

# Run seeders
docker-compose exec app php artisan db:seed

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Creating New Features

1. **Create a migration**:
   ```bash
   docker-compose exec app php artisan make:migration create_resumes_table
   ```

2. **Create a model**:
   ```bash
   docker-compose exec app php artisan make:model Resume
   ```

3. **Create a controller**:
   ```bash
   docker-compose exec app php artisan make:controller ResumeController --resource
   ```

4. **Run migrations**:
   ```bash
   make migrate
   ```

### Testing

Run the test suite:
```bash
make test
```

Create new tests:
```bash
# Feature test
docker-compose exec app php artisan make:test ResumeTest

# Unit test
docker-compose exec app php artisan make:test ResumeTest --unit
```

### Database Management

**View database**:
```bash
# SQLite
docker-compose exec app php artisan tinker
>>> App\Infrastructure\Persistence\ResumeModel::query()->get();
```

**Reset database**:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## Resources

- [PHP Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- [PSR-12 Code Style](https://www.php-fig.org/psr/psr-12/)
- [PHP 8.5 Features](https://www.php.net/releases/)
- [Laravel Documentation](https://laravel.com/docs)
- [GitHub Actions](GITHUB_ACTIONS_QUICK.md) - CI/CD Pipeline

## Next Steps

- [Docker Setup](DOCKER.md) - Container management
- [Xdebug Debugging](XDEBUG.md) - IDE debugging
- [Architecture](ARCHITECTURE.md) - Project structure
- [GitHub Actions](GITHUB_ACTIONS.md) - Automated CI/CD
