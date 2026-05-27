# ResumeHaven – Architecture

This document describes the technical architecture of the ResumeHaven MVP.

---

# 🧠 1. Overview

ResumeHaven is a **domain-driven, command/query-oriented** analysis tool.
The architecture follows modern best practices:

- **Domain-Driven Design (DDD)** (modular business areas, bounded contexts)
- **CQRS (Strict Mode)** (Command/Query strictly separated, phased introduction)
- **SOLID principles** (mandatory gate in every commit)
- **Single Action Controllers** (controllers are thin, ~34 lines)
- **Repository Pattern** (Persistence abstraction)
- **UseCase Pattern** (Business Logic Orchestration)
- **Architecture testing (Pest Arch)** (automated layer and boundary validation)
- **Maintainability, Testability, Extensibility**

---

# 🎯 1.1 Architecture Principles

## CQRS (Command Query Responsibility Segregation) — Strict Mode

**Rule:** Commands (Write) and Queries (Read) are **strictly separated**.

### Current status (phased introduction)

#### ✅ Phase 1 (completed)
- Commands implemented: `AnalyzeJobAndResumeCommand`
- Handlers implemented: `AnalyzeJobAndResumeHandler`
- Structure: `app/Domains/Analysis/Commands/` + `Handlers/`

#### 🔄 Phase 2 (in progress)
- Queries for cache accesses: `GetCachedAnalysisQuery`
- Query handler: `GetCachedAnalysisQueryHandler`
- Structure: `app/Domains/Analysis/Queries/` + `Handlers/`

#### ⏳ Phase 3 (planned)
- Switch all read operations to queries
- Reporting queries (`GetAnalysisHistoryQuery`)
- Statistics queries (`GetUserStatisticsQuery`)

### CQRS rules

**Commands:**
- ✅ Change state (Write Operations)
- ✅ Return `void` or confirmation DTO
- ✅ Example: `AnalyzeJobAndResumeCommand` → creates analysis result

**Queries:**
- ✅ Read data (Read Operations)
- ✅ Change **no** state
- ✅ Return DTO or Collection
- ✅ Example: `GetCachedAnalysisQuery` → reads cache entry

---

## DDD (Domain Driven Design)

**Rule:** Code is structured according to technical domains.

### Current Bounded Context

#### `Analysis` (main domain)
- **Responsibility:** Job/CV analysis, matching, gap analysis, scoring, cache
- **Ubiquitous Language:** Requirements, Experiences, Matches, Gaps, Score, Tags
- **Structure:** `app/Domains/Analysis/`
- **Status:** ✅ Fully implemented

#### `Profile` (Commit 22) — ✅ **Basic implemented**
- **Responsibility:** Anonymous CV storage, token management, encryption, recovery
- **Ubiquitous Language:** StoredResume, Token, EncryptedCV, LoadedResume
- **Structure:** `app/Domains/Profile/`
- **Integration:** Independent of `Analysis` (no direct coupling, only CV text as input in the UI)
- **Status:**
  - ✅ CQRS base with `StoreResumeCommand` and `GetResumeByTokenQuery`
  - ✅ Single action controller for store/load
  - ✅ AES-256-GCM with token-based derived secret (MVP compromise)
  - ✅ Persistence via `stored_resumes` + `ProfileRepository`
  - ⚠️ **Technical Debt:** Migration to user-based encryption mandatory before user accounts

### Implemented Bounded Contexts

#### `Recommendations` (Phase 4, ~Commit 17+) — ✅ **Basic structure implemented**
- **Responsibility:** AI recommendations, suggestions for improvement
- **Ubiquitous Language:** Recommendation, Suggestion, Priority, Example
- **Integration:** Part of `Analysis` domain (initially as a subdomain)
- **Status:**
  - ✅ `RecommendationDto` implemented (immutable, typed)
  - ✅ AI promptly expanded (recommendations field)
  - ✅ Parsing logic (ParseAiResponseAction)
  - ✅ UI component (result.blade.php)
  - ⏳ Separate domain extraction planned (~Commit 30+)

### Planned Bounded Contexts (Roadmap)

#### `Reporting` (Phase 5, ~Commit 35+)
- **Responsibility:** Analysis history, statistics, exports
- **Ubiquitous Language:** Report, History, Statistics, Export
- **Integration:** Read-only access to `Analysis` + `Profile`

### DDD rules

- ✅ **Bounded Context Isolation:** No direct dependencies between contexts
- ✅ **Communication:** Only via DTOs, events or shared kernel
- ✅ **Ubiquitous Language:** Code uses technical terms
- ✅ **Aggregate Roots:** Models are aggregate roots of their context

### Architecture Tests (Commit 28) — ✅ Completed

- Test suite: `src/tests/Architecture/`
- `DddArchTest`: Bounded context isolation (`Analysis`/`Profile`)
- `CqrsArchTest`: Command/Query segregation and naming conventions
- `SolidArchTest`: Single action controller, interface contracts, readonly DTOs
- Version: `make test-arch` or `composer run test:pest-arch`

---

# 🧩 2. Main Components (New Architecture)

##2.1 Domain Layers

### **Analysis Domain** (`app/Domains/Analysis/`)

The main domain for job/resume analysis.

#### **Commands** (`Commands/`)
- `AnalyzeJobAndResumeCommand`: Request object for analysis requests
- Contains `handle()` method that calls handler (Laravel Bus Pattern)

#### **Handlers** (`Handlers/`)
- `AnalyzeJobAndResumeHandler`: Orchestrates the entire analysis flow
  1. Check cache
  2. Perform AI analysis
  3. Perform matching
  4. Perform gap analysis
  5. Cache result
  6. Return DTO

#### **UseCases** (`UseCases/`)
Encapsulate reusable business logic:

- **ExtractDataUseCase**: Extracts requirements and experiences
  - `ExtractRequirementsAction`
  - `ExtractExperiencesAction`
  
- **MatchingUseCase**: Finds matches
  - `MatchAction`
  
- **GapAnalysisUseCase**: Identifies gaps
  - `FindGapsAction`

#### **Cache** (`Cache/`)
- **Actions**:
  - `GetCachedAnalysisAction`: Reads from cache
  - `StoreCachedAnalysisAction`: Writes to cache
- **Repositories**:
  - `AnalysisCacheRepository`: Abstracts database access

#### **DTOs** (`Dto/`)
Immutable Data Transfer Objects:
- `ExtractDataResultDto`
- `MatchingResultDto`
- `GapAnalysisResultDto`

---

### **Profile Domain** (`app/Domains/Profile/`)

The domain for anonymous CV storage and recovery.

#### **Commands** (`Commands/`)
- `StoreResumeCommand`: Write request to persist a CV

#### **Queries** (`Queries/`)
- `GetResumeByTokenQuery`: Read request to load a saved CV via token

#### **Handlers** (`Handlers/`)
- `StoreResumeHandler`: Generates unique token, encrypts the CV and persists it
- `GetResumeByTokenHandler`: Loads saved CV, decrypts it and updates `last_accessed_at`

#### **Actions** (`Actions/`)
- `GenerateTokenAction`: Generates URL-safe Base64 tokens from 32 random bytes
- `EncryptResumeAction`: Encrypts CV content via AES-256-GCM
- `DecryptResumeAction`: Decrypts stored CV content in a robust and fault-tolerant manner

#### **Repositories** (`Repositories/`)
- `ProfileRepository`: Abstraction via `stored_resumes` persistence

#### **DTOs** (`Dto/`)
- `StoreResumeDto`, `ResumeTokenDto`, `LoadedResumeDto`
- Immutable (`readonly`) and clearly limited to UI/domain transfer

---

##2.2 Application Layer

### **Controllers** (`app/Http/Controllers/`)

**AnalyzeController** (~34 lines, "thin"):
1. Validation
2. Create command
3. Dispatch command (bus)
4. Return view

**No business logic in the controller!**

### **Services** (`app/Services/`)

Legacy services (will be gradually migrated to domains):
- `AnalyzeApplicationService`: AI integration
- `AnalysisCacheService`: (deprecated, will be replaced by repository)

---

##2.3 Infrastructure Layer

### **Models** (`app/Models/`)
- `AnalysisCache`: Eloquent Model for cached analytics
- `User`: User management (for later)

### **Providers** (`app/Providers/`)
- `AnalysisDomainServiceProvider`: Registers domain dependencies

---

# 🔄 3. Request Flow (New)

```
HTTP POST /analyze
    ↓
AnalyzeController::__invoke()
    ├─ Validation (Laravel Validator)
    ├─ Create DTO (AnalyzeRequestDto)
    ├─ Create Command (AnalyzeJobAndResumeCommand)
    ↓
Bus::dispatch(Command)
    ↓
AnalyzeJobAndResumeCommand::handle(Handler)
    ↓
AnalyzeJobAndResumeHandler::handle()
    ├─ 1. GetCachedAnalysisAction (check cache)
    ├─ 2. AnalyzeApplicationService (AI analysis)
    ├─ 3. MatchingUseCase::handle() (matching)
    ├─ 4. GapAnalysisUseCase::handle() (gap analysis)
    ├─ 5. StoreCachedAnalysisAction (store in cache)
    ↓
AnalyzeResultDto (back to controller)
    ↓
View('result', $data)
```

---

# 🎨 4. Views

- Blade templates
- TailwindCSS
- Minimalist
- Panels for results

---

# 🐳 5. Docker architecture

Services:

- **php-fpm** (PHP 8.5)
- **nginx** (web server)
- **node** (Tailwind Build)
- **mailpit** (local SMTP)

---

# 📦 6. Dependency Management

## Service Provider Registration

`AnalysisDomainServiceProvider` registered:
- Actions (Singleton)
- UseCases (Singleton)
- Repositories (Singleton)
- Handlers (Singleton)

## Dependency Injection

- Constructor injection for critical dependencies
- Laravel Service Container for optional dependencies

---

# 🧪 7. Testing Strategy

## Unit tests
- Testing **Handlers** isolated (Mock Dependencies)
- Test **UseCases** isolated
- Test **Actions** isolated
- **No HTTP layer tests** in unit tests

## Feature testing
- Testing **complete HTTP requests**
- Testing **integration** of all components
- Mock only external services (AI)

## Test coverage
- **Minimum:** 95% (enforced via `make test-coverage`)
- **Current:** 98.2% ✅
- **Tests:** 128 (100+ Unit, 20+ Feature)
- **Assertions:** 335+

## Testing framework
- **Plague 3** (Primary Framework)
- **PHPUnit 11** (Underlying)
- **Mockery** (Mocking)

## Quality gates
- ✅ **PHPStan:** Level 9, 0 Errors
- ✅ **Pint:** PSR-12 + Laravel Style
- ✅ **Coverage:** ≥95%
- ✅ **Tests:** All green

---

# 🚫 8. Not included in MVP

- no events/listeners (planned for later)
- no API endpoints (web UI only)
- no PDF generation
- no accounts/authentication
- no email sending (only mailpit for development)

---

# 📌 9. Design Principles

## SOLID
- **S**ingle Responsibility: Each class only has one task
- **O**pen/Closed: Expandable without modification
- **L**iskov Substitution: Interfaces are respected
- **I**interface Segregation: Small, focused interfaces
- **D**ependency Inversion: Dependencies on abstractions

## Interface based design
- **"Program to an Interface, not an Implementation"**
- Dependencies on interfaces instead of on concretions
- Interchangeability across service providers
- Testability through mocking
- **Examples in the project:**
  - `AiAnalyzerInterface` (Gemini, Mock)
  - `CacheRepositoryInterface` (planned: Database, Redis)

## DRY (Don't Repeat Yourself)
- Reusable actions
- Central DTOs
- Repository Pattern

##KISS (Keep It Simple, Stupid)
- Clear naming conventions
- Understandable structure
- No over engineering

---

# 🔒 9. Security Architecture

##9.1 Input validation

### ValidateInputAction
- **Location:** `app/Domains/Analysis/UseCases/ValidateInputUseCase/`
- **Responsibility:** Input validation with security checks
- **Checks:**
  - ✅ Minimum length (30 characters)
  - ✅ Maximum length (50,000 characters)
  - ✅ Prompt injection pattern detection
  - ✅ SQL injection pattern detection
  - ✅ Input sanitization

### PatternDetector & InputSanitizer
- **Location:** `app/Domains/Analysis/UseCases/ValidateInputUseCase/Validators/` & `Sanitizers/`
- **Patterns:** SQL injection, prompt injection, control characters

## 9.2 AI-Prompt-Security (Commit 18a)
- ✅ Explicit anti-prompt injection instructions
- ✅ JSON schema based response validation
- ✅ Type guards in ParseAiResponseAction

##9.3 CSRF & SQL Injection Prevention
- ✅ `@csrf` tokens in forms + security tests
- ✅ Repository Pattern with Eloquent (Prepared Statements)

## 9.4 Error handling
- ✅ AI timeouts, invalid responses caught
- ✅ User-friendly error messages

##9.5 Security tests
- `SecurityAuditTest.php`, `ApiErrorHandlingTest.php`, `ValidateInputActionTest.php`, `ProfileResumeStorageTest.php`

---

# 🔮 10. Future / Extensibility

## Further development of existing bounded contexts

### `Profile` Context (next expansion level)
- **Status:** Base implemented in commit 22
- **Next features:**
  - User accounts
  - multiple saved CVs per user
  - Preferences/profile metadata
  - Migration to user-based encryption
- **Integration:** Continued loose coupling to `Analysis` via DTOs/UI flows

### `Recommendations` Context
- **Commit:** ~30+
- **Features:**
  - AI recommendations as an independent context
  - Suggestions for improvement
  - Example formulations
- **Structure:** `app/Domains/Recommendations/`
- **Integration:** Consumes `Analysis` results

### `Reporting` Context
- **Commit:** ~35+
- **Features:**
  - Analysis history
  - statistics
  - PDF/Word export
- **Structure:** `app/Domains/Reporting/`
- **Integration:** Read-only on `Analysis` + `Profile`

## More extensions
1. **Events & Listeners** (according to MVP)
2. **API layer** (RESTful API)
3. **Queue Processing** (Async AI analyses)
4. **Multi-Tenancy** (later, if product vision supports it)

## How to expand?
- **Add new domain:** `app/Domains/NewDomain/`
  - Commands, queries, handlers, use cases, DTOs
- **Add new UseCase:** In existing domain
- **Add new Action:** In UseCase subfolder
- **New Command/Query:** With its own handler
- **Context Integration:** Via DTOs, Events or Shared Kernel

---

# 📖 11. Coding Guidelines

See `CODING_GUIDELINES.md` for detailed best practices.

---

# 🎯 12. Goal of architecture

- **Clarity**: Each component has clear responsibilities
- **Simplicity**: No unnecessary complexity
- **Expandability**: Easily add new features
- **Testability**: Each component can be tested in isolation
- **Stability**: Robust error handling
- **Performance**: Caching, optimized DB queries

