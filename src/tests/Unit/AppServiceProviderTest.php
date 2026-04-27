<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;

describe('AppServiceProvider', function () {
    test('bindet MockAiAnalyzer wenn provider=mock', function () {
        $app = app();
        config(['ai.provider' => 'mock']);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);

        expect($instance)->toBeInstanceOf(MockAiAnalyzer::class);
    });

    test('bindet GeminiAiAnalyzer wenn provider=gemini', function () {
        $app = app();
        config(['ai.provider' => 'gemini']);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);

        expect($instance)->toBeInstanceOf(GeminiAiAnalyzer::class);
    });

    test('wirft Exception bei ungueltigem provider-String', function () {
        $app = app();
        config(['ai.provider' => 'invalid']);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'Unknown AI provider: invalid. Available: mock, gemini');
    });

    test('wirft Exception wenn provider kein String ist', function () {
        $app = app();
        config(['ai.provider' => ['not-a-string']]);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'AI provider configuration must be a string.');
    });

    test('nutzt mock als default wenn provider null ist', function () {
        $app = app();
        config(['ai.provider' => null]);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);

        expect($instance)->toBeInstanceOf(MockAiAnalyzer::class);
    });

    test('fehlermeldung nennt alle verfuegbaren provider dynamisch aus dem registry', function () {
        $app = app();
        config([
            'ai.provider'   => 'unknown',
            'ai.analyzers'  => [
                'mock'   => MockAiAnalyzer::class,
                'gemini' => GeminiAiAnalyzer::class,
            ],
        ]);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'Available: mock, gemini');
    });

    test('bindet neuen provider wenn analyzer-registry um eintrag erweitert wird', function () {
        $app = app();
        config([
            'ai.provider'  => 'mock',
            'ai.analyzers' => [
                'mock' => MockAiAnalyzer::class,
            ],
        ]);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);
        expect($instance)->toBeInstanceOf(MockAiAnalyzer::class);
    });
});
