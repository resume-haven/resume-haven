<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

/**
 * Bereinigt Input in kleinen, klaren Schritten.
 */
class InputSanitizerService
{
    public function sanitize(string $input): string
    {
        $withoutNullBytes = $this->removeNullBytes($input);
        $trimmed = $this->trimWhitespace($withoutNullBytes);
        $normalizedLineEndings = $this->normalizeLineEndings($trimmed);

        return $this->normalizeNewlines($normalizedLineEndings);
    }

    private function removeNullBytes(string $input): string
    {
        return str_replace("\0", '', $input);
    }

    private function trimWhitespace(string $input): string
    {
        return trim($input);
    }

    private function normalizeNewlines(string $input): string
    {
        return preg_replace('/\n\n+/', "\n", $input) ?? $input;
    }

    private function normalizeLineEndings(string $input): string
    {
        return str_replace("\r\n", "\n", $input);
    }
}
