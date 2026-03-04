@extends('layouts.app')

@section('title', 'Analyse-Ergebnis')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Analyse-Ergebnis</h2>

    @if ($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <span>{!! nl2br(e($error)) !!}</span>
        </div>
    @endif

    @if ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']) && $score)
        {{-- Score Panel (oberste Position) --}}
        <div class="mb-6 {{ $score->bgColor }} border-l-4 {{ str_replace('bg-', 'border-', $score->barColor) }} rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold {{ $score->textColor }} uppercase tracking-wide">Übereinstimmung</p>
                    <p class="text-5xl font-bold {{ $score->textColor }} mt-2">{{ $score->percentage }}%</p>
                    <p class="text-lg {{ $score->textColor }} mt-2">{{ $score->rating }}</p>
                    <p class="text-sm {{ $score->textColor }} mt-4">
                        ✓ {{ $score->matchCount }} Match{{ $score->matchCount !== 1 ? 'es' : '' }} •
                        ✗ {{ $score->gapCount }} Gap{{ $score->gapCount !== 1 ? 's' : '' }}
                    </p>
                </div>
                <div class="w-24 h-24">
                    {{-- Kreisförmiger Progress-Indicator --}}
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        {{-- Hintergrund-Kreis --}}
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" class="opacity-10 {{ $score->textColor }}"></circle>
                        {{-- Progress-Kreis --}}
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="{{ $score->percentage * 2.827 }} 282.7" class="{{ $score->barColor }}"></circle>
                    </svg>
                </div>
            </div>
            {{-- Fortschrittsbalken --}}
            <div class="mt-6 bg-white dark:bg-neutral-dark rounded-full h-2 overflow-hidden">
                <div class="h-full {{ $score->barColor }} transition-all duration-500" style="width: {{ $score->percentage }}%"></div>
            </div>
        </div>
    @endif

    @if ($result && is_array($result) && isset($result['tags']) && is_array($result['tags']))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Match-Tags</h3>
                @if (isset($result['tags']['matches']) && is_array($result['tags']['matches']) && count($result['tags']['matches']) > 0)
                    <div class="space-y-3">
                        @foreach ($result['tags']['matches'] as $tag)
                            <div class="rounded-md border border-green-200 bg-green-50 p-3">
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                    {{ $tag['requirement'] ?? 'Unbekannt' }}
                                </span>
                                @if (isset($tag['experience']) && is_array($tag['experience']) && count($tag['experience']) > 0)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($tag['experience'] as $expTag)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                                {{ $expTag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Keine Match-Tags vorhanden.</p>
                @endif
            </div>

            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Gap-Tags</h3>
                @if (isset($result['tags']['gaps']) && is_array($result['tags']['gaps']) && count($result['tags']['gaps']) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($result['tags']['gaps'] as $gapTag)
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                {{ $gapTag }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Keine Gap-Tags vorhanden.</p>
                @endif
            </div>
        </div>
    @endif

    @if ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']))
        <details class="mt-4 bg-white dark:bg-neutral-dark rounded-lg shadow p-4" open>
            <summary class="cursor-pointer text-base font-semibold">Details: Matches & Gaps</summary>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Matches Panel --}}
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Matches ({{ count($result['matches']) }})</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        @foreach ($result['matches'] as $match)
                            <li>
                                <span class="text-green-700 dark:text-green-400 font-semibold">{{ $match['requirement'] ?? '' }}</span>
                                &rarr;
                                <span class="text-blue-700 dark:text-blue-400">{{ $match['experience'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Gaps Panel --}}
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Gaps ({{ count($result['gaps']) }})</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        @foreach ($result['gaps'] as $gap)
                            <li class="text-red-700 dark:text-red-400">{{ $gap }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </details>

        {{-- Group 2: Requirements & Experiences --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Requirements Panel --}}
            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Anforderungen</h3>
                <ul class="list-disc pl-5 space-y-2">
                    @foreach ($result['requirements'] as $req)
                        <li>{{ $req }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Experiences Panel --}}
            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Erfahrungen</h3>
                <ul class="list-disc pl-5 space-y-2">
                    @foreach ($result['experiences'] as $exp)
                        <li>{{ $exp }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="text-gray-500 text-sm mt-8">
            Keine Analyseergebnisse vorhanden.
        </div>
    @endif

    {{-- Group 3: Raw Text Panels --}}
    <div class="grid grid-cols-1 gap-4 mt-4">
        <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4">
            <h4 class="font-bold mb-2">Stellenausschreibung (Roh-Text)</h4>
            <div class="w-full text-sm text-gray-700 dark:text-gray-300" style="white-space:pre-wrap; word-break:break-word;">
                {{ $job_text }}
            </div>
        </div>
        <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4">
            <h4 class="font-bold mb-2">Lebenslauf (Roh-Text)</h4>
            <div class="w-full text-sm text-gray-700 dark:text-gray-300" style="white-space:pre-wrap; word-break:break-word;">
                {{ $cv_text }}
            </div>
        </div>
    </div>
@endsection
