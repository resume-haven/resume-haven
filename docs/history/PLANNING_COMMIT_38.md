# COMMIT_PLAN_38 – Integration von Paperdoc.dev (Import & Export)

Dieses Ticket beschreibt die Einführung von `paperdoc.dev` zur Automatisierung des Dokumenten-Imports (PDF/DOCX) und der Generierung von Analyse-Ergebnissen als PDF.

## 🎯 Ziele
- **Import:** Extraktion von Text aus hochgeladenen PDF- und Word-Dokumenten (CV & Job).
- **Export:** Generierung einer formatierten PDF-Zusammenfassung der Analyseergebnisse.
- **Architektur:** Nahtlose Integration in den bestehenden `ExecuteAnalyzeFlowAction`.

---

## 🛠️ Aufgaben

### 1. Infrastruktur & Abhängigkeiten
- [ ] Installation von Paperdoc: `composer require paperdoc/paperdoc-laravel`
- [ ] Veröffentlichung der Konfiguration: `php artisan vendor:publish --provider="Paperdoc\PaperdocServiceProvider"`
- [ ] Validierung der lokalen Systemanforderungen (z.B. `pdftotext` oder alternative PHP-Parser).

### 2. Backend: Dokument-Parsing
- [ ] Erstellung der Action `App\Domains\Analysis\Actions\ParseDocumentAction`.
- [ ] Implementierung der Logik zur automatischen Formaterkennung (PDF/DOCX).
- [ ] Fehler-Handling bei beschädigten oder passwortgeschützten Dateien.

### 3. Flow-Integration
- [ ] Anpassung der `ExecuteAnalyzeFlowAction`:
    - Prüfung auf `cv_file` und `job_file` im Request.
    - Delegation an `ParseDocumentAction`.
    - Fallback auf Text-Input, falls keine Datei vorhanden ist.
- [ ] Erweiterung der Validierung um File-Types (`mimes:pdf,docx`) und Max-Size.

### 4. UI: Dateiupload (Frontend)
- [ ] Erweiterung der `welcome.blade.php` um Upload-Komponenten (Dropzones oder einfache File-Inputs).
- [ ] Visuelles Feedback: Anzeige des extrahierten Texts oder Bestätigung des Uploads.
- [ ] Tailwind-Styling der neuen Elemente passend zum bestehenden Design.

### 5. PDF-Export (Feature-Erweiterung)
- [ ] Erstellung eines neuen Blade-Templates `resources/views/analysis/export-pdf.blade.php`.
- [ ] Implementierung eines `ExportAnalysisController` (Single Action).
- [ ] Nutzung von Paperdoc zur HTML-zu-PDF Konvertierung.

---

## 🧪 Verifikation
- [ ] **Unit Test:** `ParseDocumentActionTest` prüft erfolgreiches Auslesen von Test-PDFs und DOCX-Dateien.
- [ ] **Feature Test:** Upload-Flow im `AnalyzeController` mit Mock-Files verifizieren.
- [ ] **Manueller Test:** Export-Funktion triggern und generiertes PDF auf Layout-Korrektheit prüfen.

---

## 📌 Notizen
- OCR wird in diesem Schritt explizit ausgelassen (gemäß Anforderung).
- Die Datensparsamkeit bleibt gewahrt: Hochgeladene Dateien werden nach dem Parsing sofort wieder gelöscht und nicht permanent gespeichert.
