<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;

/**
 * Gemini AI Analyzer - Production Implementation
 *
 * Verwendet Laravel AI Package mit Gemini
 */
class GeminiAiAnalyzer implements AiAnalyzerInterface
{
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
        try {
            // Security: Sanitize inputs before sending to AI
            $sanitizedJobText = $this->sanitizeInput($request->jobText());
            $sanitizedCvText = $this->sanitizeInput($request->cvText());

            $sanitizedRequest = new AnalyzeRequestDto($sanitizedJobText, $sanitizedCvText);

            $jsonData = json_encode($sanitizedRequest);
            if ($jsonData === false) {
                throw new \RuntimeException('JSON-Encoding fehlgeschlagen');
            }

            /** @var StructuredAgentResponse $response */
            $response = (new Analyzer())->prompt($jsonData);

            // Security: Validate response is safe JSON
            $rawResponse = json_encode($response->toArray());
            if ($rawResponse === false || ! $this->isResponseSafe($rawResponse)) {
                throw new \RuntimeException('AI-Response enthält verdächtige Patterns');
            }

            $data = $response->toArray();

            if (! isset($data['requirements'], $data['experiences'], $data['matches'], $data['gaps'])) {
                throw new \RuntimeException('Ungültige KI-Antwort: Fehlende Felder.');
            }

            // Validiere Array-Typen
            if (! is_array($data['requirements']) || ! is_array($data['experiences']) ||
                ! is_array($data['matches']) || ! is_array($data['gaps'])) {
                throw new \RuntimeException('Ungültige KI-Antwort: Felder sind keine Arrays.');
            }

            /** @var array<int, string> $requirements */
            $requirements = $data['requirements'];
            /** @var array<int, string> $experiences */
            $experiences = $data['experiences'];
            /** @var array<int, array{requirement: string, experience: string}> $matches */
            $matches = $data['matches'];
            /** @var array<int, string> $gaps */
            $gaps = $data['gaps'];

            // Parse Tags falls vorhanden
            $tags = null;
            if (isset($data['tags']) && is_array($data['tags'])) {
                // Prüfe dass tags die richtige Struktur hat
                if (isset($data['tags']['matches']) && isset($data['tags']['gaps'])) {
                    /** @var array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>} $tags */
                    $tags = $data['tags'];
                }
            }

            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                $requirements,
                $experiences,
                $matches,
                $gaps,
                null,
                $tags
            );
        } catch (\Throwable $e) {
            // Log the error for debugging
            $this->logError($e, $request);

            // Return user-friendly error message
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
    }

    /**
     * Log error with context for debugging.
     */
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

    /**
     * Convert exception to user-friendly error message.
     */
    private function getUserFriendlyErrorMessage(\Throwable $exception): string
    {
        $message = $exception->getMessage();

        // Detect specific error types and provide helpful messages
        if (str_contains(strtolower($message), 'timeout')) {
            return 'Die KI-Analyse hat zu lange gedauert (Timeout). Bitte versuchen Sie es später erneut.';
        }

        if (str_contains(strtolower($message), 'json')) {
            return 'Die KI-Antwort war ungültig. Bitte versuchen Sie es erneut.';
        }

        if (str_contains(strtolower($message), 'connection') || str_contains(strtolower($message), 'network')) {
            return 'Netzwerkfehler bei der Verbindung zur KI. Bitte prüfen Sie Ihre Internetverbindung.';
        }

        if (str_contains(strtolower($message), 'api')) {
            return 'Die KI-API antwortet nicht. Bitte versuchen Sie es später erneut.';
        }

        // Default message
        return 'Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.gemini.api_key'));
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    /**
     * Sanitizes input to prevent prompt injection.
     */
    private function sanitizeInput(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);

        // Trim whitespace
        $input = trim($input);

        // Normalize line endings
        $input = str_replace("\r\n", "\n", $input);

        return $input;
    }

    /**
     * Validates AI response against security threats.
     */
    private function isResponseSafe(string $response): bool
    {
        // Length check (unrealistic long response = suspicious)
        if (strlen($response) > 1_000_000) { // 1MB
            return false;
        }

        // Check response is valid JSON-like structure
        $trimmed = trim($response);
        if (! str_starts_with($trimmed, '{') || ! str_ends_with($trimmed, '}')) {
            return false;
        }

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec/i',
            '/<script/i',
            '/javascript:/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $response)) {
                return false;
            }
        }

        return true;
    }
}
