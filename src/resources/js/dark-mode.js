/**
 * Dark Mode Manager
 *
 * Verwaltet Dark-Mode-Toggle mit:
 * - LocalStorage-Persistierung
 * - System-Präferenz-Detection
 * - HTML-Klassen-Anwendung
 */

export const DarkModeManager = {
    /**
     * Initalisiert Dark-Mode Manager
     */
    init() {
        this.applyStoredPreference();
        this.setupToggleListener();
        this.watchSystemPreference();
    },

    /**
     * Angewandte Einstellung aus localStorage
     */
    applyStoredPreference() {
        const html = document.documentElement;
        const stored = localStorage.getItem('darkMode');

        if (stored !== null) {
            // Benutzer hat explizit gewählt
            if (stored === 'true') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        } else {
            // Kein Benutzer-Setting → System-Präferenz verwenden
            this.applySystemPreference();
        }
    },

    /**
     * System-Präferenz anwenden (Fallback)
     */
    applySystemPreference() {
        const html = document.documentElement;
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (prefersDark) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    },

    /**
     * Beobachtet System-Präferenz-Änderungen
     */
    watchSystemPreference() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Nur anwenden wenn Benutzer nicht explizit gewählt hat
            if (localStorage.getItem('darkMode') === null) {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    },

    /**
     * Toggle Dark Mode an/aus
     */
    toggle() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        } else {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        }

        // Dispatch event für UI-Updates
        window.dispatchEvent(new CustomEvent('darkModeToggle', {
            detail: { isDark: !isDark }
        }));
    },

    /**
     * Gibt aktuellen Dark-Mode-Status zurück
     */
    isDark() {
        return document.documentElement.classList.contains('dark');
    },

    /**
     * Setzt Dark Mode auf einen bestimmten Wert
     */
    set(isDark) {
        const html = document.documentElement;

        if (isDark) {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        }

        window.dispatchEvent(new CustomEvent('darkModeToggle', {
            detail: { isDark }
        }));
    },

    /**
     * Entfernt Benutzer-Präferenz (fallback zu System-Präferenz)
     */
    reset() {
        localStorage.removeItem('darkMode');
        this.applySystemPreference();
    }
};

// Auto-Init wenn DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => DarkModeManager.init());
} else {
    DarkModeManager.init();
}

