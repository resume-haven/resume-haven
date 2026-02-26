@extends('layouts.app')

@section('title', 'Analyse-Ergebnis')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Analyse-Ergebnis</h2>

    @if ($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <span>{!! nl2br(e($error)) !!}</span>
        </div>
    @endif

    @if ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']))
        {{-- Group 1: Matches & Gaps --}}
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
