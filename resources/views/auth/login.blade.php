<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login · {{ config('app.name', 'ResumeHaven') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <main class="flex min-h-screen items-center justify-center px-6 py-12">
            <section class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold">Admin Login</h1>
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">ResumeHaven</p>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600" for="email">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-600" for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <button class="mt-4 w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                        Sign in
                    </button>
                </form>
            </section>
        </main>
    </body>
</html>
