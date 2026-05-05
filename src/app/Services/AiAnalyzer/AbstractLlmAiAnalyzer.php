<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\Contracts\LlmProviderPluginInterface;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;

abstract class AbstractLlmAiAnalyzer implements AiAnalyzerInterface, LlmProviderPluginInterface
{
    /**
     * @var array{'retry_attempt': int, 'max_attempts': int, 'transient_classifier': string|null, 'retry_exhausted': bool}
     */
    private array $lastRetryContext = [
        'retry_attempt' => 1,
        'max_attempts' => 1,
        'transient_classifier' => null,
        'retry_exhausted' => false,
    ];

    public function __construct(
        private ValidateAiResponseAction $validateResponse,
        private ParseAiResponseAction $parseResponse,
    ) {}

    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
        $this->resetRetryContext();

        try {
            $sanitizedRequest = $this->buildSanitizedRequest($request);
            $response = $this->callAi($sanitizedRequest);
            $this->validateResponse->execute($response);

            $data = json_decode($response, true);

            if (! is_array($data)) {
                throw new \RuntimeException('JSON-Dekodierung fehlgeschlagen');
            }

            return $this->parseResponse->execute($data, $request);
        } catch (\Throwable $e) {
            $mappedException = $this->mapProviderException($e);
            $this->logError($mappedException, $request);

            return $this->buildErrorResult($request, $mappedException);
        }
    }

    abstract protected function createAnalyzer(): Analyzer;

    protected function buildSanitizedRequest(AnalyzeRequestDto $request): AnalyzeRequestDto
    {
        $sanitizedJobText = $this->sanitizeInput($request->jobText());
        $sanitizedCvText = $this->sanitizeInput($request->cvText());

        return new AnalyzeRequestDto($sanitizedJobText, $sanitizedCvText);
    }

    protected function callAi(AnalyzeRequestDto $sanitizedRequest): string
    {
        $retryEnabled = $this->isRetryEnabled();
        $maxAttempts = $retryEnabled ? $this->resolveMaxAttempts() : 1;
        $backoffMilliseconds = $this->resolveBackoffMilliseconds();

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = $this->performAiCall($sanitizedRequest);

                $this->lastRetryContext = [
                    'retry_attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'transient_classifier' => null,
                    'retry_exhausted' => false,
                ];

                return $response;
            } catch (\Throwable $exception) {
                $transientClassifier = $this->classifyTransientException($exception);

                $this->lastRetryContext = [
                    'retry_attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'transient_classifier' => $transientClassifier,
                    'retry_exhausted' => $retryEnabled && $transientClassifier !== null && $attempt >= $maxAttempts,
                ];

                if (! $retryEnabled || $transientClassifier === null || $attempt >= $maxAttempts) {
                    throw $exception;
                }

                $this->pauseBeforeRetry($attempt, $backoffMilliseconds);
            }
        }

        throw new \RuntimeException('AI-Aufruf konnte nach Wiederholungsversuchen nicht abgeschlossen werden.');
    }

    protected function performAiCall(AnalyzeRequestDto $sanitizedRequest): string
    {
        $payload = $this->buildPromptPayload($sanitizedRequest);

        /** @var StructuredAgentResponse $response */
        $response = $this->createAnalyzer()->prompt($payload);

        return $this->normalizeResponse($response);
    }

    public function buildPromptPayload(AnalyzeRequestDto $request): string
    {
        $jsonData = json_encode($request->toArray());

        if ($jsonData === false) {
            throw new \RuntimeException('JSON-Encoding fehlgeschlagen');
        }

        return $jsonData;
    }

    public function normalizeResponse(StructuredAgentResponse $response): string
    {
        $rawResponse = json_encode($response->toArray());

        if ($rawResponse === false) {
            throw new \RuntimeException('Response-Encoding fehlgeschlagen');
        }

        return $rawResponse;
    }

    public function mapProviderException(\Throwable $exception): \Throwable
    {
        return $exception;
    }

    protected function classifyTransientException(\Throwable $exception): ?string
    {
        return $this->classifyProviderTransientException($exception) ?? $this->classifyGlobalTransientException($exception);
    }

    protected function classifyProviderTransientException(\Throwable $exception): ?string
    {
        return null;
    }

    protected function sanitizeInput(string $input): string
    {
        $input = str_replace("\0", '', $input);
        $input = trim($input);
        $input = str_replace("\r\n", "\n", $input);

        return $input;
    }

    protected function logError(\Throwable $exception, AnalyzeRequestDto $request): void
    {
        Log::error('AI Analysis failed', [
            'provider' => $this->getProviderName(),
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'job_text_length' => strlen($request->jobText()),
            'cv_text_length' => strlen($request->cvText()),
            ...$this->lastRetryContext,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    protected function buildErrorResult(AnalyzeRequestDto $request, \Throwable $e): AnalyzeResultDto
    {
        $userMessage = $this->getUserFriendlyErrorMessage($e);

        return new AnalyzeResultDto(
            $request->jobText(),
            $request->cvText(),
            [],
            [],
            [],
            [],
            $userMessage,
            null
        );
    }

    protected function getUserFriendlyErrorMessage(\Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'timeout')) {
            return 'Die KI-Analyse hat zu lange gedauert (Timeout). Bitte versuchen Sie es später erneut.';
        }

        if (str_contains($message, 'json')) {
            return 'Die KI-Antwort war ungültig. Bitte versuchen Sie es erneut.';
        }

        if (str_contains($message, 'connection') || str_contains($message, 'network')) {
            return 'Netzwerkfehler bei der Verbindung zur KI. Bitte prüfen Sie Ihre Internetverbindung.';
        }

        if (str_contains($message, 'rate limit') || str_contains($message, '429')) {
            return 'Die KI ist momentan ausgelastet. Bitte versuchen Sie es in Kürze erneut.';
        }

        if (str_contains($message, 'overloaded') || str_contains($message, 'überlastet')) {
            return 'Die KI ist momentan überlastet. Bitte versuchen Sie es später erneut.';
        }

        if (str_contains($message, 'api')) {
            return 'Die KI-API antwortet nicht. Bitte versuchen Sie es später erneut.';
        }

        return 'Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }

    protected function pauseBeforeRetry(int $attempt, int $backoffMilliseconds): void
    {
        if ($backoffMilliseconds <= 0) {
            return;
        }

        usleep($backoffMilliseconds * 1000);
    }

    private function isRetryEnabled(): bool
    {
        return (bool) config('ai.retry.enabled', true);
    }

    private function resolveMaxAttempts(): int
    {
        $configuredMaxAttempts = config('ai.retry.max_attempts', 2);

        if (is_int($configuredMaxAttempts)) {
            return max(1, $configuredMaxAttempts);
        }

        if (is_numeric($configuredMaxAttempts)) {
            return max(1, (int) $configuredMaxAttempts);
        }

        return 2;
    }

    private function resolveBackoffMilliseconds(): int
    {
        $configuredBackoff = config('ai.retry.backoff_ms', 150);

        if (is_int($configuredBackoff)) {
            return max(0, $configuredBackoff);
        }

        if (is_numeric($configuredBackoff)) {
            return max(0, (int) $configuredBackoff);
        }

        return 150;
    }

    private function resetRetryContext(): void
    {
        $maxAttempts = $this->isRetryEnabled() ? $this->resolveMaxAttempts() : 1;

        $this->lastRetryContext = [
            'retry_attempt' => 1,
            'max_attempts' => $maxAttempts,
            'transient_classifier' => null,
            'retry_exhausted' => false,
        ];
    }

    private function classifyGlobalTransientException(\Throwable $exception): ?string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'timeout')) {
            return 'global:timeout';
        }

        if (str_contains($message, '429') || str_contains($message, 'rate limit') || str_contains($message, 'too many requests')) {
            return 'global:rate_limit';
        }

        if (str_contains($message, 'overloaded')) {
            return 'global:overloaded';
        }

        if (str_contains($message, 'connection')) {
            return 'global:connection';
        }

        if (str_contains($message, 'network')) {
            return 'global:network';
        }

        return null;
    }
}
