@extends('layouts.app')

@section('title', 'ResumeHaven')

@section('content')
    <div class="space-y-8">

        <h2 class="text-4xl font-bold text-primary">
            Willkommen bei ResumeHaven
        </h2>

        <p class="text-gray-600 dark:text-text-dark text-lg leading-relaxed max-w-3xl">
            ResumeHaven unterstützt dich dabei, die Anforderungen einer Stellenausschreibung
            mit deinen Erfahrungen abzugleichen. Schnell, klar und ohne Schnickschnack.
        </p>

        <a href="/analyze" class="inline-block px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition shadow-md hover:shadow-lg">
            Analyse starten
        </a>


    </div>
@endsection
