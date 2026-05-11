# Commit 20b - Implementation Guide

**Status:** ✅ Completed (2026-03-09)

**Quick start for the implementation of legal pages**

---

## ✅ Implementation result

**Commit 20b was completed successfully!**

### What was implemented:

#### Phase 1: Routes & Controllers ✅
- `LegalController` with named methods (imprint, data protection, licenses)
- `ContactController` with show/submit methods
- Routes defined in `routes/web.php`

#### Phase 2: Views ✅
- `resources/views/legal/impressum.blade.php`
- `resources/views/legal/datenschutz.blade.php`
- `resources/views/legal/kontakt.blade.php`
- `resources/views/legal/lizenzen.blade.php`
- All views with TailwindCSS + dark mode support

#### Phase 3: Footer navigation ✅
- Footer expanded in `resources/views/layouts/app.blade.php`
- Legal links (imprint, data protection, contact, licenses)
- Responsive design (stack vertical < 768px)

#### Phase 4: Contact form ✅
- `ContactRequest` Form Request with validation
- `ContactRequestDto` for Type-Safe Data Transfer
- CSRF protection active
- Success/Error messages in the frontend

#### Phase 5: License Generator ✅
- `GenerateLicenseDataCommand` implemented
- Parses `composer.lock` and `package-lock.json`
- Saves in `storage/app/licenses.json`
- Composer script: `composer run licenses:generate`
 - Makefile target: `make licenses-generate` ✅
- **Status:** Generated successfully (78 PHP packages, 203 node packages)

#### Phase 6: Tests ✅
- `tests/Feature/LegalPagesTest.php` (4 tests)
- `tests/Feature/ContactFormTest.php` (5 tests)
- `tests/Feature/FooterNavigationTest.php` (1 test)
- `tests/Feature/LicensesPageTest.php` (2 tests)
- All tests green ✅

#### Quality Gates ✅
- PHPStan Level 9: 0 Errors ✅
- Pint: Code style compliant ✅
- Test coverage: 98.2% ✅

---

## 📚 Useful references (if adjustments are needed)

## 🚀 Start of implementation

### Option 1: I (copilot) carry out the implementation

**If you want me to do the implementation:**

1. Just say: **"Start with commit 20b implementation"**
2. I then systematically work through the phases:
   - Phase 1: Routes + Controllers
   - Phase 2: Views
   - Phase 3: Footers
   - Phase 4-5: Contact form
   - Phase 6: Licenses
   - Phase 7: Testing
   - Phase 8: Quality Gates

3. After each step I run tests and show you the status

---

### Option 2: You implement yourself (with my support)

**If you want to implement it yourself:**

Follow the **step-by-step order** in `docs/history/PLANNING_COMMIT_20b.md`:

#### Step 1: Static Pages (Quick Win - ~30min)

```bash
# 1. Branch erstellen
git checkout -b feature/commit-20b-legal-pages

# 2. Controller erstellen
php artisan make:controller LegalController

# 3. Views-Verzeichnis erstellen
mkdir -p resources/views/legal

# 4. Routes definieren (siehe PLANNING_COMMIT_20b.md)
# 5. Views erstellen (siehe Vorlagen unten)
# 6. Footer erweitern (siehe app.blade.php-Update)

# 7. Tests ausführen
make test-feature
```

**Then ask myself:** “Is Step 1 correct?” → I review your code

---

#### Step 2: Contact form (~1h)

```bash
# 1. Form Request erstellen
php artisan make:request ContactRequest

# 2. DTO erstellen
# (siehe PLANNING_COMMIT_20b.md)

# 3. Controller erstellen
php artisan make:controller ContactController

# 4. View + Validation-UI
# (siehe kontakt.blade.php-Vorlage)

# 5. Tests schreiben
# (siehe ContactFormTest.php-Vorlage)

# 6. Tests ausführen
make test-feature
```

**Then ask myself:** “Review contact form” → I check security + validation

---

#### Step 3: License generator (~1h)

```bash
# 1. Command erstellen
php artisan make:command GenerateLicenseData

# 2. Parser-Logik implementieren
# (siehe PLANNING_COMMIT_20b.md)

# 3. Composer-Script hinzufügen
# Füge in composer.json unter "scripts" hinzu:
# "licenses:generate": "@php artisan licenses:generate"

# 4. Make-Target hinzufügen
# Füge in Makefile hinzu:
# licenses: ## Lizenzen neu generieren
#     docker exec -it resumehaven-php composer run licenses:generate

# 5. Command ausführen
make licenses
# oder direkt:
php artisan licenses:generate

# 6. View erstellen (lizenzen.blade.php)

# 7. Tests schreiben

# 8. Tests ausführen
make test
```

**Then ask myself:** "Review License Generator" → I check parsing logic

---

#### Step 4: Final Polish (~30min)

```bash
# Quality Gates
make test
make phpstan
make pint-analyse

# Manuelle Browser-Tests
npm run dev
# Öffne: http://localhost:8080/impressum

# Commit erstellen
git add .
git commit -m "feat: Implement legal pages (Commit 20b)

- Add Impressum, Datenschutz, Kontakt, Lizenzen pages
- Extend footer navigation
- Implement contact form with validation
- Add automated license generator
- Add comprehensive test coverage

Closes #20b"
```

---

## 📋 Code templates

### composer.json (extend scripts)

```json
{
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force",
            "@php artisan licenses:generate"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "licenses:generate": "@php artisan licenses:generate"
    }
}
```

### Makefile (add target)

```makefile
# --- LICENSES ---
licenses: ## Lizenzen neu generieren
	docker exec -it resumehaven-php composer run licenses:generate

licenses-local: ## Lizenzen lokal generieren (ohne Docker)
	php artisan licenses:generate
```

---

## 📋 Code templates

### LegalController.php (Basic)

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class LegalController extends Controller
{
    public function impressum(): View
    {
        return view('legal.impressum');
    }

    public function datenschutz(): View
    {
        return view('legal.datenschutz');
    }

    public function lizenzen(): View
    {
        $licenses = [];
        
        if (Storage::exists('licenses.json')) {
            $data = json_decode(Storage::get('licenses.json'), true);
            $licenses = [
                'php' => $data['php'] ?? [],
                'node' => $data['node'] ?? [],
                'generated_at' => $data['generated_at'] ?? null,
            ];
        }

        return view('legal.lizenzen', $licenses);
    }
}
```

### Routes (web.php)

```php
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LegalController;

// Legal Pages
Route::get('/impressum', [LegalController::class, 'impressum'])->name('legal.impressum');
Route::get('/datenschutz', [LegalController::class, 'datenschutz'])->name('legal.datenschutz');
Route::get('/lizenzen', [LegalController::class, 'lizenzen'])->name('legal.lizenzen');

// Contact
Route::get('/kontakt', [ContactController::class, 'show'])->name('contact.show');
Route::post('/kontakt', [ContactController::class, 'submit'])->name('contact.submit');
```

### View template (imprint.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Impressum')

@section('content')
    <div class="prose dark:prose-invert max-w-3xl mx-auto">
        <h1>Impressum</h1>
        
        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800 dark:text-yellow-200 font-semibold">
                ⚠️ Hinweis: Dies ist ein Muster-Impressum für MVP-Zwecke.
                Vor Produktivbetrieb müssen die Daten angepasst werden!
            </p>
        </div>

        <h2>Angaben gemäß § 5 TMG</h2>
        <p>
            [Muster-Firma]<br>
            [Muster-Straße 1]<br>
            [12345 Muster-Stadt]
        </p>

        <h2>Kontakt</h2>
        <p>
            E-Mail: <a href="mailto:info@example.com">info@example.com</a><br>
            Telefon: [Muster-Telefon]
        </p>

        <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
        <p>
            [Muster-Name]<br>
            [Muster-Adresse]
        </p>
    </div>
@endsection
```

### Footer update (layouts/app.blade.php)

```blade
<!-- Footer -->
<footer class="bg-white dark:bg-neutral-dark border-t mt-16">
    <div class="max-w-5xl mx-auto px-6 py-6">
        <!-- Legal Links -->
        <nav class="flex flex-wrap gap-4 justify-center sm:justify-start text-sm text-gray-600 dark:text-gray-400 mb-4">
            <a href="{{ route('legal.impressum') }}" class="hover:text-primary transition">Impressum</a>
            <span class="text-gray-300 dark:text-gray-600">•</span>
            <a href="{{ route('legal.datenschutz') }}" class="hover:text-primary transition">Datenschutz</a>
            <span class="text-gray-300 dark:text-gray-600">•</span>
            <a href="{{ route('contact.show') }}" class="hover:text-primary transition">Kontakt</a>
            <span class="text-gray-300 dark:text-gray-600">•</span>
            <a href="{{ route('legal.lizenzen') }}" class="hover:text-primary transition">Lizenzen</a>
        </nav>
        
        <!-- Copyright -->
        <div class="text-sm text-center sm:text-left text-gray-500 dark:text-gray-500">
            © {{ date('Y') }} ResumeHaven — Bewerbungsanalyse leicht gemacht.
        </div>
    </div>
</footer>
```

---

## 📊 Next steps after commit 20b

**Commit 20b is complete!** The following commits are planned:

### Commit 21: Responsive Layout & Mobile-First
- Mobile-optimized layouts for all pages
- Touch optimized interactions
- Responsive breakpoints (sm, md, lg, xl)
-Progressive Enhancement

### Commit 21a: Dark mode support
- System preference detection
- Toggle button for manual switching
- Persistent user preference (LocalStorage)
- Dark mode for all components

### Commit 22: Resume storage
- Anonymous CV storage
- Retrieve by unique tokens
- Privacy by Design (no user account required)

---

## 📚 More documentation

- **Detailed planning:** `docs/history/PLANNING_COMMIT_20b.md`
- **Commit plan:** `COMMIT_PLAN.md`
- **Architecture:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Soft reset baseline:** `docs/ai/WORKING_BASELINE.md`

---

## 🔄 Context reset after commit 20b

**If context is lost, start with:**

1. Read `docs/ai/WORKING_BASELINE.md` (soft reset entry)
2. Check `COMMIT_PLAN.md` for status overview
3. Check `docs/history/COMMIT_20b_IMPLEMENTATION_GUIDE.md` (this file) for details
4. Repository status is source of truth

**Current status:**
- ✅ Commit 20b completed (legal sites & trust)
- 🔄 Commit 21 (Responsive Layout) planned next
- Tests: All green ✅
- PHPStan: Level 9, 0 Errors ✅
- Coverage: 98.2% ✅

---

**Last updated:** 2026-03-09
**Version:** 2.0 (commit 20b completed, ready for commit 21)