# Commit 26 - Profile-Ausbau ohne Auth

**Branch:** `feature/commit-26-profile-expansion-no-auth`  
**Status:** Abgeschlossen  
**Erstellt:** 2026-03-18

### Fortschritt (2026-04-16)
- [x] Slice 0: UX-Flow fuer Speichern/Laden/Feedback stabilisiert
- [x] Slice 1: MVP-Retention technisch umgesetzt (Expiry beim Laden + Cleanup-Pfad)
- [x] Slice 2: UI-Hinweise zu Datenhaltung/Lebensdauer sichtbar ergaenzt
- [x] Slice 3: CI-Guardrails explizit gehaertet (`AI_PROVIDER=mock`, leeres `GEMINI_API_KEY`)
- [x] Slice 4: Feature-Tests + PHPStan + Pint validiert

---

## Ziel

Profile-Funktionen ohne User-Auth lokal weiter ausbauen, mit Fokus auf UX und robuster Bedienbarkeit.

Kernfragen fuer Commit 26:

- Wie wird der Profile-Flow fuer Nutzende klarer und fehlertoleranter?
- Wie setzen wir Retention im MVP pragmatisch um, ohne produktive Plattformdetails vorwegzunehmen?
- Wie verhindern wir in CI verlässlich Datenabfluss nach extern?

---

## Scope

### Enthalten
- UX-first Ausbau im `Profile`-Flow (Speichern/Laden/Feedback)
- Konsistente Fehlermeldungen und klare Erfolgshinweise
- MVP-pragmatische Retention-Mechanik
- Zusaetzliche UI-Hinweise zur Datenhaltung und Lebensdauer
- CI-Guardrails als required:
  - `AI_PROVIDER=mock`
  - Keine externen AI-Secrets in CI
  - No-Egress fuer AI-Pfade mit Ansatz "allow internal services only"
- Unit-/Feature-Tests fuer neue/angepasste Flows

### Nicht enthalten
- User/Auth/AuthZ
- Produktive, plattformspezifische Retention-Endarchitektur
- Externe LLM-Provider-Integration in CI

---

## Technische Leitplanken

- DDD/CQRS/SOLID unveraendert einhalten
- Single-Action-Controller beibehalten
- Business-Logik in Actions/UseCases/Services
- DTO-first und vollstaendige Typisierung (PHPStan Level 9)
- Lokale Entwicklung ohne externen Datenabfluss

---

## Geplante Implementierungs-Slices

### Slice 0 - UX-Flow schaerfen (Profile)
- Profile-Speichern/Laden klarer fuehrbar machen
- Nutzerfeedback fuer Erfolg/Fehler vereinheitlichen
- Edge-Cases (ungueltige Tokens, fehlende Sessiondaten, defekte Payloads) UX-seitig sauber kommunizieren

### Slice 1 - MVP-Retention technisch
- Pragmatische Retention-Regeln implementieren (ohne Plattformkopplung)
- Bestehende Datenpfade defensiv absichern
- Technische Loeschpfade fuer lokale Nutzung vorsehen

### Slice 2 - UI-Hinweise Retention
- Sichtbare Hinweise in den betroffenen Profile-Views
- Erklaerung zu lokaler Datenhaltung und Lebensdauer
- Hinweischarakter klar als MVP-Stand kennzeichnen

### Slice 3 - CI-Guardrails (required)
- CI strikt auf `AI_PROVIDER=mock`
- Externe AI-Secrets in CI als unzulaessig behandeln
- No-Egress fuer AI-Pfade mit allgemeinem Ansatz "allow internal services only"

### Slice 4 - Tests + Quality-Gates
- Gezielte Unit-/Feature-Tests fuer Profile-Flow und Retention
- `php artisan test --compact` (betroffene Bereiche, dann gesamter Lauf)
- `make phpstan`
- `vendor/bin/pint --dirty --format agent`

---

## Erfolgskriterien (DoD)

1. Profile-Flow ohne Auth ist robust, konsistent und nachvollziehbar.
2. Retention ist im MVP technisch wirksam umgesetzt.
3. Zusaetzliche UI-Hinweise zu Datenhaltung/Lebensdauer sind sichtbar.
4. CI-Guardrails sind required und verhindern externen AI-Egress.
5. Tests, PHPStan und Pint bleiben gruen.

---

## Risiken & Gegenmassnahmen

- **Risiko:** MVP-Retention wird mit finaler Produktlogik vermischt.  
  **Massnahme:** Klare Trennung, offene Punkte als Technical Debt markieren.

- **Risiko:** CI-Guardrails blockieren unbeabsichtigt legitime interne Pfade.  
  **Massnahme:** "allow internal services only" klar dokumentieren und testbar machen.

- **Risiko:** UX-Hinweise sind uneinheitlich oder zu versteckt.  
  **Massnahme:** Zentrale Platzierung in betroffenen Profile-Flows und konsistente Wortwahl.

---

## Technical Debt (plattformabhaengig, getrennte Items)

### TD-26-01 - Storage-Strategie finalisieren
**Beschreibung:** Endgueltige Speicherstrategie fuer die Zielplattform festlegen (lokal/dev vs. produktiv).  
**Definition of Ready:**
- Zielplattform und Betriebsmodell sind entschieden.
- Anforderungen an Datenhaltung und Zugriffsmuster sind dokumentiert.

**Definition of Done:**
- Finales Storage-Design ist implementiert und dokumentiert.
- Migration/Anpassung bestehender Datenpfade ist abgeschlossen.
- Relevante Tests sind vorhanden und gruen.

### TD-26-02 - Retention-Lifecycle finalisieren
**Beschreibung:** Endgueltigen Lifecycle fuer Aufbewahrung/Loeschung auf Zielplattform umsetzen.  
**Definition of Ready:**
- Plattformbezogene Retention-Anforderungen sind geklaert.
- Trigger fuer Loeschung (zeit- oder ereignisbasiert) sind festgelegt.

**Definition of Done:**
- Finaler Lifecycle ist implementiert (inkl. Cleanup-Mechanik).
- Monitoring/Transparenz fuer Retention-Prozesse ist vorhanden.
- Akzeptanztests fuer Lifecycle-Szenarien sind gruen.

### TD-26-03 - Produktive Compliance-Haertung
**Beschreibung:** Compliance-/Security-Haertung fuer Produktionsbetrieb abschliessen.  
**Definition of Ready:**
- Zielplattform und Compliance-Rahmen sind verbindlich festgelegt.
- Konkrete Anforderungen (z. B. Logging, Auditing, Secrets-Handling) sind dokumentiert.

**Definition of Done:**
- Produktive Compliance-Massnahmen sind implementiert.
- Dokumentation fuer Betrieb und Audits ist aktualisiert.
- Security-/Regression-Tests sind gruen.

---

## Hinweis zur Roadmap

Update in `docs/ROADMAP.md` (Commit 26 von geplant -> in Arbeit) ist erfolgt.



