# Detailplanung Commit 30 — CV-Verwaltung (Multi-CV CRUD)

**Branch:** `feature/commit-30-multi-cv-crud`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-04-23  
**Abgeschlossen:** 2026-04-24

---

## Ziel

Die bisherige Profilfunktion von einem einzelnen, tokenbasierten CV-Flow zu einer
robusten CV-Verwaltung pro User ausbauen. Nutzer sollen mehrere gespeicherte CVs
sehen, bearbeiten, loeschen und erneut fuer Analysen verwenden koennen.

---

## Entscheidungslog (aus Planungs-Session 2026-04-23)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Restarbeiten aus Commit 29 | Nicht als `29a`, sondern als Roadmap-Follow-up |
| 2 | Startumfang Commit 30 | Direkt kompletter CRUD-Scope |
| 3 | Migration `resume_token` → `resume_tokens[]` | Pragmatic Cutover ohne Backward-Compat-Garantie |
| 4 | Pagination | Ja, initial fixe Seitengroesse `10` |
| 5 | Testtiefe | Vollstaendiger Testkatalog inkl. Edge-Cases |

---

## Scope

### Schritt 1 — Dashboard / CV-Uebersicht

- Neue User-Ansicht fuer gespeicherte CVs
- Pagination mit fixer Seitengroesse `10`
- Sortierung: zuletzt aktualisierte CVs zuerst
- Nur eigene CVs sichtbar; Admin darf im Rahmen der Policy zugreifen

### Schritt 2 — Multi-CV speichern

- Speichern erzeugt zusaetzliche CV-Eintraege statt den impliziten Single-Flow fortzuschreiben
- Bei eingeloggten Nutzern wird `user_id` direkt gesetzt
- Session fuehrt Tokens in `resume_tokens[]`
- Duplikatbehandlung fuer Session-Tokens eindeutig definieren

### Schritt 3 — CV bearbeiten

- Owner kann vorhandene CVs aktualisieren
- Admin kann moderierend zugreifen
- Token-/Owner-Verknuepfung bleibt stabil
- Analyse-Reuse mit aktualisiertem CV bleibt moeglich

### Schritt 4 — CV loeschen

- Owner kann eigene CVs loeschen
- Admin darf loeschen
- Loeschen entfernt Token sauber aus `resume_tokens[]`
- Nicht mehr vorhandene CVs sind nicht erneut ladbar

### Schritt 5 — Ownership / Policy / Routing

- `ProfilePolicy` wird auf Dashboard-/CRUD-Flows konsequent angewandt
- Keine Repository-Bypaesse an Controllergrenzen
- Routen und Use-Cases trennen Lesefluessen und Schreibpfade klar

### Schritt 6 — Regressionen absichern

- Bestehende Flows fuer Claim, Load, Retention und Analyse-Reuse bleiben gruen
- `resume_token` wird nicht mehr als primaerer Vertrag vorausgesetzt
- Leere oder fehlende `resume_tokens[]`-Session wird robust behandelt

---

## Testkatalog

### Feature-Tests

- User sieht nur eigene CVs in paginierter Liste
- Pagination liefert maximal 10 Eintraege pro Seite
- Owner kann CV erstellen, bearbeiten, loeschen
- Fremder User darf weder bearbeiten noch loeschen
- Admin darf auf freigegebene Verwaltungsoperationen zugreifen
- `resume_tokens[]` wird beim Speichern erweitert und beim Loeschen bereinigt
- Bestehende Claim-/Load-Flows bleiben funktionsfaehig

### Unit-Tests

- Repository-Sortierung / Pagination-Vertrag
- Session-Token-Helfer fuer `resume_tokens[]`
- Policy-Entscheidungen (Owner / Fremder / Admin)
- Action-/DTO-Raender fuer Update/Delete/Cutover

### Edge-Cases

- Leere CV-Liste
- Ungueltiger oder nicht mehr vorhandener Token
- Doppelte Tokens in der Session
- Loeschen eines bereits geloeschten Datensatzes
- Update mit ungueltigem Inhalt / Validierungsfehler

---

## Nicht-Scope in Commit 30

- ❌ Keine Team-/Mandantenverwaltung
- ❌ Keine User-basierte Schluesselrotation fuer gespeicherte CVs
- ❌ Keine Admin-UI ausser notwendiger Vorbereitungen
- ❌ Keine frei konfigurierbare Pagination im MVP
- ❌ Keine Suche / Filter / Tags in der CV-Liste (spaeterer Ausbau)

---

## Erfolgskriterien

- Nutzer sehen ihre CVs paginiert und nach Aktualitaet sortiert
- CRUD funktioniert fuer Owner/Admin gemaess Policy
- `resume_tokens[]` ersetzt den alten Single-Token-Flow robust
- Claim-, Load- und Retention-Flows bleiben regressionsfrei
- PHPStan Level 9: 0 Errors
- Pint: sauber
- Coverage-Ziel bleibt eingehalten

---

## Risiken / offene Punkte

- Pragmatic Cutover auf `resume_tokens[]` erfordert saubere Session-Migration innerhalb des laufenden Flows
- Delete-/Update-Aktionen duerfen bestehende Analyse- oder Claim-Flows nicht inkonsistent machen
- Die Dashboard-UI soll klar bleiben, obwohl noch keine spaeteren Komfortfunktionen (Suche/Filter) vorhanden sind

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_29.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`

---

## Abschlussnotiz

- Commit 30 wurde erfolgreich abgeschlossen und gemerged.
- Naechste Umsetzungsreihenfolge fuer Commit 31 wurde festgelegt: **3, 1, 2**.

