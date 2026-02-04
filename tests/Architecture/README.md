# Architecture Tests

Comprehensive architecture tests using Pest's Architecture Testing plugin to enforce **DDD**, **CQRS**, **SOLID** principles, and Laravel best practices.

## Test Suites

### 1. **GeneralTest.php** - Laravel & PHP Presets
Uses official Pest presets:
- **Laravel Preset**: Ensures Laravel conventions
- **PHP Preset**: Enforces PHP best practices
- **Custom Rules**: Value Objects, DTOs must be readonly

### 2. **LayerTest.php** - DDD Layered Architecture
Enforces strict Domain-Driven Design layers:

**Domain Layer** (Pure Business Logic)
- ✅ Framework-independent (no Illuminate/Laravel)
- ✅ No dependencies on Infrastructure, Application, or UI
- ✅ Entities are mutable (not readonly)
- ✅ Value Objects are readonly and final
- ✅ Repository interfaces in Domain contracts

**Application Layer** (Use Cases & Orchestration)
- ✅ Uses only Domain and Contracts
- ✅ No direct Infrastructure or Eloquent usage
- ✅ Services and Handlers are final
- ✅ CQRS pattern (Commands/Queries/Handlers)

**Infrastructure Layer** (External Dependencies)
- ✅ Implements Domain interfaces
- ✅ Only layer allowed to use Eloquent
- ✅ Repository implementations

**UI Layer** (Controllers & Presentation)
- ✅ Uses only Application layer
- ✅ No direct Infrastructure access
- ✅ Controllers are final
- ✅ No database/Eloquent in controllers

### 3. **CqrsTest.php** - Command Query Responsibility Segregation
Enforces CQRS patterns:

**Commands** (Write Operations)
- ✅ In `App\Application\Commands`
- ✅ Readonly classes
- ✅ Suffix: `Command`
- ✅ Used only by Handlers and Controllers

**Queries** (Read Operations)
- ✅ In `App\Application\Queries`
- ✅ Readonly classes
- ✅ Suffix: `Query`
- ✅ Used only by Handlers and Controllers

**Handlers** (Process Commands/Queries)
- ✅ In `App\Application\Handlers`
- ✅ Final classes
- ✅ Suffix: `Handler`
- ✅ No direct Eloquent usage

**DTOs** (Data Transfer Objects)
- ✅ In `App\Application\DTOs`
- ✅ Readonly classes
- ✅ Suffix: `DTO`

**Domain Events**
- ✅ In `App\Domain\Events`
- ✅ Readonly classes
- ✅ Suffix: `Event`

**Read Models**
- ✅ Used only by Query Handlers
- ✅ Write models (Eloquent) not in Query Handlers

### 4. **SolidTest.php** - SOLID Principles

**Single Responsibility Principle (SRP)**
- ✅ Controllers have focused responsibilities
- ✅ Services have clear suffixes

**Open/Closed Principle (OCP)**
- ✅ Services, Handlers, Value Objects are final
- ✅ Closed for modification, open via interfaces

**Liskov Substitution Principle (LSP)**
- ✅ Clear interface contracts
- ✅ No implementation details in names

**Interface Segregation Principle (ISP)**
- ✅ Focused interfaces
- ✅ Repository interfaces segregated

**Dependency Inversion Principle (DIP)**
- ✅ High-level modules depend on abstractions
- ✅ No concrete Infrastructure in Application
- ✅ Controllers use Application, not Infrastructure
- ✅ Dependency injection over facades

### 5. **SecurityTest.php** - Security Preset
Uses official Pest Security preset:
- ✅ No raw SQL queries
- ✅ Models use fillable/guarded
- ✅ CSRF protection enabled

### 6. **StrictTest.php** - Strict Rules Preset
Uses official Pest Strict preset:
- ✅ Classes are final when possible
- ✅ No abstract classes except base controllers
- ✅ No protected methods

## Running Tests

```bash
# Run all architecture tests
docker-compose exec app ./vendor/bin/pest --testsuite=Architecture

# Run specific test file
docker-compose exec app ./vendor/bin/pest tests/Architecture/LayerTest.php
docker-compose exec app ./vendor/bin/pest tests/Architecture/CqrsTest.php
docker-compose exec app ./vendor/bin/pest tests/Architecture/SolidTest.php

# Via composer
docker-compose exec app composer test:architecture
```

## Expected Failures (For New Projects)

When starting a new project, these tests will initially fail because the DDD structure doesn't exist yet. This is **expected and intentional**.

**Initial Setup Failures:**
- Domain/Application/Infrastructure folders don't exist
- Controllers use Eloquent directly (should use Application layer)
- No CQRS structure (Commands/Queries/Handlers)
- Models in wrong location

**How to Address:**
1. **Gradually refactor** towards DDD/CQRS architecture
2. **Create namespaces** as needed (Domain, Application, Infrastructure)
3. **Move logic** from Controllers → Application Services
4. **Extract** Eloquent models → Infrastructure layer
5. **Implement** CQRS patterns (Commands, Queries, Handlers)

## Architecture Enforcement

These tests serve as **architectural guardrails**:
- ❌ **Prevent** accidental violations (e.g., Controller using DB facade)
- ✅ **Guide** developers towards correct patterns
- 📚 **Document** architectural decisions in code
- 🔒 **Enforce** in CI/CD (tests must pass before merge)

## Integration with CI/CD

Architecture tests run automatically in GitHub Actions:
- ✅ On every push to `main`/`develop`
- ✅ On every Pull Request
- ✅ Must pass before merge (if branch protection enabled)

See [GitHub Actions Documentation](../../docs/GITHUB_ACTIONS.md) for details.

## PHPStan Integration

These architecture tests complement **PHPStan Level 8** static analysis:
- **PHPStan**: Type safety, null safety, logical errors
- **Architecture Tests**: Structural patterns, layer violations, naming conventions

Both must pass for production-ready code.

## Resources

- [Pest Architecture Testing](https://pestphp.com/docs/arch-testing)
- [Domain-Driven Design (DDD)](https://martinfowler.com/tags/domain%20driven%20design.html)
- [CQRS Pattern](https://martinfowler.com/bliki/CQRS.html)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
