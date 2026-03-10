# Commit 21a – Dark-Mode Support

**Status:** ✅ Abgeschlossen (2026-03-09)

**Zweck:** Vollständige Dark-Mode-Unterstützung mit System-Präferenz-Detection, Toggle-Button und persistenter User-Präferenz.

---

## ✅ Was wurde umgesetzt

### 1. Tailwind Dark-Mode Konfiguration ✅
- `darkMode: 'class'` in `tailwind.config.js` aktiviert
- Class-based Dark-Mode (nicht media-query)
- Erlaubt manuellen Toggle per JavaScript

### 2. Dark-Mode JavaScript Manager ✅
- **Inline-Script im Layout:** `resources/views/layouts/app.blade.php`
- `window.DarkModeManager` als globales Objekt
- Features:
  - System-Präferenz-Detection (`prefers-color-scheme`)
  - LocalStorage-Persistierung (`darkMode` key)
  - Toggle-Funktion (`DarkModeManager.toggle()`)
  - Initialisierung vor Page-Render (kein Flackern)
  - Minimale, fokussierte Implementierung (ca. 50 Zeilen)

### 3. Layout-Integration ✅
- Dark-Mode Toggle Button im Header
- Sun Icon (Light Mode) 🌞
- Moon Icon (Dark Mode) 🌙
- Aria-Labels für Accessibility
- Responsive Design (funktioniert mit Mobile Menu)

### 4. Dark-Mode CSS für alle Komponenten ✅
- HTML Element: `dark:bg-neutral-dark dark:text-text-dark`
- Header: `dark:bg-neutral-dark dark:border-gray-700`
- Footer: `dark:bg-neutral-dark dark:border-gray-700`
- Navigation: `dark:text-gray-400`
- Buttons: `dark:hover:bg-gray-800`
- Alle bestehenden Views bereits dark-mode-ready

### 5. Feature-Tests ✅
- `tests/Feature/DarkModeTest.php` (10 Tests)
- Toggle-Button vorhanden
- Icons vorhanden
- Dark-Mode-Klassen in HTML/Header/Footer
- Tailwind Config prüft `darkMode: 'class'`
- Alle Standard-Seiten getestet

### 6. Quality-Gates ✅
- **Tests:** 194 passed (1499 assertions) ✅
- **PHPStan:** Level 9, 0 Errors ✅
- **Pint:** Code-Style konform ✅
- **Assets:** Neu gebaut mit Tailwind Dark-Mode Support ✅

---

## 🎯 Funktionsweise

### System-Präferenz als Default
Beim ersten Besuch wird die Browser-/OS-Präferenz erkannt:
```javascript
window.matchMedia('(prefers-color-scheme: dark)').matches
```

### Manueller Toggle
User kann Dark-Mode manuell ein-/ausschalten:
```javascript
DarkModeManager.toggle()
```

### Persistierung
User-Präferenz wird in `localStorage` gespeichert:
```javascript
localStorage.setItem('darkMode', 'true' | 'false')
```

### HTML-Klasse
Dark-Mode wird über CSS-Klasse aktiviert:
```html
<html class="dark">
```

Tailwind erkennt die Klasse und aktiviert alle `dark:` Varianten.

---

## 📐 Implementierte Dateien

### JavaScript
- **Inline-Script in `resources/views/layouts/app.blade.php`**
  - DarkModeManager als globales `window.DarkModeManager` Objekt
  - Initialisierung im `<head>` vor Page-Render
  - Kein separates JS-Modul nötig (minimale Implementierung)

### Tailwind Config
- **`tailwind.config.js`** (aktualisiert)
  - `darkMode: 'class'` aktiviert

### Layout
- **`resources/views/layouts/app.blade.php`** (aktualisiert)
  - Inline Dark-Mode-Script im `<head>`
  - Dark-Mode Toggle Button im Header
  - Sun/Moon Icons
  - Dark-Mode CSS-Klassen für HTML/Body/Header/Footer

### Tests
- **`tests/Feature/DarkModeTest.php`** (neu)
  - 10 Feature-Tests für Dark-Mode-Funktionalität

---

## 🚀 Verwendung

### Für Entwickler

**Assets neu bauen:**
```bash
npm run build
# oder im Watch-Mode:
npm run dev
```

**Tests ausführen:**
```bash
make test-feature
# oder spezifisch:
vendor/bin/pest tests/Feature/DarkModeTest.php
```

### Für User

**Toggle-Button verwenden:**
- Klick auf Sun/Moon-Icon im Header
- Präferenz wird automatisch gespeichert
- Beim nächsten Besuch wird gespeicherte Präferenz angewendet

**System-Präferenz respektieren:**
- Wenn User nicht manuell gewählt hat
- Wird automatisch OS-/Browser-Einstellung verwendet
- Updates der System-Präferenz werden live erkannt

---

## 🧪 Test-Abdeckung

### Feature-Tests (10 Tests)
1. ✅ Dark-Mode Toggle Button im Header vorhanden
2. ✅ Sun Icon für Light Mode sichtbar
3. ✅ Moon Icon für Dark Mode sichtbar
4. ✅ Dark-Mode Klassen auf HTML Element
5. ✅ Dark-Mode JavaScript geladen (app.css)
6. ✅ Header hat Dark-Mode Support
7. ✅ Footer hat Dark-Mode Support
8. ✅ Tailwind darkMode Config aktiviert
9. ✅ Alle Standard-Seiten haben Dark-Mode Support
10. ✅ Mobile Menu Button hat Dark-Mode Support

---

## 📊 Metriken

- **Neue Dateien:** 1 (DarkModeTest.php)
- **Geänderte Dateien:** 2 (tailwind.config.js, app.blade.php)
- **Neue Tests:** 10
- **Code-Zeilen JavaScript:** ~50 (Inline-Script)
- **Code-Zeilen Tests:** ~127
- **Implementierungsansatz:** Inline-Script statt separates Modul (einfacher, schneller, keine Build-Probleme)

---

## 🔄 Nächste Schritte

**Commit 21a ist abgeschlossen!**

### Commit 22: Lebenslauf-Speicherung (geplant)
- Anonymous CV-Storage
- Retrieve by unique Token
- Privacy by Design

### Commit 23+: CI/CD & Deployment
- GitHub Actions
- Production-Deployment (IONOS)

---

## 🐛 Bekannte Limitierungen

### MVP-Scope
- ✅ Keine JavaScript-Frameworks nötig (Vanilla JS)
- ✅ Keine zusätzlichen Dependencies
- ✅ Browser-Kompatibilität: Modern Browsers (ES6+)
- ✅ Keine Server-Side Dark-Mode Detection (nur Client-Side)

### Zukünftige Erweiterungen (Post-MVP)
- Dark-Mode-Präferenz in User-Account speichern (wenn Accounts implementiert)
- Dark-Mode-Vorschau in Settings
- Automatischer Wechsel basierend auf Tageszeit

---

## 🔧 Troubleshooting

### Problem: "DarkModeManager is not defined" Fehler

**Ursache:** Toggle-Button nutzt `onclick="DarkModeManager.toggle()"`, aber globales Objekt fehlt.

**Lösung (implementiert):**
- Inline-Script im `<head>` definiert `window.DarkModeManager` global
- Script wird vor Page-Render ausgeführt
- Kein separates JS-Modul nötig (einfachere Implementierung)

**Code-Snippet:**
```javascript
window.DarkModeManager = {
    toggle() {
        const next = !document.documentElement.classList.contains('dark');
        // ... toggle logic
    }
};
```

**Status:** ✅ Behoben (2026-03-10)

---

## 📚 Referenzen

- **Tailwind Dark Mode Docs:** https://tailwindcss.com/docs/dark-mode
- **MDN prefers-color-scheme:** https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-color-scheme
- **WCAG Accessibility:** https://www.w3.org/WAI/WCAG21/Understanding/use-of-color.html

---

**Letzte Aktualisierung:** 2026-03-10  
**Version:** 1.1 (Commit 21a abgeschlossen, DarkModeManager-Fix dokumentiert)






