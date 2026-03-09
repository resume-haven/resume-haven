@extends('layouts.app')

@section('title', 'Lizenzen')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-6">Open Source Lizenzen</h1>

        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-3 sm:p-4 mb-6">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                ℹ️ Diese Seite wird automatisch aus <code>composer.lock</code> und <code>package-lock.json</code> generiert.
                Fuehren Sie <code>php artisan licenses:generate</code> aus, um die Lizenzen zu aktualisieren.
            </p>
        </div>

        @if (!empty($php) || !empty($node) || $generated_at)
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                Zuletzt generiert: {{ $generated_at ?? 'Unbekannt' }}
            </div>

            <!-- PHP Packages -->
            <div class="mb-8">
                <h2 class="text-xl sm:text-2xl font-bold mb-4">PHP Packages (Composer)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-neutral-dark rounded-lg shadow">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Paket</th>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Version</th>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Lizenz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($php ?? [] as $package)
                                <tr>
                                    <td class="px-4 py-3 text-xs sm:text-sm">
                                        @if (isset($package['homepage']) && !empty($package['homepage']))
                                            <a href="{{ $package['homepage'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">
                                                {{ $package['name'] }}
                                            </a>
                                        @else
                                            {{ $package['name'] }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs sm:text-sm">{{ $package['version'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-xs sm:text-sm">{{ $package['license'] ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">
                                        Keine PHP-Pakete gefunden. Fuehren Sie <code>php artisan licenses:generate</code> aus.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Node Packages -->
            <div>
                <h2 class="text-xl sm:text-2xl font-bold mb-4">Node Packages (NPM)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white dark:bg-neutral-dark rounded-lg shadow">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Paket</th>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Version</th>
                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold">Lizenz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($node ?? [] as $package)
                                <tr>
                                    <td class="px-4 py-3 text-xs sm:text-sm">
                                        @if (isset($package['homepage']) && !empty($package['homepage']))
                                            <a href="{{ $package['homepage'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">
                                                {{ $package['name'] }}
                                            </a>
                                        @else
                                            {{ $package['name'] }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs sm:text-sm">{{ $package['version'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-xs sm:text-sm">{{ $package['license'] ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">
                                        Keine Node-Pakete gefunden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <p class="text-yellow-800 dark:text-yellow-200">
                    ⚠️ Lizenzdatei wurde noch nicht generiert. Fuehren Sie <code>php artisan licenses:generate</code> aus.
                </p>
            </div>
        @endif
    </div>
@endsection
