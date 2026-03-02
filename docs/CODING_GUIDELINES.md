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

---

## ✅ Checkliste für neue Features

- [ ] Domain-Struktur angelegt
- [ ] Command + Handler erstellt
- [ ] UseCases + Actions implementiert
- [ ] DTOs definiert
- [ ] Repository (falls DB-Zugriff)
- [ ] Service Provider registriert
- [ ] Unit-Tests geschrieben
- [ ] Feature-Tests geschrieben
- [ ] PHPStan Level 9 ohne Fehler
- [ ] Pint ohne Style-Issues
- [ ] Dokumentation aktualisiert

---

**Letzte Aktualisierung**: 2026-03-02

