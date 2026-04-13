<?php

declare(strict_types=1);

use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use App\Services\AnalyzeApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$configureMockAnalyzer = static function (string $scenario = 'realistic'): void {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => $scenario,
        'ai.mock.delay_ms' => 0,
    ]);

    app()->forgetInstance(AiAnalyzerInterface::class);
    app()->forgetInstance(MockAiAnalyzer::class);
    app()->forgetInstance(AnalyzeApplicationService::class);
    app()->forgetInstance(AnalyzeJobAndResumeHandler::class);
};

beforeEach(function () use ($configureMockAnalyzer): void {
    $configureMockAnalyzer();
});

test('nutzende koennen einen lebenslauf speichern, per token laden und erneut analysieren', function () {
    $cvText = str_repeat('Erfahrung mit Laravel, PHP, Docker, APIs und Teamarbeit im Backend. ', 2);
    $jobText = str_repeat('Senior Backend Rolle mit Laravel, Docker, Testing und Git im SaaS-Umfeld. ', 2);

    $storeResponse = $this->post(route('profile.store'), [
        'cv_text' => $cvText,
    ]);

    $storeResponse
        ->assertRedirect(route('analyze'))
        ->assertSessionHas('resume_token')
        ->assertSessionHas('resume_link');

    /** @var string $token */
    $token = $storeResponse->getSession()->get('resume_token');
    /** @var string $link */
    $link = $storeResponse->getSession()->get('resume_link');
    /** @var array<string, mixed> $storeSession */
    $storeSession = $storeResponse->getSession()->all();

    $this->withSession($storeSession)
        ->get(route('analyze'))
        ->assertOk()
        ->assertSee('Lebenslauf gespeichert!')
        ->assertSee($token)
        ->assertSee('/profile/load/'.$token)
        ->assertSee('Speichere diesen Link');

    $loadResponse = $this->get(route('profile.load', ['token' => $token]));

    $loadResponse
        ->assertRedirect(route('analyze'))
        ->assertSessionHas('loaded_cv')
        ->assertSessionHas('loaded_token', $token)
        ->assertSessionHas('success', 'Gespeicherter Lebenslauf wurde geladen.');

    /** @var string $loadedCv */
    $loadedCv = (string) $loadResponse->getSession()->get('loaded_cv');
    expect($loadedCv)->toContain('Laravel')->and($loadedCv)->toContain('Teamarbeit');

    /** @var array<string, mixed> $loadSession */
    $loadSession = $loadResponse->getSession()->all();

    $this->withSession($loadSession)
        ->get(route('analyze'))
        ->assertOk()
        ->assertSee('Gespeicherter Lebenslauf geladen:')
        ->assertSee($token)
        ->assertSee('Erfahrung mit Laravel');

    $this->withSession($loadSession)
        ->post(route('analyze.submit'), [
            'job_text' => $jobText,
            'cv_text' => $cvText,
        ])
        ->assertOk()
        ->assertViewIs('result')
        ->assertSee('Analyse-Ergebnis')
        ->assertSee('Score');
});

test('nutzende erhalten bei ungueltigem token-format eine sichtbare fehlermeldung', function () {
    $this->followingRedirects()
        ->get(route('profile.load', ['token' => '***invalid***']))
        ->assertOk()
        ->assertSee('Ungueltiger Resume-Token.')
        ->assertSee('Bitte prüfe deinen Token oder lade die Seite neu');
});
