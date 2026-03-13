# Commit 24 - Kompetenzlebenslaeufe I (MVP-light)

**Branch (geplant):** `feature/commit-24-competence-resume`  
**Status:** In Planung  
**Erstellt:** 2026-03-13

---

## Ziel

Ein Kompetenzlebenslauf soll als neues Produktartefakt erzeugt und angezeigt werden.
Der Fokus liegt auf sofortigem Nutzerwert: strukturierte Kompetenzen statt nur Fliesstext.

---

## Scope

### Enthalten
- Kompetenzprofil aus vorhandenen CV-Daten ableiten
- Kompetenzlebenslauf in der UI anzeigen
- Grundlage fuer spaetere Re-Analyse mit verbessertem CV schaffen
- Testabdeckung fuer neue Kernflows erweitern
- Datenschutz-/Retention-Aspekte in der Planung explizit beruecksichtigen

### Nicht enthalten
- Kein User-Login/Auth in Commit 24
- Keine Migration bestehender Testdaten auf User (nicht notwendig)
- Kein Deployment/Cloud-Setup
- Kein lokales LLM-Deployment

---

## Fachliche Leitplanken

- `Kompetenzlebenslauf` ist nicht nur Input, sondern explizit ein erstellbares Artefakt.
- Der Flow soll produktnah sein: erzeugen -> anzeigen -> zur Analyse wiederverwenden.
- Der bestehende Analyseflow bleibt kompatibel.

---

## Erfolgskriterien (DoD-nah)

1. Kompetenzlebenslauf kann erzeugt und angezeigt werden.
2. Re-Analyse mit korrigiertem/optimiertem CV ist moeglich.
3. Messbares Qualitaetskriterium vorbereitet:
   - bei identischem Jobtext gilt typischerweise `Score_neu > Score_alt`
   - und/oder `Gaps_neu < Gaps_alt`
4. Neue/angepasste Tests sind gruen.
5. PHPStan/Pint/Tests bleiben gruen.

---

## Datenschutz/Retention (Planungspflicht)

In Commit 24 muss dokumentiert/beruecksichtigt werden:
- welche CV-Daten wie lange gespeichert werden
- wie Test-/Entwicklungsdaten behandelt werden
- welche Loeschpfade fuer gespeicherte Artefakte vorgesehen sind

Hinweis: finale userbasierte Security/Retention folgt im spaeteren User-Block.

---

## Teststrategie (Pflicht)

- Feature-Tests fuer den neuen Kompetenzlebenslauf-Flow
- Unit-Tests fuer Ableitungslogik/Transformationen
- Vorbereitung fuer spaetere Acceptance-Tests (Commit 27)

Motivation: mit wachsender Komplexitaet ist Testausbau zwingend, um Regressionen zu vermeiden.

---

## Einordnung in die neue Reihenfolge

- Commit 24: Kompetenzlebenslaeufe I (A)
- Commit 25: Analysequalitaet & Erklaerbarkeit (B)
- Commit 26: Profile-Ausbau ohne Auth (D)
- Commit 27: Acceptance-Tests Kernflows (C)
- Commit 28: Architecture-Tests & Engineering-Haertung (E)
- Commit 29+: User/Auth/AuthZ + rudimentaere Userverwaltung

Deployment bleibt danach neu zu verorten.

