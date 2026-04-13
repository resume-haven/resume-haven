# ResumeHaven - Commit-Plan (Active)

Dieser Plan enthaelt nur den **aktiven** und **naechsten** Arbeitsfokus.  
Abgeschlossene Details sind in die Historie ausgelagert.

**Letzte Aktualisierung:** 2026-04-13  
**Aktueller Stand:** Commit 27 in Abschlussphase (`feature/commit-27-acceptance-core-flows`), Commit 26 weiterhin in Umsetzung

---

## Status-Ueberblick

### Abgeschlossen
- Commit 1-25 (kompakt in `docs/history/COMMIT_HISTORY_2026.md`)
- Hinweis: Commit 19 wurde historisch uebersprungen
- Commit 25: Analysequalität & Erklärbarkeit (abgeschlossen)

### In Planung
- Commit 27: Acceptance-Tests Kernflows (Slices 0-5 umgesetzt, Abschluss/Review offen)

### Geplante Folge-Reihenfolge (neu priorisiert)
- **Commit 27:** Acceptance-Tests Kernflows (C)
- **Commit 28:** Architecture-Tests & Engineering-Härtung (E)
- **Commit 29+:** User/Auth/AuthZ + rudimentäre Userverwaltung
- **LLM-Block (nach Commit 28):** Provider-agnostischer AI-Layer
  - Commit L1: `AbstractLlmAiAnalyzer` extrahieren, `GeminiAiAnalyzer` umstellen
  - Commit L2: Plugin-Interface + Config-Erweiterung (`AI_PROVIDER` generisch)
  - Commit L3: Erster Zweit-Provider als Proof of Concept
  - Details & offene Fragen: `docs/ROADMAP.md` → Phase 5
- **Deployment:** erst nach User-/LLM-Block neu einordnen

### Commit-27 Fortschritt (Kurzstatus)
- Acceptance-Kernflows inkl. Edge-Cases sind als eigene Suite umgesetzt
- CI-/Command-Integration ist ergänzt (`pest_acceptance`, `quality:acceptance-gate`)
- Quality-Gates lokal validiert (Acceptance, PHPStan, Pint)
- Offener Restpunkt: finaler Review/Abschluss und Einordnung in Historie

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
- Detailplanung Commit 24: `docs/history/PLANNING_COMMIT_24.md`
- Implementierungsleitfaden Commit 24: `docs/history/COMMIT_24_IMPLEMENTATION_GUIDE.md`
- Detailplanung Commit 25: `docs/history/PLANNING_COMMIT_25.md`
- Detailplanung Commit 26: `docs/history/PLANNING_COMMIT_26.md`
- Detailplanung Commit 27: `docs/PLANNING_COMMIT_27.md`
- Roadmap: `docs/ROADMAP.md`

---

## Commit 25 - Analysequalitaet & Erklaerbarkeit

**Branch:** `feature/commit-25-analysis-delta-explainability`  
**Status:** Abgeschlossen

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

---

## Commit 26 - Profile-Ausbau ohne Auth

**Branch:** `feature/commit-26-profile-expansion-no-auth`  
**Status:** In Umsetzung

### Ziel
- Profile-Flow ohne Auth für lokale Entwicklung ausbauen (UX-first)
- Retention im MVP pragmatisch umsetzen und für Nutzende transparent machen
- CI-Guardrails: AI_PROVIDER=mock, keine externen AI-Secrets, nur interne Services erlaubt

### Scope
- Verbesserter Profile-UX-Flow (Speichern/Laden/Feedback)
- Retention-Mechanik im MVP-Stil (ohne produktive Plattformfestlegung)
- Zusätzliche UI-Hinweise zur Datenhaltung/-lebensdauer
- CI-Guardrails (required) für No-Egress in AI-Pfaden

### Nicht-Scope
- User/Auth/AuthZ
- Plattformspezifische produktive Retention-Endlösung
- Externe AI-Provider in CI

### Erfolgskriterien
- Profile-Flow ist robust und nachvollziehbar ohne Auth
- Retention-Verhalten ist technisch wirksam und in der UI klar kommuniziert
- CI blockiert externe AI-Egress-Pfade (allow internal services only)
- Tests, PHPStan und Pint bleiben grün

### Detailplan
- Siehe: `docs/history/PLANNING_COMMIT_26.md`
