# KI-Agent Kontext für ResumeHaven

Du arbeitest am **ResumeHaven MVP** — einem Laravel 12 Projekt mit Domain-Driven Design, CQRS und SOLID-Prinzipien.

## 📚 Kontext-Hierarchie (in dieser Reihenfolge lesen):

1. **🎯 Agent-Kontext** (zentrale Arbeitsregeln)  
   → `docs/ai/AGENT_CONTEXT.md`  
   CQRS (phasenweise), SOLID (Pflicht-Gate), DDD, Testing, Quality-Gates

2. **🏗️ Projektüberblick**  
   → `docs/ai/PROJECT_OVERVIEW.md`  
   Was ist ResumeHaven? Ziele, Scope, Was NICHT im MVP ist

3. **⚙️ Tech Stack**  
   → `docs/ai/TECH_STACK.md`  
   Versionen (Laravel 12, PHP 8.5, Pest 3, Tailwind 3), Docker-Services

4. **🧱 Architektur**  
   → `docs/ARCHITECTURE.md`  
   Domain-Driven Design, CQRS-Strategie, Single-Action-Controller, Repository Pattern

5. **📐 Coding Guidelines**  
   → `docs/CODING_GUIDELINES.md`  
   PHP-Konventionen, Laravel Best Practices, Testing, SOLID-Enforcement

6. **🗺️ Commit-Plan**  
   → `COMMIT_PLAN.md`  
   Projektverlauf, geplante Features, DDD-Roadmap

7. **🚀 Laravel Boost Regeln**  
   → `src/AGENTS.md`  
   Automatisch generierte Guidelines (NICHT editieren)

---

## 🚨 Wichtigste Regeln (Kurzform)

### Architektur-Prinzipien
- **CQRS (strict)**: Commands/Queries strikt getrennt (phasenweise Einführung)
- **SOLID**: Pflicht-Review-Gate in jedem Commit
- **DDD**: Bounded Context `Analysis` (Erweiterung in Roadmap)

### Quality-Gates
- **Tests:** Jede Änderung benötigt Tests (Pest 3)
- **PHPStan:** Level 9, 0 Errors
- **Pint:** `vendor/bin/pint --dirty --format agent` nach Änderungen
- **Coverage:** Minimum 95% (aktuell: 98.2%)

### Code-Standards
- **Single-Action-Controller:** `__invoke()` statt named methods
- **Immutable DTOs:** `readonly` Properties
- **Repository Pattern:** Kein Raw-SQL außer in Repositories
- **Type-Hints:** Vollständige Typisierung (PHPStan Level 9)

---

## ⚙️ Schnellreferenz

```bash
make test                  # Alle Tests
make test-coverage         # Coverage-Check (min 95%)
make phpstan               # Static Analysis (Level 9)
make pint-fix              # Code-Formatting
make debug-on              # Xdebug + Coverage
```
