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
                    <!-- Light Mode Logo -->
                    <img src="/assets/logo-light.svg"
                        alt="ResumeHaven Logo"
                        class="h-8 sm:h-10 block dark:hidden">

                    <!-- Dark Mode Logo -->
                    <img src="/assets/logo-dark.svg"
                        alt="ResumeHaven Logo"
                        class="h-8 sm:h-10 hidden dark:block">

                    <span class="text-xl sm:text-2xl font-bold tracking-tight text-primary dark:text-white">
                        ResumeHaven
                    </span>
                </a>

                <!-- Desktop Navigation (ab md) -->
                <nav class="hidden md:flex gap-6 text-gray-700 dark:text-text-dark">
                    <a href="/" class="hover:text-primary transition">Home</a>
                    <a href="/analyze" class="hover:text-primary transition">Analyse</a>
                    <a href="/about" class="hover:text-primary transition">About</a>
                </nav>

                <!-- Dark Mode Toggle + Mobile Menu Button -->
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                    <button
                        onclick="DarkModeManager.toggle()"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        aria-label="Toggle Dark Mode"
                        title="Dark Mode Toggle"
                    >
                        <!-- Sun Icon (Light Mode) -->
                        <svg class="w-6 h-6 text-yellow-500 block dark:hidden" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.591a.75.75 0 101.06 1.06l1.591-1.591zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.591-1.591a.75.75 0 10-1.06 1.06l1.591 1.591zM12 18.75a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V19.5a.75.75 0 01.75-.75zM7.591 17.409a.75.75 0 10-1.06 1.06l1.591 1.591a.75.75 0 001.06-1.06l-1.591-1.591zM3.75 12a.75.75 0 01-.75-.75V8.25a.75.75 0 011.5 0v2.25a.75.75 0 01-.75.75zM6.166 6.166a.75.75 0 00-1.06 1.06l1.591 1.591a.75.75 0 001.06-1.06L6.166 6.166z" />
                        </svg>

                        <!-- Moon Icon (Dark Mode) -->
                        <svg class="w-6 h-6 text-blue-400 hidden dark:block" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.752 15.002A9.718 9.718 0 1812.146 3.1M15.08 16.637A6.75 6.75 0 1110.363 9.92" />
                        </svg>
                    </button>

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
                <a href="/" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">Home</a>
                <a href="/analyze" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">Analyse</a>
                <a href="/about" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">About</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-neutral-dark border-t dark:border-gray-700 mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Legal Links: Stack vertikal < sm, horizontal > sm -->
            <nav class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-4 justify-center sm:justify-start text-sm text-gray-600 dark:text-gray-400 mb-4">
                <a href="{{ route('legal.impressum') }}" class="hover:text-primary transition">Impressum</a>
                <span class="hidden sm:inline text-gray-300 dark:text-gray-600">•</span>
                <a href="{{ route('legal.datenschutz') }}" class="hover:text-primary transition">Datenschutz</a>
                <span class="hidden sm:inline text-gray-300 dark:text-gray-600">•</span>
                <a href="{{ route('contact.show') }}" class="hover:text-primary transition">Kontakt</a>
                <span class="hidden sm:inline text-gray-300 dark:text-gray-600">•</span>
                <a href="{{ route('legal.lizenzen') }}" class="hover:text-primary transition">Lizenzen</a>
            </nav>

            <!-- Copyright: Centered Mobile, Left Desktop -->
            <div class="text-sm text-center sm:text-left text-gray-500 dark:text-gray-500">
                © {{ date('Y') }} ResumeHaven — Bewerbungsanalyse leicht gemacht.
            </div>
        </div>
    </footer>

    <!-- Alpine.js x-cloak Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

</body>
</html>
