<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Listeners\AutoClaimResumesListener;
use App\Models\StoredResume;
use App\Policies\ProfilePolicy;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\AnthropicAiAnalyzer;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use App\Services\AiAnalyzer\OpenAiAnalyzer;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;

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

    test('bindet OpenAiAnalyzer wenn provider=openai', function () {
        $app = app();
        config(['ai.provider' => 'openai']);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);

        expect($instance)->toBeInstanceOf(OpenAiAnalyzer::class);
    });

    test('bindet AnthropicAiAnalyzer wenn provider=anthropic', function () {
        $app = app();
        config(['ai.provider' => 'anthropic']);

        (new AppServiceProvider($app))->register();

        $instance = $app->make(AiAnalyzerInterface::class);

        expect($instance)->toBeInstanceOf(AnthropicAiAnalyzer::class);
    });

    test('wirft Exception bei ungueltigem provider-String', function () {
        $app = app();
        config(['ai.provider' => 'invalid']);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'Unknown AI provider: invalid. Available: mock, gemini, openai, anthropic');
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

    test('wirft Exception wenn ai.analyzers keine array-konfiguration ist', function () {
        $app = app();
        config([
            'ai.provider' => 'mock',
            'ai.analyzers' => 'invalid',
        ]);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'AI analyzers configuration must be an array.');
    });

    test('wirft Exception wenn analyzer-klasse das interface nicht implementiert', function () {
        $app = app();
        config([
            'ai.provider' => 'bad',
            'ai.analyzers' => [
                'bad' => stdClass::class,
            ],
        ]);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'AI analyzer class must implement '.AiAnalyzerInterface::class.'.');
    });

    test('wirft Exception wenn analyzer-klasse kein String ist', function () {
        $app = app();
        config([
            'ai.provider' => 'bad',
            'ai.analyzers' => [
                'bad' => ['not-a-class-string'],
            ],
        ]);

        (new AppServiceProvider($app))->register();

        expect(fn () => $app->make(AiAnalyzerInterface::class))
            ->toThrow(InvalidArgumentException::class, 'AI analyzer class must be a string.');
    });

    test('boot registriert login-listener und policy', function () {
        $app = app();

        Event::shouldReceive('listen')
            ->once()
            ->with(Login::class, AutoClaimResumesListener::class);

        Gate::shouldReceive('policy')
            ->once()
            ->with(StoredResume::class, ProfilePolicy::class);

        (new AppServiceProvider($app))->boot();
    });
});
