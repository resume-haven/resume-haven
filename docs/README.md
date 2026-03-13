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

#### [**AGENTS.md**](AGENTS.md)
Dokumentation der AI-Agenten und deren Verwendung.

**Inhalt:**
- Übersicht über verwendete AI-Agenten
- Agent-Konfiguration
- Prompt-Engineering
- Integration mit Laravel AI
- Verwendung von Structured Outputs

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


#### [**../COMMIT_PLAN.md**](../COMMIT_PLAN.md)
Aktiver, verschlankter Commit-Plan fuer den aktuellen Arbeitsfokus.

#### [**COMMIT_HISTORY_INDEX.md**](COMMIT_HISTORY_INDEX.md)
Indexseite fuer ausgelagerte Commit-Historie.

#### [**history/COMMIT_HISTORY_2026.md**](history/COMMIT_HISTORY_2026.md)
Kompakte Historie der abgeschlossenen Commits (1-23).

#### [**history/COMMIT_22_IMPLEMENTATION_GUIDE.md**](history/COMMIT_22_IMPLEMENTATION_GUIDE.md)
Historischer Implementierungsleitfaden fuer den `Profile`-Context aus Commit 22.

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
| **Domain-Driven Design** | `ARCHITECTURE.md`, `CODING_GUIDELINES.md` |
| **Command/Handler Pattern** | `ARCHITECTURE.md` (Kap. 2.1), `CODING_GUIDELINES.md` (Kap. 5) |
| **UseCase & Actions** | `CODING_GUIDELINES.md` (Kap. 6) |
| **DTOs** | `CODING_GUIDELINES.md` (Kap. 7) |
| **Repositories** | `CODING_GUIDELINES.md` (Kap. 8) |
| **Controller Best Practices** | `CODING_GUIDELINES.md` (Kap. 9) |
| **Testing** | `CODING_GUIDELINES.md` (Kap. 10) |
| **PHPStan & Code Quality** | `CODING_GUIDELINES.md` (Kap. 11) |
| **Error Handling** | `CODING_GUIDELINES.md` (Kap. 12) |
| **Refactoring-Geschichte** | `REFACTORING_SUMMARY.md` |
| **AI-Integration** | `AGENTS.md` |
| **Feature-Roadmap** | `ROADMAP.md` |
| **Contribution Process** | `CONTRIBUTING.md` |

---

## 📝 Hinweise

- **Immer aktuell halten**: Diese Dokumentation sollte bei größeren Änderungen aktualisiert werden
- **Praxisbeispiele**: Siehe `CODING_GUIDELINES.md` für konkrete Code-Beispiele
- **Fragen?**: Erstelle ein Issue oder kontaktiere das Team

---

**Letzte Aktualisierung**: 2026-03-02  
**Version**: 1.0 (nach Domain-Architektur Refactoring)
