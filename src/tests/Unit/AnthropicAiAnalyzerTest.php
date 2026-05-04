<?php

declare(strict_types=1);

use App\Services\AiAnalyzer\AnthropicAiAnalyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use App\Ai\Agents\Analyzer;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\Contracts\LlmProviderPluginInterface;

describe('AnthropicAiAnalyzer', function () {
    function anthropicAnalyzer(): AnthropicAiAnalyzer
    {
        return new AnthropicAiAnalyzer(
            new ValidateAiResponseAction(),
            new ParseAiResponseAction()
        );
    }

    describe('Provider Identity', function () {
        it('getProviderName liefert anthropic', function () {
            $analyzer = anthropicAnalyzer();
            expect($analyzer->getProviderName())->toBe('anthropic');
        });
    });

    describe('Availability Checks', function () {
        it('isAvailable ist true wenn API-Key gesetzt ist', function () {
            config(['ai.providers.anthropic.key' => 'test-key-12345']);

            $analyzer = anthropicAnalyzer();
            expect($analyzer->isAvailable())->toBeTrue();
        });

        it('isAvailable ist false wenn API-Key leer ist', function () {
            config(['ai.providers.anthropic.key' => '']);

            $analyzer = anthropicAnalyzer();
            expect($analyzer->isAvailable())->toBeFalse();
        });

        it('isAvailable ist false wenn API-Key null ist', function () {
            config(['ai.providers.anthropic.key' => null]);

            $analyzer = anthropicAnalyzer();
            expect($analyzer->isAvailable())->toBeFalse();
        });
    });

    describe('Exception Mapping - Anthropic Spezifisch', function () {
        it('mapProviderException mappt rate_limit meldungen provider-spezifisch', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('rate_limit_error: API rate limit exceeded');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped)->toBeInstanceOf(RuntimeException::class);
            expect($mapped->getMessage())->toContain('rate limit');
            expect($mapped->getMessage())->toContain('Anthropic');
        });

        it('mapProviderException mappt 429 HTTP-Status auf rate limit', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('HTTP 429: too many requests');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('rate limit');
            expect($mapped->getMessage())->toContain('Anthropic');
        });

        it('mapProviderException mappt overloaded_error', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('overloaded_error: service temporarily unavailable');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('überlastet');
        });

        it('mapProviderException mappt authentication_error', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('authentication_error: invalid API key');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('Authentifizierung');
        });

        it('mapProviderException mappt unauthorized', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('unauthorized: cannot authenticate request');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('Authentifizierung');
        });

        it('mapProviderException mappt invalid_request_error', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('invalid_request_error: bad parameter');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('Ungültige Anfrage');
        });

        it('mapProviderException mappt insufficient_tokens', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('invalid_request_error: insufficient_tokens');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('zu lang');
        });

        it('mapProviderException mappt context_length', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('error: context_length_exceeded');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped->getMessage())->toContain('zu lang');
        });

        it('mapProviderException gibt bei nicht-anthropic-fehler die originale exception zurueck', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('some unknown error');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped)->toBe($exception);
        });

        it('mapProviderException wirft keine exception bei mapping', function () {
            $analyzer = anthropicAnalyzer();

            $exception = new RuntimeException('rate_limit_error: test');
            $mapped = $analyzer->mapProviderException($exception);

            expect($mapped)->toBeInstanceOf(Throwable::class);
        });
    });

    describe('Analyzer Creation', function () {
        it('createAnalyzer gibt Analyzer-Instanz zurueck', function () {
            $analyzer = anthropicAnalyzer();
            $createAnalyzer = new ReflectionMethod($analyzer, 'createAnalyzer');
            $createAnalyzer->setAccessible(true);

            $result = $createAnalyzer->invoke($analyzer);

            expect($result)->toBeInstanceOf(Analyzer::class);
        });
    });

    describe('Inheritance Contract', function () {
        it('AnthropicAiAnalyzer implements AiAnalyzerInterface', function () {
            $analyzer = anthropicAnalyzer();
            expect($analyzer)->toBeInstanceOf(AiAnalyzerInterface::class);
        });

        it('AnthropicAiAnalyzer implements LlmProviderPluginInterface', function () {
            $analyzer = anthropicAnalyzer();
            expect($analyzer)->toBeInstanceOf(LlmProviderPluginInterface::class);
        });
    });
});
