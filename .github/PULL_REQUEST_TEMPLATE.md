# Pull Request

## 📝 Beschreibung

<!-- Beschreibe kurz, was dieser PR implementiert -->

**Bezug:** Commit XX aus `COMMIT_PLAN.md`

---

## ✅ SOLID-Compliance (Pflicht-Gate)

**Jeder PR MUSS die SOLID-Prinzipien einhalten.**

### Single Responsibility Principle (SRP)
- [ ] Jede Klasse hat nur eine Verantwortlichkeit
- [ ] Methoden sind < 20 Zeilen
- [ ] Klassen sind < 200 Zeilen
- [ ] Cyclomatic Complexity < 5

### Open/Closed Principle (OCP)
- [ ] Neue Features ohne Änderung bestehender Klassen
- [ ] Interfaces für austauschbare Komponenten

### Liskov Substitution Principle (LSP)
- [ ] Interfaces sind korrekt austauschbar
- [ ] Keine Breaking Changes in Subtypen

### Interface Segregation Principle (ISP)
- [ ] Interfaces sind fokussiert und klein
- [ ] Keine "fetten" Interfaces

### Dependency Inversion Principle (DIP)
- [ ] Dependencies via Constructor Injection
- [ ] Abhängigkeiten zu Abstractions (Interfaces), nicht zu Konkretionen

---

## 🏗️ Architektur-Compliance

### CQRS (Command Query Responsibility Segregation)
- [ ] Commands und Queries sind getrennt
- [ ] Commands ändern Zustand, geben kein Ergebnis zurück (oder nur Bestätigung)
- [ ] Queries lesen Daten, ändern keinen Zustand
- [ ] Handler sind in korrektem Ordner (`Commands/` oder `Queries/`)

### DDD (Domain-Driven Design)
- [ ] Code liegt im korrekten Bounded Context (`app/Domains/{Context}/`)
- [ ] Keine Cross-Context-Dependencies (außer via Events/DTOs)
- [ ] Ubiquitous Language in Code-Namen verwendet

### Code-Standards
- [ ] Single-Action-Controller (`__invoke()`)
- [ ] Immutable DTOs (`readonly`)
- [ ] Repository Pattern (kein Raw-SQL außer in Repositories)
- [ ] Vollständige Type-Hints (PHPStan Level 9)

### Interface-based Design
- [ ] Dependencies sind Interfaces statt konkrete Klassen (wo sinnvoll)
- [ ] Interfaces liegen in `Contracts/` Unterordner
- [ ] Interface-Namen enden auf `Interface`
- [ ] Mindestens 2 Implementierungen vorhanden oder geplant

---

## ✅ Quality-Gates (Pflicht)

### Tests
- [ ] Unit-Tests vorhanden
- [ ] Feature-Tests vorhanden
- [ ] Edge-Cases getestet
- [ ] Alle Tests grün: `make test`
- [ ] Coverage ≥95%: `make test-coverage`

### Code-Qualität
- [ ] PHPStan Level 9: `make phpstan` → 0 Errors
- [ ] Pint: `make pint-fix` ausgeführt
- [ ] Keine neuen Warnungen

### Dokumentation
- [ ] PHPDoc für alle Public Methods
- [ ] Komplexe Logik kommentiert
- [ ] README/Docs aktualisiert (wenn nötig)

---

## 🧪 Testing

### Test-Ausführung

```bash
# Alle Tests
make test

# Coverage-Check
make test-coverage
```

**Ergebnis:**
- [ ] Alle Tests grün ✅
- [ ] Coverage ≥95% ✅

---

## 📸 Screenshots (bei UI-Änderungen)

<!-- Optional: Screenshots von Änderungen -->

---

## 🔍 Code-Review Checklist (für Reviewer)

### Architektur
- [ ] SOLID-Prinzipien eingehalten
- [ ] CQRS: Commands/Queries korrekt getrennt
- [ ] DDD: Richtiger Bounded Context
- [ ] Single-Action-Controller
- [ ] Immutable DTOs

### Code-Qualität
- [ ] PHPStan Level 9: 0 Errors
- [ ] Pint: Code-Formatting sauber
- [ ] Methoden < 20 Zeilen
- [ ] Klassen < 200 Zeilen
- [ ] Cyclomatic Complexity < 5

### Tests
- [ ] Unit-Tests vorhanden
- [ ] Feature-Tests vorhanden
- [ ] Edge-Cases getestet
- [ ] Coverage ≥95%

### Dokumentation
- [ ] PHPDoc für Public Methods
- [ ] Komplexe Logik kommentiert
- [ ] README/Docs aktualisiert

---

## 📚 Referenzen

- **Agent-Kontext:** `docs/ai/AGENT_CONTEXT.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Architektur:** `docs/ARCHITECTURE.md`
- **Commit-Plan:** `COMMIT_PLAN.md`


