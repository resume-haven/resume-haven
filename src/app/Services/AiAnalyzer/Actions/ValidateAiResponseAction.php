<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer\Actions;

/**
 * Validiert die AI-Response gegen Sicherheitsbedrohungen.
 */
class ValidateAiResponseAction
{
    private const MAX_RESPONSE_LENGTH = 1_000_000; // 1MB

    /** @var array<int, string> */
    private const SUSPICIOUS_PATTERNS = [
        '/eval\s*\(/i',
        '/exec\s*\(/i',
        '/system\s*\(/i',
        '/shell_exec/i',
        '/<script/i',
        '/javascript:/i',
    ];

    public function execute(string $rawResponse): void
    {
        $this->validateLength($rawResponse);
        $this->validateJsonStructure($rawResponse);
        $this->validateAgainstSuspiciousPatterns($rawResponse);
    }

    private function validateLength(string $response): void
    {
        if (strlen($response) <= self::MAX_RESPONSE_LENGTH) {
            return;
        }

        throw new \RuntimeException('AI-Response ist zu lang');
    }

    private function validateJsonStructure(string $response): void
    {
        $trimmed = trim($response);

        if (! str_starts_with($trimmed, '{') || ! str_ends_with($trimmed, '}')) {
            throw new \RuntimeException('AI-Response ist kein gültiges JSON-Objekt');
        }
    }

    private function validateAgainstSuspiciousPatterns(string $response): void
    {
        foreach (self::SUSPICIOUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $response) === 1) {
                throw new \RuntimeException('AI-Response enthält verdächtige Patterns');
            }
        }
    }
}
