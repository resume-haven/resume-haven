<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin' }} · {{ config('app.name', 'ResumeHaven') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&family=fraunces:400,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/admin.css'])
        @endif
    </head>
    <body class="admin">
        <div class="admin-shell">
            <aside class="admin-nav">
                <div class="brand">
                    ResumeHaven
                    <span>Admin Hub</span>
                </div>

                <div class="nav-section">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.resumes.*') ? 'active' : '' }}" href="{{ route('admin.resumes.index') }}">
                        Resumes
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        Users
                    </a>
                </div>

                <div>
                    <div class="nav-meta">Environment</div>
                    <div class="nav-link">{{ app()->environment() }}</div>
                </div>
            </aside>

            <main class="admin-main">
                @yield('content')
            </main>
        </div>
    </body>
</html>
