@extends('layouts.app')

@section('title', 'Impressum')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="prose dark:prose-invert prose-sm sm:prose-base lg:prose-lg max-w-none">
            <h1>Impressum</h1>

            <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 sm:p-4 mb-6">
                <p class="text-sm text-yellow-800 dark:text-yellow-200 font-semibold">
                    ⚠️ Hinweis: Dies ist ein Muster-Impressum für MVP-Zwecke.
                    Vor Produktivbetrieb müssen die Daten angepasst werden!
                </p>
            </div>

            <h2>Angaben gemäß § 5 TMG</h2>
            <p>
                [Muster-Firma]<br>
                [Muster-Straße 1]<br>
                [12345 Muster-Stadt]
            </p>

            <h2>Kontakt</h2>
            <p>
                E-Mail: <a href="mailto:info@example.com">info@example.com</a><br>
                Telefon: [Muster-Telefon]
            </p>

            <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
            <p>
                [Muster-Name]<br>
                [Muster-Adresse]
            </p>
        </div>
    </div>
@endsection
