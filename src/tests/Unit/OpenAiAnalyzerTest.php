<?php

declare(strict_types=1);

use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use App\Services\AiAnalyzer\OpenAiAnalyzer;

describe('OpenAiAnalyzer', function () {
    function openAiAnalyzer(): OpenAiAnalyzer
    {
        return new class (new ValidateAiResponseAction(), new ParseAiResponseAction()) extends OpenAiAnalyzer {
            public function exposedMapProviderException(Throwable $exception): Throwable
            {
                return $this->mapProviderException($exception);
            }
        };
    }

    test('getProviderName liefert openai', function () {
        expect(openAiAnalyzer()->getProviderName())->toBe('openai');
    });

    test('isAvailable ist true wenn API-Key gesetzt ist', function () {
        config(['ai.providers.openai.key' => 'test-key']);

        expect(openAiAnalyzer()->isAvailable())->toBeTrue();
    });

    test('isAvailable ist false wenn API-Key leer ist', function () {
        config(['ai.providers.openai.key' => '']);

        expect(openAiAnalyzer()->isAvailable())->toBeFalse();
    });

    test('mapProviderException mappt openai rate-limit meldungen provider-spezifisch', function () {
        $mapped = openAiAnalyzer()->exposedMapProviderException(new RuntimeException('HTTP 429 rate limit'));

        expect($mapped)->toBeInstanceOf(RuntimeException::class);
        expect($mapped->getMessage())->toContain('OpenAI API rate limit erreicht');
    });
});
