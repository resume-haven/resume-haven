# Commit 25 - Analysequalitaet & Erklaerbarkeit

**Branch:** `feature/commit-25-analysis-delta-explainability`  
**Status:** In Umsetzung  
**Erstellt:** 2026-03-16

---

## Ziel

Ergebnisse sollen fuer Nutzende nachvollziehbar werden:

- Was hat sich gegenueber der Baseline geaendert?
- Warum ist der Score gestiegen/gefallen?
- Welche Empfehlungen haben sich in der Prioritaet veraendert?

---

## Scope

### Enthalten
- Persistente Baseline im `Profile`-Context (neue Tabelle)
- Fallback auf Session-Daten, wenn keine persistente Baseline verfuegbar ist
- Delta-Engine fuer Vergleich:
  - Score-Differenz
  - Match-/Gap-Differenz
  - Recommendation-Differenz inkl. Prioritaetswechsel
- UI-Impact-Darstellung in `result`:
  - Verbesserung -> Gruenton + `↑`
  - Gleichbleibend -> Blauton + `→`
  - Verschlechterung -> Rotton + `↓`
- Mockdaten-Erweiterung fuer Vergleichsszenarien
- Unit- und Feature-Tests fuer Delta/Impact/Fallback

### Nicht enthalten
- Prompt-Feintuning (folgt in Commit 25a)
- User/Auth/AuthZ
- Cloud-/Deployment-Themen

---

## Technische Leitplanken

- DDD/CQRS phasenweise beibehalten
- Single-Action-Controller, keine neue Business-Logik im Controller
- Vergleichslogik in Actions/UseCases kapseln
- Datenuebergabe ueber immutable DTOs
- Interface-basierte Abhaengigkeiten (wo austauschbare Komponenten bestehen)

---

## Geplante Implementierungs-Slices

### Slice 0 - UI-Stabilisierung
- CTA "Kompetenzlebenslauf erstellen" visuell stabilisieren
- Asset-Pipeline/Tailwind-Build pruefen und fixen

### Slice 1 - Baseline + Delta-Engine
- Neue Persistenz fuer Baseline im `Profile`-Context
- Vergleichs-DTOs und Vergleichs-Action implementieren

### Slice 2 - Erklaerbare Ergebnis-UI
- Delta-Panel in `result` mit Kennzahlen und Impact-Hinweisen
- Prioritaetswechsel bei Empfehlungen klar visualisieren

### Slice 3 - Mockdaten + Tests + Gates
- Mock-Szenarien erweitern (Verbesserung, Gleichstand, Verschlechterung)
- Unit/Feature-Tests ergaenzen
- `make test`, `make phpstan`, `make pint-analyse` gruen

---

## Erfolgskriterien (DoD)

1. Vergleich zwischen Baseline und aktueller Analyse ist sichtbar und nachvollziehbar.
2. Empfehlungsaenderungen inkl. Prioritaetswechsel werden mit Impact (Farbe + Pfeil) angezeigt.
3. Fehlende persistente Baseline fuehrt nicht zu Fehlern (Fallback aktiv).
4. Mockdaten decken die zentralen Delta-Faelle reproduzierbar ab.
5. Quality-Gates bleiben gruen.

---

## Risiken & Gegenmassnahmen

- **Risiko:** Baseline fehlt oder ist inkonsistent.  
  **Massnahme:** Definierter Session-Fallback und defensive Type-Guards.

- **Risiko:** UI wird durch neue Delta-Daten ueberladen.  
  **Massnahme:** Kompaktes Delta-Panel mit klarer Priorisierung.

- **Risiko:** Mock-Szenarien spiegeln reale Faelle nicht ausreichend.  
  **Massnahme:** Mindestens drei Szenarien (Verbesserung, gleichbleibend, Verschlechterung) testen.

