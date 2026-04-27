<?php

declare(strict_types=1);

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\AbstractLlmAiAnalyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use Illuminate\Support\Facades\Log;

function fakeLlmAnalyzer(): AbstractLlmAiAnalyzer
{
    return new class (new ValidateAiResponseAction(), new ParseAiResponseAction()) extends AbstractLlmAiAnalyzer {
        public function isAvailable(): bool
        {
            return true;
        }

        public function getProviderName(): string
        {
            return 'fake-provider';
        }

        public function exposedBuildSanitizedRequest(AnalyzeRequestDto $request): AnalyzeRequestDto
        {
            return $this->buildSanitizedRequest($request);
        }

        public function exposedLogError(Throwable $exception, AnalyzeRequestDto $request): void
        {
            $this->logError($exception, $request);
        }

        protected function createAnalyzer(): Analyzer
        {
            return new Analyzer();
        }
    };
}

describe('AbstractLlmAiAnalyzer', function () {
    test('buildSanitizedRequest sanitisiert geerbte Eingaben provider-agnostisch', function () {
        $target = fakeLlmAnalyzer();

        $request = new AnalyzeRequestDto("  job\0\r\n ", " cv\0\r\n ");
        $sanitizedRequest = $target->exposedBuildSanitizedRequest($request);

        expect($sanitizedRequest->jobText())->toBe('job');
        expect($sanitizedRequest->cvText())->toBe('cv');
    });

    test('logError verwendet den Provider-Namen der konkreten Implementierung', function () {
        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe('AI Analysis failed');
            expect($context['provider'])->toBe('fake-provider');

            return true;
        });

        $target = fakeLlmAnalyzer();
        $target->exposedLogError(new RuntimeException('boom'), new AnalyzeRequestDto('job', 'cv'));
    });
});
