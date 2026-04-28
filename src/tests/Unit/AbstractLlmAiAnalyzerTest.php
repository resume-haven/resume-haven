<?php

declare(strict_types=1);

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
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

function fakeLlmAnalyzerForAnalyze(
    string $response,
    ?Throwable $exception = null,
    ?ValidateAiResponseAction $validateAction = null,
    ?Throwable $mappedException = null,
): AbstractLlmAiAnalyzer {
    $validateAction ??= new ValidateAiResponseAction();

    return new class ($validateAction, new ParseAiResponseAction(), $response, $exception, $mappedException) extends AbstractLlmAiAnalyzer {
        public function __construct(
            ValidateAiResponseAction $validateResponse,
            ParseAiResponseAction $parseResponse,
            private string $response,
            private ?Throwable $exception,
            private ?Throwable $mappedException,
        ) {
            parent::__construct($validateResponse, $parseResponse);
        }

        public function isAvailable(): bool
        {
            return true;
        }

        public function getProviderName(): string
        {
            return 'fake-provider';
        }

        protected function callAi(AnalyzeRequestDto $sanitizedRequest): string
        {
            if ($this->exception !== null) {
                throw $this->exception;
            }

            return $this->response;
        }

        protected function createAnalyzer(): Analyzer
        {
            return new Analyzer();
        }

        public function mapProviderException(Throwable $exception): Throwable
        {
            return $this->mappedException ?? $exception;
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

    test('analyze liefert strukturiertes Ergebnis im happy path', function () {
        $target = fakeLlmAnalyzerForAnalyze(json_encode([
            'requirements' => ['PHP'],
            'experiences' => ['Laravel'],
            'matches' => [
                ['requirement' => 'PHP', 'experience' => 'Laravel'],
            ],
            'gaps' => [],
            'tags' => ['matches' => [], 'gaps' => []],
            'recommendations' => [],
        ], JSON_THROW_ON_ERROR));

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->toBeNull();
        expect($result->requirements)->toBe(['PHP']);
        expect($result->experiences)->toBe(['Laravel']);
    });

    test('analyze mappt timeout exception auf benutzerfreundliche Fehlermeldung', function () {
        Log::shouldReceive('error')->once();

        $target = fakeLlmAnalyzerForAnalyze(
            response: '{}',
            exception: new RuntimeException('gateway timeout'),
        );

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect((string) $result->error)->toContain('Timeout');
        expect($result->requirements)->toBe([]);
    });

    test('analyze behandelt json decode ohne array als fehlerpfad', function () {
        Log::shouldReceive('error')->once();

        $noOpValidate = new class () extends ValidateAiResponseAction {
            public function execute(string $rawResponse): void {}
        };

        $target = fakeLlmAnalyzerForAnalyze(
            response: '123',
            validateAction: $noOpValidate,
        );

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect((string) $result->error)->toContain('ungültig');
        expect($result->experiences)->toBe([]);
    });

    test('analyze verwendet provider-spezifisches Exception-Mapping', function () {
        Log::shouldReceive('error')->once();

        $target = fakeLlmAnalyzerForAnalyze(
            response: '{}',
            exception: new RuntimeException('raw timeout'),
            mappedException: new RuntimeException('api mapped provider error'),
        );

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect((string) $result->error)->toContain('KI-API');
    });
});
