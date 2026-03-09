# Working Baseline

Diese Datei ist der operative Startpunkt fuer KI-gestuetzte Sessions.
Sie dient als "Soft-Reset" und hat Vorrang fuer den Tageskontext.

## Geltungsbereich

- Gilt fuer die aktuelle Implementierungsphase (MVP, aktueller Branch).
- Repository-Stand ist die Source of Truth.
- Bei Konflikten gilt: System/Tooling-Regeln > diese Datei > aeltere Chat-Kontexte.

## Aktuelle Arbeitsregeln

1. Architektur
   - DDD mit Bounded Context `Analysis`.
   - CQRS phasenweise und strikt pro Use-Case.
   - SOLID als Pflicht-Gate bei Implementierungen und Reviews.
   - Program to an interface (Interface-based Design, Abstraktion statt Konkretisierung).

2. Controller und Use-Cases
   - Single-Action-Controller bevorzugen (`__invoke()`), ausser explizit begruendet.
   - Business-Logik in Actions/UseCases/Services, nicht im Controller.
   - Kleine, testbare Methoden mit klaren Verantwortlichkeiten.

3. Datenmodellierung
   - DTO-first fuer Input/Output zwischen Schichten.
   - DTOs nach Moeglichkeit immutable (`readonly`).
   - Typisierung vollstaendig (PHPStan Level 9 kompatibel).

4. Qualitaets-Gates
   - Tests erforderlich (Feature + Unit, Pest).
   - `phpstan` ohne Fehler.
   - `pint` auf geaenderten Dateien.
   - Coverage-Mindestwert gemaess Projektkonfiguration (aktuell 95%).

5. AI und Fehlerrobustheit
   - Provider austauschbar (aktuell Gemini/Mock ueber Interface-Binding).
   - API-Timeouts, leere/ungueltige Antworten und Parsing-Fehler robust behandeln.
   - Cache-Verhalten reproduzierbar und testbar halten.

## Session-Reset-Protokoll

Bei stark gewachsenem Chatkontext diese Datei als Reset-Basis verwenden:

- Nur aktuellen Repo-Stand + diese Baseline als verbindlich betrachten.
- Aeltere Chat-Details ignorieren, ausser explizit referenziert.
- Bei Unklarheiten kurz pausieren und Rueckfrage stellen.

**Tagesaktuelle Session-Zusammenfassung:**
Siehe `docs/ai/SESSION_RESUME_YYYY-MM-DD.md` für den letzten Stand (falls vorhanden).

**Empfohlene Reset-Reihenfolge:**
1. Diese Datei (`WORKING_BASELINE.md`)
2. Tagesaktuelle Session-Resume (z. B. `SESSION_RESUME_2026-03-09.md`)
3. `COMMIT_PLAN.md` (Status-Überblick)
4. `AGENT_CONTEXT.md` (Details zu Arbeitsregeln)

## Pflege

- Diese Datei bei Architektur- oder Prozessentscheidungen aktualisieren.
- Aenderungen knapp dokumentieren (z. B. im Commit-Plan / Changelog).

### Versionierung

**Schema:** `Major.Minor`

- **Major** (z. B. 1.0 → 2.0): Grundlegende Architektur- oder Prozessänderungen
- **Minor** (z. B. 1.0 → 1.1): Ergänzungen, Präzisierungen, redaktionelle Updates

**Wann Minor erhöhen:**
- Neue Arbeitsregeln oder Quality-Gates hinzugefügt
- Bestehende Regeln präzisiert oder erweitert
- Strukturelle Anpassungen (z. B. neue Use-Cases, neue Bounded Contexts)
- Redaktionelle Überarbeitungen mit inhaltlicher Relevanz

**Wann Major erhöhen:**
- Grundlegender Wechsel der Architektur-Prinzipien (z. B. CQRS → Event Sourcing)
- Neue zentrale Patterns (z. B. Einführung von Hexagonal Architecture)
- Breaking Changes in der Entwicklungsweise

---

**Letzte Aktualisierung**: 2026-03-09  
**Version**: 1.1 (Soft-Reset-Baseline als verbindlicher Session-Startpunkt + Versionierungskonvention)
