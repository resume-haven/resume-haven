<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;
use App\Services\AiAnalyzer\Actions\ValidateAiResponseAction;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;

/**
 * Gemini AI Analyzer - Production Implementation
 *
 * Verwendet Laravel AI Package mit Gemini
 * Delegiert Response-Validierung und Parsing an dedizierte Actions.
 */
class GeminiAiAnalyzer implements AiAnalyzerInterface
{
    public function __construct(
        private ValidateAiResponseAction $validateResponse,
        private ParseAiResponseAction $parseResponse,
    ) {}

    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
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
            $this->logError($e, $request);

            return $this->buildErrorResult($request, $e);
        }
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.gemini.api_key'));
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    private function buildSanitizedRequest(AnalyzeRequestDto $request): AnalyzeRequestDto
    {
        $sanitizedJobText = $this->sanitizeInput($request->jobText());
        $sanitizedCvText = $this->sanitizeInput($request->cvText());

        return new AnalyzeRequestDto($sanitizedJobText, $sanitizedCvText);
    }

    private function callAi(AnalyzeRequestDto $sanitizedRequest): string
    {
        $jsonData = json_encode($sanitizedRequest);
        if ($jsonData === false) {
            throw new \RuntimeException('JSON-Encoding fehlgeschlagen');
        }

        /** @var StructuredAgentResponse $response */
        $response = (new Analyzer())->prompt($jsonData);

        $rawResponse = json_encode($response->toArray());

        if ($rawResponse === false) {
            throw new \RuntimeException('Response-Encoding fehlgeschlagen');
        }

        return $rawResponse;
    }

    private function sanitizeInput(string $input): string
    {
        $input = str_replace("\0", '', $input);
        $input = trim($input);
        $input = str_replace("\r\n", "\n", $input);

        return $input;
    }

    private function logError(\Throwable $exception, AnalyzeRequestDto $request): void
    {
        Log::error('AI Analysis failed', [
            'provider' => 'gemini',
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'job_text_length' => strlen($request->jobText()),
            'cv_text_length' => strlen($request->cvText()),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function buildErrorResult(AnalyzeRequestDto $request, \Throwable $e): AnalyzeResultDto
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

    private function getUserFriendlyErrorMessage(\Throwable $exception): string
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

        if (str_contains($message, 'api')) {
            return 'Die KI-API antwortet nicht. Bitte versuchen Sie es später erneut.';
        }

        return 'Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }
}
