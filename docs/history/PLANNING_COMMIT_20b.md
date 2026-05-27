# Commit 20b – Legal Sites & Trust
## Detailed implementation plan

**Date:** 2026-03-08
**Status:** 🔄 In planning
**Estimated effort:** ~4h
**Dependencies:** Commit 17 & 18a completed ✅

---

## 🎯 Target

Meet Legal MVP Requirements:
- ⚖️ Imprint (provider identification)
- 🔒 Data protection declaration (GDPR compliant)
- 📧 Contact form (validated, secure)
- 📜 Licenses (automatically generated)

---

## 📋 Checklist

### Phase 1: Routes & Controllers ⏱️ 30min

- [ ] Define routes in `web.php`
  - [ ] `GET /impressum` → `LegalController@impressum`
  - [ ] `GET /datenschutz` → `LegalController@datenschutz`
  - [ ] `GET /lizenzen` → `LegalController@lizenzen`
  - [ ] `GET /kontakt` → `ContactController@show`
  - [ ] `POST /kontakt` → `ContactController@submit`

- [ ] Create controller
  - [ ] `app/Http/Controllers/LegalController.php`
  - [ ] `app/Http/Controllers/ContactController.php`

**Architecture Notes:**
- LegalController: Named methods (Exception from single action for static content pages)
- ContactController: 2 methods (show/submit) - Standard CRUD pattern

---

### Phase 2: Legal Views ⏱️ 45min

- [ ] Create directory: `resources/views/legal/`
- [ ] Create views:
  - [ ] `impressum.blade.php` (with placeholder content)
  - [ ] `datenschutz.blade.php` (GDPR template)
  - [ ] `kontakt.blade.php` (form)
  - [ ] `lizenzen.blade.php` (table layout)

**Content strategy:**
- Placeholder texts with a clear note: “Adapt before productive operation”
- Prose styling (`prose dark:prose-invert`)
- Responsive design (Tailwind breakpoints)

---

### Phase 3: Footer navigation ⏱️ 15min

- [ ] `layouts/app.blade.php` expand
  - [ ] Add legal links (imprint, data protection, contact, licenses)
  - [ ] Responsive Layout (Stack < 768px, Horizontal ≥ 768px)
  - [ ] Dark mode support

**Design specifications:**
- Left: `hover:text-primary`
- Separator: `•` (text-gray-300)
- Footer stays down (mt-16)

---

### Phase 4: Contact form backend ⏱️ 1h

**4.1 Create DTO**
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
  - [ ] Validate input
  - [ ] Write to log (MVP: no email sending)
  - [ ] Return Success DTO

**4.4 Implement ContactController**
- [ ] `show()`: return view
- [ ] `submit()`: Validation → UseCase → Redirect with success message

---

### Phase 5: Frontend contact form ⏱️ 30min

**5.1 View** (`resources/views/legal/kontakt.blade.php`)
- [ ] Form with fields:
  - [ ] Name (Input Text)
  - [ ] Email (Input Email)
  - [ ] Message (text area)
  - [ ] Submit button
- [ ] CSRF token (`@csrf`)
- [ ] Show validation errors (`@error`)
- [ ] Show success message (`@if (session('success'))`)

**5.2 Styling**
- [ ] Tailwind Forms Plugin (if not available)
- [ ] Responsive layout
- [ ] Dark mode

---

### Phase 6: License Generator ⏱️ 1h

**6.1 Artisan Command**
- [ ] `app/Console/Commands/GenerateLicenseDataCommand.php`
  - [ ] Signature: `licenses:generate`
  - [ ] Method: `parseComposerLock()`
  - [ ] Method: `parsePackageLock()`
  - [ ] Output: `storage/app/licenses.json`

**6.2 Parser logic**
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

**6.3 LegalController::licenses()**
- [ ] Load license JSON
- [ ] Pass to View (php, node, generated_at)

**6.4 Composer integration**
- [ ] `composer.json` expand under `"scripts"`:
  ```json
  "licenses:generate": "@php artisan licenses:generate"
  ```

**6.5 Makefile integration**
- [ ] Add new target in `Makefile`:
  ```makefile
  licenses: ## Lizenzen neu generieren
      docker exec -it resumehaven-php composer run licenses:generate
  ```

**6.6 Build Integration (Post Update)**
- [ ] `composer.json` expand under `"scripts"` → `"post-update-cmd"`:
  ```json
  "post-update-cmd": [
      "@php artisan vendor:publish --tag=laravel-assets --ansi --force",
      "@php artisan licenses:generate"
  ]
  ```

---

### Phase 7: Tests ⏱️ 45min

**7.1 Create feature tests**

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

**7.2 Unit tests (optional)**
- [ ] `tests/Unit/ContactRequestDtoTest.php`
- [ ] `tests/Unit/GenerateLicenseDataCommandTest.php`

---

### Phase 8: Quality Gates ⏱️ 30min

- [ ] `make test` → All tests green
- [ ] `make phpstan` → 0 Errors (Level 9)
- [ ] `make pint-analyse` → Code style compliant
- [ ] `php artisan licenses:generate` → licenses.json created
- [ ] Manual browser tests:
  - [ ] All legal sites accessible
  - [ ] Footer links work
  - [ ] Contact form validated
  - [ ] Success message is displayed
  - [ ] Responsive Design (Mobile + Desktop)
  - [ ] Dark mode works

---

## 🚀 Implementation order (step-by-step)

### Step 1: Static Pages (Quick Win)
1. Define routes (legal notice, data protection)
2. Create LegalController (2 methods)
3. Create views (with placeholders)
4. Add footer links
5. Write + run tests

**Checkpoint:** Imprint + data protection accessible, footer links work

---

### Step 2: Contact form
1. ContactController + ContactRequest
2. contact.blade.php (form)
3. Validation + success message
4. Write + run tests

**Checkpoint:** Contact form works, validates correctly

---

### Step 3: License generator
1. GenerateLicenseDataCommand
2. parseComposerLock() + parsePackageLock()
3. licenses.blade.php
4. Run command + test
5. Write + run tests

**Checkpoint:** Licenses are generated automatically

---

### Step 4: Final polish
1. PHPStan + pint
2. Manual browser testing
3. Update documentation
4. Create commit

**Checkpoint:** All quality gates green ✅

---

## 📦 Expected file structure after implementation

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

**In total:**
- **New:** 15 files
- **Change:** 2 files
- **Generated:** 1 file

---

## ⚠️ Important Notes

### MVP limitations
- **No email sending:** Contact form only logs (later with email queue)
- **Placeholder content:** Imprint/data protection must be adjusted before productive operation
- **No rate limiting:** Coming later with Redis
- **No admin UI:** Contact messages only visible in the log

### GDPR relevance
- ✅ Data protection declaration available (sample)
- ✅ Contact form with earmarked purpose
- ⚠️ Cookie banner is still missing (comes with analytics)
- ⚠️ Opt-in for newsletter is missing (not relevant to MVP)

### Architectural compromises
- **LegalController:** Named Methods instead of Single-Action (pragmatism for static pages)
- **ContactController:** Two methods (show/submit) instead of separate controllers
- **Reason:** Single-action principle primarily for complex business logic, not for simple CRUD/views

---

## 📊 Success criteria

### Functional
- [x] All 4 legal pages accessible (200 OK)
- [x] Footer links visible on all pages
- [x] Contact form validated on the server side
- [x] Success/Error messages are displayed
- [x] Licenses generated automatically

### Technically
- [x] Tests: 100% green (plague)
- [x] PHPStan: Level 9, 0 errors
- [x] Pint: Code style compliant
- [x] CSRF protection active
- [x] Responsive + Dark Mode

###Quality
- [x] Code follows SOLID principles
- [x] DTOs immutable
- [x] Input validation strict
- [x] Security Best Practices

---

## 🎉 Done!

After completing Commit 20b, ResumeHaven is legally MVP-ready and can go live with Commit 20 (Responsive Design) and 20a (Dark Mode)! 🚀