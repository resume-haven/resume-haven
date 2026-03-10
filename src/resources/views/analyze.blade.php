@extends('layouts.app')

@section('title', 'Analyse starten')

@section('content')
    <div class="space-y-6 sm:space-y-8">

        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary">Bewerbungsanalyse</h2>

        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 leading-relaxed">
            Fuege die Stellenausschreibung und deinen Lebenslauf ein. ResumeHaven analysiert anschliessend,
            welche Erfahrungen zu welchen Anforderungen passen.
        </p>

        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-800 dark:text-green-100 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

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

        <div class="bg-white dark:bg-neutral-dark rounded-lg shadow p-4 sm:p-6 space-y-4">
            <h3 class="text-base sm:text-lg font-semibold text-text-light dark:text-text-dark">Lebenslauf wieder laden</h3>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                Falls du bereits einen Token hast, kannst du deinen gespeicherten Lebenslauf direkt laden.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    id="resume_token_input"
                    type="text"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-dark dark:text-text-dark text-sm"
                    placeholder="Resume-Token eingeben"
                    value="{{ session('loaded_token', session('resume_token', '')) }}"
                >
                <button
                    type="button"
                    id="load_resume_button"
                    class="w-full sm:w-auto px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition duration-200 ease-in-out"
                >
                    Lebenslauf laden
                </button>
            </div>

            @if (session('resume_link'))
                <div class="space-y-2">
                    <p class="text-xs sm:text-sm font-semibold text-text-light dark:text-text-dark">Dein Speicher-Link:</p>
                    <div class="flex gap-2">
                        <input
                            id="resume_link_display"
                            type="text"
                            readonly
                            class="flex-1 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-xs sm:text-sm"
                            value="{{ session('resume_link') }}"
                        >
                        <button
                            type="button"
                            id="copy_link_button"
                            title="Link kopieren"
                            class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-200 transition duration-150"
                        >
                            <svg id="copy_icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-4 12h6a2 2 0 002-2v-8a2 2 0 00-2-2h-6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg id="check_icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Speichere diesen Link, um deinen Lebenslauf spaeter wieder zu laden.
                    </p>
                </div>
            @endif
        </div>

        <form action="{{ route('analyze.submit') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Grid: 1 Column Mobile, 2 Columns Desktop -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Job Description -->
                <div>
                    <label class="block text-base sm:text-lg font-semibold mb-2 sm:mb-3 text-text-light dark:text-text-dark">
                        Stellenausschreibung
                    </label>
                    <textarea
                        name="job_text"
                        rows="12"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-dark dark:text-text-dark text-base focus:outline-none focus:ring-2 focus:ring-primary resize-none min-h-[200px] sm:min-h-[300px]"
                        placeholder="Fuege hier die Stellenausschreibung ein..."
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
                        id="cv_text"
                        name="cv_text"
                        rows="12"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-neutral-dark dark:text-text-dark text-base focus:outline-none focus:ring-2 focus:ring-primary resize-none min-h-[200px] sm:min-h-[300px]"
                        placeholder="Fuege hier deinen Lebenslauf ein..."
                        required
                    >{{ old('cv_text', session('loaded_cv', '')) }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Mindestens 30 Zeichen erforderlich</p>
                </div>

            </div>

            <!-- CTA Buttons: Full-Width Mobile, Auto Desktop -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 pt-2 sm:pt-4 justify-center">
                <button
                    type="submit"
                    class="w-full sm:w-auto px-8 py-3 sm:py-4 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg transition duration-200 ease-in-out text-base sm:text-lg min-h-[48px]"
                >
                    Analysieren
                </button>
                <button
                    type="submit"
                    formaction="{{ route('profile.store') }}"
                    formnovalidate
                    class="w-full sm:w-auto px-8 py-3 sm:py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition duration-200 ease-in-out text-base sm:text-lg min-h-[48px]"
                >
                    CV speichern
                </button>
                <a
                    href="/"
                    class="w-full sm:w-auto px-8 py-3 sm:py-4 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold rounded-lg transition duration-200 ease-in-out text-center text-base sm:text-lg min-h-[48px]"
                >
                    Zurueck
                </a>
            </div>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lebenslauf ueber Token laden
            const loadButton = document.getElementById('load_resume_button');
            const tokenInput = document.getElementById('resume_token_input');

            if (loadButton && tokenInput) {
                loadButton.addEventListener('click', function () {
                    const token = tokenInput.value.trim();
                    if (token === '') {
                        return;
                    }
                    window.location.href = '/profile/load/' + encodeURIComponent(token);
                });
            }

            // Copy-to-Clipboard fuer den Speicher-Link
            const copyButton = document.getElementById('copy_link_button');
            const linkInput = document.getElementById('resume_link_display');
            const copyIcon = document.getElementById('copy_icon');
            const checkIcon = document.getElementById('check_icon');

            if (copyButton && linkInput && copyIcon && checkIcon) {
                copyButton.addEventListener('click', function () {
                    navigator.clipboard.writeText(linkInput.value).then(function () {
                        copyIcon.classList.add('hidden');
                        checkIcon.classList.remove('hidden');
                        setTimeout(function () {
                            checkIcon.classList.add('hidden');
                            copyIcon.classList.remove('hidden');
                        }, 2000);
                    }).catch(function () {
                        // Fallback fuer Browser ohne Clipboard API
                        linkInput.select();
                        linkInput.setSelectionRange(0, 99999);
                        document.execCommand('copy');
                    });
                });
            }
        });
    </script>
@endsection
