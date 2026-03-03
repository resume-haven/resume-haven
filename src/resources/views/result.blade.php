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

    @if ($result && is_array($result) && isset($result['recommendations']) && is_array($result['recommendations']) && count($result['recommendations']) > 0)
        <div class="mt-4 bg-white dark:bg-neutral-dark rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Empfehlungen & Verbesserungsvorschläge</h3>
            <div class="space-y-4">
                @foreach ($result['recommendations'] as $rec)
                    @php
                        $priority = $rec['priority'] ?? 'medium';
                        $priorityClasses = match ($priority) {
                            'critical' => 'bg-red-100 text-red-800',
                            'high' => 'bg-orange-100 text-orange-800',
                            'medium' => 'bg-yellow-100 text-yellow-800',
                            'low' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        $category = $rec['category'] ?? 'general';
                        $confidence = isset($rec['confidence']) ? (float) $rec['confidence'] : 0.5;
                        $confidencePercent = max(0, min(100, (int) round($confidence * 100)));
                    @endphp

                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $rec['gap'] ?? 'Unbekannte Gap' }}</span>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $priorityClasses }}">
                                {{ strtoupper($priority) }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ strtoupper($category) }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                            {{ $rec['recommendation'] ?? '' }}
                        </p>

                        <div class="rounded bg-gray-50 dark:bg-neutral-dark p-3 mb-2">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Beispiel</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $rec['example'] ?? '' }}</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                <span>Confidence</span>
                                <span>{{ $confidencePercent }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ $confidencePercent }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
