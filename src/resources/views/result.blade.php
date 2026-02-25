@extends('layouts.app')

@section('title', 'Analyse-Ergebnis')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Analyse-Ergebnis</h2>
    <div class="mb-6">
        <h3 class="font-semibold">Stellenausschreibung:</h3>
        <pre class="bg-neutral-light p-2 rounded">{{ $job_text }}</pre>
    </div>
    <div class="mb-6">
        <h3 class="font-semibold">Lebenslauf:</h3>
        <pre class="bg-neutral-light p-2 rounded">{{ $cv_text }}</pre>
    </div>
    @if ($error)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <span>{!! nl2br(e($error)) !!}</span>
        </div>
    @elseif ($result && is_array($result) && isset($result['requirements'], $result['experiences'], $result['matches'], $result['gaps']))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="bg-white dark:bg-neutral-dark rounded shadow p-4">
                <h4 class="font-bold mb-2">Anforderungen</h4>
                <ul class="list-disc pl-5">
                    @foreach ($result['requirements'] as $req)
                        <li>{{ $req }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white dark:bg-neutral-dark rounded shadow p-4">
                <h4 class="font-bold mb-2">Erfahrungen</h4>
                <ul class="list-disc pl-5">
                    @foreach ($result['experiences'] as $exp)
                        <li>{{ $exp }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white dark:bg-neutral-dark rounded shadow p-4 md:col-span-2">
                <h4 class="font-bold mb-2">Matches</h4>
                <ul class="list-disc pl-5">
                    @foreach ($result['matches'] as $match)
                        <li><span class="text-green-700 font-semibold">{{ $match['requirement'] ?? '' }}</span> &rarr; <span class="text-blue-700">{{ $match['experience'] ?? '' }}</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white dark:bg-neutral-dark rounded shadow p-4 md:col-span-2">
                <h4 class="font-bold mb-2">Gaps</h4>
                <ul class="list-disc pl-5">
                    @foreach ($result['gaps'] as $gap)
                        <li class="text-red-700">{{ $gap }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="text-gray-500 text-sm">
            Keine Analyseergebnisse vorhanden.
        </div>
    @endif
@endsection
