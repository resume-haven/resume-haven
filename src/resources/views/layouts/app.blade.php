<!DOCTYPE html>
<html lang="de" class="bg-neutral-light text-text-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ResumeHaven')</title>

    <link rel="stylesheet" href="/build/app.css">
</head>
<body class="antialiased">

    <!-- Header -->
    <header class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold tracking-tight text-primary">
                ResumeHaven
            </a>

            <nav class="flex gap-6 text-gray-700 dark:text-text-dark">
                <a href="/" class="hover:text-primary transition">Home</a>
                <a href="/analyze" class="hover:text-primary transition">Analyse</a>
                <a href="/about" class="hover:text-primary transition">About</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-6 py-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-5xl mx-auto px-6 py-6 text-sm text-gray-500">
            © {{ date('Y') }} ResumeHaven — Bewerbungsanalyse leicht gemacht.
        </div>
    </footer>

</body>
</html>
