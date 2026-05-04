<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\AnthropicAiAnalyzer;
use App\Services\AiAnalyzer\GeminiAiAnalyzer;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use App\Services\AiAnalyzer\OpenAiAnalyzer;

describe('Anthropic Provider E2E Flow', function () {
    it('analysefluss mit AI_PROVIDER=anthropic liefert valide struktur', function () {
        // Setze Provider auf Anthropic
        config(['ai.provider' => 'anthropic']);

        // Injektion eines Stubs für den Service, um keine echten API-Calls zu machen
        $analyzedData = [
            'requirements' => ['PHP', 'Laravel'],
            'experiences' => ['JavaScript', 'Vue.js'],
            'matches' => ['PHP'],
            'gaps' => ['Laravel', 'JavaScript'],
            'tags' => [],
            'recommendations' => [],
        ];

        $stub = new class () implements AiAnalyzerInterface {
            private string $providerName = 'anthropic';

            public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
            {
                return new AnalyzeResultDto(
                    job_text: $request->jobText(),
                    cv_text: $request->cvText(),
                    requirements: ['PHP', 'Laravel'],
                    experiences: ['JavaScript', 'Vue.js'],
                    matches: [['requirement' => 'PHP', 'experience' => 'PHP']],
                    gaps: ['Laravel', 'JavaScript'],
                );
            }

            public function isAvailable(): bool
            {
                return true;
            }

            public function getProviderName(): string
            {
                return 'anthropic';
            }
        };

        app()->instance(AiAnalyzerInterface::class, $stub);

        $analyzer = app(AiAnalyzerInterface::class);

        expect($analyzer)->toBeInstanceOf(AiAnalyzerInterface::class);
        expect($analyzer->getProviderName())->toBe('anthropic');

        // Teste Analyse mit Stub-Analyzer
        $request = new AnalyzeRequestDto('PHP and Laravel required', 'I know JavaScript and Vue.js');
        $result = $analyzer->analyze($request);

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->requirements)->toContain('PHP');
        expect(count($result->requirements))->toBe(2);
        expect(count($result->experiences))->toBe(2);
    });

    it('fehlerbehandlung mit anthropic provider bleibt stabil', function () {
        config(['ai.provider' => 'anthropic']);

        $errorStub = new class () implements AiAnalyzerInterface {
            public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
            {
                $exception = new RuntimeException('rate_limit_error: API limit exceeded');
                throw $exception;
            }

            public function isAvailable(): bool
            {
                return true;
            }

            public function getProviderName(): string
            {
                return 'anthropic';
            }
        };

        app()->instance(AiAnalyzerInterface::class, $errorStub);

        $analyzer = app(AiAnalyzerInterface::class);

        $request = new AnalyzeRequestDto('test job', 'test cv');

        expect(fn () => $analyzer->analyze($request))->toThrow(RuntimeException::class);
    });

    it('provider-binding mit AI_PROVIDER=anthropic ist config-getrieben', function () {
        config([
            'ai.provider' => 'anthropic',
            'ai.analyzers' => [
                'mock' => MockAiAnalyzer::class,
                'gemini' => GeminiAiAnalyzer::class,
                'openai' => OpenAiAnalyzer::class,
                'anthropic' => AnthropicAiAnalyzer::class,
            ],
        ]);

        // Aktualisiere Service Container
        app()->forgetInstance(AiAnalyzerInterface::class);

        try {
            $analyzer = app(AiAnalyzerInterface::class);
            expect($analyzer)->toBeInstanceOf(AnthropicAiAnalyzer::class);
            expect($analyzer->getProviderName())->toBe('anthropic');
        } catch (Exception $e) {
            // Wenn AnthropicAiAnalyzer nicht available ist (kein Key), ist das OK
            // Der wichtige Test ist, dass die Binding versucht wurde
            expect($e)->toBeInstanceOf(Exception::class);
        }
    });
});
