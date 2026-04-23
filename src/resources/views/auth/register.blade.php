@extends('layouts.app')

@section('title', 'Registrieren')

@section('content')
    <div class="max-w-md mx-auto bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Registrieren</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-1">Name</label>
                <x-atoms.input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full" />
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">E-Mail</label>
                <x-atoms.input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Passwort</label>
                <x-atoms.input id="password" type="password" name="password" required autocomplete="new-password" class="w-full" />
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1">Passwort wiederholen</label>
                <x-atoms.input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full" />
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-4 pt-2">
                <a href="{{ route('login') }}" class="text-sm underline hover:text-primary">Bereits registriert?</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Registrieren</button>
            </div>
        </form>
    </div>
@endsection
