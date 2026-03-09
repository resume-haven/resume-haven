# 🤖 Agent-Kontext – Zentrale Arbeitsregeln

Diese Datei ist die **Single Source of Truth** für alle KI-Agenten (GitHub Copilot, Windsurf, etc.).

> Session-Startpunkt / Soft-Reset: `docs/ai/WORKING_BASELINE.md`
> 
> Nutze diese Baseline fuer den aktuellen Tageskontext und behandle den Repository-Stand als verbindlich.

---

## 🎯 Architektur-Prinzipien (Pflicht)

### 1. CQRS (Command Query Responsibility Segregation) — Strict Mode

**Regel:** Commands und Queries sind **strikt getrennt**.

#### Commands (Write Operations)
- **Zweck:** Zustandsänderungen
- **Return:** `void` oder Bestätigungs-DTO
- **Beispiel:** `AnalyzeJobAndResumeCommand` → `AnalyzeJobAndResumeHandler`
- **Struktur:**
  ```php
  app/Domains/{Context}/Commands/     // Command-DTOs
  app/Domains/{Context}/Handlers/     // Command-Handler
  ```

#### Queries (Read Operations)
- **Zweck:** Daten lesen, keine Zustandsänderungen
- **Return:** DTO oder Collection
- **Beispiel:** `GetCachedAnalysisQuery` → `GetCachedAnalysisHandler`
- **Struktur:**
  ```php
  app/Domains/{Context}/Queries/      // Query-DTOs
  app/Domains/{Context}/Handlers/     // Query-Handler
  ```

#### Phasenweise Einführung (aktueller Stand)
- ✅ **Phase 1 (abgeschlossen):** Commands + Handlers implementiert
- 🔄 **Phase 2 (in Arbeit):** Queries + Query-Handler für Cache-Zugriffe
- ⏳ **Phase 3 (geplant):** Alle Read-Operationen auf Queries umstellen

**DoD für CQRS-Compliance:**
- [ ] Command hat keinen Return-Wert außer Bestätigungs-DTO
- [ ] Query ändert keinen Zustand
- [ ] Handler ist in korrektem Ordner (`Commands/` oder `Queries/`)

---

### 2. SOLID-Prinzipien — Pflicht-Review-Gate

**Regel:** Jeder Commit MUSS SOLID-Prinzipien einhalten.

#### Single Responsibility Principle (SRP)
- ✅ Eine Klasse = Eine Verantwortlichkeit
- ✅ Methoden < 20 Zeilen
- ✅ Klassen < 200 Zeilen
- ✅ Cyclomatic Complexity < 5

**Beispiel (gut):**
```php
class CalculateScoreAction {
    public function execute(array $matches, array $gaps): ScoreResultDto { }
}
```

**Beispiel (schlecht):**
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
- ✅ Erweiterbar ohne Änderung
- ✅ Nutze Interfaces (z.B. `AiAnalyzerInterface`)
- ✅ Strategy Pattern für austauschbare Komponenten

#### Liskov Substitution Principle (LSP)
- ✅ Interfaces müssen austauschbar sein
- ✅ Keine Breaking Changes in Subtypen

#### Interface Segregation Principle (ISP)
- ✅ Kleine, fokussierte Interfaces
- ✅ Clients sollen nicht von ungenutzten Methoden abhängen

#### Dependency Inversion Principle (DIP)
- ✅ Abhängigkeiten zu Abstractions (Interfaces), nicht zu Konkretionen
- ✅ Constructor Injection für alle Dependencies

**SOLID-Gate Checkliste (in jedem PR):**
- [ ] SRP: Jede Klasse/Methode hat nur eine Verantwortlichkeit
- [ ] OCP: Neue Features ohne Änderung bestehender Klassen
- [ ] LSP: Interfaces sind korrekt austauschbar
- [ ] ISP: Keine "fetten" Interfaces
- [ ] DIP: Dependencies via Constructor Injection

---

### 3. Domain-Driven Design (DDD)

**Regel:** Code ist nach fachlichen Domänen strukturiert.

#### Aktueller Bounded Context
- **`Analysis`** (Haupt-Domain)
  - Job-/CV-Analyse
  - Matching & Gap-Analysis
  - Scoring
  - Cache-Management

#### Struktur
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

#### Geplante Bounded Contexts (Roadmap)
- **`Profile`** (Phase 3) — Lebenslauf-Speicherung, User-Präferenzen
- **`Recommendations`** (Phase 4) — KI-Empfehlungen, Verbesserungsvorschläge
- **`Reporting`** (Phase 5) — Analyse-Historie, Statistiken, Exports

**DDD-Compliance Checkliste:**
- [ ] Code liegt in korrektem Bounded Context (`app/Domains/{Context}/`)
- [ ] Keine Cross-Context-Dependencies (außer via Events/DTOs)
- [ ] Ubiquitous Language in Code-Namen verwendet

---

### 4. Interface-based Design

**Regel:** "Program to an Interface, not an Implementation"

#### Warum Interfaces?
- ✅ **Austauschbarkeit:** Implementierungen können ohne Code-Änderung gewechselt werden
- ✅ **Testbarkeit:** Interfaces können einfach gemockt werden
- ✅ **Dependency Inversion:** High-Level-Module abhängig von Abstractions
- ✅ **Open/Closed:** Neue Implementierungen ohne Änderung bestehender Klassen

#### Wann ein Interface erstellen?

**JA — Interface erstellen wenn:**
- ✅ Mehrere Implementierungen existieren (z.B. `GeminiAiAnalyzer`, `MockAiAnalyzer`)
- ✅ Implementierung austauschbar sein soll (z.B. Cache-Provider, AI-Provider)
- ✅ External Dependencies (z.B. API-Calls, Datenbank)
- ✅ Strategie-Pattern benötigt wird

**NEIN — Kein Interface wenn:**
- ❌ Nur eine Implementierung existiert und keine weitere geplant
- ❌ Reine Data Objects (DTOs)
- ❌ Simple Actions ohne External Dependencies
- ❌ Laravel-Framework-Klassen (Controller, Models)

#### Beispiel (gut)

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

#### Beispiel (schlecht)

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

| Interface | Konvention | Beispiel |
|-----------|------------|----------|
| **Service/Provider** | `{Noun}Interface` | `AiAnalyzerInterface` |
| **Repository** | `{Noun}RepositoryInterface` | `CacheRepositoryInterface` |
| **Strategy** | `{Noun}StrategyInterface` | `ScoringStrategyInterface` |

**NICHT:** `I{Noun}` (C#-Style) oder `{Noun}Contract` (Laravel alt)

#### Interface-Checkliste

- [ ] Interface liegt in `Contracts/` Unterordner
- [ ] Methoden-Signaturen vollständig typisiert
- [ ] PHPDoc mit `@return` für komplexe Typen
- [ ] Mindestens 2 Implementierungen (aktuell oder geplant)
- [ ] Interface-Name endet auf `Interface`

#### Aktuelle Interfaces im Projekt

✅ **Vorhanden:**
- `AiAnalyzerInterface` (Gemini, Mock)

⏳ **Geplant (Roadmap):**
- `CacheRepositoryInterface` (Database, Redis, Memory)
- `ScoringStrategyInterface` (Simple, Weighted, ML-based)
- `RecommendationProviderInterface` (AI, Rule-based)

---

## ✅ Quality-Gates (Pflicht)

### Test-Coverage
- **Minimum:** 95% Total Coverage
- **Aktuell:** 98.2% ✅
- **GeminiAiAnalyzer:** ≥80%
- **Prüfung:** `make test-coverage`

### PHPStan
- **Level:** 9 (strict)
- **Errors:** 0
- **Prüfung:** `make phpstan`

### Pint (Code-Formatting)
- **Regel:** Nach jeder PHP-Änderung ausführen
- **Befehl:** `vendor/bin/pint --dirty --format agent`
- **Prüfung:** `make pint-analyse`

### Tests
- **Pflicht:** Jede Änderung benötigt Tests
- **Framework:** Pest 3
- **Typen:** Unit + Feature
- **Prüfung:** `make test`

### OWASP-Compliance
- **Regel:** Sicherheitsrelevante Änderungen müssen OWASP-orientiert geprüft werden
- **Mindestens:** Input-Validation, Output-Encoding, AuthZ/CSRF, Secret-Handling
- **Prüfung:** Security-Tests + Review gegen OWASP Top 10

---

## 🚫 Verbotene Patterns

### ❌ God Objects
```php
// SCHLECHT
class AnalyzeController {
    public function analyze() {
        // 200+ Zeilen Code
    }
}
```

### ❌ Raw SQL außerhalb von Repositories
```php
// SCHLECHT
DB::table('analysis_cache')->where(...)->get();

// GUT
$this->cacheRepository->getByHash($hash);
```

### ❌ `env()` außerhalb von Config-Files
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

### ❌ Mixed Responsibilities
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

Jeder Commit ist erst "Done", wenn:

1. ✅ **Tests:** Alle Tests grün (Pest)
2. ✅ **Coverage:** ≥95%
3. ✅ **PHPStan:** Level 9, 0 Errors
4. ✅ **Pint:** Code-Formatting sauber
5. ✅ **SOLID:** Alle SOLID-Prinzipien eingehalten
6. ✅ **CQRS:** Commands/Queries korrekt getrennt
7. ✅ **DDD:** Code im korrekten Bounded Context
8. ✅ **Documentation:** PHPDoc für alle Public Methods

---

## 🔍 Code-Review Checkliste

### Architektur
- [ ] SOLID-Prinzipien eingehalten?
- [ ] CQRS: Commands/Queries korrekt getrennt?
- [ ] DDD: Richtiger Bounded Context?
- [ ] Single-Action-Controller (`__invoke()`)?
- [ ] Immutable DTOs (`readonly`)?

### Code-Qualität
- [ ] PHPStan Level 9: 0 Errors?
- [ ] Pint: Code-Formatting sauber?
- [ ] Methoden < 20 Zeilen?
- [ ] Klassen < 200 Zeilen?
- [ ] Cyclomatic Complexity < 5?

### Tests
- [ ] Unit-Tests vorhanden?
- [ ] Feature-Tests vorhanden?
- [ ] Edge-Cases getestet?
- [ ] Coverage ≥95%?

### Security (OWASP)
- [ ] Input als untrusted behandelt?
- [ ] Output kontextgerecht escaped/encoded?
- [ ] AuthN/AuthZ/CSRF berücksichtigt?
- [ ] Keine Secrets im Code?
- [ ] Security-Tests bei sicherheitsrelevanten Änderungen?

### Dokumentation
- [ ] PHPDoc für Public Methods?
- [ ] Komplexe Logik kommentiert?
- [ ] README/Docs aktualisiert (wenn nötig)?

---

## 📚 Siehe auch

- **Projektüberblick:** `docs/ai/PROJECT_OVERVIEW.md`
- **Tech Stack:** `docs/ai/TECH_STACK.md`
- **Architektur:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Commit-Plan:** `COMMIT_PLAN.md`
- **Roadmap:** `docs/ROADMAP.md`
- **Changelog:** `CHANGELOG.md`
- **Laravel Boost:** `src/AGENTS.md`

---

**Letzte Aktualisierung**: 2026-03-09  
**Version**: 2.1 (Verweis auf WORKING_BASELINE als Session-Startpunkt)
