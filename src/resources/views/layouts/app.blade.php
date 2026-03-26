<!DOCTYPE html>
<html lang="de" class="bg-neutral-light text-text-light dark:bg-neutral-dark dark:text-text-dark" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ResumeHaven')</title>

    <link rel="stylesheet" href="/build/app.css">

    <script>
        // Dark-Mode Manager global bereitstellen (wird vom Header-Button genutzt)
        (function () {
            const storageKey = 'darkMode';

            function applyDarkClass(enabled) {
                const root = document.documentElement;
                if (enabled) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            }

            function systemPrefersDark() {
                return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            function initialState() {
                const stored = localStorage.getItem(storageKey);
                if (stored === 'true') {
                    return true;
                }
                if (stored === 'false') {
                    return false;
                }

                return systemPrefersDark();
            }

            window.DarkModeManager = {
                toggle() {
                    const next = !document.documentElement.classList.contains('dark');
                    applyDarkClass(next);
                    localStorage.setItem(storageKey, next ? 'true' : 'false');
                },
            };

            // Vor dem Rendern anwenden, um Flackern zu vermeiden
            applyDarkClass(initialState());
        })();
    </script>

    <!-- Alpine.js for Mobile Menu Toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-neutral-light dark:bg-neutral-dark text-text-light dark:text-text-dark">

    <!-- Header -->
    <header class="bg-white dark:bg-neutral-dark border-b dark:border-gray-700">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                 <a href="/" class="flex items-center gap-2 sm:gap-3">
                     <x-atoms.logo />
                     <x-atoms.brandname />
                 </a>

                <!-- Desktop Navigation (ab md) -->
                <nav class="hidden md:flex gap-6 text-gray-700 dark:text-text-dark">
                     <x-atoms.link href="/" class="hover:text-primary transition">Home</x-atoms.link>
                     <x-atoms.link href="/analyze" class="hover:text-primary transition">Analyse</x-atoms.link>
                     <x-atoms.link href="/about" class="hover:text-primary transition">About</x-atoms.link>
                </nav>

                <!-- Dark Mode Toggle + Mobile Menu Button -->
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                     <x-atoms.darkmode-button />

                    <!-- Mobile Menu Button (< md) -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        aria-label="Toggle Menu"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            <!-- Mobile Menu (toggleable) -->
            <nav
                x-show="mobileMenuOpen"
                x-cloak
                class="md:hidden pb-4 pt-2 space-y-2"
            >
                 <x-atoms.link href="/" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">Home</x-atoms.link>
                 <x-atoms.link href="/analyze" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">Analyse</x-atoms.link>
                 <x-atoms.link href="/about" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">About</x-atoms.link>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @yield('content')
    </main>

    <!-- Footer (Molecule) -->
    <x-molecules.footer-bar />

    <!-- Alpine.js x-cloak Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

</body>
</html>
