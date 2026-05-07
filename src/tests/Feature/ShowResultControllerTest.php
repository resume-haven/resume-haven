<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('leitet auf analyze um, wenn kein gespeichertes analyse-ergebnis in der session liegt', function (): void {
    $response = $this->get(route('result.show'));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors('result');
});

test('zeigt claim-spezifischen fallback-hinweis nach redirect auf analyze', function (): void {
    $response = $this->followingRedirects()->get(route('result.show'));

    $response->assertOk();
    $response->assertSee('Kein gespeichertes Analyse-Ergebnis mehr verfuegbar. Bitte fuehre die Analyse erneut aus, um den Claim-Flow fortzusetzen.');
});

test('rendert die result-view aus session-daten', function (): void {
    $response = $this->withSession([
        'analysis_result_view_data' => [
            'job_text' => 'Beispiel Jobtext',
            'cv_text' => 'Beispiel CV-Text',
            'result' => null,
            'error' => null,
            'score' => null,
            'tags' => null,
            'comparison' => null,
        ],
    ])->get(route('result.show'));

    $response->assertOk();
    $response->assertViewIs('result');
    $response->assertSee('Beispiel Jobtext');
    $response->assertSee('Beispiel CV-Text');
});
