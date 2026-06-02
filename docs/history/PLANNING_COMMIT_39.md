# Planungs-Doku: Commit 39 – Bewerbungserstellung & Lifecycle

Dieses Dokument beschreibt die detaillierte Planung für den Übergang von einer abgeschlossenen Analyse zur Erstellung einer konkreten Bewerbung.

## 1. Zielsetzung
Dem Nutzer ermöglichen, basierend auf einer Analyse sofort eine Bewerbung anzulegen, zusätzliche Informationen zu pflegen und optimierte Dokumente (Kompetenzlebenslauf) zu generieren und zu speichern.

## 2. User Flow & UI-Spezifikation

### 2.1 Startpunkt: Analyse-Ergebnisseite
*   **Button:** "🚀 Bewerbung für diesen Job starten"
*   **Aktion:** Leitet auf `/applications/create` weiter und übergibt die Analyse-ID (oder den Snapshot per Session/Cache).

### 2.2 Die Erstellungs-Maske (`/applications/create`)
Die Seite ist in drei logische Bereiche unterteilt:

#### A. Bewerbungs-Metadaten (Eingabe)
*   **Status:** Dropdown (Entwurf, Abgeschickt, Wartend auf Antwort).
*   **Gehaltswunsch:** Freitextfeld (z.B. "85.000 €" oder "Verhandlungsbasis").
*   **Bewerbungsdatum:** Datums-Picker (Default: heute).
*   **Notizen:** Mehrzeiliges Textfeld für persönliche Gedanken zur Strategie oder Recherche-Notizen zum Unternehmen.

#### B. KI-Generierungs-Zentrum & Editor (Interaktiv)
*   **Editierbare Vorschläge:**
    *   Sowohl der **Kompetenzlebenslauf** als auch das **Anschreiben-Skelett** werden in einem interaktiven Editor (Markdown-basiert) angezeigt.
    *   Der Nutzer kann die KI-Vorschläge direkt im Browser anpassen, ergänzen oder kürzen.
    *   Speicherung erfolgt als `content_raw` in der Dokument-Version.
*   **KI-Aktionen:** Buttons zum (Neu-)Generieren der Entwürfe basierend auf den Analysedaten.

#### C. Notizen & Ereignis-Historie
*   **Allgemeines Notizfeld:** Ein persistentes Feld für strategische Gedanken, Firmeninfos oder allgemeine Recherche.
*   **Chronologische Ereignisse (Timeline):**
    *   Möglichkeit, spezifische Ereignisse mit **Datum und Uhrzeit** hinzuzufügen.
    *   Beispiele: "Rückmeldung erhalten", "Telefoninterview vereinbart", "Vorstellungsgespräch".
    *   Einfache Listenansicht (Timeline) unterhalb der Hauptdaten.

#### D. Preview & Save
*   Live-Vorschau der wichtigsten Daten.
*   Button "Bewerbung finalisieren & speichern".

## 3. Datenmodell-Details (Domain: ApplicationManagement)

### Schema: `applications`
*   `id` (UUID, Primary)
*   `user_id` (UUID, Foreign Key)
*   `title` (String)
*   `company` (String)
*   `analysis_snapshot` (JSONB)
*   `salary_expectation` (String)
*   `application_date` (Date)
*   `current_status` (String)
*   `general_notes` (Text, nullable) - Das allgemeine Notizfeld.
*   `created_at / updated_at` (Timestamps)

### Schema: `application_events` (Neu: Chronologie)
*   `id` (UUID, Primary)
*   `application_id` (UUID, Foreign Key)
*   `type` (String, z.B. "note", "milestone", "interview")
*   `description` (Text)
*   `occurred_at` (DateTime) - Datum und Uhrzeit des Ereignisses.
*   `created_at / updated_at` (Timestamps)

### Schema: `application_documents` (Versioniert & Editierbar)
*   `id` (UUID, Primary)
*   `application_id` (UUID, Foreign Key)
*   `type` (Enum: cv, cover_letter, job_description)
*   `content_raw` (Text) - Der editierbare Markdown/Text.
*   `version` (Integer)
*   `is_active` (Boolean) - Welche Version soll für den PDF-Druck verwendet werden?
*   `metadata` (JSONB) - Speichert z.B. den verwendeten Prompt oder KI-Parameter.

## 4. Technische Implementierungsschritte (Detailliert)

1.  **Migration & Models:** Tabellen anlegen und Eloquent-Models in der neuen Domain `ApplicationManagement` erstellen.
2.  **Route & Controller:** `ApplicationController` mit `create` (View) und `store` (Action) Methoden.
3.  **UseCase `PrepareApplicationData`:** Sammelt die Analysedaten und bereitet das DTO für die View vor.
4.  **KI-Service Erweiterung:** Neuer Prompt-Typ `GENERATE_SKILLS_CV` und `GENERATE_COVER_LETTER_SKELETON`.
5.  **UI-Prototyp:** Erstellung der Blade-Views mit Tailwind-Komponenten für Formulare und Markdown-Vorschau.

## 5. Meilensteine
1.  **Datenbank-Migration:** Tabellen für `applications` und `application_documents` erstellen.
2.  **Basis-CRUD:** Erstellen und Speichern der Bewerbung mit Metadaten.
3.  **CV-Generierung:** Logik zur Erstellung des Kompetenzlebenslaufs aus Matches/Gaps.
4.  **UI-Integration:** Button auf Analyse-Seite und die neue Erstellungs-Maske.

## 6. Offene Punkte / Spätere Erweiterungen
*   Echtzeit-Editing der Rohdaten (Commit 41).
*   Event-Historie (z.B. "Eingangsbestätigung erhalten").
*   Dashboard-Anbindung (Commit 42).
