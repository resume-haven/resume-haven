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
    <div class="text-gray-500 text-sm">
        KI-Analyse folgt in einem späteren Schritt.
    </div>
@endsection
