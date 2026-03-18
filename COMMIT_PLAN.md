# ResumeHaven - Commit-Plan (Active)

Dieser Plan enthaelt nur den **aktiven** und **naechsten** Arbeitsfokus.  
Abgeschlossene Details sind in die Historie ausgelagert.

**Letzte Aktualisierung:** 2026-03-16  
**Aktueller Stand:** Commit 24 abgeschlossen, Commit 25 gestartet (`feature/commit-25-analysis-delta-explainability`)

---

## Status-Ueberblick

### Abgeschlossen
- Commit 1-24 (kompakt in `docs/history/COMMIT_HISTORY_2026.md`)
- Hinweis: Commit 19 wurde historisch uebersprungen

### In Planung
- **Commit 25:** Analysequalitaet & Erklaerbarkeit (B)
  - Detailplan: `docs/PLANNING_COMMIT_25.md`

### Geplante Folge-Reihenfolge (neu priorisiert)
- **Commit 26:** Profile-Ausbau ohne Auth (D)
- **Commit 27:** Acceptance-Tests Kernflows (C)
- **Commit 28:** Architecture-Tests & Engineering-Haertung (E)
- **Commit 29+:** User/Auth/AuthZ + rudimentaere Userverwaltung
- **LLM-Block (nach Commit 28):** Provider-agnostischer AI-Layer
  - Commit L1: `AbstractLlmAiAnalyzer` extrahieren, `GeminiAiAnalyzer` umstellen
  - Commit L2: Plugin-Interface + Config-Erweiterung (`AI_PROVIDER` generisch)
  - Commit L3: Erster Zweit-Provider als Proof of Concept
  - Details & offene Fragen: `docs/ROADMAP.md` → Phase 5
- **Deployment:** erst nach User-/LLM-Block neu einordnen

---

## Commit 24 - Kompetenzlebenslaeufe I (MVP-light)

**Branch:** `feature/commit-24-competence-resume`  
**Status:** Abgeschlossen

### Ziel
- Kompetenzlebenslauf als neues Produktartefakt erzeugen und anzeigen
- Strukturierte Kompetenzen statt nur Freitext-CV nutzbar machen

### Scope
- Kompetenzprofil aus CV ableiten
- Kompetenzlebenslauf erstellen und in der UI darstellen
- Kompetenzlebenslauf als Analyseartefakt rendern und in Session speichern
- Kompetenzlebenslauf explizit als Analysequelle wiederverwenden (Use-Flow)
- Testausbau als Pflicht
- Datenschutz/Retention in der Planung beruecksichtigen

### Erfolgskriterien
- Kompetenzlebenslauf kann erzeugt und angezeigt werden
- Re-Analyse mit verbessertem CV ist moeglich
- Qualitaetsziel vorbereitet: typischerweise `Score_neu > Score_alt` und/oder `Gaps_neu < Gaps_alt`
- Tests/PHPStan/Pint bleiben gruen (validiert)

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
- Implementierungsleitfaden Commit 24: `docs/history/COMMIT_24_IMPLEMENTATION_GUIDE.md`
- Detailplanung Commit 25: `docs/PLANNING_COMMIT_25.md`
- Roadmap: `docs/ROADMAP.md`

---

## Commit 25 - Analysequalitaet & Erklaerbarkeit

**Branch:** `feature/commit-25-analysis-delta-explainability`  
**Status:** In Umsetzung

### Ziel
- Vergleichbarkeit zwischen Original-CV und optimiertem CV transparent machen
- Erklaerbarkeit liefern, warum sich Score/Matches/Gaps veraendern

### Scope
- Persistente Baseline im `Profile`-Context (neue Tabelle) fuer Vergleichsdaten
- Fallback-Verhalten, wenn Baseline nicht vorliegt (Session-basierter Vergleich)
- Delta-Engine fuer:
  - Score-Differenz
  - Match-/Gap-Differenz
  - Recommendations-Differenz inkl. Prioritaetswechsel
- Impact-Visualisierung in der Ergebnis-UI:
  - Verbesserung: Gruenton
  - Gleichbleibend: Blauton
  - Verschlechterung: Rotton
  - zusaetzlich Richtungspfeile (`↑`, `→`, `↓`)
- Mockdaten-Erweiterung fuer reproduzierbare Vergleichsfaelle

### Geplante Slices
- **Slice 0:** CTA/Button-Styles stabilisieren ("Kompetenzlebenslauf erstellen")
- **Slice 1:** Baseline + Delta-DTOs + Vergleichs-Action
- **Slice 2:** Result-UI fuer Delta/Impact
- **Slice 3:** Mockdaten + Unit/Feature-Tests + Quality-Gates

### Erfolgskriterien
- Vergleichspanel zeigt nachvollziehbare Delta-Werte fuer Score/Matches/Gaps/Recommendations
- Prioritaetsaenderungen bei Empfehlungen sind inkl. Impact sichtbar
- Fallback funktioniert ohne Fehler, wenn persistente Baseline fehlt
- Tests, PHPStan und Pint bleiben gruen

