# ResumeHaven – Architektur

Dieses Dokument beschreibt die technische Architektur des ResumeHaven‑MVP.

---

# 🧠 1. Überblick

ResumeHaven ist ein **Domain-driven, Command/Query-orientiertes** Analyse‑Tool.  
Die Architektur folgt modernen Best Practices:

- **Domain-Driven Design** (modulare Geschäftsbereiche)
- **CQRS-Light** (Command/Handler Pattern)
- **Single Action Controllers** (Controller sind dünn)
- **Repository Pattern** (Persistence-Abstraktion)
- **UseCase Pattern** (Business-Logic-Orchestrierung)
- **Wartbarkeit, Testbarkeit, Erweiterbarkeit**

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
AnalyzeController::analyze()
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
- Ziel: >80%
- Aktuell: 18 Tests, 45 Assertions

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

## DRY (Don't Repeat Yourself)
- Wiederverwendbare Actions
- Zentrale DTOs
- Repository Pattern

## KISS (Keep It Simple, Stupid)
- Klare Namenskonventionen
- Verständliche Struktur
- Keine Over-Engineering

---

# 🔮 10. Zukunft / Erweiterbarkeit

## Geplante Erweiterungen
1. **Events & Listeners** (Notification-System)
2. **API-Layer** (RESTful API)
3. **Queue-Processing** (Async AI-Analysen)
4. **Multi-Tenancy** (User-Accounts)
5. **PDF-Export** (Analyse-Berichte)

## Wie erweitern?
- Neue Domain hinzufügen: `app/Domains/NewDomain/`
- Neue UseCase hinzufügen: In bestehende Domain
- Neue Action hinzufügen: In UseCase-Unterordner
- Neuer Command: Mit eigenem Handler

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
  
