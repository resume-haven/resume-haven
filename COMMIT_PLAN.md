# ResumeHaven - Commit-Plan (Active)

Dieser Plan enthaelt nur den **aktiven** und **naechsten** Arbeitsfokus.  
Abgeschlossene Details sind in die Historie ausgelagert.

**Letzte Aktualisierung:** 2026-04-28  
**Aktueller Stand:** Commit 31 abgeschlossen, LLM-Block in Arbeit

---

## Status-Ueberblick

### Abgeschlossen
- Commit 1-25 (kompakt in `docs/history/COMMIT_HISTORY_2026.md`)
- Hinweis: Commit 19 wurde historisch uebersprungen
- Commit 25: Analysequalität & Erklärbarkeit (abgeschlossen)
- Commit 26: Profile-Ausbau ohne Auth (abgeschlossen)
- Commit 27: Acceptance-Tests Kernflows (abgeschlossen)
- Commit 28: Architecture-Tests & Engineering-Härtung (abgeschlossen)
- Commit 29: Auth + Rollen + Claim-Flow (abgeschlossen)
- Commit 30: CV-Verwaltung (Multi-CV CRUD) (abgeschlossen)
- Commit 31: Delete/AuthZ-Gate + provider-generische AI-Basis (abgeschlossen)

### In Arbeit
- LLM-Block: Provider-agnostischer AI-Layer (Fortsetzung nach L1)

### In Planung (Folge)
- Deployment: nach CV-Verwaltung und LLM-Block neu einordnen

### Commit-31 Reihenfolge (abgeschlossen)
- 3) Acceptance-Gate fuer Multi-CV erweitern (S, niedriges Risiko)
- 1) Autorisierter Delete-Flow mit Session-Token-Cleanup (M, mittleres Risiko)
- 2) Session-Token-Handling vereinheitlichen (S-M, niedrig-mittleres Risiko)

### Geplante Folge-Reihenfolge (neu priorisiert)
- **LLM-Block (nach Commit 31):** Provider-agnostischer AI-Layer ← **aktuell**
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

## Commit 29 — Auth + Rollen + Claim-Flow

**Branch:** `feature/commit-29-auth-roles-claim`  
**Status:** Abgeschlossen

### Ziel
- Laravel Breeze (Blade, minimal): Login / Registrierung / Logout
- `UserRole`-Enum (`user`, `admin`) auf `users`-Tabelle
- Auto-Claim: anonyme Session-CVs werden beim Login automatisch dem User zugeordnet
- Direkter Claim beim `store`, wenn User bereits eingeloggt
- Inline Claim-CTA in `result.blade.php`
- `ProfilePolicy` + Admin-Middleware-Vorbereitung

### Scope-Highlights
- Breeze auf Minimal-Auth getrimmt (kein Dashboard, keine E-Mail-Verifizierung)
- `stored_resumes.user_id` (nullable FK) — geclaimte CVs vom Retention-Pruning ausgenommen
- `ProfileRepository`: `claimByToken()`, `getByUser()`, `store()` mit optionalem `$userId`
- `AutoClaimResumesListener` auf `Illuminate\Auth\Events\Login`
- Tech-Debt dokumentiert: `resume_token` (singular) → `resume_tokens[]`-Array bei Commit 30+

### Nicht-Scope
- Kein User-Dashboard / CV-Übersichtsseite
- Keine E-Mail-Verifizierung (MVP-Entscheidung)
- Keine Admin-Views/-Routen (Middleware-Alias nur vorbereitet)
- Keine User-basierte CV-Verschlüsselung

### Erfolgskriterien
- Register / Login / Logout funktionieren
- Claim-Flow (direkt + Auto-Claim) funktioniert und ist getestet
- `pruneExpired()` schont geclaimte CVs
- PHPStan Level 9: 0 Errors, Pint: sauber, Coverage ≥ 95 %

### Ergebnis
- Minimal-Auth via Breeze (Login / Registrierung / Logout) ist eingeführt
- Rollenmodell (`user`, `admin`), `ProfilePolicy` und Admin-Middleware-Vorbereitung sind umgesetzt
- Direkter Claim beim Speichern sowie Auto-Claim beim Login funktionieren
- Claim-CTA und Success-Hinweis sind in `result.blade.php` integriert und getestet
- Verbleibende UX-Polishes werden als Roadmap-Item weitergefuehrt, nicht als eigener Commit-Block

### Detailplan
- Siehe: `docs/history/PLANNING_COMMIT_29.md`

---

## Commit 30 — CV-Verwaltung (Multi-CV CRUD)

**Branch:** `feature/commit-30-multi-cv-crud`  
**Status:** Abgeschlossen

### Ziel
- Ein paginiertes User-Dashboard fuer mehrere gespeicherte CVs bereitstellen
- Den singulaeren Session-Token-Flow auf `resume_tokens[]` umstellen
- CVs pro User sicher anzeigen, bearbeiten, loeschen und erneut fuer Analysen verwenden

### Scope
- User-Dashboard / CV-Uebersicht mit Pagination (`perPage=10` fuer den MVP)
- CRUD fuer eigene CVs (Create, Read, Update, Delete)
- Ownership/Policy-Durchsetzung fuer User/Admin
- Session-Cutover: `resume_token` → `resume_tokens[]`
- Token- und User-Flows sauber verzahnen (Load, Claim, Delete, Re-Use)
- Tests pro Slice (Feature + Unit + Edge-Cases)

### Nicht-Scope
- Keine Team-/Mandantenverwaltung
- Keine User-basierte Schluesselrotation fuer verschluesselte CVs
- Keine Admin-Oberflaechen ausser vorbereiteter Berechtigungsbasis
- Keine Konfigurierbarkeit der Pagination im MVP

### Erfolgskriterien
- Nutzer sehen ihre eigenen CVs paginiert und sortiert
- CRUD-Operationen funktionieren ausschliesslich fuer Owner bzw. Admins
- `resume_tokens[]` ersetzt den bisherigen Single-Token-Flow robust
- Claim-, Load- und Retention-Flows bleiben regressionsfrei
- Tests, PHPStan und Pint bleiben gruen; Coverage-Ziel bleibt eingehalten

### Detailplan
- Siehe: `docs/history/PLANNING_COMMIT_30.md`

---

## Tech-Debt-Register

| ID | Beschreibung | Geplant für |
|----|-------------|-------------|
| TD-01 | `resume_token` in Session (singular) → `resume_tokens[]`-Array | Commit 30 (CV-Verwaltung) |
| TD-02 | User-basierte CV-Verschlüsselung (aktuell Token-as-Secret) | Separates Refactoring |

---



- CI/Branch-Protection wurde in Commit 23 abgeschlossen
- Planung wurde auf produktnahen Mehrwert neu priorisiert (`A,B,D,C,E`)
- User/Auth wurde in Commit 29 eingefuehrt; aktueller Fokus ist die Multi-CV-Verwaltung
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
- Detailplanung Commit 27: `docs/history/PLANNING_COMMIT_27.md`
- Detailplanung Commit 28: `docs/history/PLANNING_COMMIT_28.md`
- Detailplanung Commit 29: `docs/history/PLANNING_COMMIT_29.md`
- Detailplanung Commit 30: `docs/history/PLANNING_COMMIT_30.md`
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
**Status:** Abgeschlossen

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
