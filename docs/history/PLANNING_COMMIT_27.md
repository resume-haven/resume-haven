# Commit 27 - Acceptance-Tests Kernflows

**Branch:** `feature/commit-27-acceptance-core-flows`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-04-13

### Fortschritt (2026-04-13)
- [x] Slice 0: Ist-Stand/Lueckenanalyse abgeschlossen
- [x] Slice 1: Analyse-Flow + Validation-Edge-Case in Acceptance abgesichert
- [x] Slice 2: Kompetenzlebenslauf-Flow und Delta-Session-Fallback abgesichert
- [x] Slice 3: Profile-Flow inkl. Token-Edge-Case abgesichert
- [x] Slice 4: CI-/Command-Feinschliff umgesetzt (Composer/Makefile/CI-Job)
- [x] Slice 5: Quality-Gates validiert (Acceptance + phpstan + pint)

---

## Ziel

Die Kern-Userflows des MVP werden ueber eine dedizierte Acceptance-Suite stabil abgesichert.

Kernfragen fuer Commit 27:

- Welche End-to-End-Flows sind release-kritisch und muessen als Regression-Guard vorhanden sein?
- Welche Edge-Cases muessen zwingend in der Acceptance-Suite abgedeckt sein?
- Wie halten wir die Suite in Pest schnell, deterministisch und CI-stabil?

---

## Scope

### Enthalten
- Dedizierte Acceptance-Suite fuer Kernflows im Verzeichnis `src/tests/Acceptance/`
- Kernflows:
  - Analyse-Flow (Job + CV -> Ergebnis)
  - Kompetenzlebenslauf-Flow (Erzeugen, Anzeigen, Wiederverwenden)
  - Delta-/Vergleichs-Flow (Baseline/Fallback sichtbar)
  - Profile-Flow ohne Auth (Speichern/Laden/Feedback)
- Edge-Case-Abdeckung fuer diese Flows:
  - Validierungsfehler (leere/ungueltige Eingaben)
  - Fehlende Session-/Profildaten
  - Fehlerhafte oder ungueltige Tokens/IDs
  - Defensives Verhalten bei Analyzer-Fehlern/Timeout-Resultaten
- Pest-konforme Testdatenbank-Strategie inkl. Dokumentation
- Integration in Makefile/Composer/CI und Projekt-Dokumentation

### Nicht enthalten
- Neue Produktfeatures ausserhalb bestehender Kernflows
- Auth/AuthZ-Flow
- Externe Provider-Integration in CI
- UI-Redesign ohne direkten Testbezug

---

## Testdatenbank-Strategie (Pest/Laravel)

### Standard (bevorzugt)
- `RefreshDatabase` fuer isolierte Tests (pro Test sauberer Zustand)
- `sqlite` in-memory als schneller Default in der Testumgebung
- Migrationen laufen kontrolliert ueber Laravel-Testbootstrap

### Fallback/Option
- Separate Testing-DB nur dann, wenn in-memory fuer einzelne Szenarien nicht ausreicht
- Umschalten transparent ueber `.env.testing` und CI-Variablen

### Guardrails
- Keine geteilten Seiteneffekte zwischen Tests
- Deterministische Daten ueber Factories/Seeder/Fixtures
- Keine Abhaengigkeit von externer AI-Kommunikation in CI (`AI_PROVIDER=mock`)

---

## Technische Leitplanken

- DDD/CQRS/SOLID unveraendert einhalten
- Single-Action-Controller und Action/UseCase-Grenzen respektieren
- Keine fachliche Logik in Acceptance-Tests duplizieren
- Assertions auf sichtbares Verhalten und Vertragsgrenzen fokussieren
- Testnamen im Given-When-Then-Stil fuer Lesbarkeit

---

## Geplante Implementierungs-Slices

### Slice 0 - Ist-Stand und Lueckenanalyse
- Bestehende Acceptance-Tests inventarisieren
- Kernflow-Matrix (Flow x Szenario) erstellen
- Fehlende Edge-Cases als konkrete Testfaelle aufnehmen

### Slice 1 - Analyse- und Ergebnisflow
- Happy Path fuer Analyse-Ende-zu-Ende absichern
- Edge-Cases: Validation, unvollstaendige Payload, Analyzer-Fehlerausgabe
- Ergebnisdarstellung auf stabile Schluesselinhalte pruefen

### Slice 2 - Kompetenzlebenslauf- und Delta-Flow
- Erzeugen/Anzeigen/Wiederverwenden von Kompetenzdaten absichern
- Delta-Fallback (ohne persistente Baseline) als Akzeptanzfall testen
- Edge-Cases: fehlende Baseline, inkonsistente Vergleichsdaten

### Slice 3 - Profile-Flow ohne Auth
- Speichern/Laden/Feedback als Kernpfad absichern
- Edge-Cases: fehlende Sessiondaten, ungueltige Referenzen, defensive Redirects
- Retention-Hinweise und erwartetes UX-Feedback pruefen

### Slice 4 - Stabilisierung und CI-Integration
- Wiederverwendbare Test-Helper konsolidieren
- Suite in Make-/Composer-Commands und CI einhaengen
- Laufzeit/Stabilitaet optimieren (Reihenfolge, Isolation, Datenaufbau)

### Slice 5 - Quality Gates
- Acceptance-Suite vollstaendig gruener Lauf
- `make test` bzw. zielgerichtete Acceptance-Targets validieren
- `make phpstan` und `vendor/bin/pint --dirty --format agent` ohne Regression

---

## Erfolgskriterien (DoD)

1. Alle definierten Kernflows sind durch Acceptance-Tests abgedeckt.
2. Definierte Edge-Cases sind mit expliziten Testfaellen abgesichert.
3. Testdatenbank-Strategie ist dokumentiert und reproduzierbar konfigurierbar.
4. Acceptance-Suite laeuft stabil lokal und in CI mit `AI_PROVIDER=mock`.
5. Tests, PHPStan und Pint bleiben gruen.

---

## Risiken & Gegenmassnahmen

- **Risiko:** Flaky Tests durch geteilten Zustand oder nicht-deterministische Daten.  
  **Massnahme:** `RefreshDatabase`, klare Fixtures, keine externen Calls.

- **Risiko:** Zu enge Assertions auf Markup-Details erzeugen fragile Tests.  
  **Massnahme:** Verhalten und relevante UI-Texte statt CSS/HTML-Details pruefen.

- **Risiko:** Acceptance-Suite wird zu langsam.  
  **Massnahme:** in-memory DB als Standard, redundante Setup-Schritte reduzieren.

- **Risiko:** Edge-Cases werden uneinheitlich abgedeckt.  
  **Massnahme:** zentrale Szenario-Matrix und Review gegen DoD.

---

## Definition of Ready

- Kernflow-Matrix ist abgestimmt.
- Bestehende Acceptance-Suite ist analysiert.
- Testdatenbank-Strategie fuer lokal/CI ist festgelegt.
- No-Egress-Guardrails fuer AI-Pfade sind klar dokumentiert.

## Definition of Done

- Alle geplanten Slices sind umgesetzt.
- Kern- und Edge-Case-Szenarien sind gruen.
- CI fuehrt die Acceptance-Suite reproduzierbar aus.
- Doku und Verweise in `COMMIT_PLAN.md` sind aktualisiert.

---

## Verweise

- Aktivplan: `COMMIT_PLAN.md`
- Vorheriger Detailplan: `docs/history/PLANNING_COMMIT_26.md`
- Historie: `docs/history/COMMIT_HISTORY_2026.md`
- Roadmap: `docs/ROADMAP.md`
- Agent-Kontext: `docs/ai/AGENT_CONTEXT.md`



