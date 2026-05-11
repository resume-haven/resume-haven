# Commit 21a – Dark mode support

**Status:** ✅ Completed (2026-03-09)

**Purpose:** Full dark mode support with system preference detection, toggle button and persistent user preference.

---

## ✅ What was implemented

### 1. Tailwind Dark Mode Configuration ✅
- `darkMode: 'class'` activated in `tailwind.config.js`
- Class-based dark mode (not media query)
- Allows manual toggle via JavaScript

### 2. Dark Mode JavaScript Manager ✅
- **Inline script in layout:** `resources/views/layouts/app.blade.php`
- `window.DarkModeManager` as a global object
- Features:
  - System preference detection (`prefers-color-scheme`)
  - LocalStorage persistence (`darkMode` key)
  - Toggle function (`DarkModeManager.toggle()`)
  - Initialization before page render (no flickering)
  - Minimal, focused implementation (around 50 lines)

### 3. Layout integration ✅
- Dark mode toggle button in the header
- Sun Icon (Light Mode) 🌞
- Moon Icon (Dark Mode) 🌙
- Aria labels for accessibility
- Responsive Design (works with Mobile Menu)

### 4. Dark mode CSS for all components ✅
- HTML element: `dark:bg-neutral-dark dark:text-text-dark`
- Header: `dark:bg-neutral-dark dark:border-gray-700`
- Footer: `dark:bg-neutral-dark dark:border-gray-700`
- Navigation: `dark:text-gray-400`
- Buttons: `dark:hover:bg-gray-800`
- All existing views are already dark mode ready

### 5. Feature testing ✅
- `tests/Feature/DarkModeTest.php` (10 tests)
- Toggle button available
- Icons available
- Dark mode classes in HTML/Header/Footer
- Tailwind Config checks `darkMode: 'class'`
- All standard pages tested

### 6. Quality gates ✅
- **Tests:** 194 passed (1499 assertions) ✅
- **PHPStan:** Level 9, 0 Errors ✅
- **Pint:** Code style compliant ✅
- **Assets:** Newly built with Tailwind dark mode support ✅

---

## 🎯 How it works

### System preference as default
On the first visit, the browser/OS preference is recognized:
```javascript
window.matchMedia('(prefers-color-scheme: dark)').matches
```

### Manual toggle
User can switch dark mode on/off manually:
```javascript
DarkModeManager.toggle()
```

### Persistence
User preference is stored in `localStorage`:
```javascript
localStorage.setItem('darkMode', 'true' | 'false')
```

### HTML class
Dark mode is activated via CSS class:
```html
<html class="dark">
```

Tailwind recognizes the class and activates all `dark:` variants.

---

## 📐 Implemented files

### JavaScript
- **Inline script in `resources/views/layouts/app.blade.php`**
  - DarkModeManager as a global `window.DarkModeManager` object
  - Initialization in `<head>` before page render
  - No separate JS module required (minimal implementation)

### Tailwind Config
- **`tailwind.config.js`** (updated)
  - `darkMode: 'class'` activated

###Layout
- **`resources/views/layouts/app.blade.php`** (updated)
  - Inline dark mode script in `<head>`
  - Dark mode toggle button in the header
  - Sun/Moon icons
  - Dark mode CSS classes for HTML/Body/Header/Footer

### Tests
- **`tests/Feature/DarkModeTest.php`** (new)
  - 10 feature tests for dark mode functionality

---

## 🚀 Usage

### For developers

**Rebuild assets:**
```bash
npm run build
# oder im Watch-Mode:
npm run dev
```

**Run tests:**
```bash
make test-feature
# oder spezifisch:
vendor/bin/pest tests/Feature/DarkModeTest.php
```

### For users

**Use toggle button:**
- Click on the Sun/Moon icon in the header
- Preference is saved automatically
- Saved preference will be applied on next visit

**Respect system preference:**
- If user did not select manually
- Automatically uses OS/browser setting
- System preference updates are detected live

---

## 🧪 Test coverage

### Feature testing (10 tests)
1. ✅ Dark mode toggle button present in the header
2. ✅ Sun icon visible for light mode
3. ✅ Moon icon visible for dark mode
4. ✅ Dark mode classes on HTML elements
5. ✅ Dark mode JavaScript loaded (app.css)
6. ✅ Header has dark mode support
7. ✅ Footer has dark mode support
8. ✅ Tailwind darkMode Config activated
9. ✅ All standard sites have dark mode support
10. ✅ Mobile Menu Button has dark mode support

---

## 📊 Metrics

- **New files:** 1 (DarkModeTest.php)
- **Changed files:** 2 (tailwind.config.js, app.blade.php)
- **New Tests:** 10
- **Lines of JavaScript code:** ~50 (inline script)
- **Lines of Code Tests:** ~127
- **Implementation approach:** Inline script instead of separate module (easier, faster, no build problems)

---

## 🔄 Next steps

**Commit 21a is complete!**

### Commit 22: Resume storage (planned)
- Anonymous CV storage
- Retrieve by unique tokens
- Privacy by design

### Commit 23+: CI/CD & Deployment
- GitHub Actions
- Production deployment (IONOS)

---

## 🐛 Known limitations

### MVP scope
- ✅ No JavaScript frameworks required (Vanilla JS)
- ✅ No additional dependencies
- ✅ Browser Compatibility: Modern Browsers (ES6+)
- ✅ No server-side dark mode detection (only client-side)

### Future Expansions (Post-MVP)
- Save dark mode preference in user account (if accounts implemented)
- Dark mode preview in Settings
- Automatic switching based on time of day

---

## 🔧 Troubleshooting

### Problem: "DarkModeManager is not defined" error

**Cause:** Toggle button uses `onclick="DarkModeManager.toggle()"`, but global object is missing.

**Solution (implemented):**
- Inline script in `<head>` defines `window.DarkModeManager` globally
- Script is executed before page render
- No separate JS module required (easier implementation)

**Code snippet:**
```javascript
window.DarkModeManager = {
    toggle() {
        const next = !document.documentElement.classList.contains('dark');
        // ... toggle logic
    }
};
```

**Status:** ✅ Fixed (2026-03-10)

---

## 📚 References

- **Tailwind Dark Mode Docs:** https://tailwindcss.com/docs/dark-mode
- **MDN prefers-color-scheme:** https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-color-scheme
- **WCAG Accessibility:** https://www.w3.org/WAI/WCAG21/Understanding/use-of-color.html

---

**Last updated:** 2026-03-10
**Version:** 1.1 (Commit 21a completed, DarkModeManager fix documented)