<?php

declare(strict_types=1);

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\AbstractLlmAiAnalyzer;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;

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

        public function exposedBuildPromptPayload(AnalyzeRequestDto $request): string
        {
            return $this->buildPromptPayload($request);
        }

        public function exposedMapProviderException(Throwable $exception): Throwable
        {
            return $this->mapProviderException($exception);
        }

        public function exposedGetUserFriendlyErrorMessage(Throwable $exception): string
        {
            return $this->getUserFriendlyErrorMessage($exception);
        }

        public function exposedClassifyTransientException(Throwable $exception): ?string
        {
            return $this->classifyTransientException($exception);
        }

        public function exposedPauseBeforeRetry(int $attempt, int $backoffMilliseconds): void
        {
            $this->pauseBeforeRetry($attempt, $backoffMilliseconds);
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

        protected function performAiCall(AnalyzeRequestDto $sanitizedRequest): string
        {
            if ($this->exception !== null) {
                throw $this->exception;
            }

            return $this->response;
        }

        protected function pauseBeforeRetry(int $attempt, int $backoffMilliseconds): void {}

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

function fakeLlmAnalyzerForRetrySequence(array $attemptOutcomes)
{
    return new class (new ValidateAiResponseAction(), new ParseAiResponseAction(), $attemptOutcomes) extends AbstractLlmAiAnalyzer {
        /**
         * @param array<int, Throwable|string> $attemptOutcomes
         */
        public function __construct(
            ValidateAiResponseAction $validateResponse,
            ParseAiResponseAction $parseResponse,
            private array $attemptOutcomes,
        ) {
            parent::__construct($validateResponse, $parseResponse);
        }

        /**
         * @var list<int>
         */
        private array $pauseHistory = [];

        private int $attemptCount = 0;

        public function isAvailable(): bool
        {
            return true;
        }

        public function getProviderName(): string
        {
            return 'fake-provider';
        }

        /**
         * @return list<int>
         */
        public function exposedPauseHistory(): array
        {
            return $this->pauseHistory;
        }

        public function exposedAttemptCount(): int
        {
            return $this->attemptCount;
        }

        protected function performAiCall(AnalyzeRequestDto $sanitizedRequest): string
        {
            $outcome = $this->attemptOutcomes[$this->attemptCount] ?? end($this->attemptOutcomes);
            $this->attemptCount++;

            if ($outcome instanceof Throwable) {
                throw $outcome;
            }

            return (string) $outcome;
        }

        protected function pauseBeforeRetry(int $attempt, int $backoffMilliseconds): void
        {
            $this->pauseHistory[] = $backoffMilliseconds;
        }

        protected function createAnalyzer(): Analyzer
        {
            return new Analyzer();
        }
    };
}

function fakeLlmAnalyzerWithInjectedAnalyzer(Analyzer $analyzer): AbstractLlmAiAnalyzer
{
    return new class (new ValidateAiResponseAction(), new ParseAiResponseAction(), $analyzer) extends AbstractLlmAiAnalyzer {
        public function __construct(
            ValidateAiResponseAction $validateResponse,
            ParseAiResponseAction $parseResponse,
            private Analyzer $analyzer,
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

        protected function createAnalyzer(): Analyzer
        {
            return $this->analyzer;
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
            expect($context['retry_attempt'])->toBe(1);
            expect($context['max_attempts'])->toBe(1);
            expect($context['transient_classifier'])->toBeNull();
            expect($context['retry_exhausted'])->toBeFalse();

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

    test('analyze nutzt performAiCall und normalizeResponse ueber createAnalyzer im happy path', function () {
        $responseData = [
            'requirements' => ['PHP'],
            'experiences' => ['Laravel'],
            'matches' => [
                ['requirement' => 'PHP', 'experience' => 'Laravel'],
            ],
            'gaps' => [],
            'tags' => ['matches' => [], 'gaps' => []],
            'recommendations' => [],
        ];

        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn($responseData);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->withArgs(function (string $payload): bool {
            $decodedPayload = json_decode($payload, true);

            expect($decodedPayload)->toBe([
                'job_text' => "Jobtitel\nmit Zeilenumbruch",
                'cv_text' => 'CV mit Nullbyte',
            ]);

            return true;
        })->andReturn($mockResponse);

        $target = fakeLlmAnalyzerWithInjectedAnalyzer($mockAnalyzer);

        $result = $target->analyze(new AnalyzeRequestDto("  Jobtitel\r\nmit Zeilenumbruch  ", "\0CV mit Nullbyte\0  "));

        expect($result->error)->toBeNull();
        expect($result->requirements)->toBe(['PHP']);
        expect($result->experiences)->toBe(['Laravel']);
    });

    test('analyze behandelt normalizeResponse encoding-fehler als fehlerpfad', function () {
        Log::shouldReceive('error')->once();

        $mockResponse = Mockery::mock(StructuredAgentResponse::class);
        $mockResponse->shouldReceive('toArray')->once()->andReturn(['broken' => "\xB1\x31"]);

        $mockAnalyzer = Mockery::mock(Analyzer::class);
        $mockAnalyzer->shouldReceive('prompt')->once()->andReturn($mockResponse);

        $target = fakeLlmAnalyzerWithInjectedAnalyzer($mockAnalyzer);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect((string) $result->error)->toBe('Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.');
    });

    test('analyze retryt bei transientem fehler und liefert beim zweiten versuch ein ergebnis', function () {
        config([
            'ai.retry.enabled' => true,
            'ai.retry.max_attempts' => 2,
            'ai.retry.backoff_ms' => 150,
        ]);

        $target = fakeLlmAnalyzerForRetrySequence([
            new RuntimeException('gateway timeout'),
            json_encode([
                'requirements' => ['PHP'],
                'experiences' => ['Laravel'],
                'matches' => [
                    ['requirement' => 'PHP', 'experience' => 'Laravel'],
                ],
                'gaps' => [],
                'tags' => ['matches' => [], 'gaps' => []],
                'recommendations' => [],
            ], JSON_THROW_ON_ERROR),
        ]);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($result->error)->toBeNull();
        expect($target->exposedAttemptCount())->toBe(2);
        expect($target->exposedPauseHistory())->toBe([150]);
    });

    test('analyze verwendet numerische string-config fuer retries und backoff', function () {
        config([
            'ai.retry.enabled' => true,
            'ai.retry.max_attempts' => '3',
            'ai.retry.backoff_ms' => '7',
        ]);

        $target = fakeLlmAnalyzerForRetrySequence([
            new RuntimeException('gateway timeout'),
            new RuntimeException('gateway timeout'),
            json_encode([
                'requirements' => ['PHP'],
                'experiences' => ['Laravel'],
                'matches' => [
                    ['requirement' => 'PHP', 'experience' => 'Laravel'],
                ],
                'gaps' => [],
                'tags' => ['matches' => [], 'gaps' => []],
                'recommendations' => [],
            ], JSON_THROW_ON_ERROR),
        ]);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result->error)->toBeNull();
        expect($target->exposedAttemptCount())->toBe(3);
        expect($target->exposedPauseHistory())->toBe([7, 7]);
    });

    test('analyze bricht bei nicht transientem fehler sofort ab und loggt retry-metadaten', function () {
        config([
            'ai.retry.enabled' => true,
            'ai.retry.max_attempts' => 2,
            'ai.retry.backoff_ms' => 150,
        ]);

        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe('AI Analysis failed');
            expect($context['retry_attempt'])->toBe(1);
            expect($context['max_attempts'])->toBe(2);
            expect($context['transient_classifier'])->toBeNull();
            expect($context['retry_exhausted'])->toBeFalse();

            return true;
        });

        $target = fakeLlmAnalyzerForRetrySequence([
            new RuntimeException('validation failed'),
        ]);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($target->exposedAttemptCount())->toBe(1);
        expect($target->exposedPauseHistory())->toBe([]);
        expect((string) $result->error)->toBe('Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.');
    });

    test('analyze respektiert retry deaktiviert als rollback-pfad', function () {
        config([
            'ai.retry.enabled' => false,
            'ai.retry.max_attempts' => 5,
            'ai.retry.backoff_ms' => 150,
        ]);

        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe('AI Analysis failed');
            expect($context['retry_attempt'])->toBe(1);
            expect($context['max_attempts'])->toBe(1);
            expect($context['transient_classifier'])->toBe('global:timeout');
            expect($context['retry_exhausted'])->toBeFalse();

            return true;
        });

        $target = fakeLlmAnalyzerForRetrySequence([
            new RuntimeException('gateway timeout'),
        ]);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($target->exposedAttemptCount())->toBe(1);
        expect($target->exposedPauseHistory())->toBe([]);
        expect((string) $result->error)->toContain('Timeout');
    });

    test('analyze faellt bei ungueltiger retry-config auf defaults zurueck und markiert retry exhausted', function () {
        config([
            'ai.retry.enabled' => true,
            'ai.retry.max_attempts' => ['ungueltig'],
            'ai.retry.backoff_ms' => ['ungueltig'],
        ]);

        Log::shouldReceive('error')->once()->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe('AI Analysis failed');
            expect($context['retry_attempt'])->toBe(2);
            expect($context['max_attempts'])->toBe(2);
            expect($context['transient_classifier'])->toBe('global:rate_limit');
            expect($context['retry_exhausted'])->toBeTrue();

            return true;
        });

        $target = fakeLlmAnalyzerForRetrySequence([
            new RuntimeException('HTTP 429 rate limit'),
            new RuntimeException('HTTP 429 rate limit'),
        ]);

        $result = $target->analyze(new AnalyzeRequestDto('job', 'cv'));

        expect($result)->toBeInstanceOf(AnalyzeResultDto::class);
        expect($target->exposedAttemptCount())->toBe(2);
        expect($target->exposedPauseHistory())->toBe([150]);
        expect((string) $result->error)->toContain('ausgelastet');
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

    test('buildPromptPayload wirft exception bei json-encoding fehler', function () {
        $target = fakeLlmAnalyzer();

        $request = new AnalyzeRequestDto("\xB1\x31", 'cv');

        expect(fn () => $target->exposedBuildPromptPayload($request))
            ->toThrow(RuntimeException::class, 'JSON-Encoding fehlgeschlagen');
    });

    test('mapProviderException gibt im default die originale exception zurueck', function () {
        $target = fakeLlmAnalyzer();
        $exception = new RuntimeException('keine provider-spezifische map');

        expect($target->exposedMapProviderException($exception))->toBe($exception);
    });

    test('getUserFriendlyErrorMessage mappt connection und network auf netzwerkfehler', function () {
        $target = fakeLlmAnalyzer();

        $connectionMessage = $target->exposedGetUserFriendlyErrorMessage(new RuntimeException('connection reset by peer'));
        $networkMessage = $target->exposedGetUserFriendlyErrorMessage(new RuntimeException('network unreachable while sending'));

        expect($connectionMessage)->toContain('Netzwerkfehler');
        expect($networkMessage)->toContain('Netzwerkfehler');
    });

    test('getUserFriendlyErrorMessage mappt rate limit auf ausgelastete ki', function () {
        $target = fakeLlmAnalyzer();

        $message = $target->exposedGetUserFriendlyErrorMessage(new RuntimeException('HTTP 429 rate limit'));

        expect($message)->toContain('ausgelastet');
    });

    test('getUserFriendlyErrorMessage mappt overloaded auf ueberlastete ki', function () {
        $target = fakeLlmAnalyzer();

        $message = $target->exposedGetUserFriendlyErrorMessage(new RuntimeException('service overloaded'));

        expect($message)->toContain('überlastet');
    });

    test('pauseBeforeRetry ignoriert nicht positive backoff-werte und wartet bei positiven werten', function () {
        $target = fakeLlmAnalyzer();

        $startedWithoutBackoff = hrtime(true);
        $target->exposedPauseBeforeRetry(1, 0);
        $elapsedWithoutBackoff = hrtime(true) - $startedWithoutBackoff;

        $startedWithBackoff = hrtime(true);
        $target->exposedPauseBeforeRetry(1, 5);
        $elapsedWithBackoff = hrtime(true) - $startedWithBackoff;

        expect($elapsedWithoutBackoff)->toBeGreaterThanOrEqual(0);
        expect($elapsedWithBackoff)->toBeGreaterThan(1_000_000);
    });

    test('classifyTransientException nutzt globalen fallback fuer timeout', function () {
        $target = fakeLlmAnalyzer();

        expect($target->exposedClassifyTransientException(new RuntimeException('gateway timeout')))
            ->toBe('global:timeout');
    });

    test('classifyTransientException erkennt globale rate-limit und overloaded fallback-pfade', function () {
        $target = fakeLlmAnalyzer();

        expect($target->exposedClassifyTransientException(new RuntimeException('too many requests')))
            ->toBe('global:rate_limit');
        expect($target->exposedClassifyTransientException(new RuntimeException('service overloaded')))
            ->toBe('global:overloaded');
    });

    test('classifyTransientException erkennt connection, network und null-fallback', function () {
        $target = fakeLlmAnalyzer();

        expect($target->exposedClassifyTransientException(new RuntimeException('connection reset by peer')))
            ->toBe('global:connection');
        expect($target->exposedClassifyTransientException(new RuntimeException('network unreachable')))
            ->toBe('global:network');
        expect($target->exposedClassifyTransientException(new RuntimeException('not transient at all')))
            ->toBeNull();
    });
});
