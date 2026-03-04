<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
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
            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage(),
                null
            );
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
