<?php

declare(strict_types=1);

use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Models\AnalysisBaseline;
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
    $configureMockAnalyzer('low_score');
});

test('nutzende sehen einen delta-vergleich aus dem session-fallback wenn keine persistente baseline vorhanden ist', function () use ($configureMockAnalyzer) {
    $jobText = str_repeat('Laravel Backend API, Docker, Git und Testing fuer eine SaaS-Plattform. ', 2);
    $baselineCv = str_repeat('Erste PHP Projekte mit etwas Frontend und wenig Laravel Erfahrung. ', 2);
    $improvedCv = str_repeat('Senior Laravel Backend Entwickler mit Docker, API-Design, Git und TDD Erfahrung. ', 2);

    $baselineResponse = $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => $baselineCv,
    ]);

    $baselineResponse
        ->assertOk()
        ->assertViewIs('result')
        ->assertSessionHas('analysis_baseline_snapshot');

    expect(AnalysisBaseline::query()->count())->toBe(1);

    /** @var mixed $baselineSnapshot */
    $baselineSnapshot = session('analysis_baseline_snapshot');
    expect($baselineSnapshot)->toBeArray();

    AnalysisBaseline::query()->delete();
    expect(AnalysisBaseline::query()->count())->toBe(0);

    $configureMockAnalyzer('high_score');

    $this->withSession([
        'cv_source' => 'competence_resume',
        'analysis_baseline_snapshot' => $baselineSnapshot,
        'competence_resume_text' => $improvedCv,
    ])->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => $improvedCv,
    ])
        ->assertOk()
        ->assertViewIs('result')
        ->assertSee('Delta')
        ->assertSee('Vergleich zur Baseline')
        ->assertSee('Vergleich aus Session-Fallback (keine persistente Baseline gefunden).')
        ->assertSee('Score-Delta')
        ->assertSee('Match-Delta')
        ->assertSee('Gap-Delta');
});
