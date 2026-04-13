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

test('nutzende koennen den analyse-kernflow bis zur ergebnisansicht durchlaufen', function () {
    $jobText = str_repeat('Senior Laravel Backend Developer mit API, Docker und Testing Erfahrung. ', 2);
    $cvText = str_repeat('Mehrjaehrige PHP und Laravel Projekte mit REST APIs, Git und Teamarbeit. ', 2);

    $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => $cvText,
    ])
        ->assertOk()
        ->assertViewIs('result')
        ->assertViewHasAll(['result', 'job_text', 'cv_text', 'score'])
        ->assertSee('Analyse-Ergebnis')
        ->assertSee('Score')
        ->assertSee('Match-Tags')
        ->assertSee('Gap-Tags')
        ->assertSee('Empfehlungen')
        ->assertSee('Stellenausschreibung (Roh-Text)')
        ->assertSee('Lebenslauf (Roh-Text)');
});

test('nutzende werden bei ungueltigen analyse-eingaben auf das formular zurueckgeleitet', function () {
    $this->from(route('analyze'))
        ->post(route('analyze.submit'), [
            'job_text' => 'zu kurz',
            'cv_text' => '',
        ])
        ->assertRedirect(route('analyze'))
        ->assertSessionHasErrors(['job_text', 'cv_text']);
});
