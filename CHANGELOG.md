# Changelog

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
und dieses Projekt folgt [Semantic Versioning](https://semver.org/lang/de/).

---

## [Unreleased]

### Added
- **Commit 25 – Analysequalitaet & Erklaerbarkeit (in Umsetzung)**
  - Persistente Baseline im `Profile`-Context (neue Tabelle `analysis_baselines`):
    - `AnalysisBaseline`-Model + Migration
    - `AnalysisBaselineDto` (immutable)
    - `AnalysisBaselineRepository` mit `upsert()` und `find()`
  - Delta-Engine fuer erklaerbare Vergleiche:
    - `ScoreDeltaDto`, `RecommendationDeltaDto`, `AnalysisComparisonDto`
    - `BuildAnalysisComparisonAction` – erzeugt Delta-Daten aus persistenter Baseline oder Session-Fallback
    - `ResolveBaselineKeyAction` – bestimmt Baseline-Schluessel (Token- oder Session-basiert)
  - Fallback-Mechanismus: Session-Snapshot, wenn keine persistente Baseline vorhanden
  - Result-UI mit Delta/Impact-Panel:
    - Score-Delta, Match-Delta, Gap-Delta mit Richtungspfeilen (`↑`, `→`, `↓`)
    - Farbkodierung: Verbesserung (gruen), Gleichstand (blau), Verschlechterung (rot)
    - Prioritaetswechsel bei Empfehlungen sichtbar
  - Neue Unit-Tests:
    - `BuildAnalysisComparisonActionTest`:
      - Null-Score → kein Vergleich
      - Baseline-Speicherung bei normaler Analyse
      - Delta-Berechnung (Verbesserung, Gleichstand, Verschlechterung)
      - Session-Fallback inkl. Empfehlungs-Normalisierung
      - Ungültiger Session-Snapshot → `null`
    - `BuildAnalyzeViewDataActionTest` um explizite `comparison`-Assertions erweitert
    - `AnalyzeControllerUnitTest`: View-Daten enthalten `comparison` korrekt
  - Neue Feature-Tests:
    - `AnalysisBaselineRepositoryTest`:
      - `find()` liefert `null` ohne Treffer
      - `upsert()` aktualisiert statt Duplikate anzulegen
      - Normalisierung filtert ungültige Empfehlungs-Eintraege
      - `null`-Persistenzwert ergibt leere Empfehlungsliste
    - `AnalysisComparisonTest` erweitert:
      - Kompetenz-Analyse mit neutralem Vergleich (`→`)
      - Kompetenz-Analyse mit Verschlechterung (`↓`)
    - `GenerateLicenseDataCommandTest` erweitert:
      - Fehlende Lock-Dateien → leere Paketlisten
      - Ungueltige Lock-Formate → robustes Fehlerverhalten
      - Parsing, Normalisierung und alphabetische Sortierung

- **Commit 24 – Kompetenzlebenslaeufe I (MVP-light) finalisiert**
  - Neues Analyseartefakt fuer Kompetenzlebenslaeufe:
    - `RenderCompetenceResumeTextAction` rendert `CompetenceResumeDto` deterministisch in Textform
  - Neuer Reuse-Flow fuer Analysequelle:
    - `UseCompetenceResumeController`
    - Route: `POST /profile/competence-resume/use` (`profile.competence-resume.use`)
  - Analyze-UI erweitert:
    - Vorschau inkl. Analyseartefakt (`competence_resume_text`)
    - Hinweis auf aktive Analysequelle (`cv_source=competence_resume`)
    - Aktion "Kompetenzlebenslauf fuer Analyse verwenden"
  - Session-Daten fuer Nachvollziehbarkeit erweitert:
    - `competence_resume_text`, `original_cv_text`

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
- `GenerateLicenseDataCommand`: Ungültige `packages`-Struktur in `composer.lock` führt nicht mehr zu `assert()`-Abbruch (Commit 25)
- `GenerateLicenseDataCommand`: Leere Lizenz-Arrays werden als `unknown` normalisiert (Commit 25)
- `ExecuteAnalyzeFlowAction`: Snapshot-Logik und Baseline-Aufbau in `buildComparisonData()` gekapselt (Commit 25)
- `StoreResumeController`: defensiven, praktisch unerreichbaren String-Guard entfernt (Commit 24)
  - Typgarantie erfolgt bereits ueber `StoreResumeRequest` (`cv_text` als `string`)
- `COMMIT_PLAN.md` auf Commit-25-Status aktualisiert
- `docs/PLANNING_COMMIT_24.md` auf abgeschlossen gesetzt und mit Umsetzungsstand finalisiert
- `docs/history/COMMIT_HISTORY_2026.md` um Commit 24 ergaenzt
- Dokumentation fuer Commit 22 erweitert/aktualisiert:
  - `docs/ARCHITECTURE.md`, `docs/CODING_GUIDELINES.md`
  - `docs/history/PLANNING_COMMIT_22.md`, `docs/history/COMMIT_22_IMPLEMENTATION_GUIDE.md`

### Fixed
- `GenerateLicenseDataCommand` crashte bei `composer.lock` mit `packages`-Wert != Array (Commit 25)
- Leere Lizenz-Arrays (`[]`) wurden als Leerstring serialisiert statt als `unknown` (Commit 25)
- Kompetenzlebenslauf-Reuse: klarer Fehlerpfad bei fehlendem Analyseartefakt in der Session (Commit 24)
- Coverage-Luecken fuer Commit-22-nahe Komponenten geschlossen (Commit 24):
  - `DecryptResumeAction`, `StoreResumeController`, `GetCachedAnalysisAction` auf 100 %
  - `LegalController` und `ContactController` auf 100 %
- Zusätzliche Tests fuer Edge Cases und UX-Flows (Commit 24):
  - Feature: `ProfileResumeStorageTest`, `AnalyzeResumeStorageUiTest`, `LegalPagesTest`, `ContactFormTest`
  - Unit: `ResumeCryptoActionsTest`, `GetCachedAnalysisActionTest`

### Security
- Fehlerfaelle bei ungueltigen Tokens und defekten Payloads explizit abgesichert (Commit 24)
- Keine Klartextpersistenz fuer gespeicherte CV-Inhalte (Commit 24)

### Documentation
- Finaler Implementierungsleitfaden fuer Commit 22: `docs/history/COMMIT_22_IMPLEMENTATION_GUIDE.md`
- Finaler Implementierungsleitfaden fuer Commit 24: `docs/history/COMMIT_24_IMPLEMENTATION_GUIDE.md`

### Quality
- Test-Coverage von **98.2 % → 98.4 %** (Minimum: 95 %)
- Neue/erweiterte Hotspot-Abdeckungen:
  - `Console/Commands/GenerateLicenseDataCommand` → **96.8 %** (vorher: 69.1 %)
  - `Domains/Profile/Repositories/AnalysisBaselineRepository` → **100.0 %** (vorher: 91.1 %)
  - `Domains/Analysis/UseCases/PresentationUseCase/BuildAnalysisComparisonAction` → **98.2 %** (vorher: 96.5 %)
- `make phpstan` → **0 Errors** (Level 9, 96 Dateien)
- `make pint-analyse` → **pass**
- **254 Tests**, **1764 Assertions**

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

**Letzte Aktualisierung**: 2026-03-17
