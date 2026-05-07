# Detailplanung Commit 35 - Auth/Claim UX-Polish

**Branch:** `feature/commit-35-auth-claim-ux-polish`  
**Status:** In Arbeit  
**Erstellt:** 2026-05-07

---

## Ziel

Den bestehenden Auth-/Claim-Flow aus Nutzersicht konsistent machen, damit ein bereits erzeugtes
Analyse-Ergebnis nach Login/Registrierung sauber wieder angezeigt wird und der Claim-Zustand klar
kommuniziert ist - ohne neue Domain-Features einzufuehren.

---

## Entscheidungen (Planungs-Session 2026-05-07)

| # | Frage | Entscheidung |
|---|-------|--------------|
| 1 | Redirect ohne Ergebnisdaten | Claim-spezifischer Redirect mit Hinweis auf erneute Analyse |
| 2 | Session-Verhalten Ergebnisdaten | Back-Button-freundlich, Daten bleiben in der Session erhalten |
| 3 | Scope-Grenze | Commit strikt auf UX-/Flow-Polish begrenzt |
| 4 | Session-Key | Explizit: `analysis_result_view_data` |

---

## Scope

### Schritt 1 - Ergebnis-Route fuer Session-Restore

- `GET /result` mit Route-Name `result.show`
- Single-Action-Controller zum Rendern von `result.blade.php` aus Session-Daten
- Redirect auf `analyze`, wenn `analysis_result_view_data` fehlt

### Schritt 2 - Session-Persistenz im Analyse-Flow

- In `AnalyzeController` die View-Daten unter `analysis_result_view_data` persistieren
- Bei neuer Analyse bestehende Daten bewusst ueberschreiben
- Keine aggressive Session-Bereinigung (Back-Button-freundlich)

### Schritt 3 - Auth-Redirects auf Ergebnisfluss abstimmen

- Login/Register-Redirect mit `resume_token` in Session auf `result.show`
- Ohne Token weiterhin Standardziel `analyze`
- Bestehendes `intended`-Verhalten bleibt erhalten

### Schritt 4 - Claim-Feedback und Microcopy schaerfen

- Claim-spezifische Info bei fehlendem Ergebniszustand
- Klarere CTA-Texte in `result.blade.php` sowie kontextbezogene Hinweise in Login/Register
- Erfolgsfeedback fuer Auto-Claim sichtbar und konsistent

### Schritt 5 - Tests und Quality-Gates

- Feature-Tests fuer `result.show` (mit/ohne Session-Daten)
- Feature-Tests fuer Auth-Redirect-Verhalten mit Session-Token
- Relevante Listener-/Result-Tests fuer Claim-Feedback erweitern

---

## Nicht-Scope in Commit 35

- Keine neue Claim-Domainlogik
- Kein Dashboard-Ausbau, keine neuen Produktfeatures
- Keine Deployment-Neueinordnung
- Kein Provider-/AI-Layer-Refactoring

---

## Erfolgskriterien

- Ergebnis bleibt nach Login/Registrierung fuer den Nutzer nachvollziehbar verfuegbar
- Fehlende Ergebnisdaten fuehren claim-spezifisch und klar auf `analyze`
- Session-Verhalten ist back-button-freundlich und regressionsfrei
- Tests, PHPStan und Pint bleiben gruen

---

## Verweise

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Working Baseline: `../ai/WORKING_BASELINE.md`
- Historie-Index: `../COMMIT_HISTORY_INDEX.md`
- Vorheriger Detailplan: `PLANNING_COMMIT_34.md`
- Roadmap: `../ROADMAP.md`

