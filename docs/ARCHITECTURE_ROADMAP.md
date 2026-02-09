# Architecture Tests - Expected Failures & Roadmap

This document tracks which architecture tests are expected to fail initially and provides a roadmap for implementing the required structure.

## Current Status (New Project)

### ✅ Passing Tests (Expected)
- General Laravel/PHP presets
- Security preset (no insecure functions)
- Basic naming conventions

### ❌ Failing Tests (Expected - DDD Structure Not Yet Implemented)

#### 1. Domain Layer Tests
**Status:** 🔴 Failing (Domain namespace doesn't exist)

**Missing Structure:**
```
app/Domain/
├── Entities/           # Business entities (mutable)
├── ValueObjects/       # Immutable values
├── Services/          # Domain services
├── Events/            # Domain events
└── Contracts/         # Repository interfaces
```

**Action Required:**
- [ ] Create Domain namespace
- [ ] Define Entities for core business concepts
- [ ] Create Value Objects (e.g., Email, Money, Status)
- [ ] Define Repository interfaces

#### 2. Application Layer Tests
**Status:** 🔴 Failing (Application namespace doesn't exist)

**Missing Structure:**
```
app/Application/
├── Commands/          # Write operations (CQRS)
├── Queries/           # Read operations (CQRS)
├── Handlers/          # Command/Query handlers
│   ├── CommandHandlers/
│   └── QueryHandlers/
├── Services/          # Application services
├── DTOs/              # Data Transfer Objects
└── Exceptions/        # Application exceptions
```

**Action Required:**
- [ ] Create Application namespace
- [ ] Implement CQRS structure (Commands/Queries/Handlers)
- [ ] Create DTOs for data transfer
- [ ] Move business logic from Controllers to Services

#### 3. Infrastructure Layer Tests
**Status:** 🔴 Failing (Infrastructure namespace doesn't exist)

**Missing Structure:**
```
app/Infrastructure/
├── Repositories/      # Repository implementations
├── Persistence/       # Eloquent models
├── ReadModels/        # Query read models
└── External/          # External API integrations
```

**Action Required:**
- [ ] Create Infrastructure namespace
- [ ] Implement Repository pattern
- [x] Move Eloquent models from `app/Models` to `Infrastructure/Persistence`
- [ ] Create Read Models for queries

#### 4. CQRS Tests
**Status:** 🔴 Failing (CQRS structure doesn't exist)

**Missing Components:**
- Commands (write operations)
- Queries (read operations)
- Command Handlers
- Query Handlers
- Domain Events

**Action Required:**
- [ ] Define Commands for mutations (CreateResume, UpdateProfile)
- [ ] Define Queries for reads (GetResumeById, ListResumes)
- [ ] Create Handlers for each Command/Query
- [ ] Implement Event system for side effects

#### 5. Controller Tests
**Status:** ⚠️ Partially Failing

**Issues:**
- Controllers use Eloquent directly (should use Application layer)
- Controllers access Infrastructure (should only use Application)
- Base Controller is abstract (Strict preset expects final)

**Action Required:**
- [ ] Refactor Controllers to use Application Services
- [ ] Remove direct Eloquent usage
- [ ] Inject Application Services via constructor

#### 6. SOLID Tests
**Status:** ⚠️ Partially Failing

**Issues:**
- No interface contracts yet
- Direct dependencies on concrete implementations
- Missing final keywords on classes

**Action Required:**
- [ ] Define interfaces in Domain/Contracts
- [ ] Apply final keyword to Services, Handlers, Value Objects
- [ ] Use Dependency Injection with interfaces

## Implementation Priority

### Phase 1: Foundation (Week 1-2)
**Goal:** Basic DDD structure

1. ✅ Create namespace structure
   ```bash
   mkdir -p app/{Domain,Application,Infrastructure}/{Entities,ValueObjects,Services,Contracts}
   mkdir -p app/Application/{Commands,Queries,Handlers,DTOs}
   mkdir -p app/Infrastructure/{Repositories,Persistence,ReadModels}
   ```

2. ✅ Define core Domain concepts
   - Identify main Entities (Resume, User, Section, etc.)
   - Create Value Objects (Email, ResumeStatus, etc.)
   - Define Repository interfaces

3. ✅ Move existing code
   - Models → Infrastructure/Persistence
   - Logic from Controllers → Application/Services

### Phase 2: CQRS Implementation (Week 3-4)
**Goal:** Implement Command/Query separation

1. ✅ Create Commands
   - CreateResumeCommand
   - UpdateResumeCommand
   - DeleteResumeCommand

2. ✅ Create Queries
   - GetResumeByIdQuery
   - ListResumesQuery
   - SearchResumesQuery

3. ✅ Implement Handlers
   - Command Handlers (write operations)
   - Query Handlers (read operations)

4. ✅ Refactor Controllers
   - Controllers dispatch Commands/Queries
   - Remove business logic from Controllers

### Phase 3: Refinement (Week 5-6)
**Goal:** Polish architecture

1. ✅ Apply SOLID principles
   - Final classes where appropriate
   - Interface segregation
   - Dependency inversion

2. ✅ Add Domain Events
   - ResumeCreated
   - ResumePublished
   - ProfileUpdated

3. ✅ Read Models for queries
   - Optimized query models
   - Separate from write models

### Phase 4: Testing & Documentation (Week 7-8)
**Goal:** Ensure quality

1. ✅ Architecture tests all pass
2. ✅ Unit tests for Domain logic
3. ✅ Integration tests for Handlers
4. ✅ Update documentation

## Quick Wins (Can Do Now)

### 1. Make Existing Classes Final
```bash
# Add final keyword to classes that don't need extension
docker-compose exec app ./vendor/bin/rector process app --dry-run
```

### 2. Add Strict Types
Already enforced by Rector and Pint ✅

### 3. Extract Value Objects
```php
// Instead of:
public function __construct(string $email) { }

// Use:
public function __construct(Email $email) { }
```

### 4. Create First Repository Interface
```php
namespace App\Domain\Contracts;

interface ResumeRepositoryInterface
{
    public function findById(int $id): ?Resume;
    public function save(Resume $resume): void;
    public function delete(int $id): void;
}
```

## Monitoring Progress

### Run Tests Regularly
```bash
# Check architecture compliance
docker-compose exec app composer test:architecture

# Watch for improvements
docker-compose exec app ./vendor/bin/pest tests/Architecture --filter=LayerTest
docker-compose exec app ./vendor/bin/pest tests/Architecture --filter=CqrsTest
docker-compose exec app ./vendor/bin/pest tests/Architecture --filter=SolidTest
```

### Track Metrics
- **Failing Tests:** Should decrease week by week
- **PHPStan Level:** Already at 8 ✅
- **Code Coverage:** Target >80%

## Resources

- [DDD Examples](https://github.com/dddinphp)
- [CQRS Journey](https://docs.microsoft.com/en-us/previous-versions/msp-n-p/jj554200(v=pandp.10))
- [Laravel DDD](https://github.com/laravel-beyond-crud)
- [Pest Architecture Testing](https://pestphp.com/docs/arch-testing)

## Notes

**Don't Rush!** 
- Implement gradually, feature by feature
- Keep existing code working during refactoring
- Use feature flags if needed
- Tests guide you, not block you

**Remember:**
- These tests are **guardrails**, not gates
- Failures show **what to build next**
- Architecture evolves with the project
