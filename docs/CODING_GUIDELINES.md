# ResumeHaven – Coding Guidelines

This document defines the coding standards and best practices for the ResumeHaven project.

---

## 📋 Table of Contents

1. [General Principles](#1-general-principles)
2. [Project Structure](#2-project-structure)
3. [Naming Conventions](#3-naming conventions)
4. [Domain Driven Design](#4-domain-driven-design)
5. [Commands & Handlers](#5-commands--handlers)
6. [UseCases & Actions](#6-usecases--actions)
7. [DTOs (Data Transfer Objects)](#7-dtos-data-transfer-objects)
8. [Repositories](#8-repositories)
9. [Controllers](#9-controllers)
10. [Testing](#10-testing)
11. [Code Quality](#11-code-quality)
12. [Error Handling](#12-error-handling)

---

##1. General principles

### SOLID Principles

We follow the **SOLID principles**:

- **S**ingle Responsibility Principle: One class = one responsibility
- **O**pen/Closed Principle: Open to expansion, closed to change
- **L**iskov Substitution Principle: Subtypes must be interchangeable
- **I**interface Segregation Principle: Small, focused interfaces
- **D**ependency Inversion Principle: Dependencies on abstractions

### OWASP Security by Design

Security requirements are **mandatory** and are implemented according to OWASP recommendations.

- Always treat input as untrusted
- Escape/encode output context-aware
- Strictly separate and check AuthN/AuthZ
- Secrets never in code, only via Config/Env
- Security regression tests for security-relevant changes
- Explicitly warn off prompt/input injection

#### OWASP Mapping (Top 10 -> Project Rules)

| OWASP Top 10 | Risk in the project | Binding measure | Test/Review Check |
|---|---|---|---|
| A01 Broken Access Control | Unauthorized access to endpoints | Route/policy check, no implicit admin paths | Feature testing for allowed/denied access |
| A02 Cryptographic Failures | Insecure storage/transmission | No secrets in the code, secure defaults, HTTPS in Prod | Secret Scan + Config Review |
| A03 Injection | SQL/XSS/Prompt Injection | Input validation, prepared statements, output encoding, prompt hardening | Security tests (SQL/XSS/Prompt patterns) |
| A04 Insecure Design | Lack of security requirements | Threat-aware design in use cases + review checklist | PR check against OWASP table |
| A05 Security Misconfiguration | Unsafe defaults | Safe env/config values, debug locally only | Config review + smoke tests |
| A06 Vulnerable Components | Vulnerable Dependencies | Regular updates via Renovate + CVE checks | Dependency update PRs + CVE report |
| A07 Identification/Auth Failures | Weak auth mechanics | Laravel Auth/Policies, no self-made auth | Feature testing for Login/Authorization |
| A08 Software/Data Integrity Failures | Manipulated dependencies/builds | Lock files, reproducible builds, signed releases (later) | CI checks for lock file changes |
| A09 Logging/Monitoring Failures | Security incidents undetected | Structured security logs without secrets | Log review in security testing |
| A10 SSRF | External calls to internal targets | Whitelisting/timeouts for external requests | Blocked Target Hosts Tests |

### DRY (Don't Repeat Yourself)

- No code duplication
- Create reusable components
- Use central DTOs and actions

### KISS (Keep It Simple, Stupid)

- Avoid over engineering
- Favor clear, understandable solutions
- Complexity only where necessary

---

##2. Project structure

### Domain structure

```
app/Domains/
└── {DomainName}/
    ├── Commands/        # Request-Objekte (Write)
    ├── Handlers/        # Command-Handler
    ├── Queries/         # Query-Objekte (Read) - für später
    ├── QueryHandlers/   # Query-Handler - für später
    ├── UseCases/        # Business-Logic-Orchestrierung
    │   └── {UseCaseName}/
    │       ├── Actions/     # Granulare Business-Logic
    │       └── Contracts/   # Interfaces
    ├── Dto/             # Data Transfer Objects
    ├── Models/          # Eloquent Models (falls Domain-spezifisch)
    ├── Cache/           # Cache-Layer
    │   ├── Actions/
    │   └── Repositories/
    └── Events/          # Domain Events (für später)
```

### Why this structure?

- **Domain Focused**: Business logic is organized into domains
- **UseCase-oriented**: Reusable business logic
- **Testable**: Each component can be tested in isolation
- **Scalable**: Easily add new domains

---

##3. Naming conventions

### Files & Classes

| Type | Naming convention | Example |
|-----|------------------|----------|
| **Command** | `{Verb}{Noun}Command` | `AnalyzeJobAndResumeCommand` |
| **Handler** | `{Command}Handler` | `AnalyzeJobAndResumeHandler` |
| **Query** | `Get{Noun}Query` | `GetAnalysisResultQuery` |
| **UseCase** | `{Noun}UseCase` | `MatchingUseCase` |
| **Action** | `{Verb}{Noun}Action` | `FindGapsAction` |
| **DTO** | `{Noun}Dto` | `AnalyzeResultDto` |
| **Repository** | `{Noun}Repository` | `AnalysisCacheRepository` |
| **Controller** | `{Noun}Controller` | `AnalyzeController` |
| **Model** | `{Noun}` | `AnalysisCache` |

###Methods

| Type | Naming convention | Example |
|-----|------------------|----------|
| **Command/Query Handler** | `handle()` | `public function handle(Command $cmd)` |
| **Action** | `execute()` | `public function execute(array $data)` |
| **Repository** | CRUD verbs | `getByHash()`, `store()`, `update()` |

### Variables

- **camelCase** for variables: `$jobText`, `$analyzeResult`
- **snake_case** for database fields: `job_text`, `request_hash`
- **PascalCase** for classes: `AnalyzeController`

---

##4. Domain Driven Design

### Create new domain

**Step 1**: Create directory structure

```bash
mkdir -p app/Domains/NewDomain/{Commands,Handlers,UseCases,Dto,Cache}
```

**Step 2**: Create command

```php
<?php
namespace App\Domains\NewDomain\Commands;

class DoSomethingCommand
{
    public function __construct(
        public readonly string $data,
    ) {}

    public function handle(DoSomethingHandler $handler): ResultDto
    {
        return $handler->handle($this);
    }
}
```

**Step 3**: Create handler

```php
<?php
namespace App\Domains\NewDomain\Handlers;

class DoSomethingHandler
{
    public function __construct(
        private SomeAction $action,
    ) {}

    public function handle(DoSomethingCommand $command): ResultDto
    {
        // Orchestriere Actions
        $result = $this->action->execute($command->data);
        return new ResultDto($result);
    }
}
```

**Step 4**: Register service providers

```php
// In AnalysisDomainServiceProvider oder neuem Provider
$this->app->singleton(DoSomethingHandler::class);
$this->app->singleton(SomeAction::class);
```

---

##5. Commands & Handlers

### Command pattern

**Commands are immutable request objects:**

```php
class AnalyzeJobAndResumeCommand
{
    public function __construct(
        public readonly AnalyzeRequestDto $request,
        public readonly bool $demoMode = false,
    ) {}

    public function handle(AnalyzeJobAndResumeHandler $handler): AnalyzeResultDto
    {
        return $handler->handle($this);
    }
}
```

### Handler pattern

**Handlers orchestrate UseCases:**

```php
class AnalyzeJobAndResumeHandler
{
    public function __construct(
        private MatchingUseCase $matchingUseCase,
        private GapAnalysisUseCase $gapAnalysisUseCase,
        private GetCachedAnalysisAction $getCachedAnalysis,
        private StoreCachedAnalysisAction $storeCachedAnalysis,
        private AnalyzeApplicationService $analyzeService,
    ) {}

    public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto
    {
        // 1. Check cache
        // 2. Execute business logic
        // 3. Cache result
        // 4. Return DTO
    }
}
```

**Best Practices:**

✅ **DO**:
- Handler only orchestrates (no business logic code)
- Dependencies via constructor injection
- Comment on clear steps
- Return DTO

❌ **DON'T**:
- No direct DB queries in the handler
- No business logic in the handler
- No Service Locator Pattern (avoid `app()->make()`)

---

## 6. UseCases & Actions

### UseCase pattern

**UseCases encapsulate reusable business logic:**

```php
class MatchingUseCase
{
    public function __construct(
        private MatchAction $matchAction,
    ) {}

    public function handle(array $requirements, array $experiences): MatchingResultDto
    {
        $matches = $this->matchAction->execute($requirements, $experiences);
        return new MatchingResultDto($matches);
    }
}
```

### Action patterns

**Actions are granular, reusable logic:**

```php
class MatchAction
{
    /**
     * @param array<int, string> $requirements
     * @param array<int, string> $experiences
     * @return array<int, array{requirement: string, experience: string}>
     */
    public function execute(array $requirements, array $experiences): array
    {
        // Implementierung der Match-Logik
        return $matches;
    }
}
```

**Best Practices:**

✅ **DO**:
- One action = one task
- Type hints for all parameters and return types
- PHPDoc for complex array types
- Pure functions where possible (no side effects)

❌ **DON'T**:
- No God actions (too much responsibility)
- No direct DB queries (use repository)
- No service calls (inject dependencies)

---

##7. DTOs (Data Transfer Objects)

### DTO pattern

**DTOs are immutable data containers:**

```php
readonly class AnalyzeResultDto
{
    /**
     * @param array<int, string> $requirements
     * @param array<int, string> $experiences
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string> $gaps
     */
    public function __construct(
        public string $jobText,
        public string $cvText,
        public array $requirements,
        public array $experiences,
        public array $matches,
        public array $gaps,
        public ?string $error = null,
    ) {}

    public function toArray(): array
    {
        return [
            'job_text' => $this->jobText,
            'cv_text' => $this->cvText,
            'requirements' => $this->requirements,
            'experiences' => $this->experiences,
            'matches' => $this->matches,
            'gaps' => $this->gaps,
            'error' => $this->error,
        ];
    }
}
```

**Best Practices:**

✅ **DO**:
- Use `readonly class` or `public readonly` properties
- Add PHPDoc for complex array types
- `toArray()` Provide method for transport/serialization only
- Only use `fromArray()` where external data needs to be safely typed
- Keep DTOs small and use case specific (e.g. `StoreResumeDto`, `LoadedResumeDto`)

❌ **DON'T**:
- No setters (immutable!)
- No business logic in the DTO
- No persistence or infrastructure logic in the DTO
- No validation in the DTO other than minimal type checks

### DTO rule for new features

- Commands, queries and handlers prefer to communicate via DTOs.
- Views do not receive domain models directly, but rather view data or DTO-converted arrays.
- For sensitive data, always explicitly specify which fields are transported.

---

##8. Repositories

### Repository pattern

**Repositories abstract persistence:**

```php
class AnalysisCacheRepository
{
    /**
     * @return array{...}|null
     */
    public function getByHash(string $hash): ?array
    {
        $entry = AnalysisCache::where('request_hash', $hash)->first();
        return $entry?->result;
    }

    public function store(string $hash, string $jobText, string $cvText, array $result): void
    {
        AnalysisCache::updateOrCreate(
            ['request_hash' => $hash],
            ['job_text' => $jobText, 'cv_text' => $cvText, 'result' => $result]
        );
    }
}
```

**Best Practices:**

✅ **DO**:
- Clear method names (CRUD-oriented)
- Type hints for all parameters
- Eloquent queries only in the repository
- Exceptions are thrown on errors

❌ **DON'T**:
- No business logic in the repository
- No complex joins (rather several queries)
- No raw SQL (use eloquently)

---

##9. Controllers

### Single Action Controllers

**Controllers are thin and delegate to actions, commands or queries:**

```php
class StoreResumeController extends Controller
{
    public function __invoke(StoreResumeRequest $request, Dispatcher $dispatcher): RedirectResponse
    {
        $cvText = $request->validated('cv_text');

        /** @var ResumeTokenDto $tokenDto */
        $tokenDto = $dispatcher->dispatch(
            new StoreResumeCommand(new StoreResumeDto($cvText))
        );

        return redirect()->route('analyze')
            ->with('resume_token', $tokenDto->token);
    }
}
```

**Best Practices:**

✅ **DO**:
- Use `__invoke()` for HTTP endpoints with a clear task
- Keep validation in the form request or in the controller entry
- Use commands/queries/actions for business logic
- Document type hint for `dispatch()` return
- Only assemble redirects/views/responses

❌ **DON'T**:
- No business logic in the controller
- No direct DB queries in the controller
- No cryptographic operations in the controller
- No complex transformations or fallback orchestration in the controller

### Security rules for token-based storage

- Tokens must be generated with `random_bytes()` and encoded in a URL-safe manner.
- Sensitive content may never be persisted in plain text.
- Cryptography belongs in dedicated actions, not in controllers or views.
- MVP exceptions such as “Token as Secret” must be explicitly documented and migrated later.
- Decryption errors or invalid tokens must be handled securely and in a user-friendly manner.

---

##10. Testing

###Test strategy

**3 test types:**

1. **Unit testing**: Test individual components in isolation
2. **Feature testing**: Test HTTP requests end-to-end
3. **Integration testing**: Test the interaction of several components

### Unit test patterns

```php
it('MatchingUseCase findet korrekte Matches', function () {
    $mockAction = Mockery::mock(MatchAction::class);
    $mockAction->shouldReceive('execute')->andReturn([
        ['requirement' => 'PHP', 'experience' => 'PHP Developer']
    ]);
    
    $useCase = new MatchingUseCase($mockAction);
    $result = $useCase->handle(['PHP'], ['PHP Developer']);
    
    expect($result->matches)->toHaveCount(1);
    expect($result->matches[0]['requirement'])->toBe('PHP');
});
```

### Feature Test Pattern

```php
test('POST /analyze shows analysis result', function () {
    $response = post('/analyze', [
        'job_text' => 'PHP Developer wanted',
        'cv_text' => '5 years PHP experience',
    ]);
    
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('result');
});
```

**Best Practices:**

✅ **DO**:
- Mock external services (AI, API)
- Use RefreshDatabase for DB testing
- Test happy path AND error cases
- Descriptive test names

❌ **DON'T**:
- No testing for framework code
- No tests for getters/setters
- No tests for trivial logic

---

##11. Code quality

### PHPStan (Level 9)

We use **PHPStan Level 9** (strictest level):

```bash
make phpstan
```

**What PHPStan checks:**

- Type safety (all parameters/returns typed)
- Unused Variables
- Dead code
- Possible null pointers
- Array key existence

### Laravel Pint (code style)

We use **Laravel Pint** for consistent code style:

```bash
make pint-fix    # Auto-fix
make pint-analyse # Check only (no fix)
```

**What Pint checks:**

- PSR-12 standard
- Laravel Conventions
- Import sorting
- Spacing & indentation

### Best practices

✅ **DO**:
- `declare(strict_types=1);` in every PHP file
- Type hints for all properties, parameters, returns
- PHPDoc for complex array types
- Readonly properties where possible

❌ **DON'T**:
- No `@phpstan-ignore` without comment
- No `mixed` Types (be specific)
- No suppress warnings

---

##12. Error handling

### Exception handling

**Strategy:**

1. **Try-Catch in Handlers** (not in Actions)
2. **Throw specific exceptions**
3. **Return DTOs with Error property**

###Examples

```php
class AnalyzeJobAndResumeHandler
{
    public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto
    {
        try {
            $analyzeResult = $this->analyzeService->analyze($command->request);
            
            // Propagate error from service
            if ($analyzeResult->error !== null) {
                return $analyzeResult;
            }
            
            // ... further processing
        } catch (\Throwable $e) {
            // Fallback DTO with error
            return new AnalyzeResultDto(
                $command->request->jobText(),
                $command->request->cvText(),
                [], [], [], [],
                'AI analysis failed: ' . $e->getMessage()
            );
        }
    }
}
```

**Best Practices:**

✅ **DO**:
- Catch generic exceptions (`\Throwable`)
- Log error context
- User-friendly error messages
- Return fallback values

❌ **DON'T**:
- No empty catch blocks
- No exception suppression
- No technical details to users

---

## 📚 More resources

- **Laravel Docs**: https://laravel.com/docs
- **PHPStan**: https://phpstan.org/
- **SOLID Principles**: https://en.wikipedia.org/wiki/SOLID
- **DDD**: https://martinfowler.com/tags/domain%20driven%20design.html
- **CQRS**: https://martinfowler.com/bliki/CQRS.html

---

## 🤖 AI Agent Specific Rules

### Test enforcement
- **Every change requires testing** (Plague 3)
- After code changes: `php artisan test --compact` or `make test`
- At least feature tests, ideally also unit tests
- Coverage minimum: **99%**

### Pint formatting
- **After each PHP change:** `vendor/bin/pint --dirty --format agent`
- Or via Makefile: `make pint-fix`
- Formatting is not optional, but mandatory

### PHPStan validation
- **Level 9 is mandatory**
- Do not introduce new errors
- Update baseline if necessary: ​​`vendor/bin/phpstan analyse --generate-baseline`
- Command: `make phpstan`

### Coverage gate
- **Minimum:** 99% total coverage
- **GeminiAiAnalyzer.php:** ≥80%
- Run tests before commit: `make test-coverage`
- HTML report: `make test-coverage-report && make coverage-open`

### Documentation
- Only create documentation files upon explicit request
- PHPDoc for all public methods
- Commenting on complex logic (why, not what)

---

## 🛡️ Architecture Enforcement

### SOLID Gate (mandatory review)

Every commit and every PR MUST adhere to the SOLID principles.

#### Single Responsibility Principle (SRP)
**Checklist:**
- [ ] Each class only has one responsibility
- [ ] Methods are < 20 lines
- [ ] Classes are < 200 lines
- [ ] Cyclomatic Complexity < 5

**Example (good):**
```php
// One class = one responsibility
class CalculateScoreAction {
    public function execute(array $matches, array $gaps): ScoreResultDto {
        $total = count($matches) + count($gaps);
        if ($total === 0) return new ScoreResultDto(0, 'No data', ...);
        
        $percentage = (int) round((count($matches) / $total) * 100);
        return new ScoreResultDto($percentage, $this->getRating($percentage), ...);
    }
}
```

**Example (bathroom):**
```php
// Zu viele Verantwortlichkeiten!
class AnalyzeController {
    public function analyze(Request $request) {
        // Validation
        $validated = $request->validate([...]);
        
        // AI-Aufruf
        $aiResult = $this->gemini->analyze(...);
        
        // Score-Berechnung
        $score = ($matches / ($matches + $gaps)) * 100;
        
        // View-Building
        return view('result', [...]);
    }
}
```

#### Open/Closed Principle (OCP)
**Checklist:**
- [ ] New features without changing existing classes
- [ ] Interfaces for interchangeable components
- [ ] Strategy Pattern for different implementations

**Example:**
```php
// Interface definieren
interface AiAnalyzerInterface {
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto;
}

// Austauschbare Implementierungen
class GeminiAiAnalyzer implements AiAnalyzerInterface { }
class MockAiAnalyzer implements AiAnalyzerInterface { }

// Service Provider bindet je nach Config
$this->app->bind(AiAnalyzerInterface::class, function ($app) {
    return match(config('ai.provider')) {
        'gemini' => $app->make(GeminiAiAnalyzer::class),
        'mock' => $app->make(MockAiAnalyzer::class),
    };
});
```

#### Liskov Substitution Principle (LSP)
**Checklist:**
- [ ] Interfaces are interchangeable without breaking changes
- [ ] Subtypes comply with interface contract
- [ ] No exception changes in subtypes

#### Interface Segregation Principle (ISP)
**Checklist:**
- [ ] Interfaces are small and focused
- [ ] No "fat" interfaces with many methods
- [ ] Clients depend only on required methods

#### Dependency Inversion Principle (DIP)
**Checklist:**
- [ ] Dependencies via constructor injection
- [ ] Dependencies on abstractions (interfaces), not on concretions
- [ ] No `new` in business logic (except DTOs)

**Example:**
```php
// ✅ GUT: Dependency zu Interface
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private AiAnalyzerInterface $aiAnalyzer,  // Interface!
        private MatchingUseCase $matchingUseCase,
    ) {}
}

// ❌ BAD: Dependency on concrete class
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private GeminiAiAnalyzer $geminiAnalyzer,  // Concrete class!
    ) {}
}
```

---

### Interface-based Design (Program to an Interface)

**Basic principle:** Code should be programmed against abstractions (interfaces), not against concretions.

#### When to create an interface?

**✅ YES — Create interface:**
- Several implementations exist or are planned
- Implementation should be interchangeable
- External dependencies (API, DB, cache)
- Strategy pattern is needed
- Unit tests with mocks are necessary

**Example:**
```php
// Interface
interface CacheRepositoryInterface {
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl): void;
    public function has(string $key): bool;
    public function delete(string $key): void;
}

// Implementierungen
class DatabaseCacheRepository implements CacheRepositoryInterface { }
class RedisCacheRepository implements CacheRepositoryInterface { }
class MemoryCacheRepository implements CacheRepositoryInterface { }

// Service Provider bindet je nach Umgebung
$this->app->bind(CacheRepositoryInterface::class, function ($app) {
    return match(config('cache.driver')) {
        'redis' => $app->make(RedisCacheRepository::class),
        'database' => $app->make(DatabaseCacheRepository::class),
        'array' => $app->make(MemoryCacheRepository::class),
    };
});

// Consumer nutzt Interface
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private CacheRepositoryInterface $cache,  // Austauschbar!
    ) {}
}
```

**❌ NO — No interface required:**
- Just one implementation and no more planned
- Pure Data Objects (DTOs)
- Simple actions without external dependencies
- Laravel Framework classes (Controllers, Models)

#### Avoid anti-patterns

**❌ BAD: Concrete dependencies**
```php
class ReportService {
    public function __construct(
        private GeminiAiAnalyzer $gemini,        // Concrete class!
        private MySqlRepository $repository,      // Concrete class!
        private SendGridMailer $mailer,          // Concrete class!
    ) {}
}

// Probleme:
// → Nicht testbar (keine Mocks möglich)
// → Nicht austauschbar (fest an Gemini/MySQL/SendGrid gekoppelt)
// → Verletzt OCP (Neue Provider = Code-Änderung nötig)
```

**✅ GOOD: Interface-based dependencies**
```php
class ReportService {
    public function __construct(
        private AiAnalyzerInterface $aiAnalyzer,        // Interface!
        private ReportRepositoryInterface $repository,   // Interface!
        private MailerInterface $mailer,                 // Interface!
    ) {}
}

// Vorteile:
// ✅ Testbar (Mocks für alle Dependencies)
// ✅ Austauschbar (Provider via Config wechselbar)
// ✅ OCP-konform (Neue Provider ohne Code-Änderung)
```

#### Naming Convention

| Type | Convention | Example |
|-----|------------|----------|
| **Service** | `{Noun}Interface` | `AiAnalyzerInterface` |
| **Repository** | `{Noun}RepositoryInterface` | `CacheRepositoryInterface` |
| **Strategy** | `{Noun}StrategyInterface` | `ScoringStrategyInterface` |
| **Providers** | `{Noun}ProviderInterface` | `RecommendationProviderInterface` |

**DO NOT use:**
- `I{Noun}` (C# style, e.g. `IAiAnalyzer`)
- `{Noun}Contract` (Laravel old, deprecated)
- `Abstract{Noun}` (these are abstract classes, not interfaces)

#### Directory structure

```
app/Domains/{Context}/
└── Contracts/              # Alle Interfaces hier
    ├── AiAnalyzerInterface.php
    ├── CacheRepositoryInterface.php
    └── ScoringStrategyInterface.php
```

#### Interface checklist

- [ ] Interface is located in `Contracts/` subfolder
- [ ] Methods fully typed (parameter + return)
- [ ] PHPDoc for complex array types
- [ ] At least 2 implementations (current or planned)
- [ ] Interface name ends in `Interface`
- [ ] No business logic in the interface (only signatures)

---

### CQRS enforcement (strict mode)

Commands and queries must be strictly separated.

#### Commands (Write Operations)
**Regulate:**
- [ ] Change state
- [ ] Return `void` or confirmation DTO
- [ ] Lying in `app/Domains/{Context}/Commands/`
- [ ] Handler is located in `app/Domains/{Context}/Handlers/`

**Example:**
```php
// Command DTO
readonly class AnalyzeJobAndResumeCommand {
    public function __construct(
        public AnalyzeRequestDto $request,
        public bool $demoMode = false,
    ) {}
}

// Handler
class AnalyzeJobAndResumeHandler {
    public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto {
        // Write Operation: Creates analysis result
        return new AnalyzeResultDto(...);
    }
}
```

#### Queries (Read Operations)
**Regulate:**
- [ ] Change **no** state
- [ ] Return DTO or Collection
- [ ] Lying in `app/Domains/{Context}/Queries/`
- [ ] Query handler is in `app/Domains/{Context}/Handlers/`

**Example (planned):**
```php
// Query DTO
readonly class GetCachedAnalysisQuery {
    public function __construct(
        public string $requestHash,
    ) {}
}

// Query-Handler
class GetCachedAnalysisQueryHandler {
    public function handle(GetCachedAnalysisQuery $query): ?array {
        // Read-Only: Liest aus Cache
        return $this->repository->getByHash($query->requestHash);
    }
}
```

---

### DDD enforcement

Code must be organized in correctly bounded contexts.

#### Bounded Context Rules
**Checklist:**
- [ ] Code is in `app/Domains/{Context}/`
- [ ] No cross-context dependencies (except via DTOs/events)
- [ ] Ubiquitous Language used in code
- [ ] Models are aggregate roots

**Current Contexts:** `Analysis`, `Profile`

**Other contexts (roadmap):**
- `Recommendations` (Phase 4, ~Commit 30+)
- `Reporting` (Phase 5, ~Commit 35+)

**Integration between contexts:**
```php
// ✅ GUT: Integration via DTO
class RecommendationService {
    public function __construct(
        private GetAnalysisResultQuery $analysisQuery,  // Query aus anderem Context
    ) {}
    
    public function generate(string $hash): RecommendationDto {
        $analysis = $this->analysisQuery->execute($hash);  // DTO als Boundary
        return new RecommendationDto(...);
    }
}

// ❌ SCHLECHT: Direkte Dependency
class RecommendationService {
    public function __construct(
        private AnalyzeJobAndResumeHandler $analysisHandler,  // Direkt auf anderen Context!
    ) {}
}
```

---

## ✅ Checklist for new features

###Architecture
- [ ] Domain structure created (correct bounded context)
- [ ] Command/Query + Handler created
- [ ] UseCases + Actions implemented
- [ ] DTOs defined (immutable, `readonly`)
- [ ] Repository (if DB access)
- [ ] Service provider registered

### SOLID compliance
- [ ] SRP: Each class only one responsibility
- [ ] OCP: Expandable without modification
- [ ] LSP: Interfaces interchangeable
- [ ] ISP: Interfaces focused
- [ ] DIP: Dependencies via Constructor Injection
- [ ] Interface-based design: Dependencies on interfaces instead of concretions

### CQRS compliance
- [ ] Commands/Queries separated correctly
- [ ] Commands change state, queries do not
- [ ] Handler in correct folder

### DDD compliance
- [ ] Code in the correctly bounded context
- [ ] No cross-context dependencies
- [ ] Ubiquitous Language used

### Tests & Quality
- [ ] Write unit tests
- [ ] Write feature tests
- [ ] Security tests for security-relevant changes (OWASP-oriented)
- [ ] Coverage ≥99%
- [ ] PHPStan level 9 without errors
- [ ] Pint without style issues

### Documentation
- [ ] PHPDoc for public methods
- [ ] Complex logic commented
- [ ] ARCHITECTURE.md updated (if necessary)
- [ ] README.md updated (if necessary)

---

**Last updated**: 2026-03-10

---

## 🛠️ Test and Quality Gate Order

**Recommended order for local and CI checks:**

1. `make pint-fix` → Code formatting (always first, prevents style errors)
2. `make phpstan` → Static analysis (early error catching, faster than testing)
3. `make test` → Quick test run without coverage (recommended for quick feedback)
4. `make test-coverage` → Runs all tests including coverage (test run is included, separate `make test` is then not necessary)

**Notes:**
- For quick feedback: pint-fix → phpstan → test
- For complete quality gates (e.g. before release): pint-fix → phpstan → test-coverage
- `make test-coverage` is slower, but covers everything (including test run)
- A separate `make test` is not necessary before `make test-coverage` because all tests are running anyway.

(See also skill comment in the Makefile)

---

