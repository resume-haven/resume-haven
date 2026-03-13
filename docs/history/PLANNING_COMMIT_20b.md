# Commit 20b – Legal-Seiten & Vertrauen
## Detaillierter Implementierungsplan

**Datum:** 2026-03-08  
**Status:** 🔄 In Planung  
**Geschätzter Aufwand:** ~4h  
**Abhängigkeiten:** Commit 17 & 18a abgeschlossen ✅

---

## 🎯 Ziel

Rechtliche MVP-Anforderungen erfüllen:
- ⚖️ Impressum (Anbieterkennzeichnung)
- 🔒 Datenschutzerklärung (DSGVO-konform)
- 📧 Kontaktformular (validiert, sicher)
- 📜 Lizenzen (automatisch generiert)

---

## 📋 Checkliste

### Phase 1: Routes & Controller ⏱️ 30min

- [ ] Routes in `web.php` definieren
  - [ ] `GET /impressum` → `LegalController@impressum`
  - [ ] `GET /datenschutz` → `LegalController@datenschutz`
  - [ ] `GET /lizenzen` → `LegalController@lizenzen`
  - [ ] `GET /kontakt` → `ContactController@show`
  - [ ] `POST /kontakt` → `ContactController@submit`

- [ ] Controller erstellen
  - [ ] `app/Http/Controllers/LegalController.php`
  - [ ] `app/Http/Controllers/ContactController.php`

**Architektur-Notizen:**
- LegalController: Named methods (Ausnahme von Single-Action für statische Content-Seiten)
- ContactController: 2 methods (show/submit) - Standard-CRUD-Pattern

---

### Phase 2: Legal Views ⏱️ 45min

- [ ] Verzeichnis erstellen: `resources/views/legal/`
- [ ] Views erstellen:
  - [ ] `impressum.blade.php` (mit Platzhalter-Content)
  - [ ] `datenschutz.blade.php` (DSGVO-Vorlage)
  - [ ] `kontakt.blade.php` (Formular)
  - [ ] `lizenzen.blade.php` (Tabellen-Layout)

**Content-Strategie:**
- Platzhalter-Texte mit deutlichem Hinweis: "Vor Produktivbetrieb anpassen"
- Prose-Styling (`prose dark:prose-invert`)
- Responsive Design (Tailwind-Breakpoints)

---

### Phase 3: Footer-Navigation ⏱️ 15min

- [ ] `layouts/app.blade.php` erweitern
  - [ ] Legal-Links hinzufügen (Impressum, Datenschutz, Kontakt, Lizenzen)
  - [ ] Responsive Layout (Stack < 768px, Horizontal ≥ 768px)
  - [ ] Dark-Mode-Support

**Design-Vorgaben:**
- Links: `hover:text-primary`
- Separator: `•` (text-gray-300)
- Footer bleibt unten (mt-16)

---

### Phase 4: Kontaktformular Backend ⏱️ 1h

**4.1 DTO erstellen**
- [ ] `app/Dto/ContactRequestDto.php`
  ```php
  readonly class ContactRequestDto {
      public function __construct(
          public string $name,
          public string $email,
          public string $message,
      ) {}
  }
  ```

**4.2 Form Request**
- [ ] `app/Http/Requests/ContactRequest.php`
  - [ ] Validation Rules:
    - `name`: required, string, min:2, max:100
    - `email`: required, email, max:255
    - `message`: required, string, min:10, max:5000

**4.3 UseCase (optional)**
- [ ] `app/Domains/Contact/UseCases/SendContactMessageAction.php`
  - [ ] Eingabe validieren
  - [ ] In Log schreiben (MVP: kein E-Mail-Versand)
  - [ ] Success-DTO zurückgeben

**4.4 ContactController implementieren**
- [ ] `show()`: return view
- [ ] `submit()`: Validierung → UseCase → Redirect mit Success-Message

---

### Phase 5: Kontaktformular Frontend ⏱️ 30min

**5.1 View** (`resources/views/legal/kontakt.blade.php`)
- [ ] Formular mit Feldern:
  - [ ] Name (Input Text)
  - [ ] E-Mail (Input Email)
  - [ ] Nachricht (Textarea)
  - [ ] Submit-Button
- [ ] CSRF-Token (`@csrf`)
- [ ] Validation-Errors anzeigen (`@error`)
- [ ] Success-Message anzeigen (`@if (session('success'))`)

**5.2 Styling**
- [ ] Tailwind Forms Plugin (falls nicht vorhanden)
- [ ] Responsive Layout
- [ ] Dark-Mode

---

### Phase 6: Lizenzen-Generator ⏱️ 1h

**6.1 Artisan Command**
- [ ] `app/Console/Commands/GenerateLicenseDataCommand.php`
  - [ ] Signature: `licenses:generate`
  - [ ] Methode: `parseComposerLock()`
  - [ ] Methode: `parsePackageLock()`
  - [ ] Output: `storage/app/licenses.json`

**6.2 Parser-Logik**
```php
parseComposerLock() {
    $composer = json_decode(file_get_contents('composer.lock'), true);
    foreach ($composer['packages'] as $pkg) {
        yield [
            'name' => $pkg['name'],
            'version' => $pkg['version'],
            'license' => implode(', ', $pkg['license'] ?? ['Unknown']),
        ];
    }
}
```

**6.3 LegalController::lizenzen()**
- [ ] Lizenzen-JSON laden
- [ ] An View übergeben (php, node, generated_at)

**6.4 Composer-Integration**
- [ ] `composer.json` erweitern unter `"scripts"`:
  ```json
  "licenses:generate": "@php artisan licenses:generate"
  ```

**6.5 Makefile-Integration**
- [ ] Neues Target in `Makefile` hinzufügen:
  ```makefile
  licenses: ## Lizenzen neu generieren
      docker exec -it resumehaven-php composer run licenses:generate
  ```

**6.6 Build-Integration (Post-Update)**
- [ ] `composer.json` erweitern unter `"scripts"` → `"post-update-cmd"`:
  ```json
  "post-update-cmd": [
      "@php artisan vendor:publish --tag=laravel-assets --ansi --force",
      "@php artisan licenses:generate"
  ]
  ```

---

### Phase 7: Tests ⏱️ 45min

**7.1 Feature-Tests erstellen**

- [ ] `tests/Feature/LegalPagesTest.php`
  - [ ] `test('impressum ist erreichbar')`
  - [ ] `test('datenschutz ist erreichbar')`
  - [ ] `test('lizenzen ist erreichbar')`

- [ ] `tests/Feature/ContactFormTest.php`
  - [ ] `test('kontakt-seite zeigt formular')`
  - [ ] `test('kontakt validiert pflichtfelder')`
  - [ ] `test('kontakt akzeptiert valide eingabe')`
  - [ ] `test('kontakt erfordert csrf-token')`
  - [ ] `test('kontakt zeigt success-message')`

- [ ] `tests/Feature/FooterNavigationTest.php`
  - [ ] `test('footer enthält alle legal-links')`
  - [ ] `test('footer-links sind klickbar')`

- [ ] `tests/Feature/LicensesPageTest.php`
  - [ ] `test('lizenzen zeigt php-pakete')`
  - [ ] `test('lizenzen zeigt node-pakete')`
  - [ ] `test('lizenzen zeigt generierungsdatum')`

**7.2 Unit-Tests (optional)**
- [ ] `tests/Unit/ContactRequestDtoTest.php`
- [ ] `tests/Unit/GenerateLicenseDataCommandTest.php`

---

### Phase 8: Quality Gates ⏱️ 30min

- [ ] `make test` → Alle Tests grün
- [ ] `make phpstan` → 0 Errors (Level 9)
- [ ] `make pint-analyse` → Code-Style konform
- [ ] `php artisan licenses:generate` → licenses.json erstellt
- [ ] Manuelle Browser-Tests:
  - [ ] Alle Legal-Seiten erreichbar
  - [ ] Footer-Links funktionieren
  - [ ] Kontaktformular validiert
  - [ ] Success-Message wird angezeigt
  - [ ] Responsive Design (Mobile + Desktop)
  - [ ] Dark-Mode funktioniert

---

## 🚀 Implementierungsreihenfolge (Step-by-Step)

### Step 1: Statische Seiten (Quick Win)
1. Routes definieren (Impressum, Datenschutz)
2. LegalController erstellen (2 Methoden)
3. Views erstellen (mit Platzhalter)
4. Footer-Links hinzufügen
5. Tests schreiben + ausführen

**Checkpoint:** Impressum + Datenschutz erreichbar, Footer-Links funktionieren

---

### Step 2: Kontaktformular
1. ContactController + ContactRequest
2. kontakt.blade.php (Formular)
3. Validation + Success-Message
4. Tests schreiben + ausführen

**Checkpoint:** Kontaktformular funktioniert, validiert korrekt

---

### Step 3: Lizenzen-Generator
1. GenerateLicenseDataCommand
2. parseComposerLock() + parsePackageLock()
3. lizenzen.blade.php
4. Command ausführen + testen
5. Tests schreiben + ausführen

**Checkpoint:** Lizenzen werden automatisch generiert

---

### Step 4: Final Polish
1. PHPStan + Pint
2. Manuelle Browser-Tests
3. Dokumentation aktualisieren
4. Commit erstellen

**Checkpoint:** Alle Quality Gates grün ✅

---

## 📦 Erwartete Dateistruktur nach Implementierung

```
src/
├── app/
│   ├── Console/Commands/
│   │   └── GenerateLicenseDataCommand.php          [NEU]
│   ├── Dto/
│   │   └── ContactRequestDto.php                   [NEU]
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ContactController.php               [NEU]
│   │   │   └── LegalController.php                 [NEU]
│   │   └── Requests/
│   │       └── ContactRequest.php                  [NEU]
│   └── Domains/Contact/                            [NEU]
│       └── UseCases/
│           └── SendContactMessageAction.php        [NEU, optional]
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php                           [ÄNDERN]
│   └── legal/                                      [NEU]
│       ├── impressum.blade.php                     [NEU]
│       ├── datenschutz.blade.php                   [NEU]
│       ├── kontakt.blade.php                       [NEU]
│       └── lizenzen.blade.php                      [NEU]
├── routes/
│   └── web.php                                     [ÄNDERN]
├── tests/Feature/
│   ├── ContactFormTest.php                         [NEU]
│   ├── FooterNavigationTest.php                    [NEU]
│   ├── LegalPagesTest.php                          [NEU]
│   └── LicensesPageTest.php                        [NEU]
└── storage/app/
    └── licenses.json                               [GENERIERT]
```

**Gesamt:**
- **Neu:** 15 Dateien
- **Ändern:** 2 Dateien
- **Generiert:** 1 Datei

---

## ⚠️ Wichtige Hinweise

### MVP-Einschränkungen
- **Kein E-Mail-Versand:** Kontaktformular loggt nur (später mit Mail-Queue)
- **Platzhalter-Content:** Impressum/Datenschutz müssen vor Produktivbetrieb angepasst werden
- **Kein Rate-Limiting:** Kommt später mit Redis
- **Keine Admin-UI:** Contact-Messages nur im Log sichtbar

### DSGVO-Relevanz
- ✅ Datenschutzerklärung vorhanden (Muster)
- ✅ Kontaktformular mit Zweckbindung
- ⚠️ Cookie-Banner fehlt noch (kommt mit Analytics)
- ⚠️ Opt-in für Newsletter fehlt (nicht MVP-relevant)

### Architektur-Kompromisse
- **LegalController:** Named Methods statt Single-Action (Pragmatismus für statische Seiten)
- **ContactController:** Zwei Methods (show/submit) statt separate Controller
- **Begründung:** Single-Action-Prinzip primär für komplexe Business-Logic, nicht für simple CRUD/Views

---

## 📊 Erfolgskriterien

### Funktional
- [x] Alle 4 Legal-Seiten erreichbar (200 OK)
- [x] Footer-Links auf allen Seiten sichtbar
- [x] Kontaktformular validiert serverseitig
- [x] Success-/Error-Messages werden angezeigt
- [x] Lizenzen automatisch generiert

### Technisch
- [x] Tests: 100% grün (Pest)
- [x] PHPStan: Level 9, 0 Errors
- [x] Pint: Code-Style konform
- [x] CSRF-Protection aktiv
- [x] Responsive + Dark-Mode

### Qualität
- [x] Code folgt SOLID-Prinzipien
- [x] DTOs immutable
- [x] Input-Validierung strikt
- [x] Security Best Practices

---

## 🎉 Fertig!

Nach Abschluss von Commit 20b ist ResumeHaven rechtlich MVP-ready und kann mit Commit 20 (Responsive Design) und 20a (Dark-Mode) produktiv gehen! 🚀


