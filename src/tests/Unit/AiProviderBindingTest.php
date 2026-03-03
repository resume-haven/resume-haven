<?php

declare(strict_types=1);

use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;

it('bindet MockAiAnalyzer wenn AI_PROVIDER=mock', function () {
    config(['ai.provider' => 'mock']);

    $analyzer = app(AiAnalyzerInterface::class);

    expect($analyzer)->toBeInstanceOf(MockAiAnalyzer::class);
    expect($analyzer->getProviderName())->toBe('mock');
});

it('bindet GeminiAiAnalyzer wenn AI_PROVIDER=gemini', function () {
    config(['ai.provider' => 'gemini']);

    $analyzer = app(AiAnalyzerInterface::class);

    expect($analyzer)->toBeInstanceOf(GeminiAiAnalyzer::class);
    expect($analyzer->getProviderName())->toBe('gemini');
});

it('wirft Exception bei unbekanntem Provider', function () {
    config(['ai.provider' => 'unknown']);

    expect(fn () => app(AiAnalyzerInterface::class))
        ->toThrow(\InvalidArgumentException::class, 'Unknown AI provider: unknown');
});

it('verwendet mock als Default wenn Provider leer/null ist', function () {
    // Explicitly set provider to null - should default to mock
    config(['ai.provider' => null]);

    $analyzer = app(AiAnalyzerInterface::class);

    // Default sollte mock sein
    expect($analyzer)->toBeInstanceOf(MockAiAnalyzer::class);
    expect($analyzer->getProviderName())->toBe('mock');
});
