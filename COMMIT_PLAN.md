# ResumeHaven - Commit-Plan (Active)

Dieser Plan enthaelt nur den **aktiven** und **naechsten** Arbeitsfokus.  
Abgeschlossene Details sind in die Historie ausgelagert.

**Letzte Aktualisierung:** 2026-03-13  
**Aktueller Stand:** Commit 23 abgeschlossen, Commit 24 in Planung (Kompetenzlebenslaeufe)

---

## Status-Ueberblick

### Abgeschlossen
- Commit 1-23 (kompakt in `docs/history/COMMIT_HISTORY_2026.md`)
- Hinweis: Commit 19 wurde historisch uebersprungen

### In Planung
- **Commit 24:** Kompetenzlebenslaeufe I (Erstellen + Anzeigen, MVP-light)
  - Detailplan: `docs/PLANNING_COMMIT_24.md`

### Geplante Folge-Reihenfolge (neu priorisiert)
- **Commit 25:** Analysequalitaet & Erklaerbarkeit (B)
- **Commit 26:** Profile-Ausbau ohne Auth (D)
- **Commit 27:** Acceptance-Tests Kernflows (C)
- **Commit 28:** Architecture-Tests & Engineering-Haertung (E)
- **Commit 29+:** User/Auth/AuthZ + rudimentaere Userverwaltung
- **Deployment:** erst nach User-/LLM-Block neu einordnen

---

## Commit 24 - Kompetenzlebenslaeufe I (MVP-light)

**Branch (geplant):** `feature/commit-24-competence-resume`  
**Status:** In Planung

### Ziel
- Kompetenzlebenslauf als neues Produktartefakt erzeugen und anzeigen
- Strukturierte Kompetenzen statt nur Freitext-CV nutzbar machen

### Scope
- Kompetenzprofil aus CV ableiten
- Kompetenzlebenslauf erstellen und in der UI darstellen
- Grundlage fuer Re-Analyse mit verbessertem CV
- Testausbau als Pflicht
- Datenschutz/Retention in der Planung beruecksichtigen

### Erfolgskriterien
- Kompetenzlebenslauf kann erzeugt und angezeigt werden
- Re-Analyse mit verbessertem CV ist moeglich
- Qualitaetsziel vorbereitet: typischerweise `Score_neu > Score_alt` und/oder `Gaps_neu < Gaps_alt`
- Tests/PHPStan/Pint bleiben gruen

### Nicht-Scope in Commit 24
- Kein User-Login/Auth
- Keine Migration bestehender Testdaten auf User (nicht notwendig)
- Kein Deployment/Cloud-Setup
- Kein lokales LLM-Deployment

---

## Decision Log (kurz)

- CI/Branch-Protection wurde in Commit 23 abgeschlossen
- Planung wurde auf produktnahen Mehrwert neu priorisiert (`A,B,D,C,E`)
- User/Auth wird nach den Produkt- und Test-Haertungscommits gestartet
- Deployment wird bewusst spaeter und kontextabhaengig neu bewertet

---

## Verweise

- Historie-Index: `docs/COMMIT_HISTORY_INDEX.md`
- Historie 2026 (kompakt): `docs/history/COMMIT_HISTORY_2026.md`
- Detailplanung Commit 23: `docs/history/PLANNING_COMMIT_23.md`
- Detailplanung Commit 24: `docs/PLANNING_COMMIT_24.md`
- Roadmap: `docs/ROADMAP.md`
