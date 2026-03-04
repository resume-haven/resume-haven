<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ValidateInputUseCase;

/**
 * DTO für validierte Input-Daten.
 *
 * Immutable Value Object, der Informationen über den validierten Input enthält.
 */
final class ValidatedInputDto
{
    /**
     * @param string             $originalInput         Der ursprüngliche Input
     * @param string             $sanitizedInput        Der bereinigte Input
     * @param int                $lengthBytes           Länge des bereinigten Inputs in Bytes
     * @param bool               $hasSuspiciousPatterns Ob verdächtige Patterns gefunden wurden
     * @param array<int, string> $suspiciousPatterns    Array der erkannten Patterns
     */
    public function __construct(
        public readonly string $originalInput,
        public readonly string $sanitizedInput,
        public readonly int $lengthBytes,
        public readonly bool $hasSuspiciousPatterns,
        public readonly array $suspiciousPatterns = [],
    ) {}

    /**
     * Gibt an, ob der Input als sicher gilt (keine verdächtigen Patterns).
     */
    public function isSafe(): bool
    {
        return ! $this->hasSuspiciousPatterns;
    }

    /**
     * Gibt eine lesbare Zusammenfassung zurück.
     */
    public function summary(): string
    {
        if ($this->isSafe()) {
            return sprintf('Input safe (%d bytes)', $this->lengthBytes);
        }

        return sprintf(
            'Input contains suspicious patterns: %s (%d bytes)',
            implode(', ', $this->suspiciousPatterns),
            $this->lengthBytes
        );
    }
}
