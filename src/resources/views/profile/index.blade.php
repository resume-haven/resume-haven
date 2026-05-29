@extends('layouts.app')

@section('title', 'Meine Lebensläufe')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-atoms.heading-h1 class="mb-2">Meine Lebensläufe</x-atoms.heading-h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Hier findest du deine gespeicherten Lebensläufe. Du kannst sie erneut laden und für weitere Analysen verwenden.
                </p>
            </div>
            <a href="{{ route('analyze') }}" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 transition">
                Neuen Lebenslauf analysieren
            </a>
        </div>

        <form method="GET" action="{{ route('profile.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Lebenslauf suchen..." class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-dark px-4 py-2 text-sm text-text-light dark:text-text-dark focus:border-primary focus:ring-primary">
            </div>
            <div class="relative">
                <select name="sort" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-dark px-4 py-2 text-sm text-text-light dark:text-text-dark focus:border-primary focus:ring-primary">
                    <option value="updated_at" {{ $sort === 'updated_at' ? 'selected' : '' }}>Datum</option>
                    <option value="file_name" {{ $sort === 'file_name' ? 'selected' : '' }}>Name</option>
                </select>
            </div>
            <div class="relative">
                <select name="direction" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-dark px-4 py-2 text-sm text-text-light dark:text-text-dark focus:border-primary focus:ring-primary">
                    <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Absteigend</option>
                    <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Aufsteigend</option>
                </select>
            </div>
            <div class="relative">
                <select name="per_page" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-dark px-4 py-2 text-sm text-text-light dark:text-text-dark focus:border-primary focus:ring-primary">
                    <option value="10" {{ $pagination['per_page'] == 10 ? 'selected' : '' }}>10 pro Seite</option>
                    <option value="25" {{ $pagination['per_page'] == 25 ? 'selected' : '' }}>25 pro Seite</option>
                    <option value="50" {{ $pagination['per_page'] == 50 ? 'selected' : '' }}>50 pro Seite</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-text-light dark:text-text-dark hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                Suchen
            </button>
            @if ($search)
                <a href="{{ route('profile.index') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                    Zurücksetzen
                </a>
            @endif
        </form>

        @if ($items === [])
            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-dark p-6 text-center">
                @if ($search)
                    <p class="text-base font-semibold text-text-light dark:text-text-dark">Keine Lebensläufe für "{{ $search }}" gefunden.</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Versuche es mit einem anderen Suchbegriff oder setze die Suche zurück.</p>
                    <div class="mt-4">
                        <a href="{{ route('profile.index') }}" class="text-sm font-semibold text-primary hover:underline">Suche zurücksetzen</a>
                    </div>
                @else
                    <p class="text-base font-semibold text-text-light dark:text-text-dark">Noch keine gespeicherten Lebensläufe vorhanden.</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Speichere zuerst einen Lebenslauf über die Analyse-Seite, um ihn hier wiederzufinden.</p>
                @endif
            </div>
        @else
            <div class="grid gap-4">
                @foreach ($items as $resume)
                    <article class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-dark p-4 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-semibold text-text-light dark:text-text-dark">
                                        {{ $resume['file_name'] ?? $resume['original_filename'] ?? 'Lebenslauf' }}
                                    </h2>
                                    @if ($resume['is_current'])
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-100">
                                            Aktueller Token
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 break-all text-xs text-gray-500 dark:text-gray-400">Token: {{ $resume['token'] }}</p>
                                <p class="mt-3 text-sm text-gray-700 dark:text-gray-200">{{ $resume['preview'] }}</p>
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Zuletzt aktualisiert: {{ $resume['updated_at'] }}</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:w-auto">
                                <a href="{{ route('profile.load', ['token' => $resume['token']]) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                                    Lebenslauf laden
                                </a>
                                <form method="POST" action="{{ route('profile.delete', ['token' => $resume['token']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-700 dark:text-red-300 dark:hover:bg-red-950">
                                        Lebenslauf loeschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($pagination['last_page'] > 1)
                <nav class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Seite {{ $pagination['current_page'] }} von {{ $pagination['last_page'] }} · {{ $pagination['total'] }} Einträge
                    </p>
                    <div class="flex items-center gap-2">
                        @if ($pagination['has_previous'])
                            <a href="{{ route('profile.index', ['page' => $pagination['previous_page'], 'search' => $search, 'sort' => $sort, 'direction' => $direction, 'per_page' => $pagination['per_page']]) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-text-light dark:text-text-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                Zurück
                            </a>
                        @endif

                        @if ($pagination['has_next'])
                            <a href="{{ route('profile.index', ['page' => $pagination['next_page'], 'search' => $search, 'sort' => $sort, 'direction' => $direction, 'per_page' => $pagination['per_page']]) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-text-light dark:text-text-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                Weiter
                            </a>
                        @endif
                    </div>
                </nav>
            @endif
        @endif
    </div>
@endsection



