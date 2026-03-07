# ResumeHaven – Coding Guidelines

Dieses Dokument definiert die Coding-Standards und Best Practices für das ResumeHaven-Projekt.

---

## 📋 Inhaltsverzeichnis

1. [Allgemeine Prinzipien](#1-allgemeine-prinzipien)
2. [Projekt-Struktur](#2-projekt-struktur)
3. [Namenskonventionen](#3-namenskonventionen)
4. [Domain-Driven Design](#4-domain-driven-design)
5. [Commands & Handlers](#5-commands--handlers)
6. [UseCases & Actions](#6-usecases--actions)
7. [DTOs (Data Transfer Objects)](#7-dtos-data-transfer-objects)
8. [Repositories](#8-repositories)
9. [Controllers](#9-controllers)
10. [Testing](#10-testing)
11. [Code Quality](#11-code-quality)
12. [Error Handling](#12-error-handling)

---

## 1. Allgemeine Prinzipien

### SOLID Principles

Wir folgen den **SOLID-Prinzipien**:

- **S**ingle Responsibility Principle: Eine Klasse = eine Verantwortung
- **O**pen/Closed Principle: Offen für Erweiterung, geschlossen für Änderung
- **L**iskov Substitution Principle: Subtypen müssen austauschbar sein
- **I**nterface Segregation Principle: Kleine, fokussierte Interfaces
- **D**ependency Inversion Principle: Abhängigkeiten auf Abstraktionen

### OWASP Security by Design

Sicherheitsanforderungen sind **Pflicht** und werden nach OWASP-Empfehlungen umgesetzt.

- Eingaben grundsätzlich als untrusted behandeln
- Output Context-Aware escapen/encoden
- AuthN/AuthZ strikt trennen und prüfen
- Secrets niemals im Code, nur via Config/Env
- Security-Regression-Tests bei sicherheitsrelevanten Änderungen
- Prompt-/Input-Injection explizit abwehren

#### OWASP Mapping (Top 10 -> Projektregeln)

| OWASP Top 10 | Risiko im Projekt | Verbindliche Maßnahme | Test-/Review-Check |
|---|---|---|---|
| A01 Broken Access Control | Unberechtigter Zugriff auf Endpunkte | Route-/Policy-Prüfung, keine impliziten Admin-Pfade | Feature-Tests für erlaubte/verbotene Zugriffe |
| A02 Cryptographic Failures | Unsichere Speicherung/Übertragung | Keine Secrets im Code, sichere Defaults, HTTPS in Prod | Secret-Scan + Config-Review |
| A03 Injection | SQL/XSS/Prompt-Injection | Input-Validierung, Prepared Statements, Output-Encoding, Prompt-Härtung | Security-Tests (SQL/XSS/Prompt-Patterns) |
| A04 Insecure Design | Fehlende Sicherheitsanforderungen | Threat-aware Design in UseCases + Review-Checkliste | PR-Check gegen OWASP-Tabelle |
| A05 Security Misconfiguration | Unsichere Defaults | Sichere Env-/Config-Werte, Debug nur lokal | Config-Review + Smoke-Tests |
| A06 Vulnerable Components | Verwundbare Dependencies | Regelmäßige Updates via Renovate + CVE-Checks | Dependency-Update-PRs + CVE-Report |
| A07 Identification/Auth Failures | Schwache Auth-Mechanik | Laravel Auth/Policies, keine Eigenbau-Auth | Feature-Tests für Login/Authorization |
| A08 Software/Data Integrity Failures | Manipulierte Abhängigkeiten/Builds | Lockfiles, reproduzierbare Builds, signierte Releases (später) | CI-Checks auf Lockfile-Änderungen |
| A09 Logging/Monitoring Failures | Sicherheitsvorfälle unentdeckt | Strukturierte Security-Logs ohne Secrets | Log-Review in Security-Tests |
| A10 SSRF | Externe Calls auf interne Ziele | Whitelisting/Timeouts bei externen Requests | Tests für blockierte Ziel-Hosts |

### DRY (Don't Repeat Yourself)

- Keine Code-Duplizierung
- Wiederverwendbare Komponenten erstellen
- Zentrale DTOs und Actions nutzen

### KISS (Keep It Simple, Stupid)

- Vermeide Over-Engineering
- Klare, verständliche Lösungen bevorzugen
- Komplexität nur wo nötig

---

## 2. Projekt-Struktur

### Domain-Struktur

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

### Warum diese Struktur?

- **Domain-fokussiert**: Geschäftslogik ist in Domains organisiert
- **UseCase-orientiert**: Wiederverwendbare Business-Logic
- **Testbar**: Jede Komponente isoliert testbar
- **Skalierbar**: Neue Domains einfach hinzufügen

---

## 3. Namenskonventionen

### Dateien & Klassen

| Typ | Namenskonvention | Beispiel |
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

### Methoden

| Typ | Namenskonvention | Beispiel |
|-----|------------------|----------|
| **Command/Query Handler** | `handle()` | `public function handle(Command $cmd)` |
| **Action** | `execute()` | `public function execute(array $data)` |
| **Repository** | CRUD-Verben | `getByHash()`, `store()`, `update()` |

### Variablen

- **camelCase** für Variablen: `$jobText`, `$analyzeResult`
- **snake_case** für Datenbank-Felder: `job_text`, `request_hash`
- **PascalCase** für Klassen: `AnalyzeController`

---

## 4. Domain-Driven Design

### Neue Domain erstellen

**Schritt 1**: Verzeichnisstruktur anlegen

```bash
mkdir -p app/Domains/NewDomain/{Commands,Handlers,UseCases,Dto,Cache}
```

**Schritt 2**: Command erstellen

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

**Schritt 3**: Handler erstellen

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

**Schritt 4**: Service Provider registrieren

```php
// In AnalysisDomainServiceProvider oder neuem Provider
$this->app->singleton(DoSomethingHandler::class);
$this->app->singleton(SomeAction::class);
```

---

## 5. Commands & Handlers

### Command-Pattern

**Commands sind immutable Request-Objekte:**

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

### Handler-Pattern

**Handlers orchestrieren UseCases:**

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
        // 1. Cache prüfen
        // 2. Business-Logic ausführen
        // 3. Ergebnis cachen
        // 4. DTO zurückgeben
    }
}
```

**Best Practices:**

✅ **DO**:
- Handler orchestriert nur (kein Business-Logic-Code)
- Dependencies via Constructor Injection
- Klare Schritte kommentieren
- DTO zurückgeben

❌ **DON'T**:
- Keine direkte DB-Queries im Handler
- Keine Business-Logic im Handler
- Keine Service Locator Pattern (`app()->make()` vermeiden)

---

## 6. UseCases & Actions

### UseCase-Pattern

**UseCases kapseln wiederverwendbare Business-Logic:**

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

### Action-Pattern

**Actions sind granulare, wiederverwendbare Logik:**

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
- Eine Action = eine Aufgabe
- Type-Hints für alle Parameter und Return-Types
- PHPDoc für komplexe Array-Typen
- Pure Functions wo möglich (keine Side-Effects)

❌ **DON'T**:
- Keine God-Actions (zu viel Verantwortung)
- Keine direkten DB-Queries (Repository nutzen)
- Keine Service-Aufrufe (Dependencies injizieren)

---

## 7. DTOs (Data Transfer Objects)

### DTO-Pattern

**DTOs sind immutable Datencontainer:**

```php
class AnalyzeResultDto
{
    /**
     * @param array<int, string> $requirements
     * @param array<int, string> $experiences
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string> $gaps
     */
    public function __construct(
        public readonly string $job_text,
        public readonly string $cv_text,
        public readonly array $requirements,
        public readonly array $experiences,
        public readonly array $matches,
        public readonly array $gaps,
        public readonly ?string $error = null,
    ) {}

    public function toArray(): array
    {
        return [
            'job_text' => $this->job_text,
            'cv_text' => $this->cv_text,
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
- `public readonly` Properties (PHP 8.2+)
- PHPDoc für komplexe Array-Typen
- `toArray()` Methode für Serialisierung
- `fromArray()` statische Factory-Methode für Deserialisierung

❌ **DON'T**:
- Keine Setter (immutable!)
- Keine Business-Logic im DTO
- Keine Validierung im DTO (nur Type-Checks)

---

## 8. Repositories

### Repository-Pattern

**Repositories abstrahieren Persistence:**

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
- Klare Methoden-Namen (CRUD-orientiert)
- Type-Hints für alle Parameter
- Eloquent-Queries nur im Repository
- Exceptions werfen bei Fehlern

❌ **DON'T**:
- Keine Business-Logic im Repository
- Keine komplexen Joins (lieber mehrere Queries)
- Keine Raw-SQL (Eloquent nutzen)

---

## 9. Controllers

### Single Action Controllers

**Controller sind dünn und delegieren an Commands:**

```php
class AnalyzeController extends Controller
{
    public function __construct(
        private Dispatcher $dispatcher,
    ) {}

    public function analyze(Request $request): View
    {
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        $dto = AnalyzeRequestDto::fromArray($validated);
        
        /** @var AnalyzeResultDto $result */
        $result = $this->dispatcher->dispatch(
            new AnalyzeJobAndResumeCommand($dto)
        );

        return view('result', [
            'job_text' => $result->job_text,
            'cv_text' => $result->cv_text,
            'result' => $result->toArray(),
            'error' => $result->error,
        ]);
    }
}
```

**Best Practices:**

✅ **DO**:
- Validierung im Controller
- Command-Dispatch für Business-Logic
- Type-Hint für dispatch()-Return
- Klare View-Rückgabe

❌ **DON'T**:
- Keine Business-Logic im Controller
- Keine direkten Service-Calls
- Keine DB-Queries im Controller
- Keine komplexe Transformationen

---

## 10. Testing

### Test-Strategie

**3 Test-Typen:**

1. **Unit-Tests**: Teste einzelne Komponenten isoliert
2. **Feature-Tests**: Teste HTTP-Requests end-to-end
3. **Integration-Tests**: Teste Zusammenspiel mehrerer Komponenten

### Unit-Test Pattern

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

### Feature-Test Pattern

```php
test('POST /analyze zeigt Analyseergebnis', function () {
    $response = post('/analyze', [
        'job_text' => 'PHP Developer gesucht',
        'cv_text' => '5 Jahre PHP Erfahrung',
    ]);
    
    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('result');
});
```

**Best Practices:**

✅ **DO**:
- Mock externe Services (AI, API)
- Nutze RefreshDatabase für DB-Tests
- Teste Happy-Path UND Error-Cases
- Descriptive Test-Namen

❌ **DON'T**:
- Keine Tests für Framework-Code
- Keine Tests für Getter/Setter
- Keine Tests für triviale Logik

---

## 11. Code Quality

### PHPStan (Level 9)

Wir nutzen **PHPStan Level 9** (strengstes Level):

```bash
make phpstan
```

**Was PHPStan prüft:**

- Type-Safety (alle Parameter/Returns typisiert)
- Unused Variables
- Dead Code
- Mögliche Null-Pointer
- Array-Key-Existence

### Laravel Pint (Code-Style)

Wir nutzen **Laravel Pint** für konsistenten Code-Style:

```bash
make pint-fix    # Auto-Fix
make pint-analyse # Nur prüfen
```

**Was Pint prüft:**

- PSR-12 Standard
- Laravel Conventions
- Import-Sortierung
- Spacing & Indentation

### Best Practices

✅ **DO**:
- `declare(strict_types=1);` in jeder PHP-Datei
- Type-Hints für alle Properties, Parameter, Returns
- PHPDoc für komplexe Array-Typen
- Readonly Properties wo möglich

❌ **DON'T**:
- Keine `@phpstan-ignore` ohne Kommentar
- Keine `mixed` Types (spezifisch sein)
- Keine Suppress-Warnings

---

## 12. Error Handling

### Exception-Handling

**Strategie:**

1. **Try-Catch in Handlers** (nicht in Actions)
2. **Spezifische Exceptions werfen**
3. **DTOs mit Error-Property zurückgeben**

### Beispiel

```php
class AnalyzeJobAndResumeHandler
{
    public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto
    {
        try {
            $analyzeResult = $this->analyzeService->analyze($command->request);
            
            // Fehler aus Service übernehmen
            if ($analyzeResult->error !== null) {
                return $analyzeResult;
            }
            
            // ... weitere Verarbeitung
        } catch (\Throwable $e) {
            // Fallback-DTO mit Fehler
            return new AnalyzeResultDto(
                $command->request->jobText(),
                $command->request->cvText(),
                [], [], [], [],
                'AI-Analyse fehlgeschlagen: ' . $e->getMessage()
            );
        }
    }
}
```

**Best Practices:**

✅ **DO**:
- Generische Exceptions catchen (`\Throwable`)
- Fehler-Kontext loggen
- User-friendly Error-Messages
- Fallback-Werte zurückgeben

❌ **DON'T**:
- Keine leeren Catch-Blöcke
- Keine Exception-Suppression
- Keine technischen Details an User

---

## 📚 Weitere Ressourcen

- **Laravel Docs**: https://laravel.com/docs
- **PHPStan**: https://phpstan.org/
- **SOLID Principles**: https://en.wikipedia.org/wiki/SOLID
- **DDD**: https://martinfowler.com/tags/domain%20driven%20design.html
- **CQRS**: https://martinfowler.com/bliki/CQRS.html

---

## 🤖 KI-Agent Spezifische Regeln

### Test-Enforcement
- **Jede Änderung benötigt Tests** (Pest 3)
- Nach Codeänderungen: `php artisan test --compact` oder `make test`
- Mindestens Feature-Tests, idealerweise auch Unit-Tests
- Coverage-Minimum: **95%**

### Pint-Formatting
- **Nach jeder PHP-Änderung:** `vendor/bin/pint --dirty --format agent`
- Oder via Makefile: `make pint-fix`
- Formatierung ist nicht optional, sondern Pflicht

### PHPStan-Validierung
- **Level 9 ist Pflicht**
- Keine neuen Errors einführen
- Bei Bedarf Baseline aktualisieren: `vendor/bin/phpstan analyse --generate-baseline`
- Command: `make phpstan`

### Coverage-Gate
- **Minimum:** 95% Total Coverage
- **GeminiAiAnalyzer.php:** ≥80%
- Tests vor Commit ausführen: `make test-coverage`
- HTML-Report: `make test-coverage-report && make coverage-open`

### Dokumentation
- Nur auf explizite Anfrage Dokumentationsdateien erstellen
- PHPDoc für alle Public Methods
- Komplexe Logik kommentieren (Warum, nicht Was)

---

## 🛡️ Architecture Enforcement

### SOLID-Gate (Pflicht-Review)

Jeder Commit und jeder PR MUSS die SOLID-Prinzipien einhalten.

#### Single Responsibility Principle (SRP)
**Checkliste:**
- [ ] Jede Klasse hat nur eine Verantwortlichkeit
- [ ] Methoden sind < 20 Zeilen
- [ ] Klassen sind < 200 Zeilen
- [ ] Cyclomatic Complexity < 5

**Beispiel (gut):**
```php
// Eine Klasse = Eine Verantwortung
class CalculateScoreAction {
    public function execute(array $matches, array $gaps): ScoreResultDto {
        $total = count($matches) + count($gaps);
        if ($total === 0) return new ScoreResultDto(0, 'Keine Daten', ...);
        
        $percentage = (int) round((count($matches) / $total) * 100);
        return new ScoreResultDto($percentage, $this->getRating($percentage), ...);
    }
}
```

**Beispiel (schlecht):**
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
**Checkliste:**
- [ ] Neue Features ohne Änderung bestehender Klassen
- [ ] Interfaces für austauschbare Komponenten
- [ ] Strategy Pattern für verschiedene Implementierungen

**Beispiel:**
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
**Checkliste:**
- [ ] Interfaces sind austauschbar ohne Breaking Changes
- [ ] Subtypen halten Interface-Kontrakt ein
- [ ] Keine Exception-Änderungen in Subtypen

#### Interface Segregation Principle (ISP)
**Checkliste:**
- [ ] Interfaces sind klein und fokussiert
- [ ] Keine "fetten" Interfaces mit vielen Methoden
- [ ] Clients abhängig nur von benötigten Methods

#### Dependency Inversion Principle (DIP)
**Checkliste:**
- [ ] Dependencies via Constructor Injection
- [ ] Abhängigkeiten zu Abstraktionen (Interfaces), nicht zu Konkretionen
- [ ] Kein `new` in Business-Logic (außer DTOs)

**Beispiel:**
```php
// ✅ GUT: Dependency zu Interface
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private AiAnalyzerInterface $aiAnalyzer,  // Interface!
        private MatchingUseCase $matchingUseCase,
    ) {}
}

// ❌ SCHLECHT: Dependency zu Konkretion
class AnalyzeJobAndResumeHandler {
    public function __construct(
        private GeminiAiAnalyzer $geminiAnalyzer,  // Konkrete Klasse!
    ) {}
}
```

---

### Interface-based Design (Program to an Interface)

**Grundprinzip:** Code sollte gegen Abstractions (Interfaces) programmiert werden, nicht gegen Konkretionen.

#### Wann ein Interface erstellen?

**✅ JA — Interface erstellen:**
- Mehrere Implementierungen existieren oder geplant sind
- Implementierung austauschbar sein soll
- External Dependencies (API, DB, Cache)
- Strategie-Pattern benötigt wird
- Unit-Tests mit Mocks nötig sind

**Beispiel:**
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

**❌ NEIN — Kein Interface nötig:**
- Nur eine Implementierung und keine weitere geplant
- Reine Data Objects (DTOs)
- Simple Actions ohne External Dependencies
- Laravel Framework-Klassen (Controller, Models)

#### Anti-Patterns vermeiden

**❌ SCHLECHT: Konkrete Abhängigkeiten**
```php
class ReportService {
    public function __construct(
        private GeminiAiAnalyzer $gemini,        // Konkrete Klasse!
        private MySqlRepository $repository,      // Konkrete Klasse!
        private SendGridMailer $mailer,          // Konkrete Klasse!
    ) {}
}

// Probleme:
// → Nicht testbar (keine Mocks möglich)
// → Nicht austauschbar (fest an Gemini/MySQL/SendGrid gekoppelt)
// → Verletzt OCP (Neue Provider = Code-Änderung nötig)
```

**✅ GUT: Interface-basierte Abhängigkeiten**
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

| Typ | Convention | Beispiel |
|-----|------------|----------|
| **Service** | `{Noun}Interface` | `AiAnalyzerInterface` |
| **Repository** | `{Noun}RepositoryInterface` | `CacheRepositoryInterface` |
| **Strategy** | `{Noun}StrategyInterface` | `ScoringStrategyInterface` |
| **Provider** | `{Noun}ProviderInterface` | `RecommendationProviderInterface` |

**NICHT verwenden:**
- `I{Noun}` (C#-Style, z.B. `IAiAnalyzer`)
- `{Noun}Contract` (Laravel alt, deprecated)
- `Abstract{Noun}` (das sind abstrakte Klassen, keine Interfaces)

#### Verzeichnisstruktur

```
app/Domains/{Context}/
└── Contracts/              # Alle Interfaces hier
    ├── AiAnalyzerInterface.php
    ├── CacheRepositoryInterface.php
    └── ScoringStrategyInterface.php
```

#### Interface-Checklist

- [ ] Interface liegt in `Contracts/` Unterordner
- [ ] Methoden vollständig typisiert (Parameter + Return)
- [ ] PHPDoc für komplexe Array-Typen
- [ ] Mindestens 2 Implementierungen (aktuell oder geplant)
- [ ] Interface-Name endet auf `Interface`
- [ ] Keine Business-Logic im Interface (nur Signaturen)

---

### CQRS-Enforcement (Strict Mode)

Commands und Queries müssen strikt getrennt sein.

#### Commands (Write Operations)
**Regeln:**
- [ ] Ändern Zustand
- [ ] Geben `void` oder Bestätigungs-DTO zurück
- [ ] Liegen in `app/Domains/{Context}/Commands/`
- [ ] Handler liegt in `app/Domains/{Context}/Handlers/`

**Beispiel:**
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
        // Write Operation: Erstellt Analyse-Ergebnis
        return new AnalyzeResultDto(...);
    }
}
```

#### Queries (Read Operations)
**Regeln:**
- [ ] Ändern **keinen** Zustand
- [ ] Geben DTO oder Collection zurück
- [ ] Liegen in `app/Domains/{Context}/Queries/`
- [ ] Query-Handler liegt in `app/Domains/{Context}/Handlers/`

**Beispiel (geplant):**
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

### DDD-Enforcement

Code muss in korrekten Bounded Contexts organisiert sein.

#### Bounded Context Rules
**Checkliste:**
- [ ] Code liegt in `app/Domains/{Context}/`
- [ ] Keine Cross-Context-Dependencies (außer via DTOs/Events)
- [ ] Ubiquitous Language in Code verwendet
- [ ] Models sind Aggregate Roots

**Aktueller Context:** `Analysis`

**Geplante Contexts (Roadmap):**
- `Profile` (Phase 3, ~Commit 22+)
- `Recommendations` (Phase 4, ~Commit 30+)
- `Reporting` (Phase 5, ~Commit 35+)

**Integration zwischen Contexts:**
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

## ✅ Checkliste für neue Features

### Architektur
- [ ] Domain-Struktur angelegt (richtiger Bounded Context)
- [ ] Command/Query + Handler erstellt
- [ ] UseCases + Actions implementiert
- [ ] DTOs definiert (immutable, `readonly`)
- [ ] Repository (falls DB-Zugriff)
- [ ] Service Provider registriert

### SOLID-Compliance
- [ ] SRP: Jede Klasse nur eine Verantwortlichkeit
- [ ] OCP: Erweiterbar ohne Änderung
- [ ] LSP: Interfaces austauschbar
- [ ] ISP: Interfaces fokussiert
- [ ] DIP: Dependencies via Constructor Injection
- [ ] Interface-based Design: Dependencies zu Interfaces statt Konkretionen

### CQRS-Compliance
- [ ] Commands/Queries korrekt getrennt
- [ ] Commands ändern Zustand, Queries nicht
- [ ] Handler in korrektem Ordner

### DDD-Compliance
- [ ] Code im korrekten Bounded Context
- [ ] Keine Cross-Context-Dependencies
- [ ] Ubiquitous Language verwendet

### Tests & Quality
- [ ] Unit-Tests geschrieben
- [ ] Feature-Tests geschrieben
- [ ] Security-Tests für sicherheitsrelevante Änderungen (OWASP-orientiert)
- [ ] Coverage ≥95%
- [ ] PHPStan Level 9 ohne Fehler
- [ ] Pint ohne Style-Issues

### Dokumentation
- [ ] PHPDoc für Public Methods
- [ ] Komplexe Logik kommentiert
- [ ] ARCHITECTURE.md aktualisiert (falls nötig)
- [ ] README.md aktualisiert (falls nötig)

---

**Letzte Aktualisierung**: 2026-03-07
