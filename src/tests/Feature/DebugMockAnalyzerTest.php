<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Debug-Test: Prüfe ob MockAiAnalyzer mit realistic Szenario korrekte Daten zurückgibt
 */
test('MockAiAnalyzer realistic Szenario hat matches und gaps', function () {
    config(['ai.mock.scenario' => 'realistic']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->requirements)->not()->toBeEmpty();
    expect($result->experiences)->not()->toBeEmpty();
    expect($result->matches)->not()->toBeEmpty();
    expect($result->gaps)->not()->toBeEmpty();
    expect($result->tags)->not()->toBeNull();
    expect($result->tags['matches'])->not()->toBeEmpty();
    expect($result->tags['gaps'])->not()->toBeEmpty();
});

test('Handler mit demoMode erzeugt Score korrekt (Cache deaktiviert)', function () {
    config(['ai.mock.scenario' => 'realistic']);

    $request = new \App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand(
        new AnalyzeRequestDto(str_repeat('A', 31), str_repeat('B', 31)),
        demoMode: true  // Cache deaktiviert
    );

    $result = $request->handle(app(\App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler::class));

    expect($result->error)->toBeNull();
    expect($result->matches)->not()->toBeEmpty();
    expect($result->gaps)->not()->toBeEmpty();
    expect($result->tags)->not()->toBeNull();

    // Berechne Score
    $scoringUseCase = app(\App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase::class);
    $score = $scoringUseCase->handle($result->matches, $result->gaps);

    expect($score)->not()->toBeNull();
    expect($score->percentage)->toBeGreaterThan(0);
});

test('Cache bricht alte DTOs ohne tags nicht', function () {
    // Dieser Test prüft, ob der Cache alte Daten ohne tags korrekt verarbeitet
    config(['ai.mock.scenario' => 'realistic']);

    // Speichere alte Daten ohne tags im Cache (simuliert alte Einträge)
    $oldResultArray = [
        'job_text' => 'Old Job',
        'cv_text' => 'Old CV',
        'requirements' => ['PHP'],
        'experiences' => ['5 Jahre PHP'],
        'matches' => [['requirement' => 'PHP', 'experience' => '5 Jahre PHP']],
        'gaps' => [],
        'error' => null,
        'tags' => null,  // ← Alte Version ohne tags!
    ];

    \App\Models\AnalysisCache::create([
        'request_hash' => hash('sha256', 'Old JobOld CV'),
        'job_text' => 'Old Job',
        'cv_text' => 'Old CV',
        'result' => $oldResultArray,
    ]);

    // Rufe nun mit gleichem Input ab - sollte alte Daten mit fallback-tags zurückgeben
    $action = app(\App\Domains\Analysis\Cache\Actions\GetCachedAnalysisAction::class);
    $cached = $action->execute(new AnalyzeRequestDto('Old Job', 'Old CV'));

    expect($cached)->not()->toBeNull();
    expect($cached->tags)->not()->toBeNull();  // Fallback sollte tags erzeugt haben
});


