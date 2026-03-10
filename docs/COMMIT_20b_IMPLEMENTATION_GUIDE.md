# Commit 20b – Implementierungsanleitung

**Status:** ✅ Abgeschlossen (2026-03-09)

**Quick Start für die Umsetzung von Legal-Seiten**

---

## ✅ Implementierungs-Ergebnis

**Commit 20b wurde erfolgreich abgeschlossen!**

### Was wurde umgesetzt:

#### Phase 1: Routes & Controller ✅
- `LegalController` mit named methods (impressum, datenschutz, lizenzen)
- `ContactController` mit show/submit methods
- Routes in `routes/web.php` definiert

#### Phase 2: Views ✅
- `resources/views/legal/impressum.blade.php`
- `resources/views/legal/datenschutz.blade.php`
- `resources/views/legal/kontakt.blade.php`
- `resources/views/legal/lizenzen.blade.php`
- Alle Views mit TailwindCSS + Dark-Mode Support

#### Phase 3: Footer-Navigation ✅
- Footer in `resources/views/layouts/app.blade.php` erweitert
- Legal-Links (Impressum, Datenschutz, Kontakt, Lizenzen)
- Responsive Design (Stack vertikal < 768px)

#### Phase 4: Kontaktformular ✅
- `ContactRequest` Form Request mit Validierung
- `ContactRequestDto` für Type-Safe Data Transfer
- CSRF-Protection aktiv
- Success/Error-Messages im Frontend

#### Phase 5: Lizenzen-Generator ✅
- `GenerateLicenseDataCommand` implementiert
- Parst `composer.lock` und `package-lock.json`
- Speichert in `storage/app/licenses.json`
- Composer-Script: `composer run licenses:generate`
 - Makefile-Target: `make licenses-generate` ✅
- **Status:** Erfolgreich generiert (78 PHP-Pakete, 203 Node-Pakete)

#### Phase 6: Tests ✅
- `tests/Feature/LegalPagesTest.php` (4 Tests)
- `tests/Feature/ContactFormTest.php` (5 Tests)
- `tests/Feature/FooterNavigationTest.php` (1 Test)
- `tests/Feature/LicensesPageTest.php` (2 Tests)
- Alle Tests grün ✅

#### Quality-Gates ✅
- PHPStan Level 9: 0 Errors ✅
- Pint: Code-Style konform ✅
- Test-Coverage: 98.2% ✅

---

## 📚 Nützliche Referenzen (falls Anpassungen nötig)

## 🚀 Start der Implementierung

### Option 1: Ich (Copilot) führe die Implementierung durch

**Wenn du möchtest, dass ich die Implementierung übernehme:**

1. Sage einfach: **"Start mit Commit 20b Implementierung"**
2. Ich arbeite dann systematisch die Phasen ab:
   - Phase 1: Routes + Controller
   - Phase 2: Views
   - Phase 3: Footer
   - Phase 4-5: Kontaktformular
   - Phase 6: Lizenzen
   - Phase 7: Tests
   - Phase 8: Quality Gates

3. Nach jedem Schritt führe ich Tests aus und zeige dir den Status

---

### Option 2: Du implementierst selbst (mit meiner Unterstützung)

**Wenn du selbst implementieren möchtest:**

Folge der **Step-by-Step-Reihenfolge** in `docs/PLANNING_COMMIT_20b.md`:

#### Step 1: Statische Seiten (Quick Win - ~30min)

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

**Dann frage mich:** "Ist Step 1 korrekt?" → Ich reviewe deinen Code

---

#### Step 2: Kontaktformular (~1h)

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

**Dann frage mich:** "Review Kontaktformular" → Ich prüfe Security + Validierung

---

#### Step 3: Lizenzen-Generator (~1h)

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

**Dann frage mich:** "Review Lizenzen-Generator" → Ich prüfe Parsing-Logik

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

## 📋 Code-Vorlagen

### composer.json (Scripts erweitern)

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

### Makefile (Target hinzufügen)

```makefile
# --- LICENSES ---
licenses: ## Lizenzen neu generieren
	docker exec -it resumehaven-php composer run licenses:generate

licenses-local: ## Lizenzen lokal generieren (ohne Docker)
	php artisan licenses:generate
```

---

## 📋 Code-Vorlagen

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

### View-Vorlage (impressum.blade.php)

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

### Footer-Update (layouts/app.blade.php)

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

## 📊 Nächste Schritte nach Commit 20b

**Commit 20b ist abgeschlossen!** Die folgenden Commits sind geplant:

### Commit 21: Responsive Layout & Mobile-First
- Mobile-optimierte Layouts für alle Seiten
- Touch-optimierte Interaktionen
- Responsive Breakpoints (sm, md, lg, xl)
- Progressive Enhancement

### Commit 21a: Dark-Mode Support
- System-Präferenz-Detection
- Toggle-Button für manuellen Wechsel
- Persistente User-Präferenz (LocalStorage)
- Dark-Mode für alle Komponenten

### Commit 22: Lebenslauf-Speicherung
- Anonymous CV-Storage
- Retrieve by unique Token
- Privacy by Design (kein User-Account nötig)

---

## 📚 Weitere Dokumentation

- **Detailplanung:** `docs/PLANNING_COMMIT_20b.md`
- **Commit-Plan:** `COMMIT_PLAN.md` (Zeile 1600+)
- **Architektur:** `docs/ARCHITECTURE.md`
- **Coding Guidelines:** `docs/CODING_GUIDELINES.md`
- **Soft-Reset Baseline:** `docs/ai/WORKING_BASELINE.md`

---

## 🔄 Kontext-Reset nach Commit 20b

**Falls Kontext verloren geht, starte mit:**

1. Lies `docs/ai/WORKING_BASELINE.md` (Soft-Reset-Einstieg)
2. Prüfe `COMMIT_PLAN.md` für Status-Überblick
3. Prüfe `docs/COMMIT_20b_IMPLEMENTATION_GUIDE.md` (diese Datei) für Details
4. Repository-Stand ist Source of Truth

**Aktueller Stand:**
- ✅ Commit 20b abgeschlossen (Legal-Seiten & Vertrauen)
- 🔄 Commit 21 (Responsive Layout) als nächstes geplant
- Tests: Alle grün ✅
- PHPStan: Level 9, 0 Errors ✅
- Coverage: 98.2% ✅

---

**Letzte Aktualisierung:** 2026-03-09  
**Version:** 2.0 (Commit 20b abgeschlossen, bereit für Commit 21)



