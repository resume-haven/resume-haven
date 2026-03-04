<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Ergebnis der Datenextraktion (Anforderungen + Erfahrungen)
 */
class ExtractDataResultDto
{
    /**
     * @param array<int, string> $requirements
     * @param array<int, string> $experiences
     */
    public function __construct(
        public readonly array $requirements,
        public readonly array $experiences,
    ) {}

    /**
     * @return array{requirements: array<int, string>, experiences: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'requirements' => $this->requirements,
            'experiences' => $this->experiences,
        ];
    }
}

