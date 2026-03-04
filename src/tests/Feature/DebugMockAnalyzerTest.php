<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\MockAiAnalyzer;

/**
 * Feature-Test: MockAiAnalyzer Szenarien
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

test('MockAiAnalyzer high_score Szenario', function () {
    config(['ai.mock.scenario' => 'high_score']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect(count($result->matches))->toBeGreaterThan(5);
});

test('MockAiAnalyzer low_score Szenario', function () {
    config(['ai.mock.scenario' => 'low_score']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect(count($result->gaps))->toBeGreaterThan(3);
});

test('MockAiAnalyzer no_match Szenario', function () {
    config(['ai.mock.scenario' => 'no_match']);
    $analyzer = new MockAiAnalyzer();

    $request = new AnalyzeRequestDto('Job Text', 'CV Text');
    $result = $analyzer->analyze($request);

    expect($result->error)->toBeNull();
    expect($result->matches)->toBeEmpty();
    expect($result->gaps)->not()->toBeEmpty();
});


