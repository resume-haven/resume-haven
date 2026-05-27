# 📚 ResumeHaven – Documentation

Welcome to the **ResumeHaven** documentation!
Here you will find all important information about the architecture, development and use of the project.

> 🌐 **This documentation is also available online:**
> [GitHub Pages](https://github.com/pages) *(after activation)*
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

#### [**AGENTS.md**](AGENTS.md)
Documentation of AI agents and their use.

**Contents:**
- Overview of AI agents used
- Agent configuration
- Prompt engineering
- Integration with Laravel AI
- Use of structured outputs

**For whom:** Developers working with the AI ​​agents


---

### 🛣️ Project planning

#### [**ROADMAP.md**](ROADMAP.md)
Long-term vision and feature roadmap for ResumeHaven.

**Contents:**
- MVP features (current)
- Planned features (Phase 2, 3, ...)
- Technical improvements
- UI/UX enhancements
- API development
- Timeline

**For whom:** Product owners, stakeholders, developers


#### [**../COMMIT_PLAN.md**](../COMMIT_PLAN.md)
Active, streamlined commit plan for the current work focus.

#### [**COMMIT_HISTORY_INDEX.md**](COMMIT_HISTORY_INDEX.md)
Index page for paged commit history.

#### [**history/COMMIT_HISTORY_2026.md**](history/COMMIT_HISTORY_2026.md)
Compact history of completed commits (1-24).

#### [**history/COMMIT_22_IMPLEMENTATION_GUIDE.md**](history/COMMIT_22_IMPLEMENTATION_GUIDE.md)
Historical implementation guide for the `Profile` context from commit 22.

#### [**history/COMMIT_24_IMPLEMENTATION_GUIDE.md**](history/COMMIT_24_IMPLEMENTATION_GUIDE.md)
Historical implementation guide for Commit 24 (Competency CVs I).

---

### 🤝 Contributing

#### [**CONTRIBUTING.md**](CONTRIBUTING.md)
Guidelines for contributions to the project.

**Contents:**
- How can I contribute?
- Code of Conduct
- Pull request process
- Branch strategy
- Commit conventions
- Testing requirements

**For whom:** External contributors, team members


---

## 🚀 Quick start

### For developers

1. **Start**: Read [`../README.md`](../README.md) for installation & setup
2. **Understanding Architecture**: Read [`ARCHITECTURE.md`](ARCHITECTURE.md)
3. **Coding Standards**: Read [`CODING_GUIDELINES.md`](CODING_GUIDELINES.md)
4. **Develop feature**: Follow the checklist in `CODING_GUIDELINES.md`

### For Product Owners

1. **Vision**: Read [`ROADMAP.md`](ROADMAP.md)
2. **Status**: See [`../COMMIT_PLAN.md`](../COMMIT_PLAN.md)
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
| **Domain Driven Design** | `ARCHITECTURE.md`, `CODING_GUIDELINES.md` |
| **Command/Handler Pattern** | `ARCHITECTURE.md` (Chapter 2.1), `CODING_GUIDELINES.md` (Chapter 5) |
| **UseCase & Actions** | `CODING_GUIDELINES.md` (Ch. 6) |
| **DTOs** | `CODING_GUIDELINES.md` (Ch. 7) |
| **Repositories** | `CODING_GUIDELINES.md` (Ch. 8) |
| **Controller Best Practices** | `CODING_GUIDELINES.md` (Ch. 9) |
| **Testing** | `CODING_GUIDELINES.md` (Ch. 10) |
| **PHPStan & Code Quality** | `CODING_GUIDELINES.md` (Ch. 11) |
| **Error Handling** | `CODING_GUIDELINES.md` (Ch. 12) |
| **Refactoring story** | `REFACTORING_SUMMARY.md` |
| **AI integration** | `AGENTS.md` |
| **Feature Roadmap** | `ROADMAP.md` |
| **Contribution Process** | `CONTRIBUTING.md` |

---

## 📝Notes

- **Always keep it up to date**: This documentation should be updated when major changes occur
- **Practical Examples**: See `CODING_GUIDELINES.md` for specific code examples
- **Questions?**: Create an issue or contact the team

---

**Last updated**: 2026-05-11
**Version**: 1.1
