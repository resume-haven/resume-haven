@extends('layouts.app')

@section('title', 'Datenschutz')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="prose dark:prose-invert prose-sm sm:prose-base lg:prose-lg max-w-none">
            <h1>Datenschutzerklärung</h1>

            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 sm:p-4 mb-6">
                <p class="text-sm text-yellow-800 dark:text-yellow-200 font-semibold">
                    ⚠️ Hinweis: Dies ist eine Muster-Datenschutzerklärung für MVP-Zwecke.
                    Vor Produktivbetrieb muss eine DSGVO-konforme Datenschutzerklärung erstellt werden!
                </p>
            </div>

            <h2>1. Datenschutz auf einen Blick</h2>
            <h3>Allgemeine Hinweise</h3>
            <p>
                Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten passiert,
                wenn Sie diese Website besuchen.
            </p>

            <h2>2. Datenerfassung auf dieser Website</h2>
            <h3>Welche Daten werden erfasst?</h3>
            <p>
                Aktuell werden nur technisch notwendige Daten erfasst:
            </p>
            <ul>
                <li>Eingaben in Formulare (Stellenausschreibung, Lebenslauf) - werden NICHT gespeichert</li>
                <li>Session-Cookies (technisch notwendig)</li>
                <li>Server-Logs (IP-Adresse, Browser, Zeitstempel)</li>
            </ul>

            <h2>3. Ihre Rechte</h2>
            <p>
                Sie haben jederzeit das Recht auf Auskunft, Berichtigung, Löschung und Einschränkung der Verarbeitung
                Ihrer personenbezogenen Daten.
            </p>
        </div>
    </div>
@endsection
