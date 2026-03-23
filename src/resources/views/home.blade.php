@extends('layouts.app')

@section('title', 'ResumeHaven')

@section('content')
    <div class="space-y-8">
        <x-molecules.page-heading>
            Willkommen bei ResumeHaven
        </x-molecules.page-heading>

        <x-molecules.call-to-action>
            <x-slot name="button">
                <x-atoms.link href="/analyze" class="inline-block px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition shadow-md hover:shadow-lg">
                    Analyse starten
                </x-atoms.link>
            </x-slot>
            <p class="text-gray-600 dark:text-text-dark text-lg leading-relaxed max-w-3xl">
                ResumeHaven unterstützt dich dabei, die Anforderungen einer Stellenausschreibung
                mit deinen Erfahrungen abzugleichen. Schnell, klar und ohne Schnickschnack.
            </p>
        </x-molecules.call-to-action>
    </div>
@endsection
