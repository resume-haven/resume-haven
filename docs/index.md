# 📚 ResumeHaven – Dokumentation

Willkommen zur Dokumentation von **ResumeHaven**!  
Hier findest du alle wichtigen Informationen zur Architektur, Entwicklung und Nutzung des Projekts.

> 🌐 **Diese Dokumentation ist auch online verfügbar:**  
> [GitHub Pages](https://username.github.io/resume-haven/) (nach Aktivierung)  
> [Setup-Anleitung für GitHub Pages](GITHUB_PAGES_SETUP.md)

---

## 🗂️ Übersicht

### 🏗️ Architektur & Design

#### [**ARCHITECTURE.md**](ARCHITECTURE.md)
Vollständige technische Architektur-Dokumentation des Projekts.

**Inhalt:**
- Überblick über Domain-Driven Design
- Command/Handler Pattern
- UseCase & Action Pattern
- Repository Pattern
- Request-Flow Diagramme
- Dependency Management
- Testing-Strategie
- Zukünftige Erweiterungen

**Für wen:** Entwickler, die die technische Architektur verstehen wollen


#### [**CODING_GUIDELINES.md**](CODING_GUIDELINES.md)
Umfassende Best Practices und Coding-Standards für das Projekt.

**Inhalt:**
- SOLID Principles & DRY
- Projekt-Struktur (Domain-Struktur)
- Namenskonventionen
- Domain-Driven Design Guidelines
- Commands, Handlers, UseCases, Actions
- DTOs (Data Transfer Objects)
- Repositories
- Controllers (Single Action)
- Testing (Unit, Feature, Integration)
- Code Quality (PHPStan, Pint)
- Error Handling
- Checkliste für neue Features

**Für wen:** Alle Entwickler, die am Projekt arbeiten


#### [**REFACTORING_SUMMARY.md**](REFACTORING_SUMMARY.md)
Zusammenfassung der Domain-Architektur-Refaktorierung (Commit 15a).

**Inhalt:**
- Ziel der Refaktorierung
- Was wurde umgesetzt (Pattern, Struktur)
- Vorher/Nachher-Vergleich
- Controller-Reduktion (94 → 34 Zeilen)
- Metriken & Quality Checks
- Request-Flow Diagramm
- Lessons Learned
- Nächste Schritte

**Für wen:** Entwickler, die verstehen wollen, warum die Architektur so ist


---

### 🤖 AI & Agenten

#### [**ai/WORKING_BASELINE.md**](ai/WORKING_BASELINE.md)
Operativer Session-Startpunkt fuer KI-gestuetzte Arbeit (Soft-Reset-Basis).

**Inhalt:**
- Verbindliche Tages-Baseline fuer KI-Sessions
- Prioritaet: aktueller Repository-Stand
- Architektur- und Qualitaets-Leitplanken in Kurzform
- Reset-Protokoll fuer lange Chat-Verlaeufe

**Für wen:** KI-Agenten und Entwickler fuer einen schnellen, konsistenten Einstieg

#### [**ai/SESSION_RESUME_2026-03-09.md**](ai/SESSION_RESUME_2026-03-09.md)
Tagesaktuelle Zusammenfassung (Soft-Reset nach Kontextverlust).

**Inhalt:**
- Was wurde heute erreicht (Kontext-Konsolidierung, Dokumentations-Updates)
- Aktueller Projekt-Stand (Commit 20b abgeschlossen)
- Nächste geplante Commits (21, 21a, 22)
- Soft-Reset-Protokoll und Lesefolge
- Action Items für neue Sessions

**Für wen:** KI-Agenten nach Kontext-Reset, Entwickler beim Wiedereinstieg nach längerer Pause

#### [**ai/AGENT_CONTEXT.md**](ai/AGENT_CONTEXT.md)
Zentrale Arbeitsregeln für KI-Agenten (GitHub Copilot, etc.)

**Inhalt:**
- CQRS (strict mode, phasenweise Einführung)
- SOLID-Prinzipien (Pflicht-Review-Gate)
- Domain-Driven Design (Bounded Contexts)
- Quality-Gates (Tests, Coverage, PHPStan, Pint)
- Definition of Done
- Code-Review-Checkliste

**Für wen:** KI-Agenten, Entwickler, die die Architekturprinzipien verstehen wollen

#### [**ai/PROJECT_OVERVIEW.md**](ai/PROJECT_OVERVIEW.md)
Projektüberblick und MVP-Scope

**Inhalt:**
- Was ist ResumeHaven?
- MVP-Funktionsumfang
- Architektur-Kurzform
- Datenstrukturen (Kern-DTOs)
- Request-Flow
- Roadmap

**Für wen:** Neue Entwickler, Product Owner, KI-Agenten

#### [**ai/TECH_STACK.md**](ai/TECH_STACK.md)
Technologie-Stack und Konfiguration

**Inhalt:**
- Versionen (PHP, Laravel, Pest, PHPStan, etc.)
- Docker-Services
- Make-Kommandos
- Konfiguration (.env)
- URLs
- Update-Strategie

**Für wen:** DevOps, Entwickler, KI-Agenten

#### [**AGENTS.md**](AGENTS.md)
Dokumentation der AI-Agenten und deren Verwendung.

**Inhalt:**
- Übersicht über verwendete AI-Agenten
- Agent-Konfiguration
- Prompt-Engineering
- Integration mit Laravel AI
- Verwendung von Structured Outputs
- **Verweis auf neue Kontext-Struktur**

**Für wen:** Entwickler, die mit den AI-Agenten arbeiten


---

### 🛣️ Projekt-Planung

#### [**ROADMAP.md**](ROADMAP.md)
Langfristige Vision und Feature-Roadmap für ResumeHaven.

**Inhalt:**
- MVP-Features (aktuell)
- Geplante Features (Phase 2, 3, ...)
- Technische Verbesserungen
- UI/UX Enhancements
- API-Entwicklung
- Zeitplan

**Für wen:** Product Owner, Stakeholder, Entwickler


#### [**COMMIT_22_IMPLEMENTATION_GUIDE.md**](COMMIT_22_IMPLEMENTATION_GUIDE.md)
Implementierungsleitfaden fuer den neuen `Profile`-Context und die tokenbasierte CV-Speicherung.

**Inhalt:**
- Architektur und Dateistruktur von Commit 22
- CQRS-Flow fuer Speichern/Laden
- Token- und Krypto-Entscheidungen
- Testabdeckung und Quality Gates
- MVP-Limitierungen und naechste Schritte

**Für wen:** Entwickler und KI-Agenten, die an Commit 22 weiterarbeiten

---

### 🛠️ Entwicklung & Debugging

#### [**DEVELOPMENT.md**](DEVELOPMENT.md)
Lokales Entwicklungs-Setup mit Docker, Makefile-Kommandos und Workflow-Empfehlungen.

**Inhalt:**
- Setup & Start der Container
- Tests, Linting, PHPStan
- Shell-/DB-Kommandos
- Xdebug-Quickstart und Coverage-Workflows

#### [**DEBUGGING.md**](DEBUGGING.md)
Vollständige Xdebug-Anleitung für VSCode/PhpStorm inkl. Coverage-Reports.

**Inhalt:**
- `make debug-on/off/status`
- IDE-Setup (Port 9003, Path-Mapping)
- CLI-Debugging
- Coverage in Konsole und als Dateien (`coverage-report/`)
- Troubleshooting

---

### 🤝 Contributing

#### [**CONTRIBUTING.md**](CONTRIBUTING.md)
Richtlinien für Beiträge zum Projekt.

**Inhalt:**
- Wie kann ich beitragen?
- Code of Conduct
- Pull Request Prozess
- Branch-Strategie
- Commit-Konventionen
- Testing-Anforderungen

**Für wen:** Externe Contributor, Team-Mitglieder


---

## 🚀 Schnelleinstieg

### Für KI-Agenten (Copilot, Windsurf, etc.)

1. **Einstieg (Soft-Reset)**: Lies [`ai/WORKING_BASELINE.md`](ai/WORKING_BASELINE.md)
2. **Arbeitsregeln**: Lies [`ai/AGENT_CONTEXT.md`](ai/AGENT_CONTEXT.md)
3. **Projektüberblick**: Lies [`ai/PROJECT_OVERVIEW.md`](ai/PROJECT_OVERVIEW.md)
4. **Architektur**: Lies [`ARCHITECTURE.md`](ARCHITECTURE.md)

### Für Entwickler

1. **Start**: Lies [`../README.md`](../README.md) für Installation & Setup
2. **Architektur verstehen**: Lies [`ARCHITECTURE.md`](ARCHITECTURE.md)
3. **Coding Standards**: Lies [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)
4. **Feature entwickeln**: Folge der Checkliste in `CODING_GUIDELINES.md`

### Für Product Owner

1. **Vision**: Lies [`ROADMAP.md`](ROADMAP.md)
2. **Status**: Siehe [`../COMMIT_PLAN.md`](../COMMIT_PLAN.md)
3. **Architektur-Überblick**: Lies [`ARCHITECTURE.md`](ARCHITECTURE.md) (Kapitel 1-3)

### Für Contributor

1. **Guidelines**: Lies [`CONTRIBUTING.md`](CONTRIBUTING.md)
2. **Coding Standards**: Lies [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)
3. **Architektur**: Lies [`ARCHITECTURE.md`](ARCHITECTURE.md)

---

## 📖 Weitere Dokumentation

### Im Root-Verzeichnis

- **[README.md](../README.md)**: Projekt-Übersicht, Installation, Quick Start
- **[COMMIT_PLAN.md](../COMMIT_PLAN.md)**: Detaillierter Entwicklungsplan (Commit-by-Commit)
- **[LICENSE.md](../LICENSE.md)**: Lizenzinformationen

### In `.github/`

- **[copilot-instructions.md](../.github/copilot-instructions.md)**: GitHub Copilot Konfiguration
- **[agents/](../.github/agents/)**: Agent-Definitionen

---

## 🔍 Suche nach Themen

| Thema | Datei |
|-------|-------|
| **CQRS (Command Query)** | `ai/AGENT_CONTEXT.md`, `ARCHITECTURE.md` (Kap. 2.1) |
| **SOLID-Prinzipien** | `ai/AGENT_CONTEXT.md`, `CODING_GUIDELINES.md` (Kap. 2) |
| **Interface-based Design** | `ai/AGENT_CONTEXT.md`, `CODING_GUIDELINES.md`, `ARCHITECTURE.md` (Kap. 9) |
| **Domain-Driven Design (DDD)** | `ai/AGENT_CONTEXT.md`, `ARCHITECTURE.md`, `CODING_GUIDELINES.md` |
| **Command/Handler Pattern** | `ARCHITECTURE.md` (Kap. 2.1), `CODING_GUIDELINES.md` (Kap. 5) |
| **UseCase & Actions** | `CODING_GUIDELINES.md` (Kap. 6) |
| **DTOs** | `CODING_GUIDELINES.md` (Kap. 7), `ai/PROJECT_OVERVIEW.md` |
| **Repositories** | `CODING_GUIDELINES.md` (Kap. 8) |
| **Controller Best Practices** | `CODING_GUIDELINES.md` (Kap. 9) |
| **Testing** | `CODING_GUIDELINES.md` (Kap. 10), `ai/AGENT_CONTEXT.md` |
| **PHPStan & Code Quality** | `CODING_GUIDELINES.md` (Kap. 11), `ai/TECH_STACK.md` |
| **Error Handling** | `CODING_GUIDELINES.md` (Kap. 12) |
| **Refactoring-Geschichte** | `REFACTORING_SUMMARY.md` |
| **AI-Integration** | `AGENTS.md`, `ai/PROJECT_OVERVIEW.md` |
| **Tech Stack & Versionen** | `ai/TECH_STACK.md` |
| **Feature-Roadmap** | `ROADMAP.md`, `../COMMIT_PLAN.md` |
| **Contribution Process** | `CONTRIBUTING.md`, `../.github/PULL_REQUEST_TEMPLATE.md` |

---

## 📝 Hinweise

- **Immer aktuell halten**: Diese Dokumentation sollte bei größeren Änderungen aktualisiert werden
- **Praxisbeispiele**: Siehe `CODING_GUIDELINES.md` für konkrete Code-Beispiele
- **Fragen?**: Erstelle ein Issue oder kontaktiere das Team

---

**Letzte Aktualisierung**: 2026-03-09  
**Version**: 2.1 (inkl. WORKING_BASELINE als Soft-Reset-Einstieg)
