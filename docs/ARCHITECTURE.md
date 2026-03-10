# ResumeHaven – Architektur

Dieses Dokument beschreibt die technische Architektur des ResumeHaven‑MVP.

---

# 🧠 1. Überblick

ResumeHaven ist ein **Domain-driven, Command/Query-orientiertes** Analyse‑Tool.  
Die Architektur folgt modernen Best Practices:

- **Domain-Driven Design (DDD)** (modulare Geschäftsbereiche, Bounded Contexts)
- **CQRS (Strict Mode)** (Command/Query strikt getrennt, phasenweise Einführung)
- **SOLID-Prinzipien** (Pflicht-Gate in jedem Commit)
- **Single Action Controllers** (Controller sind dünn, ~34 Zeilen)
- **Repository Pattern** (Persistence-Abstraktion)
- **UseCase Pattern** (Business-Logic-Orchestrierung)
- **Wartbarkeit, Testbarkeit, Erweiterbarkeit**

---

# 🎯 1.1 Architektur-Prinzipien

## CQRS (Command Query Responsibility Segregation) — Strict Mode

**Regel:** Commands (Write) und Queries (Read) sind **strikt getrennt**.

### Aktueller Stand (Phasenweise Einführung)

#### ✅ Phase 1 (abgeschlossen)
- Commands implementiert: `AnalyzeJobAndResumeCommand`
- Handlers implementiert: `AnalyzeJobAndResumeHandler`
- Struktur: `app/Domains/Analysis/Commands/` + `Handlers/`

#### 🔄 Phase 2 (in Arbeit)
- Queries für Cache-Zugriffe: `GetCachedAnalysisQuery`
- Query-Handler: `GetCachedAnalysisQueryHandler`
- Struktur: `app/Domains/Analysis/Queries/` + `Handlers/`

#### ⏳ Phase 3 (geplant)
- Alle Read-Operationen auf Queries umstellen
- Reporting-Queries (`GetAnalysisHistoryQuery`)
- Statistics-Queries (`GetUserStatisticsQuery`)

### CQRS-Regeln

**Commands:**
- ✅ Ändern Zustand (Write Operations)
- ✅ Geben `void` oder Bestätigungs-DTO zurück
- ✅ Beispiel: `AnalyzeJobAndResumeCommand` → erstellt Analyse-Ergebnis

**Queries:**
- ✅ Lesen Daten (Read Operations)
- ✅ Ändern **keinen** Zustand
- ✅ Geben DTO oder Collection zurück
- ✅ Beispiel: `GetCachedAnalysisQuery` → liest Cache-Eintrag

---

## DDD (Domain-Driven Design)

**Regel:** Code ist nach fachlichen Domänen strukturiert.

### Aktueller Bounded Context

#### `Analysis` (Haupt-Domain)
- **Verantwortlichkeit:** Job-/CV-Analyse, Matching, Gap-Analysis, Scoring, Cache
- **Ubiquitous Language:** Requirements, Experiences, Matches, Gaps, Score, Tags
- **Struktur:** `app/Domains/Analysis/`
- **Status:** ✅ Vollständig implementiert

#### `Profile` (Commit 22) — ✅ **Basis implementiert**
- **Verantwortlichkeit:** Anonyme CV-Speicherung, Token-Verwaltung, Verschlüsselung, Wiederherstellung
- **Ubiquitous Language:** StoredResume, Token, EncryptedCV, LoadedResume
- **Struktur:** `app/Domains/Profile/`
- **Integration:** Unabhängig von `Analysis` (keine direkte Kopplung, nur CV-Text als Input in der UI)
- **Status:**
  - ✅ CQRS-Basis mit `StoreResumeCommand` und `GetResumeByTokenQuery`
  - ✅ Single-Action-Controller für Store/Load
  - ✅ AES-256-GCM mit tokenbasiert abgeleitetem Secret (MVP-Kompromiss)
  - ✅ Persistenz über `stored_resumes` + `ProfileRepository`
  - ⚠️ **Technische Schuld:** Migration zu User-basierter Verschlüsselung vor User-Accounts verpflichtend

### Implementierte Bounded Contexts

#### `Recommendations` (Phase 4, ~Commit 17+) — ✅ **Grundstruktur implementiert**
- **Verantwortlichkeit:** KI-Empfehlungen, Verbesserungsvorschläge
- **Ubiquitous Language:** Recommendation, Suggestion, Priority, Example
- **Integration:** Teil von `Analysis`-Domain (zunächst als Sub-Domain)
- **Status:** 
  - ✅ `RecommendationDto` implementiert (immutable, typed)
  - ✅ AI-Prompt erweitert (recommendations-Feld)
  - ✅ Parsing-Logic (ParseAiResponseAction)
  - ✅ UI-Component (result.blade.php)
  - ⏳ Separate Domain-Extraktion geplant (~Commit 30+)

### Geplante Bounded Contexts (Roadmap)

#### `Reporting` (Phase 5, ~Commit 35+)
- **Verantwortlichkeit:** Analyse-Historie, Statistiken, Exports
- **Ubiquitous Language:** Report, History, Statistics, Export
- **Integration:** Read-Only Zugriff auf `Analysis` + `Profile`

### DDD-Regeln

- ✅ **Bounded Context Isolation:** Keine direkten Dependencies zwischen Contexts
- ✅ **Communication:** Nur via DTOs, Events oder Shared Kernel
- ✅ **Ubiquitous Language:** Code verwendet fachliche Begriffe
- ✅ **Aggregate Roots:** Models sind Aggregate Roots ihres Contexts

---

# 🧩 2. Hauptkomponenten (Neue Architektur)

## 2.1 Domain Layer

### **Analysis Domain** (`app/Domains/Analysis/`)

Die Hauptdomain für Job-/Lebenslauf-Analysen.

#### **Commands** (`Commands/`)
- `AnalyzeJobAndResumeCommand`: Request-Objekt für Analyse-Anfragen
- Enthält `handle()` Methode die Handler aufruft (Laravel Bus Pattern)

#### **Handlers** (`Handlers/`)
- `AnalyzeJobAndResumeHandler`: Orchestriert den gesamten Analyse-Flow
  1. Cache prüfen
  2. AI-Analyse durchführen
  3. Matching durchführen
  4. Gap-Analyse durchführen
  5. Ergebnis cachen
  6. DTO zurückgeben

#### **UseCases** (`UseCases/`)
Kapseln wiederverwendbare Business-Logik:

- **ExtractDataUseCase**: Extrahiert Anforderungen und Erfahrungen
  - `ExtractRequirementsAction`
  - `ExtractExperiencesAction`
  
- **MatchingUseCase**: Findet Übereinstimmungen
  - `MatchAction`
  
- **GapAnalysisUseCase**: Identifiziert Lücken
  - `FindGapsAction`

#### **Cache** (`Cache/`)
- **Actions**: 
  - `GetCachedAnalysisAction`: Liest aus Cache
  - `StoreCachedAnalysisAction`: Schreibt in Cache
- **Repositories**: 
  - `AnalysisCacheRepository`: Abstrahiert Datenbank-Zugriff

#### **DTOs** (`Dto/`)
Immutable Data Transfer Objects:
- `ExtractDataResultDto`
- `MatchingResultDto`
- `GapAnalysisResultDto`

---

### **Profile Domain** (`app/Domains/Profile/`)

Die Domain für anonyme CV-Speicherung und Wiederherstellung.

#### **Commands** (`Commands/`)
- `StoreResumeCommand`: Write-Request zum persistierten Speichern eines CVs

#### **Queries** (`Queries/`)
- `GetResumeByTokenQuery`: Read-Request zum Laden eines gespeicherten CVs per Token

#### **Handlers** (`Handlers/`)
- `StoreResumeHandler`: Generiert eindeutigen Token, verschlüsselt den CV und persistiert ihn
- `GetResumeByTokenHandler`: Lädt gespeicherten CV, entschlüsselt ihn und aktualisiert `last_accessed_at`

#### **Actions** (`Actions/`)
- `GenerateTokenAction`: Erzeugt URL-safe Base64-Token aus 32 zufälligen Bytes
- `EncryptResumeAction`: Verschlüsselt CV-Inhalt via AES-256-GCM
- `DecryptResumeAction`: Entschlüsselt gespeicherte CV-Inhalte robust und fehlertolerant

#### **Repositories** (`Repositories/`)
- `ProfileRepository`: Abstraktion über `stored_resumes`-Persistenz

#### **DTOs** (`Dto/`)
- `StoreResumeDto`, `ResumeTokenDto`, `LoadedResumeDto`
- Immutable (`readonly`) und klar auf UI-/Domain-Transfer beschränkt

---

## 2.2 Application Layer

### **Controllers** (`app/Http/Controllers/`)

**AnalyzeController** (~34 Zeilen, "thin"):
1. Validierung
2. Command erstellen
3. Command dispatchen (Bus)
4. View zurückgeben

**Keine Business-Logik im Controller!**

### **Services** (`app/Services/`)

Legacy-Services (werden nach und nach in Domains migriert):
- `AnalyzeApplicationService`: AI-Integration
- `AnalysisCacheService`: (deprecated, wird durch Repository ersetzt)

---

## 2.3 Infrastructure Layer

### **Models** (`app/Models/`)
- `AnalysisCache`: Eloquent Model für gecachte Analysen
- `User`: User-Management (für später)

### **Providers** (`app/Providers/`)
- `AnalysisDomainServiceProvider`: Registriert Domain-Dependencies

---

# 🔄 3. Request-Flow (Neu)

```
HTTP POST /analyze
    ↓
AnalyzeController::__invoke()
    ├─ Validierung (Laravel Validator)
    ├─ DTO erstellen (AnalyzeRequestDto)
    ├─ Command erstellen (AnalyzeJobAndResumeCommand)
    ↓
Bus::dispatch(Command)
    ↓
AnalyzeJobAndResumeCommand::handle(Handler)
    ↓
AnalyzeJobAndResumeHandler::handle()
    ├─ 1. GetCachedAnalysisAction (Cache prüfen)
    ├─ 2. AnalyzeApplicationService (AI-Analyse)
    ├─ 3. MatchingUseCase::handle() (Matching)
    ├─ 4. GapAnalysisUseCase::handle() (Gap-Analyse)
    ├─ 5. StoreCachedAnalysisAction (Cache speichern)
    ↓
AnalyzeResultDto (zurück zu Controller)
    ↓
View('result', $data)
```

---

# 🎨 4. Views

- Blade Templates  
- TailwindCSS  
- Minimalistisch  
- Panels für Ergebnisse  

---

# 🐳 5. Docker‑Architektur

Services:

- **php-fpm** (PHP 8.5)  
- **nginx** (Webserver)
- **node** (Tailwind Build)  
- **mailpit** (lokaler SMTP)

---

# 📦 6. Dependency Management

## Service Provider Registration

`AnalysisDomainServiceProvider` registriert:
- Actions (Singleton)
- UseCases (Singleton)
- Repositories (Singleton)
- Handlers (Singleton)

## Dependency Injection

- Constructor Injection für kritische Dependencies
- Laravel Service Container für optionale Dependencies

---

# 🧪 7. Testing Strategy

## Unit Tests
- Testen **Handlers** isoliert (Mock Dependencies)
- Testen **UseCases** isoliert
- Testen **Actions** isoliert
- **Keine HTTP-Layer-Tests** in Unit-Tests

## Feature Tests
- Testen **komplette HTTP-Requests**
- Testen **Integration** aller Komponenten
- Mock nur externe Services (AI)

## Test-Coverage
- **Minimum:** 95% (enforced via `make test-coverage`)
- **Aktuell:** 98.2% ✅
- **Tests:** 128 (100+ Unit, 20+ Feature)
- **Assertions:** 335+

## Testing-Framework
- **Pest 3** (Primary Framework)
- **PHPUnit 11** (Underlying)
- **Mockery** (Mocking)

## Quality-Gates
- ✅ **PHPStan:** Level 9, 0 Errors
- ✅ **Pint:** PSR-12 + Laravel Style
- ✅ **Coverage:** ≥95%
- ✅ **Tests:** Alle grün

---

# 🚫 8. Nicht im MVP enthalten

- keine Events/Listeners (geplant für später)
- keine API-Endpoints (nur Web-UI)
- keine PDF‑Generierung  
- keine Accounts/Authentication
- keine E‑Mail‑Versand (nur Mailpit für Entwicklung)

---

# 📌 9. Design Principles

## SOLID
- **S**ingle Responsibility: Jede Klasse hat nur eine Aufgabe
- **O**pen/Closed: Erweiterbar ohne Änderung
- **L**iskov Substitution: Interfaces werden eingehalten
- **I**nterface Segregation: Kleine, fokussierte Interfaces
- **D**ependency Inversion: Abhängigkeiten auf Abstraktionen

## Interface-based Design
- **"Program to an Interface, not an Implementation"**
- Dependencies zu Interfaces statt zu Konkretionen
- Austauschbarkeit über Service Provider
- Testbarkeit durch Mocking
- **Beispiele im Projekt:**
  - `AiAnalyzerInterface` (Gemini, Mock)
  - `CacheRepositoryInterface` (geplant: Database, Redis)

## DRY (Don't Repeat Yourself)
- Wiederverwendbare Actions
- Zentrale DTOs
- Repository Pattern

## KISS (Keep It Simple, Stupid)
- Klare Namenskonventionen
- Verständliche Struktur
- Keine Over-Engineering

---

# 🔒 9. Security Architecture

## 9.1 Input-Validierung

### ValidateInputAction
- **Location:** `app/Domains/Analysis/UseCases/ValidateInputUseCase/`
- **Verantwortlichkeit:** Eingabe-Validierung mit Security-Checks
- **Checks:**
  - ✅ Mindestlänge (30 Zeichen)
  - ✅ Maximallänge (50.000 Zeichen)
  - ✅ Prompt-Injection-Pattern-Erkennung
  - ✅ SQL-Injection-Pattern-Erkennung
  - ✅ Input-Sanitization

### PatternDetector & InputSanitizer
- **Location:** `app/Domains/Analysis/UseCases/ValidateInputUseCase/Validators/` & `Sanitizers/`
- **Patterns:** SQL-Injection, Prompt-Injection, Control-Characters

## 9.2 AI-Prompt-Security (Commit 18a)
- ✅ Explizite Anti-Prompt-Injection-Anweisungen
- ✅ JSON-Schema-basierte Response-Validierung
- ✅ Type-Guards in ParseAiResponseAction

## 9.3 CSRF & SQL-Injection-Prevention
- ✅ `@csrf`-Token in Forms + Security-Tests
- ✅ Repository Pattern mit Eloquent (Prepared Statements)

## 9.4 Error-Handling
- ✅ AI-Timeouts, ungültige Responses gefangen
- ✅ User-freundliche Fehlermeldungen

## 9.5 Security-Tests
- `SecurityAuditTest.php`, `ApiErrorHandlingTest.php`, `ValidateInputActionTest.php`, `ProfileResumeStorageTest.php`

---

# 🔮 10. Zukunft / Erweiterbarkeit

## Weiterentwicklung bestehender Bounded Contexts

### `Profile` Context (naechste Ausbaustufe)
- **Status:** Basis in Commit 22 implementiert
- **Naechste Features:**
  - User-Accounts
  - mehrere gespeicherte CVs pro Benutzer
  - Praeferenzen / Profil-Metadaten
  - Migration auf userbasierte Verschluesselung
- **Integration:** Weiterhin lose Kopplung an `Analysis` via DTOs/UI-Flows

### `Recommendations` Context
- **Commit:** ~30+
- **Features:**
  - KI-Empfehlungen als eigenstaendiger Kontext
  - Verbesserungsvorschlaege
  - Beispiel-Formulierungen
- **Struktur:** `app/Domains/Recommendations/`
- **Integration:** Konsumiert `Analysis`-Ergebnisse

### `Reporting` Context
- **Commit:** ~35+
- **Features:**
  - Analyse-Historie
  - Statistiken
  - PDF/Word-Export
- **Struktur:** `app/Domains/Reporting/`
- **Integration:** Read-Only auf `Analysis` + `Profile`

## Weitere Erweiterungen
1. **Events & Listeners** (nach MVP)
2. **API-Layer** (RESTful API)
3. **Queue-Processing** (Async AI-Analysen)
4. **Multi-Tenancy** (spaeter, falls Produktvision es traegt)

## Wie erweitern?
- **Neue Domain hinzufügen:** `app/Domains/NewDomain/`
  - Commands, Queries, Handlers, UseCases, DTOs
- **Neue UseCase hinzufügen:** In bestehende Domain
- **Neue Action hinzufügen:** In UseCase-Unterordner
- **Neuer Command/Query:** Mit eigenem Handler
- **Context-Integration:** Via DTOs, Events oder Shared Kernel

---

# 📖 11. Coding Guidelines

Siehe `CODING_GUIDELINES.md` für detaillierte Best Practices.

---

# 🎯 12. Ziel der Architektur

- **Klarheit**: Jede Komponente hat klare Verantwortung
- **Einfachheit**: Keine unnötige Komplexität
- **Erweiterbarkeit**: Neue Features einfach hinzufügen
- **Testbarkeit**: Jede Komponente isoliert testbar
- **Stabilität**: Robuste Fehlerbehandlung
- **Performance**: Caching, optimierte DB-Queries

