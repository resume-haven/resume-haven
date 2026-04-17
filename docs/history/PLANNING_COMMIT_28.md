# Commit 28 – Architecture-Tests & Engineering-Härtung

**Branch:** `feature/commit-28-architecture-tests`  
**Status:** In Planung  
**Erstellt:** 2026-04-17

---

## Ziel

Automatisierte Architektur-Tests (Pest Arch) sichern die bestehenden Layer-Regeln, DDD/CQRS-Grenzen und SOLID-Prinzipien ab. Ergänzend werden Engineering-Härtungsmaßnahmen (Mutation-Testing-Vorbereitung, Git-Hooks, erweiterte Quality-Gates) implementiert.

### Kernfragen für Commit 28

- Welche Layer- und Boundary-Regeln müssen automatisiert abgesichert sein?
- Wie wird Mutation-Testing als optionales, isoliertes Tool vorbereitet?
- Wie werden Git-Hooks sinnvoll und optional integriert?

---

## Scope

### Enthalten

- **Architecture-Test-Suite** in `src/tests/Architecture/` mit Pest 3 `arch()`-API:
  - `DddArchTest.php`: Bounded-Context-Grenzen (Profile ↔ Analysis)
  - `CqrsArchTest.php`: Command/Query-Segregation
  - `SolidArchTest.php`: Single-Action-Controller, Interface-based Design, readonly DTOs
- **Vollständiger Scope:** Alle `app/`-Namespaces (Domains, Http, Services, Ai, Dto)
- **Mutation-Testing-Vorbereitung** mit `pestphp/pest-plugin-mutate`:
  - Composer dev-dependency hinzugefügt
  - `make test-mutation` als eigenes Target (Scope: `app/Domains`)
  - Nicht im Standard-CI enthalten (separater Workflow möglich)
  - Offene Detailfragen in `docs/ROADMAP.md` dokumentiert
- **Git-Hooks** in `.githooks/pre-commit`:
  - `pint --dirty`, `phpstan analyse --no-progress`, Commit-Message-Convention-Check
  - Manuell via `make hooks-install` aktivierbar (kein Auto-Install)
- **Makefile & Composer-Scripts erweitert:**
  - `make test-arch` – nur Architecture-Tests
  - `make test-arch-gate` – Arch + PHPStan + Pint
  - `make test-mutation` – Mutation-Tests (Domains, dry-run)
  - `make hooks-install` – Git-Hooks manuell aktivieren
  - Composer-Script `quality:arch-gate`
- **CI-Integration:**
  - Architecture-Suite in `phpunit.xml` hinzugefügt
  - CI-Job `pest_architecture` in `.github/workflows/ci.yml` (optional: separater Mutation-Workflow)
- **Dokumentation:**
  - Detailplan nach `docs/history/PLANNING_COMMIT_28.md` ausgelagert
  - `docs/ARCHITECTURE.md` aktualisiert (Architecture-Testing Status)
  - `docs/ROADMAP.md` erweitert (Mutation-Testing offene Fragen)
  - `COMMIT_PLAN.md` aktualisiert (Status und nächste Commits)

### Nicht enthalten

- Neue Produktfeatures
- Mutation-Testing im Standard-CI (nur Optional/Workflow-Dispatch)
- Automatische Git-Hook-Installation via `composer install`
- Weitere Engineering-Tools (Deptrac, PhpMetrics, etc.)

---

## Technische Leitplanken

- **Pest Arch vs. Deptrac:** Pest 3 genügt für Layer-Regeln; Deptrac würde zu viel Komplexität bringen
- **Architecture-Tests:** Schreiben Tests in Given-Style (prägnante Aussagen), nicht als Einzelheiten
- **Mutation-Testing:** Scope begrenzt auf `app/Domains` zur Laufzeitoptimierung
- **Git-Hooks:** Shell-Script (POSIX-kompatibel), funktioniert auf WSL + macOS + Linux
- **SOLID-Enforcement:** Überprüfung von Schnittstellen-Implementierungen, readonly-Klassen, Controller-Pattern

---

## Geplante Implementierungs-Slices

### Slice 0 – Architektur-Analyse & Vorbereitung
- Architecture-Test-Suite Struktur planen (DDD, CQRS, SOLID)
- Bestehende Layer-Regeln inventarisieren
- Pest Arch Syntax und Pest-Plugin-Mutate dokumentieren

### Slice 1 – DDD-Tests implementieren
- `DddArchTest.php`: Bounded-Context-Grenzen (Profile ↔ Analysis)
- Kopplung auf Action-/UseCase-Ebene validieren
- Modell-Zugriffe (Eloquent) verhindern

### Slice 2 – CQRS-Tests implementieren
- `CqrsArchTest.php`: Command/Query-Segregation
- Namespace-Konventionen validieren (Commands, Queries, Handlers)
- Keine Command-in-Query-Nutzung, keine Query-in-Command-Nutzung

### Slice 3 – SOLID-Tests implementieren
- `SolidArchTest.php`: Single-Action-Controller, Interface-based Design, readonly DTOs
- Controller `__invoke`-Methode prüfen
- DTOs immutable validieren
- Service-Schnittstellen-Nutzung prüfen

### Slice 4 – Mutation-Testing vorbereiten
- `pestphp/pest-plugin-mutate` in `composer.json` als dev-dependency hinzufügen
- `test:pest-mutation` Composer-Script implementieren (Scope: `app/Domains`)
- `make test-mutation` Target ergänzen
- Offene Fragen (MSI-Schwellwert, Slow-Test-Strategie) in Roadmap dokumentieren

### Slice 5 – Git-Hooks einrichten
- `.githooks/pre-commit` Script schreiben (Pint, PHPStan, Commit-Message-Check)
- `make hooks-install` Target implementieren
- Hook-Ausführung lokal validieren

### Slice 6 – Makefile & Composer erweitern
- `make test-arch`, `make test-arch-gate`, `make test-mutation`, `make hooks-install` hinzufügen
- `quality:arch-gate` Composer-Script definieren
- `.PHONY` Targets aktualisieren

### Slice 7 – CI-Integration & Quality-Gates
- `phpunit.xml`: Architecture-Suite als Testsuite registrieren
- `pest/Pest.php`: Architecture-Tests einbinden
- Neuer CI-Job `pest_architecture` in `.github/workflows/ci.yml` (oder separater `mutation.yml`)
- `make test` und Quality-Gates validieren

### Slice 8 – Dokumentation abschließen
- Detailplan nach `docs/history/PLANNING_COMMIT_28.md` auslagern
- `docs/ARCHITECTURE.md`: Architecture-Testing-Status aktualisieren
- `docs/ROADMAP.md`: Mutation-Testing offene Fragen dokumentieren
- `COMMIT_PLAN.md`: Status auf Commit 28 setzen, nächste Commits aufzählen
- Changelog im Unreleased-Block aktualisieren

---

## Erfolgskriterien (DoD)

1. Architecture-Suite ist vollständig grün (alle drei Test-Dateien bestanden).
2. DDD-Grenzen werden automatisiert überprüft (Profile ↔ Analysis koppeln nicht auf Command/Handler-Ebene).
3. CQRS-Segregation ist validiert (Commands → `void`, Queries → read-only).
4. SOLID-Regeln sind getestet (Single-Action-Controller, readonly DTOs, Interface-Nutzung).
5. Mutation-Testing ist vorbereitet (dev-dependency, Script, Target vorhanden; offene Fragen dokumentiert).
6. Git-Hooks sind installierbar und funktionsfähig (`make hooks-install`).
7. Makefile und Composer-Scripts sind erweitert (test-arch, test-mutation, hooks-install).
8. CI ist angepasst (Architecture-Suite läuft im Standard-CI).
9. Alle Standard-Gates bleiben grün (Pint, PHPStan, Test-Suites).
10. Dokumentation ist aktualisiert und verlinkt.

---

## Risiken & Gegenmassnahmen

- **Risiko:** Arch-Tests zu streng/fragmentiert → zu viele False-Positives.  
  **Massnahme:** Schrittweise Implementierung, Review nach Slice 3.

- **Risiko:** Mutation-Tests werden zu langsam.  
  **Massnahme:** Scope auf `app/Domains` begrenzt; Option für parallele Ausführung vorbereiten.

- **Risiko:** Git-Hooks funktionieren nicht auf allen Plattformen.  
  **Massnahme:** POSIX-kompatibles Shell-Script, WSL/macOS/Linux getestet.

- **Risiko:** CI-Komplexität wächst.  
  **Massnahme:** Architecture-Tests im Standard-CI, Mutation im separaten Workflow (optional).

---

## Definition of Ready

- Pest Arch Syntax ist dokumentiert.
- Layer-Regeln sind konkret aufgelistet.
- Mutation-Testing Framework ist ausgewählt (pest-plugin-mutate).
- Git-Hook-Anforderungen sind klar (Pint, PHPStan, Commit-Message).

## Definition of Done

- Alle geplanten Slices sind umgesetzt.
- Architecture-Suite läuft lokal und in CI grün.
- Mutation-Testing ist vorbereitet (dev-dependency, Script, offene Fragen dokumentiert).
- Git-Hooks sind manuell aktivierbar und funktionsfähig.
- `make test-arch-gate`, `make test-mutation`, `make hooks-install` funktionieren.
- Doku ist aktualisiert und verlinkt.
- Changelog ist im Unreleased-Block gepflegt.

---

## Verweise

- Aktivplan: `COMMIT_PLAN.md`
- Vorheriger Detailplan: `docs/history/PLANNING_COMMIT_27.md`
- Roadmap: `docs/ROADMAP.md`
- Architektur-Dokumentation: `docs/ARCHITECTURE.md`
- Agent-Kontext: `docs/ai/AGENT_CONTEXT.md`

