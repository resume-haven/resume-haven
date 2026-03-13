# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/lang/de/).

---

## [Unreleased]

### Added
- **Commit 22 – Anonyme CV-Speicherung (Profile Context) finalisiert**
  - Neuer Bounded Context `Profile` mit CQRS-Basis:
    - `StoreResumeCommand`, `GetResumeByTokenQuery`
    - `StoreResumeHandler`, `GetResumeByTokenHandler`
    - `ProfileRepository`
    - immutable DTOs (`StoreResumeDto`, `ResumeTokenDto`, `LoadedResumeDto`)
  - Persistenz fuer gespeicherte Lebenslaeufe in `stored_resumes`
    - Felder inkl. `token`, `encrypted_cv`, `last_accessed_at`
  - Verschluesselung via AES-256-GCM (MVP: tokenbasierte Secret-Ableitung)
  - UI-Erweiterung in `analyze.blade.php`:
    - Generierter Speicher-Link
    - Copy-to-Clipboard-Button mit visuellem Feedback

### Changed
- `StoreResumeController`: defensiven, praktisch unerreichbaren String-Guard entfernt
  - Typgarantie erfolgt bereits ueber `StoreResumeRequest` (`cv_text` als `string`)
- Dokumentation fuer Commit 22 erweitert/aktualisiert
  - `COMMIT_PLAN.md`
  - `docs/ARCHITECTURE.md`
  - `docs/CODING_GUIDELINES.md`
  - `docs/history/PLANNING_COMMIT_22.md`
  - `docs/history/COMMIT_22_IMPLEMENTATION_GUIDE.md`

### Fixed
- Coverage-Luecken fuer Commit-22-nahe Komponenten geschlossen
  - `DecryptResumeAction` auf 100%
  - `StoreResumeController` auf 100%
  - `GetCachedAnalysisAction` auf 100%
  - `LegalController` und `ContactController` auf 100%
- Zusätzliche Tests fuer Edge Cases und UX-Flows
  - Feature: `ProfileResumeStorageTest`, `AnalyzeResumeStorageUiTest`, `LegalPagesTest`, `ContactFormTest`
  - Unit: `ResumeCryptoActionsTest`, `GetCachedAnalysisActionTest`

### Security
- Fehlerfaelle bei ungueltigen Tokens und defekten Payloads explizit abgesichert
- Keine Klartextpersistenz fuer gespeicherte CV-Inhalte

### Documentation
- Finaler Implementierungsleitfaden fuer Commit 22 hinzugefuegt:
  - `docs/history/COMMIT_22_IMPLEMENTATION_GUIDE.md`

---

## [0.4.0] - 2026-03-08

### Added - Recommendations & Security Hardening

- **Commit 17 – Empfehlungen & Verbesserungsvorschläge**
  - `RecommendationDto` (immutable, typed mit priority: high|medium|low)
  - AI-Prompt erweitert um `recommendations`-Feld
  - `ParseAiResponseAction` parst recommendations mit Type-Guards
  - MockAiAnalyzer: Alle Szenarien mit realistischen Empfehlungen
  - UI: Neues Panel "💡 Empfehlungen & Verbesserungsvorschläge" in result.blade.php
    - Prioritäts-Badges (farbcodiert: high=rot, medium=gelb, low=grün)
    - Verbesserungsvorschläge mit Beispiel-Formulierungen
  - Tests: `RecommendationDtoTest`, `ParseAiResponseActionTest` (erweitert), `RecommendationsUiTest`
  - Cache-Integration: `GetCachedAnalysisAction` rekonstruiert recommendations als DTOs

- **Commit 18a – Security Härtung**
  - Prompt-Injection-Schutz im Analyzer-Prompt (explizite Anti-Injection-Anweisungen)
  - Input-Validierung mit PatternDetector & InputSanitizer
  - Error-Handling für AI-API-Timeouts und ungültige Responses
  - Security-Tests: `SecurityAuditTest`, `ApiErrorHandlingTest`

### Changed
- `composer.json`: Security-Test-Scripts korrigiert (Filter → Dateinamen)
  - **Fix:** `test:pest-security` hängte nicht mehr (Timeout-Problem behoben)
- `ARCHITECTURE.md`: Security-Sektion hinzugefügt, Recommendations-Status aktualisiert
- `AnalyzeJobAndResumeHandler`: Leitet recommendations von AI-Analyse durch
- Cache-Struktur: PHPDoc in `AnalysisCacheRepository` und `AnalysisCache` erweitert

### Fixed
- PHPStan Level 9 Errors behoben:
  - RecommendationDto: default-Cases in match-Expressions entfernt (Type-Hints vollständig)
  - ParseAiResponseAction: Type-Guards für recommendation-Parsing
  - GetCachedAnalysisAction: Redundante Runtime-Checks entfernt (PHPDoc-basierte Typisierung)
- Feature-Tests: `RecommendationsUiTest` (4 Tests) - Recommendations werden jetzt korrekt durch Handler und Cache durchgereicht

---

## [0.3.0] - 2026-03-08

### Added - Security & Quality Gates
- **Security-Testing**: OWASP-orientierte Testinfrastruktur
  - `make test-security` für grundlegende Security-Tests
  - `make test-security-strict` mit erweitertem Filter und stop-on-failure
  - `make test-security-gate` kombiniert Security + PHPStan + Pint
  - Composer-Scripts: `test:pest-security` und `test:pest-security-strict`
- **OWASP-Mapping-Tabelle** in `docs/CODING_GUIDELINES.md`
  - Mapping von OWASP Top 10 zu konkreten Projektmaßnahmen
  - Test-/Review-Checks für jedes Risiko
- **Security-Test-Template** in `docs/DEVELOPMENT.md`
  - PR-Checkliste Security
  - OWASP-Kurzcheck
  - Empfohlene Testdatei-Namen
- **Legal-Seiten-Planung** in `COMMIT_PLAN.md` (Commit 20b)
  - Impressum, Datenschutz, Kontakt, Lizenzen
  - Kontaktformular ohne mailto-Fallback (Option A)
  - Lizenzen automatisiert aus Lockfiles (Option B)

### Changed
- **Roadmap aktualisiert** (`docs/ROADMAP.md`)
  - Status-Markierungen für alle Phasen
  - Security-Testing als "✅ umgesetzt" markiert
  - "Aktueller Stand"-Sektion hinzugefügt
  - Make-Kommandos-Übersicht ergänzt
- **Makefile erweitert**
  - Neue Targets: `test-security`, `test-security-strict`, `test-security-gate`
  - `.PHONY` aktualisiert
- **Coding Guidelines erweitert**
  - OWASP Security by Design als verbindliches Prinzip
  - Interface-based Design detailliert dokumentiert
  - SOLID-Gate, CQRS-Enforcement, DDD-Enforcement ergänzt

### Documentation
- Alle Engineering-Dokumentationen konsolidiert:
  - `docs/ai/AGENT_CONTEXT.md` (zentrale Arbeitsregeln)
  - `docs/ai/PROJECT_OVERVIEW.md` (Projektüberblick)
  - `docs/ai/TECH_STACK.md` (Versionen, Docker, Config)
  - `.github/PULL_REQUEST_TEMPLATE.md` (SOLID-Gate-Checkliste)

---

## [0.2.0] - 2026-03-05

### Added - Domain Architecture & CQRS
- **Domain-Driven Design**: Vollständige Refaktorierung zu DDD-Architektur
  - Bounded Context: `Analysis`
  - Commands: `AnalyzeJobAndResumeCommand`
  - Handlers: `AnalyzeJobAndResumeHandler`
  - UseCases: `MatchingUseCase`, `GapAnalysisUseCase`, `ScoringUseCase`, `GenerateTagsUseCase`, `ValidateInputUseCase`
  - Actions: Granulare Business-Logic in einzelnen Actions
- **CQRS-Pattern**: Phase 1 abgeschlossen
  - Commands für Write-Operationen
  - Queries für Read-Operationen (Phase 2 geplant)
- **Interface-based Design**: `AiAnalyzerInterface`
  - `GeminiAiAnalyzer` (Production)
  - `MockAiAnalyzer` (Development mit verschiedenen Szenarien)
- **Analysis Cache Management**: `cache:clear-analysis` Artisan-Command
  - Optionaler `--older-than` Filter
  - Makefile-Target: `cache-clear-analysis`
- **Tag-Struktur erweitert**: AI-Response mit strukturierten Tags
  - `tags.matches` (gruppierte Matches)
  - `tags.gaps` (Array von Strings)
  - Fallback-Generierung via `GenerateTagsAction`
- **Score-Berechnung**: `CalculateScoreAction` mit farbkodierter Anzeige
  - SVG-Kreisindikator
  - Farbskala: Rot (0-40%), Gelb (40-70%), Grün (70-100%)
- **Xdebug-Integration**: Vollständige Debug- und Coverage-Unterstützung
  - `make debug-on/debug-off/debug-status`
  - Coverage-Reports mit 98.2% (Minimum: 95%)

### Changed
- **AnalyzeController** von 94 auf 34 Zeilen reduziert (63% kleiner)
- **Test-Coverage** von ~85% auf 98.2% erhöht
- **PHPStan Level** von 5 auf 9 angehoben (0 Errors)

### Fixed
- Docker-Permissions-Issue: PHP-Container mit korrekter UID/GID
- 502 Bad Gateway Problem behoben
- PHP-FPM `www.conf` auf alle Interfaces konfiguriert

---

## [0.1.0] - 2026-02-28

### Added - MVP Foundation
- **Projekt initialisiert**: Docker-Setup mit PHP 8.5, Laravel 12, Nginx, Node, Mailpit
- **TailwindCSS 3** integriert mit Dark-Mode-Support
- **Basis-UI**: Landing Page, Analyse-Formular, Ergebnis-Seite
- **AnalyzeController**: Grundlegende Validierung und Routing
- **Laravel AI Integration**: Gemini 2.5 Flash für Analyse
- **Ergebnis-Darstellung**: Anforderungen, Erfahrungen, Matches, Gaps
- **Basis-Tests**: Feature-Tests und Unit-Tests mit Pest 3
- **Code-Quality**: Laravel Pint für PSR-12 Formatting

### Documentation
- `README.md` mit Setup-Anleitung
- `docs/ARCHITECTURE.md` mit Grundstruktur
- `docs/DEVELOPMENT.md` mit lokaler Setup-Anleitung
- `COMMIT_PLAN.md` mit detailliertem Entwicklungsplan

---

## Kategorien

- **Added**: Neue Features
- **Changed**: Änderungen an bestehenden Features
- **Deprecated**: Bald zu entfernende Features
- **Removed**: Entfernte Features
- **Fixed**: Bugfixes
- **Security**: Sicherheitsrelevante Änderungen
- **Documentation**: Dokumentationsänderungen

---

## Version-Schema

**MAJOR.MINOR.PATCH** (Semantic Versioning)

- **MAJOR**: Breaking Changes (API-Änderungen, Architektur-Refactorings)
- **MINOR**: Neue Features (abwärtskompatibel)
- **PATCH**: Bugfixes (abwärtskompatibel)

**Beispiel**:
- `0.1.0` → MVP Foundation
- `0.2.0` → Domain Architecture
- `0.3.0` → Security & Quality Gates
- `1.0.0` → Production-Ready Release

---

**Letzte Aktualisierung**: 2026-03-10
