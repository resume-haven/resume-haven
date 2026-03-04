@extends('layouts.app')

@section('title', 'Analyse starten')

@section('content')
    <div class="space-y-6 sm:space-y-8">

        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary">Bewerbungsanalyse</h2>

        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
            Füge die Stellenausschreibung und deinen Lebenslauf ein. ResumeHaven analysiert anschließend,
            welche Erfahrungen zu welchen Anforderungen passen.
        </p>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/analyze" method="POST" class="space-y-6 sm:space-y-8">
            @csrf

            <!-- Job Description -->
            <div>
                <label class="block text-base sm:text-lg font-semibold mb-2 sm:mb-3 text-text-light dark:text-text-dark">
                    Stellenausschreibung
                </label>
                <textarea
                    name="job_text"
                    rows="8"
                    class="w-full p-3 sm:p-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-dark dark:text-text-dark text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                    placeholder="Füge hier die Stellenausschreibung ein..."
                    required
                >{{ old('job_text') }}</textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mindestens 30 Zeichen erforderlich</p>
            </div>

            <!-- CV -->
            <div>
                <label class="block text-base sm:text-lg font-semibold mb-2 sm:mb-3 text-text-light dark:text-text-dark">
                    Lebenslauf
                </label>
                <textarea
                    name="cv_text"
                    rows="8"
                    class="w-full p-3 sm:p-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-dark dark:text-text-dark text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                    placeholder="Füge hier deinen Lebenslauf ein..."
                    required
                >{{ old('cv_text') }}</textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mindestens 30 Zeichen erforderlich</p>
            </div>

            <!-- CTA Button -->
            <div class="flex gap-2 sm:gap-4 pt-2 sm:pt-4">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-6 sm:px-8 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg transition duration-200 ease-in-out text-sm sm:text-base"
                >
                    Analysieren
                </button>
                <a
                    href="/"
                    class="flex-1 sm:flex-none px-6 sm:px-8 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition duration-200 ease-in-out text-center text-sm sm:text-base"
                >
                    Zurück
                </a>
            </div>

        </form>

    </div>
@endsection
