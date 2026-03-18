<?php

declare(strict_types=1);

use App\Models\AnalysisBaseline;
use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Services\AnalyzeApplicationService;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function resetMockAnalyzerBindings(): void
{
    app()->forgetInstance(AiAnalyzerInterface::class);
    app()->forgetInstance(MockAiAnalyzer::class);
    app()->forgetInstance(AnalyzeApplicationService::class);
    app()->forgetInstance(AnalyzeJobAndResumeHandler::class);
}

it('speichert bei normaler Analyse eine persistente Baseline', function () {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => 'low_score',
        'ai.mock.delay_ms' => 0,
    ]);

    $response = $this->post(route('analyze.submit'), [
        'job_text' => str_repeat('Laravel Backend API Anforderungen ', 3),
        'cv_text' => str_repeat('PHP Erfahrung mit ersten Projekten ', 3),
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('result');

    expect(AnalysisBaseline::query()->count())->toBe(1);
});

it('zeigt bei Kompetenz-Analyse einen Delta-Vergleich mit Impact', function () {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => 'low_score',
        'ai.mock.delay_ms' => 0,
    ]);

    $jobText = str_repeat('Laravel Backend API Anforderungen ', 3);

    // Baseline erzeugen
    $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('PHP Erfahrung mit ersten Projekten ', 3),
    ])->assertStatus(200);

    config([
        'ai.mock.scenario' => 'high_score',
    ]);

    // Binding neu aufloesen, damit das geaenderte Mock-Szenario wirkt.
    resetMockAnalyzerBindings();

    $response = $this->withSession([
        'cv_source' => 'competence_resume',
    ])->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('Kompetenzprofil optimiert mit Laravel API MySQL Docker TDD ', 2),
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('result');
    $response->assertViewHas('comparison');

    /** @var mixed $comparison */
    $comparison = $response->viewData('comparison');
    expect($comparison)->toBeArray();

    /** @var array<string, mixed> $comparisonArray */
    $comparisonArray = $comparison;

    expect($comparisonArray['has_comparison'] ?? false)->toBeTrue();
    expect($comparisonArray['score_delta'])->toBeArray();

    /** @var array<string, mixed> $scoreDelta */
    $scoreDelta = is_array($comparisonArray['score_delta']) ? $comparisonArray['score_delta'] : [];

    expect((int) ($scoreDelta['delta'] ?? 0))->toBeGreaterThan(0);
    expect($scoreDelta['arrow'] ?? null)->toBe('↑');
});

it('zeigt bei unverändertem Kompetenzprofil einen neutralen Vergleich', function () {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => 'low_score',
        'ai.mock.delay_ms' => 0,
    ]);

    $jobText = str_repeat('Laravel Backend API Anforderungen ', 3);

    $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('PHP Erfahrung mit ersten Projekten ', 3),
    ])->assertStatus(200);

    resetMockAnalyzerBindings();

    $response = $this->withSession([
        'cv_source' => 'competence_resume',
    ])->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('PHP Erfahrung mit ersten Projekten ', 3),
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('result');

    /** @var mixed $comparison */
    $comparison = $response->viewData('comparison');
    expect($comparison)->toBeArray();

    /** @var array<string, mixed> $comparisonArray */
    $comparisonArray = $comparison;
    $scoreDelta = is_array($comparisonArray['score_delta'] ?? null) ? $comparisonArray['score_delta'] : [];

    expect($comparisonArray['has_comparison'] ?? false)->toBeTrue()
        ->and((int) ($scoreDelta['delta'] ?? -1))->toBe(0)
        ->and($scoreDelta['arrow'] ?? null)->toBe('→')
        ->and((int) ($comparisonArray['match_delta'] ?? -1))->toBe(0)
        ->and((int) ($comparisonArray['gap_delta'] ?? -1))->toBe(0);
});

it('zeigt bei schlechterem Kompetenzprofil eine Verschlechterung an', function () {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => 'high_score',
        'ai.mock.delay_ms' => 0,
    ]);

    $jobText = str_repeat('Laravel Backend API Anforderungen ', 3);

    $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('Kompetenzprofil optimiert mit Laravel API MySQL Docker TDD ', 2),
    ])->assertStatus(200);

    config([
        'ai.mock.scenario' => 'low_score',
    ]);

    resetMockAnalyzerBindings();

    $response = $this->withSession([
        'cv_source' => 'competence_resume',
    ])->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => str_repeat('PHP Erfahrung mit ersten Projekten ', 3),
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('result');

    /** @var mixed $comparison */
    $comparison = $response->viewData('comparison');
    expect($comparison)->toBeArray();

    /** @var array<string, mixed> $comparisonArray */
    $comparisonArray = $comparison;
    $scoreDelta = is_array($comparisonArray['score_delta'] ?? null) ? $comparisonArray['score_delta'] : [];

    expect($comparisonArray['has_comparison'] ?? false)->toBeTrue()
        ->and((int) ($scoreDelta['delta'] ?? 0))->toBeLessThan(0)
        ->and($scoreDelta['arrow'] ?? null)->toBe('↓')
        ->and((int) ($comparisonArray['match_delta'] ?? 0))->toBeLessThan(0)
        ->and((int) ($comparisonArray['gap_delta'] ?? 0))->toBeGreaterThan(0);
});
