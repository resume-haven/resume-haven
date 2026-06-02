# Detailplanung Commit 37 - CV-Verwaltung Ausbau

**Branch:** `feature/cv-management-ux`
**Status:** In Arbeit
**Erstellt:** 2026-05-27

---

## Ziel

Verbesserung der Nutzbarkeit des User-Dashboards bei einer größeren Anzahl an gespeicherten Lebensläufen durch Einführung von Such-, Filter- und erweiterten Paginierungsfunktionen.

---

## Scope

## Aktueller Status & Grobplanung

### Slice 1: Daten-Infrastruktur & Metadaten
- [ ] Migration: Erweiterung der Tabelle `stored_resumes` um `file_name` und `original_filename`.
- [ ] DTO & Command: Anpassung von `StoreResumeDto` und `StoreResumeCommand` zur Aufnahme der Metadaten.
- [ ] Handler: Aktualisierung des `StoreResumeHandler` zur Speicherung der neuen Felder.

### Slice 2: Erfassung der Metadaten
- [ ] UI/Controller: Sicherstellen, dass beim Upload der ursprüngliche Dateiname erfasst und an den Store-Service übergeben wird.
- [ ] Fallback-Logik: Generierung eines Standard-Namens, falls kein Dateiname vorhanden ist (z.B. "Lebenslauf vom [Datum]").

### Slice 3: Backend-Logik für Suche & Filter
- [ ] Repository: Erweiterung des `ProfileRepository` um `search` und `filter` Parameter in der Paginate-Methode.
- [ ] Query/Handler: Anpassung von `ListStoredResumesQuery` und `ListStoredResumesHandler` zur Durchreichung der Suchparameter.

### Slice 4: Frontend-Integration (UI)
- [ ] View: Integration eines Suchfeldes und einfacher Filter-Elemente in `profile/index.blade.php`.
- [ ] UX: Implementierung von Sortier-Optionen (Datum absteigend/aufsteigend, Name).
- [ ] Feedback: Anzeige von "Keine Ergebnisse gefunden" bei aktiven Filtern.

### Slice 5: UX-Feinschliff & Quality Gates
- [ ] Paginierung: Optionale Einstellung der Treffer pro Seite.
- [ ] Tests: Unit-Tests für die neue Repository-Logik und Feature-Tests für die Dashboard-Suche.
- [ ] Qualität: PHPStan, Pint und Coverage-Check (>= 99%).

---

## Nicht-Scope

- Massen-Aktionen (Bulk-Delete).
- PDF-Vorschau oder Inhalts-Vorschau.
- Volltextsuche innerhalb der verschlüsselten Inhalte (Performance/Komplexität im MVP).

---

## Erfolgskriterien

- Nutzer können ihre CVs nach Name suchen.
- Paginierung funktioniert korrekt zusammen mit Such- und Filterparametern.
- Alle Tests sind grün, Coverage >= 99%.
- PHPStan Level 9: 0 Errors.

---

## Referenzen

- Aktiver Plan: `../../COMMIT_PLAN.md`
- Roadmap: `../ROADMAP.md`
- Vorheriger Commit: `PLANNING_COMMIT_36.md`
