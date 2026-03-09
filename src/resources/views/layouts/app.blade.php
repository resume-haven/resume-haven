<!DOCTYPE html>
<html lang="de" class="bg-neutral-light text-text-light" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ResumeHaven')</title>

    <link rel="stylesheet" href="/build/app.css">

    <!-- Alpine.js for Mobile Menu Toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased">

    <!-- Header -->
    <header class="bg-white border-b">
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
    <footer class="bg-white border-t mt-16">
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
