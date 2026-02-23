@extends('layouts.app')

@section('title', 'Analyse starten')

@section('content')
    <div class="space-y-8">

        <h2 class="text-3xl font-bold text-primary">Bewerbungsanalyse</h2>

        <p class="text-gray-600 dark:text-text-dark">
            Füge die Stellenausschreibung und deinen Lebenslauf ein. ResumeHaven analysiert anschließend,
            welche Erfahrungen zu welchen Anforderungen passen.
        </p>

        <form action="/analyze" method="POST" class="space-y-8">
            @csrf

            <!-- Stellenausschreibung -->
            <div>
                <label class="block text-lg font-semibold mb-2 text-text-light dark:text-text-dark">
                    Stellenausschreibung
                </label>
                <textarea
                    name="job_text"
                    rows="10"
                    class="w-full p-4 rounded-lg border border-gray-300 bg-white dark:bg-neutral-dark dark:border-neutral-dark dark:text-text-dark focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Füge hier die Stellenausschreibung ein..."
                ></textarea>
            </div>

            <!-- Lebenslauf -->
            <div>
                <label class="block text-lg font-semibold mb-2 text-text-light dark:text-text-dark">
                    Lebenslauf
                </label>
                <textarea
                    name="cv_text"
                    rows="10"
                    class="w-full p-4 rounded-lg border border-gray-300 bg-white dark:bg-neutral-dark dark:border-neutral-dark dark:text-text-dark focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="Füge hier deinen Lebenslauf ein..."
                ></textarea>
            </div>

            <!-- CTA -->
            <div>
                <button
                    type="submit"
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition"
                >
                    Analysieren
                </button>
            </div>

        </form>

    </div>
@endsection
