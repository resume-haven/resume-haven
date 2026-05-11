# Detailplanung Commit 36 - Roadmap-Planung & Doku-Sync

**Branch:** `feature/commit-36-roadmap-planning-docs`  
**Status:** In Arbeit  
**Erstellt:** 2026-05-11

---

## Ziel

Nach Abschluss von Commit 35 die Planungs- und Statusdokumente auf einen konsistenten,
nachvollziehbaren Stand bringen und die naechste Umsetzungsreihenfolge fuer Folge-Commits
klar priorisieren.

---

## Scope

### Schritt 1 - Statusangleichung der Kern-Dokumente

- `COMMIT_PLAN.md` auf Commit-35-Abschluss und Commit-36-Fokus umstellen
- `docs/COMMIT_HISTORY_INDEX.md` auf neuen aktiven Detailplan aktualisieren
- `docs/history/COMMIT_HISTORY_2026.md` um Commit 35 ergaenzen
- `docs/ai/WORKING_BASELINE.md` auf Commit-36-Fokus anheben

### Schritt 2 - Roadmap-Stand bereinigen

- In `docs/ROADMAP.md` den Abschnitt "Aktueller Stand" auf den realen Fortschritt heben
- Veraltete In-Arbeit-Markierungen aus Commit-31-Altstand entfernen
- Phase-5-Fortschritt (L1-L4 abgeschlossen) und offene Folgefragen sauber trennen

### Schritt 3 - Priorisierung fuer naechsten Umsetzungs-Commit

- Kandidaten fuer Commit 37 vergleichbar machen (z. B. Deployment-Neueinordnung, CV-Management-Follow-up, Phase-5-Hardening)
- Eine priorisierte Empfehlung inkl. Nicht-Scope und Risiken dokumentieren

---

## Nicht-Scope in Commit 36

- Keine Produktlogik-Implementierung
- Keine Controller-/Domain-/Migrationsaenderungen
- Keine Deployment-Ausfuehrung

---

## Erfolgskriterien

- Planungsdokumente sind widerspruchsfrei (Commit-Status, Fokus, aktive Detailplanung)
- Roadmap bildet den aktuellen technischen Stand korrekt ab
- Naechster Umsetzungs-Commit ist priorisiert und begruendet

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Working Baseline: `../ai/WORKING_BASELINE.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_35.md`
- Roadmap: `../ROADMAP.md`

