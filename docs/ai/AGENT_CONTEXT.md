# 🤖 Agent Context – Central Working Rules

This file is the **Single Source of Truth** for all AI agents (GitHub Copilot, Windsurf, etc.).

> Session start point / soft reset: `docs/ai/WORKING_BASELINE.md`
> 
> Use this baseline for the current daily context and treat the repository status as binding.

---

## 🎯 Architecture principles (required)

### 1. CQRS (Command Query Responsibility Segregation) — Strict Mode

**Rule:** Commands and queries are **strictly separated**.

#### Commands (Write Operations)
- **Purpose:** State changes
- **Return:** `void` or confirmation DTO
- **Example:** `AnalyzeJobAndResumeCommand` → `AnalyzeJobAndResumeHandler`
- **Structure:**
  ```php
  app/Domains/{Context}/Commands/     // Command-DTOs
  app/Domains/{Context}/Handlers/     // Command-Handler
  ```

#### Queries (Read Operations)
- **Purpose:** Read data, no state changes
- **Return:** DTO or Collection
- **Example:** `GetCachedAnalysisQuery` → `GetCachedAnalysisHandler`
- **Structure:**
  ```php
  app/Domains/{Context}/Queries/      // Query-DTOs
  app/Domains/{Context}/Handlers/     // Query-Handler
  ```

#### Phased introduction (current status)
- ✅ **Phase 1 (complete):** Commands + Handlers implemented
- 🔄 **Phase 2 (in progress):** Queries + query handler for cache accesses
- ⏳ **Phase 3 (planned):** Switch all read operations to queries

**DoD for CQRS Compliance:**
- [ ] Command has no return value except confirmation DTO
- [ ] Query does not change state
- [ ] Handler is in correct folder (`Commands/` or `Queries/`)

---

### 2. SOLID Principles — Mandatory Review Gate

**Rule:** Every commit MUST adhere to SOLID principles.

#### Single Responsibility Principle (SRP)
- ✅ One class = One responsibility
- ✅ Methods < 20 lines
- ✅ Classes < 200 lines
- ✅ Cyclomatic Complexity < 5

**Example (good):**
```php
class CalculateScoreAction {
    public function execute(array $matches, array $gaps): ScoreResultDto { }
}
```

**Example (bathroom):**
```php
class AnalyzeController {
    public function analyze() {
        // Validation
        // AI-Aufruf
        // Score-Berechnung
        // View-Building
        // → Zu viele Verantwortlichkeiten!
    }
}
```

#### Open/Closed Principle (OCP)
- ✅ Expandable without modification
- ✅ Use interfaces (e.g. `AiAnalyzerInterface`)
- ✅ Strategy Pattern for interchangeable components

#### Liskov Substitution Principle (LSP)
- ✅ Interfaces must be interchangeable
- ✅ No breaking changes in subtypes

#### Interface Segregation Principle (ISP)
- ✅ Small, focused interfaces
- ✅ Clients should not depend on unused methods

#### Dependency Inversion Principle (DIP)
- ✅ Dependencies on abstractions (interfaces), not on concretions
- ✅ Constructor Injection for all dependencies

**SOLID Gate checklist (in every PR):**
- [ ] SRP: Each class/method has only one responsibility
- [ ] OCP: New features without changing existing classes
- [ ] LSP: Interfaces are correctly interchangeable
- [ ] ISP: No "fat" interfaces
- [ ] DIP: Dependencies via constructor injection

---

### 3. Domain Driven Design (DDD)

**Rule:** Code is structured according to technical domains.

#### Current Bounded Context
- **`Analysis`** (main domain)
  - Job/CV analysis
  - Matching & gap analysis
  - Scoring
  - Cache management

####Structure
```
app/Domains/Analysis/
├── Commands/        # CQRS Commands
├── Queries/         # CQRS Queries (geplant)
├── Handlers/        # Command/Query-Handler
├── UseCases/        # Business-Logic (orchestriert Actions)
│   ├── ValidateInputUseCase/
│   ├── ExtractDataUseCase/
│   ├── MatchingUseCase/
│   ├── GapAnalysisUseCase/
│   ├── ScoringUseCase/
│   └── GenerateTagsUseCase/
├── Cache/           # Cache-Layer
│   ├── Actions/
│   └── Repositories/
└── Dto/             # Data Transfer Objects (immutable)
```

#### Planned Bounded Contexts (Roadmap)
- **`Profile`** (Phase 3) — CV storage, user preferences
- **`Recommendations`** (Phase 4) — AI recommendations, suggestions for improvement
- **`Reporting`** (Phase 5) — Analysis history, statistics, exports

**DDD compliance checklist:**
- [ ] Code is in correctly bounded context (`app/Domains/{Context}/`)
- [ ] No cross-context dependencies (except via events/DTOs)
- [ ] Ubiquitous Language used in code names

---

### 4. Interface-based design

**Rule:** "Program to an Interface, not an Implementation"

#### Why interfaces?
- ✅ **Interchangeability:** Implementations can be changed without code changes
- ✅ **Testability:** Interfaces can be easily mocked
- ✅ **Dependency Inversion:** High-level modules dependent on abstractions
- ✅ **Open/Closed:** New implementations without changing existing classes

#### When to create an interface?

**YES — Create interface if:**
- ✅ Several implementations exist (e.g. `GeminiAiAnalyzer`, `MockAiAnalyzer`)
- ✅ Implementation should be interchangeable (e.g. cache provider, AI provider)
- ✅ External dependencies (e.g. API calls, database)
- ✅ Strategy pattern is needed

**NO — No interface if:**
- ❌ Only one implementation exists and no others are planned
- ❌ Pure Data Objects (DTOs)
- ❌ Simple actions without external dependencies
- ❌ Laravel framework classes (Controllers, Models)

#### Example (good)

```php
// Interface definieren
interface AiAnalyzerInterface {
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto;
    public function isAvailable(): bool;
    public function getProviderName(): string;
}

// Implementierungen
class GeminiAiAnalyzer implements AiAnalyzerInterface { }
class MockAiAnalyzer implements AiAnalyzerInterface { }
class OpenAiAnalyzer implements AiAnalyzerInterface { }  // Zukünftig

// Consumer verwendet Interface
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private AiAnalyzerInterface $aiAnalyzer,  // Interface, nicht Konkretion!
    ) {}
}
```

#### Example (bad)

```php
// ❌ SCHLECHT: Direkte Dependency auf Konkretion
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private GeminiAiAnalyzer $geminiAnalyzer,  // Konkrete Klasse!
    ) {}
}

// Problem: Handler ist jetzt fest an Gemini gekoppelt
// → Kann nicht einfach zu MockAiAnalyzer wechseln
// → Schwer zu testen (kein Mocking möglich)
```

#### Naming Convention

| Interface | Convention | Example |
|-----------|------------|----------|
| **Service/Provider** | `{Noun}Interface` | `AiAnalyzerInterface` |
| **Repository** | `{Noun}RepositoryInterface` | `CacheRepositoryInterface` |
| **Strategy** | `{Noun}StrategyInterface` | `ScoringStrategyInterface` |

**NOT:** `I{Noun}` (C# style) or `{Noun}Contract` (Laravel old)

#### Interface checklist

- [ ] Interface is located in `Contracts/` subfolder
- [ ] Method signatures fully typed
- [ ] PHPDoc with `@return` for complex types
- [ ] At least 2 implementations (current or planned)
- [ ] Interface name ends in `Interface`

#### Current interfaces in the project

✅ **Available:**
- `AiAnalyzerInterface` (Gemini, Mock)

⏳ **Planned (Roadmap):**
- `CacheRepositoryInterface` (Database, Redis, Memory)
- `ScoringStrategyInterface` (Simple, Weighted, ML-based)
- `RecommendationProviderInterface` (AI, rule-based)

---

## ✅ Quality gates (mandatory)

### Test coverage
- **Minimum:** 99% total coverage
- **Current:** 98.2% ✅
- **GeminiAiAnalyzer:** ≥80%
- **Exam:** `make test-coverage`

### PHPStan
- **Level:** 9 (strict)
- **Errors:** 0
- **Exam:** `make phpstan`

### Pint (code formatting)
- **Rule:** Run after every PHP change
- **Command:** `vendor/bin/pint --dirty --format agent`
- **Exam:** `make pint-analyse`

### Tests
- **Mandatory:** Every change requires testing
- **Framework:** Plague 3
- **Types:** Unit + Feature
- **Exam:** `make test`

### OWASP compliance
- **Rule:** Security-relevant changes must be checked in an OWASP-oriented manner
- **At least:** Input validation, output encoding, AuthZ/CSRF, secret handling
- **Test:** Security tests + review against OWASP Top 10

---

## 🚫 Forbidden Patterns

### ❌ God Objects
```php
// SCHLECHT
class AnalyzeController {
    public function analyze() {
        // 200+ Zeilen Code
    }
}
```

### ❌ Raw SQL outside of repositories
```php
// SCHLECHT
DB::table('analysis_cache')->where(...)->get();

// GUT
$this->cacheRepository->getByHash($hash);
```

### ❌ `env()` outside of config files
```php
// SCHLECHT
$apiKey = env('GEMINI_API_KEY');

// GUT
$apiKey = config('ai.gemini.api_key');
```

### ❌ Mutable DTOs
```php
// SCHLECHT
class AnalyzeRequestDto {
    public string $jobText;
}

// GUT
readonly class AnalyzeRequestDto {
    public function __construct(
        public readonly string $jobText,
    ) {}
}
```

### ❌ Mixed responsibilities
```php
// SCHLECHT: Validation + Business Logic gemischt
class AnalyzeController {
    public function analyze(Request $request) {
        // Validation
        $validated = $request->validate([...]);
        
        // Business Logic
        $result = $this->engine->analyze(...);
        
        // → Trennen in ValidateInputAction + Handler
    }
}
```

---

## 📋 Definition of Done (DoD)

Every commit is only “Done” when:

1. ✅ **Tests:** All tests green (plague)
2. ✅ **Coverage:** ≥99%
3. ✅ **PHPStan:** Level 9, 0 Errors
4. ✅ **Pint:** Code formatting clean
5. ✅ **SOLID:** All SOLID principles adhered to
6. ✅ **CQRS:** Commands/Queries separated correctly
7. ✅ **DDD:** Code in the correctly bounded context
8. ✅ **Documentation:** PHPDoc for all public methods

---

## 🔍 Code review checklist

###Architecture
- [ ] SOLID principles adhered to?
- [ ] CQRS: Commands/Queries separated correctly?
- [ ] DDD: Correct Bounded Context?
- [ ] Single action controller (`__invoke()`)?
- [ ] Immutable DTOs (`readonly`)?

### Code quality
- [ ] PHPStan Level 9: 0 Errors?
- [ ] Pint: Code formatting clean?
- [ ] Methods < 20 lines?
- [ ] Classes < 200 lines?
- [ ] Cyclomatic Complexity < 5?

### Tests
- [ ] Unit tests available?
- [ ] Feature testing available?
- [ ] Edge cases tested?
- [ ] Coverage ≥99%?

### Security (OWASP)
- [ ] Input treated as untrusted?
- [ ] Output contextually escaped/encoded?
- [ ] AuthN/AuthZ/CSRF taken into account?
- [ ] No secrets in the code?
- [ ] Security testing for security-relevant changes?

### Documentation
- [ ] PHPDoc for public methods?
- [ ] Complex logic commented?
- [ ] README/Docs updated (if necessary)?

---

## 📚 See then

- **Project overview:** `docs/ai/PROJECT_OVERVIEW.md`
- **Tech Stack:** `docs/ai/TECH_STACK.md`
- **Architecture:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Commit plan:** `COMMIT_PLAN.md`
- **Roadmap:** `docs/ROADMAP.md`
- **Changelog:** `CHANGELOG.md`
- **Laravel Boost:** `src/AGENTS.md`

---

**Last updated**: 2026-03-09
**Version**: 2.1 (reference to WORKING_BASELINE as session starting point)
