<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin' }} · {{ config('app.name', 'ResumeHaven') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="flex min-h-screen">
            <aside class="hidden w-64 flex-col border-r border-slate-200 bg-white/80 px-6 py-8 backdrop-blur md:flex">
                <div class="space-y-1">
                    <div class="text-xl font-semibold">ResumeHaven</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Admin Hub</div>
                </div>

                <nav class="mt-10 space-y-2 text-sm">
                    <a class="flex items-center justify-between rounded-xl border px-4 py-2 transition {{ request()->routeIs('admin.dashboard') ? 'border-slate-900 bg-slate-900 text-white' : 'border-transparent hover:border-slate-200 hover:bg-slate-100' }}" href="{{ route('admin.dashboard') }}">
                        Dashboard
                    </a>
                    <a class="flex items-center justify-between rounded-xl border px-4 py-2 transition {{ request()->routeIs('admin.resumes.*') ? 'border-slate-900 bg-slate-900 text-white' : 'border-transparent hover:border-slate-200 hover:bg-slate-100' }}" href="{{ route('admin.resumes.index') }}">
                        Resumes
                    </a>
                    <a class="flex items-center justify-between rounded-xl border px-4 py-2 transition {{ request()->routeIs('admin.users.*') ? 'border-slate-900 bg-slate-900 text-white' : 'border-transparent hover:border-slate-200 hover:bg-slate-100' }}" href="{{ route('admin.users.index') }}">
                        Users
                    </a>
                </nav>

                <div class="mt-auto pt-10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    Environment
                </div>
                <div class="mt-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600">
                    {{ app()->environment() }}
                </div>
            </aside>

            <div class="flex min-h-screen w-full flex-col">
                <header class="flex items-center justify-between border-b border-slate-200 bg-white/80 px-6 py-4 md:hidden">
                    <div class="text-sm font-semibold">ResumeHaven · Admin</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ app()->environment() }}</div>
                </header>

                <main class="flex-1 px-6 py-8 md:px-10">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
