# 📚 ResumeHaven – Documentation

Welcome to the **ResumeHaven** documentation!
Here you will find all important information about the architecture, development and use of the project.

> 🌐 **This documentation is also available online:**
> [GitHub Pages](__TK49__ (after activation)
> [GitHub Pages Setup Guide](GITHUB_PAGES_SETUP.md)

---

## 🗂️ Overview

### 🏗️ Architecture & Design

#### [**ARCHITECTURE.md**](ARCHITECTURE.md)
Complete technical architecture documentation of the project.

**Contents:**
- Overview of Domain Driven Design
- Command/Handler Pattern
- UseCase & Action Patterns
- Repository Patterns
- Request flow diagrams
- Dependency management
- Testing strategy
- Future expansions

**For whom:** Developers who want to understand the technical architecture


#### [**CODING_GUIDELINES.md**](CODING_GUIDELINES.md)
Comprehensive best practices and coding standards for the project.

**Contents:**
- SOLID Principles & DRY
- Project structure (domain structure)
- Naming conventions
- Domain Driven Design Guidelines
- Commands, Handlers, UseCases, Actions
- DTOs (Data Transfer Objects)
- Repositories
- Controllers (Single Action)
- Testing (unit, feature, integration)
- Code Quality (PHPStan, Pint)
- Error handling
- Checklist for new features

**For whom:** All developers working on the project


#### [**REFACTORING_SUMMARY.md**](REFACTORING_SUMMARY.md)
Summary of domain architecture refactoring (Commit 15a).

**Contents:**
- Goal of the refactoring
- What was implemented (pattern, structure)
- Before/after comparison
- Controller reduction (94 → 34 lines)
- Metrics & Quality Checks
- Request flow diagram
- Lessons learned
- Next Steps

**For whom:** Developers who want to understand why the architecture is the way it is


---

### 🤖 AI & Agents

#### [**ai/WORKING_BASELINE.md**](ai/WORKING_BASELINE.md)
Operational session starting point for AI-supported work (soft reset basis).

**Contents:**
- Binding daily baseline for AI sessions
- Priority: current repository status
- Architecture and quality guidelines in short form
- Reset protocol for long chat histories

**For whom:** AI agents and developers for a quick, consistent start

#### [**ai/SESSION_RESUME_2026-03-09.md**](ai/SESSION_RESUME_2026-03-09.md)
Daily updated summary (soft reset after loss of context).

**Contents:**
- What was achieved today (context consolidation, documentation updates)
- Current project status (Commit 20b completed)
- Next scheduled commits (21, 21a, 22)
- Soft reset protocol and reading sequence
- Action items for new sessions

**For whom:** AI agents after context reset, developers returning after a long break

#### [**ai/AGENT_CONTEXT.md**](ai/AGENT_CONTEXT.md)
Central work rules for AI agents (GitHub Copilot, etc.)

**Contents:**
- CQRS (strict mode, phased introduction)
- SOLID principles (mandatory review gate)
- Domain-Driven Design (Bounded Contexts)
- Quality gates (tests, coverage, PHPStan, Pint)
- Definition of Done
- Code review checklist

**For whom:** AI agents, developers who want to understand architectural principles

#### [**ai/PROJECT_OVERVIEW.md**](ai/PROJECT_OVERVIEW.md)
Project overview and MVP scope

**Contents:**
- What is ResumeHaven?
- MVP feature set
- Architecture short form
- Data structures (core DTOs)
- Request flow
- Roadmap

**For whom:** New developers, product owners, AI agents

#### [**ai/TECH_STACK.md**](ai/TECH_STACK.md)
Technology stack and configuration

**Contents:**
- Versions (PHP, Laravel, Pest, PHPStan, etc.)
- Docker Services
- Make commands
- Configuration (.env)
- URLs
- Update strategy

**For whom:** DevOps, developers, AI agents

#### [**AGENTS.md**](AGENTS.md)
Documentation of AI agents and their use.

**Contents:**
- Overview of AI agents used
- Agent configuration
- Prompt engineering
- Integration with Laravel AI
- Use of structured outputs
- **Reference to new context structure**

**For whom:** Developers working with the AI ​​agents


---

### 🛣️ Project planning

#### [**ROADMAP.md**](ROADMAP.md)
Long-term vision and feature roadmap for ResumeHaven.

**Contents:**
- MVP features (current)
- Planned features (Phase 2, 3, ...)
-Technical improvements
- UI/UX enhancements
- API development
- schedule

**For whom:** Product owners, stakeholders, developers


#### [**../COMMIT_PLAN.md**](../COMMIT_PLAN.md)
Active, streamlined commit plan with current focus and next steps.

**Contents:**
- Current status (Active Plan)
- Commit 24 Focus + DoD
- Prioritized follow orders
- Decision log and references

#### [**COMMIT_HISTORY_INDEX.md**](COMMIT_HISTORY_INDEX.md)
Entry into the outsourced commit history.

**Contents:**
- Links to archived commit summaries
- Separation of active plan and history

#### [**history/COMMIT_HISTORY_2026.md**](history/COMMIT_HISTORY_2026.md)
Compact history of completed commits (1-24).

#### [**history/COMMIT_22_IMPLEMENTATION_GUIDE.md**](history/COMMIT_22_IMPLEMENTATION_GUIDE.md)
Historical implementation guide for the `Profile` context from commit 22.

#### [**history/COMMIT_24_IMPLEMENTATION_GUIDE.md**](history/COMMIT_24_IMPLEMENTATION_GUIDE.md)
Historical implementation guide for Commit 24 (Competency CVs I).

---

### 🛠️ Development & Debugging

#### [**DEVELOPMENT.md**](DEVELOPMENT.md)
Local development setup with Docker, Makefile commands and workflow recommendations.

**Contents:**
- Setup & start of the containers
- Testing, linting, PHPStan
- Shell/DB commands
- Xdebug quickstart and coverage workflows

#### [**DEBUGGING.md**](DEBUGGING.md)
Complete Xdebug guide for VSCode/PhpStorm including coverage reports.

**Contents:**
- `make debug-on/off/status`
- IDE setup (port 9003, path mapping)
- CLI debugging
- Coverage in console and as files (`coverage-report/`)
- Troubleshooting

---

### 🤝 Contributing

#### [**CONTRIBUTING.md**](CONTRIBUTING.md)
Guidelines for contributions to the project.

**Contents:**
-How can I contribute?
- Code of Conduct
- Pull request process
- Branch strategy
- Commit conventions
- Testing requirements

**For whom:** External contributors, team members


---

## 🚀 Quick start

### For AI agents (copilot, windsurf, etc.)

1. **Getting started (soft reset)**: Read [`ai/WORKING_BASELINE.md`](ai/WORKING_BASELINE.md)
2. **Work Rules**: Read [`ai/AGENT_CONTEXT.md`](ai/AGENT_CONTEXT.md)
3. **Project Overview**: Read [`ai/PROJECT_OVERVIEW.md`](ai/PROJECT_OVERVIEW.md)
4. **Architecture**: Read [`ARCHITECTURE.md`](ARCHITECTURE.md)

### For developers

1. **Start**: Read [`../README.md`](../README.md) for installation & setup
2. **Understanding Architecture**: Read [`ARCHITECTURE.md`](ARCHITECTURE.md)
3. **Coding Standards**: Read [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)
4. **Develop feature**: Follow the checklist in `CODING_GUIDELINES.md`

### For Product Owners

1. **Vision**: Read [`ROADMAP.md`](ROADMAP.md)
2. **Status**: Lake [`../COMMIT_PLAN.md`](../COMMIT_PLAN.md)
3. **Architecture Overview**: Read [`ARCHITECTURE.md`](ARCHITECTURE.md) (Chapters 1-3)

### For Contributors

1. **Guidelines**: Read [`CONTRIBUTING.md`](CONTRIBUTING.md)
2. **Coding Standards**: Read [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)
3. **Architecture**: Read [`ARCHITECTURE.md`](ARCHITECTURE.md)

---

## 📖 More documentation

### In the root directory

- **[README.md](../README.md)**: Project overview, installation, quick start
- **[COMMIT_PLAN.md](../COMMIT_PLAN.md)**: Detailed development plan (commit-by-commit)
- **[LICENSE.md](../LICENSE.md)**: License information

### In `.github/`

- **[copilot-instructions.md](../.github/copilot-instructions.md)**: GitHub Copilot configuration
- **[agents/](../.github/agents/)**: Agent definitions

---

## 🔍 Search by topic

| Topic | File |
|-------|-------|
| **CQRS (Command Query)** | `ai/AGENT_CONTEXT.md`, `ARCHITECTURE.md` (Section 2.1) |
| **SOLID Principles** | `ai/AGENT_CONTEXT.md`, `CODING_GUIDELINES.md` (Chapter 2) |
| **Interface-based design** | `ai/AGENT_CONTEXT.md`, `CODING_GUIDELINES.md`, `ARCHITECTURE.md` (Chapter 9) |
| **Domain Driven Design (DDD)** | `ai/AGENT_CONTEXT.md`, `ARCHITECTURE.md`, `CODING_GUIDELINES.md` |
| **Command/Handler Pattern** | `ARCHITECTURE.md` (Chapter 2.1), `CODING_GUIDELINES.md` (Chapter 5) |
| **UseCase & Actions** | `CODING_GUIDELINES.md` (Ch. 6) |
| **DTOs** | `CODING_GUIDELINES.md` (Ch. 7), `ai/PROJECT_OVERVIEW.md` |
| **Repositories** | `CODING_GUIDELINES.md` (Ch. 8) |
| **Controller Best Practices** | `CODING_GUIDELINES.md` (Ch. 9) |
| **Testing** | `CODING_GUIDELINES.md` (Ch. 10), `ai/AGENT_CONTEXT.md` |
| **PHPStan & Code Quality** | `CODING_GUIDELINES.md` (ch. 11), `ai/TECH_STACK.md` |
| **Error Handling** | `CODING_GUIDELINES.md` (Ch. 12) |
| **Refactoring story** | `REFACTORING_SUMMARY.md` |
| **AI integration** | `AGENTS.md`, `ai/PROJECT_OVERVIEW.md` |
| **Tech Stack & Versions** | `ai/TECH_STACK.md` |
| **Feature Roadmap** | `ROADMAP.md`, `../COMMIT_PLAN.md` |
| **Contribution Process** | `CONTRIBUTING.md`, `../.github/PULL_REQUEST_TEMPLATE.md` |

---

## 📝Notes

- **Always keep it up to date**: This documentation should be updated when major changes occur
- **Practical Examples**: See `CODING_GUIDELINES.md` for specific code examples
- **Questions?**: Create an issue or contact the team

---

**Last updated**: 2026-03-09
**Version**: 2.1 (incl. WORKING_BASELINE as soft reset entry)