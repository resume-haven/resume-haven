@extends('layouts.app')

@section('title', 'Analyse-Ergebnis')

@section('content')
    <div class="space-y-6 sm:space-y-8">
        <!-- Heading -->
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 sm:mb-6">Analyse-Ergebnis</h2>

        <!-- Error Handling -->
        @if ($error)
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg mb-6">
                <span>{!! nl2br(e($error)) !!}</span>
            </div>
        @endif

        <!-- Score Panel -->
        @if ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']) && $score)
            <div class="mb-6 {{ $score->bgColor }} dark:bg-slate-800 border-l-4 {{ str_replace('bg-', 'border-', $score->barColor) }} rounded-lg shadow p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
                    <div class="flex-1 w-full">
                        <p class="text-xs sm:text-sm font-semibold {{ $score->textColor }} uppercase tracking-wide">Übereinstimmung</p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-bold {{ $score->textColor }} mt-2">{{ $score->percentage }}%</p>
                        <p class="text-base sm:text-lg {{ $score->textColor }} mt-2">{{ $score->rating }}</p>
                        <p class="text-xs sm:text-sm {{ $score->textColor }} mt-4">
                            ✓ {{ $score->matchCount }} Match{{ $score->matchCount !== 1 ? 'es' : '' }} •
                            ✗ {{ $score->gapCount }} Gap{{ $score->gapCount !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <!-- Circle Indicator -->
                    <div class="w-20 sm:w-24 h-20 sm:h-24 flex-shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" class="opacity-10 {{ $score->textColor }}"></circle>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" stroke-dasharray="{{ $score->percentage * 2.827 }} 282.7" class="{{ $score->barColor }}"></circle>
                        </svg>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mt-4 sm:mt-6 bg-white dark:bg-neutral-dark rounded-full h-2 overflow-hidden">
                    <div class="h-full {{ $score->barColor }} transition-all duration-500" style="width: {{ $score->percentage }}%"></div>
                </div>
            </div>
        @endif

        <!-- Tags Section -->
        @if ($result && is_array($result) && isset($result['tags']) && is_array($result['tags']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                <!-- Match Tags -->
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4">Match-Tags</h3>
                    @if (isset($result['tags']['matches']) && is_array($result['tags']['matches']) && count($result['tags']['matches']) > 0)
                        <div class="space-y-3">
                            @foreach ($result['tags']['matches'] as $tag)
                                <div class="rounded-md border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900 p-3">
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-800 px-3 py-1 text-xs font-semibold text-green-800 dark:text-green-100">
                                        {{ $tag['requirement'] ?? 'Unbekannt' }}
                                    </span>
                                    @if (isset($tag['experience']) && is_array($tag['experience']) && count($tag['experience']) > 0)
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach ($tag['experience'] as $expTag)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-800 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-100">
                                                    {{ $expTag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Keine Match-Tags vorhanden.</p>
                    @endif
                </div>

                <!-- Gap Tags -->
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4">Gap-Tags</h3>
                    @if (isset($result['tags']['gaps']) && is_array($result['tags']['gaps']) && count($result['tags']['gaps']) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach ($result['tags']['gaps'] as $gapTag)
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-800 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-100">
                                    {{ $gapTag }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Keine Gap-Tags vorhanden.</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Details Section -->
        @if ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']))
            <details class="mt-4 sm:mt-6 bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6" open>
                <summary class="cursor-pointer text-base sm:text-lg font-semibold">Details: Matches & Gaps</summary>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                    <!-- Matches Panel -->
                    <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold mb-4">Matches ({{ count($result['matches']) }})</h3>
                        <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base">
                            @foreach ($result['matches'] as $match)
                                <li>
                                    <span class="text-green-700 dark:text-green-400 font-semibold">{{ $match['requirement'] ?? '' }}</span>
                                    &rarr;
                                    <span class="text-blue-700 dark:text-blue-400">{{ $match['experience'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Gaps Panel -->
                    <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold mb-4">Gaps ({{ count($result['gaps']) }})</h3>
                        <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base">
                            @foreach ($result['gaps'] as $gap)
                                <li class="text-red-700 dark:text-red-400">{{ $gap }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </details>

            <!-- Group 2: Requirements & Experiences -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                <!-- Requirements Panel -->
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4">Anforderungen</h3>
                    <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base">
                        @foreach ($result['requirements'] as $req)
                            <li class="text-gray-700 dark:text-gray-300">{{ $req }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Experiences Panel -->
                <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4">Erfahrungen</h3>
                    <ul class="list-disc pl-5 space-y-2 text-sm sm:text-base">
                        @foreach ($result['experiences'] as $exp)
                            <li class="text-gray-700 dark:text-gray-300">{{ $exp }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="text-gray-500 dark:text-gray-400 text-sm mt-8">
                Keine Analyseergebnisse vorhanden.
            </div>
        @endif

        <!-- Group 3: Raw Text Panels -->
        <div class="grid grid-cols-1 gap-4 sm:gap-6 mt-4 sm:mt-6">
            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                <h4 class="font-bold mb-3 text-base sm:text-lg">Stellenausschreibung (Roh-Text)</h4>
                <div class="w-full text-xs sm:text-sm text-gray-700 dark:text-gray-300 overflow-x-auto" style="white-space:pre-wrap; word-break:break-word; line-height:1.5;">
                    {{ $job_text }}
                </div>
            </div>
            <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6">
                <h4 class="font-bold mb-3 text-base sm:text-lg">Lebenslauf (Roh-Text)</h4>
                <div class="w-full text-xs sm:text-sm text-gray-700 dark:text-gray-300 overflow-x-auto" style="white-space:pre-wrap; word-break:break-word; line-height:1.5;">
                    {{ $cv_text }}
                </div>
            </div>
        </div>
    </div>
@endsection
