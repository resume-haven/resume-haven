<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use Illuminate\Support\Facades\Log;

describe('GeminiAiAnalyzer', function () {
    function analyzer(): GeminiAiAnalyzer
    {
        return new GeminiAiAnalyzer(
            new ValidateAiResponseAction(),
            new ParseAiResponseAction(),
        );
    }

    test('getProviderName liefert gemini', function () {
        expect(analyzer()->getProviderName())->toBe('gemini');
    });

    test('isAvailable ist true wenn API-Key gesetzt ist', function () {
        config(['ai.gemini.api_key' => 'test-key']);

        expect(analyzer()->isAvailable())->toBeTrue();
    });

    test('isAvailable ist false wenn API-Key leer ist', function () {
        config(['ai.gemini.api_key' => '']);

        expect(analyzer()->isAvailable())->toBeFalse();
    });

    test('sanitizeInput entfernt Null-Bytes, trimmt und normalisiert Zeilenumbrüche', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'sanitizeInput');
        $method->setAccessible(true);

        $raw = "  foo\0bar\r\nline2  ";
        $sanitized = $method->invoke($target, $raw);

        expect($sanitized)->toBe("foobar\nline2");
    });

    test('buildSanitizedRequest sanitiziert beide Eingaben', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'buildSanitizedRequest');
        $method->setAccessible(true);

        $request = new AnalyzeRequestDto("  job\0\r\n ", " cv\0\r\n ");
        $sanitizedRequest = $method->invoke($target, $request);

        expect($sanitizedRequest)->toBeInstanceOf(AnalyzeRequestDto::class);
        expect($sanitizedRequest->jobText())->toBe('job');
        expect($sanitizedRequest->cvText())->toBe('cv');
    });

    test('getUserFriendlyErrorMessage mappt timeout', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'getUserFriendlyErrorMessage');
        $method->setAccessible(true);

        $msg = $method->invoke($target, new RuntimeException('Request timeout while calling api'));

        expect($msg)->toContain('Timeout');
    });

    test('getUserFriendlyErrorMessage mappt json', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'getUserFriendlyErrorMessage');
        $method->setAccessible(true);

        $msg = $method->invoke($target, new RuntimeException('json parse error'));

        expect($msg)->toContain('ungültig');
    });

    test('getUserFriendlyErrorMessage mappt connection und network', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'getUserFriendlyErrorMessage');
        $method->setAccessible(true);

        $msg1 = $method->invoke($target, new RuntimeException('connection refused'));
        $msg2 = $method->invoke($target, new RuntimeException('network interrupted'));

        expect($msg1)->toContain('Netzwerkfehler');
        expect($msg2)->toContain('Netzwerkfehler');
    });

    test('getUserFriendlyErrorMessage mappt api und default', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'getUserFriendlyErrorMessage');
        $method->setAccessible(true);

        $apiMsg = $method->invoke($target, new RuntimeException('api unavailable'));
        $defaultMsg = $method->invoke($target, new RuntimeException('unexpected failure'));

        expect($apiMsg)->toContain('KI-API');
        expect($defaultMsg)->toBe('Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.');
    });

    test('buildErrorResult baut leeres Ergebnis mit Fehlermeldung', function () {
        $target = analyzer();
        $method = new ReflectionMethod($target, 'buildErrorResult');
        $method->setAccessible(true);

        $request = new AnalyzeRequestDto('job', 'cv');
        $result = $method->invoke($target, $request, new RuntimeException('api down'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->job_text)->toBe('job');
        expect($result->cv_text)->toBe('cv');
        expect($result->requirements)->toBe([]);
        expect($result->matches)->toBe([]);
        expect($result->error)->toContain('KI-API');
    });

    test('logError schreibt erwarteten Kontext', function () {
        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe('AI Analysis failed');
            expect($context['provider'])->toBe('gemini');
            expect($context)->toHaveKey('exception_class');
            expect($context)->toHaveKey('exception_message');
            expect($context['job_text_length'])->toBe(3);
            expect($context['cv_text_length'])->toBe(2);
            expect($context)->toHaveKey('timestamp');

            return true;
        });

        $target = analyzer();
        $method = new ReflectionMethod($target, 'logError');
        $method->setAccessible(true);

        $method->invoke($target, new RuntimeException('boom'), new AnalyzeRequestDto('job', 'cv'));
    });

    test('analyze faengt Fehler ab und gibt Error-DTO zurueck', function () {
        // In manchen Umgebungen triggert dieser Input JSON-Fehler, in anderen den API-Fehlerpfad.
        $invalidUtf8 = "\xB1\x31";

        $request = new AnalyzeRequestDto($invalidUtf8, 'cv');

        Log::shouldReceive('error')->once();

        $result = analyzer()->analyze($request);

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->not()->toBeNull();

        $errorMessage = (string) $result->error;
        expect(
            str_contains($errorMessage, 'ungültig') || str_contains($errorMessage, 'KI-API')
        )->toBeTrue();

        expect($result->requirements)->toBe([]);
        expect($result->experiences)->toBe([]);
    });
});
