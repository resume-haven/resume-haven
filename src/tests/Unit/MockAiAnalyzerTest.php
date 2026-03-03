<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\MockAiAnalyzer;

it('MockAiAnalyzer ist immer verfügbar', function () {
    $analyzer = new MockAiAnalyzer();
    expect($analyzer->isAvailable())->toBeTrue();
});

it('MockAiAnalyzer gibt "mock" als Provider-Namen zurück', function () {
    $analyzer = new MockAiAnalyzer();
    expect($analyzer->getProviderName())->toBe('mock');
});

it('MockAiAnalyzer gibt realistic scenario zurück', function () {
    config(['ai.mock.scenario' => 'realistic']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->requirements)->toBeArray()->not()->toBeEmpty();
    expect($result->experiences)->toBeArray()->not()->toBeEmpty();
    expect($result->matches)->toBeArray();
    expect($result->gaps)->toBeArray();

    // Realistic scenario sollte Matches UND Gaps haben
    expect(count($result->matches))->toBeGreaterThan(0);
    expect(count($result->gaps))->toBeGreaterThan(0);
});

it('MockAiAnalyzer gibt realistic scenario mit Tags zurück', function () {
    config(['ai.mock.scenario' => 'realistic']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->tags)->not()->toBeNull();
    expect($result->tags)->toHaveKey('matches');
    expect($result->tags)->toHaveKey('gaps');
    expect($result->tags['matches'])->toBeArray();
    expect($result->tags['gaps'])->toBeArray();
});

it('MockAiAnalyzer gibt high_score scenario zurück', function () {
    config(['ai.mock.scenario' => 'high_score']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->requirements)->toBeArray()->not()->toBeEmpty();
    expect($result->experiences)->toBeArray()->not()->toBeEmpty();
    expect($result->matches)->toBeArray()->not()->toBeEmpty();

    // High score sollte viele Matches haben
    expect(count($result->matches))->toBeGreaterThanOrEqual(8);
    // Und wenige Gaps
    expect(count($result->gaps))->toBeLessThanOrEqual(2);
});

it('MockAiAnalyzer gibt low_score scenario zurück', function () {
    config(['ai.mock.scenario' => 'low_score']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->requirements)->toBeArray()->not()->toBeEmpty();
    expect($result->experiences)->toBeArray()->not()->toBeEmpty();

    // Low score sollte wenige Matches haben
    expect(count($result->matches))->toBeLessThanOrEqual(3);
    // Und viele Gaps
    expect(count($result->gaps))->toBeGreaterThanOrEqual(5);
});

it('MockAiAnalyzer gibt no_match scenario zurück', function () {
    config(['ai.mock.scenario' => 'no_match']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->requirements)->toBeArray()->not()->toBeEmpty();
    expect($result->experiences)->toBeArray()->not()->toBeEmpty();

    // No match sollte KEINE Matches haben
    expect($result->matches)->toBeArray()->toBeEmpty();
    // Aber alle Requirements als Gaps
    expect($result->gaps)->toBeArray()->not()->toBeEmpty();
    expect(count($result->gaps))->toBe(count($result->requirements));
});

it('MockAiAnalyzer simuliert API-Delay', function () {
    config(['ai.mock.delay_ms' => 100]);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');

    $start = microtime(true);
    $analyzer->analyze($request);
    $duration = (microtime(true) - $start) * 1000; // in ms

    // Sollte mindestens 100ms gedauert haben
    expect($duration)->toBeGreaterThanOrEqual(90); // Etwas Toleranz
});

it('MockAiAnalyzer funktioniert ohne Delay', function () {
    config(['ai.mock.delay_ms' => 0]);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');

    $start = microtime(true);
    $result = $analyzer->analyze($request);
    $duration = (microtime(true) - $start) * 1000; // in ms

    expect($result->error)->toBeNull();
    // Sollte sehr schnell sein (< 50ms)
    expect($duration)->toBeLessThan(50);
});
