<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login · {{ config('app.name', 'ResumeHaven') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/admin.css'])
        @endif
    </head>
    <body class="admin">
        <main class="admin-main">
            <section class="panel" style="max-width: 480px; margin: 6rem auto;">
                <div class="top-bar">
                    <div>
                        <h1>Admin Login</h1>
                        <p class="badge">ResumeHaven</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="badge" style="background: #1f2937;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="card">
                    @csrf
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}">

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>

                    <div style="margin-top: 1rem;">
                        <button class="link-chip" type="submit">Sign in</button>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>
