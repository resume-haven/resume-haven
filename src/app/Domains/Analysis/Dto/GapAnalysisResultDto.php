<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Ergebnis der Gap-Analyse (fehlende Anforderungen)
 */
class GapAnalysisResultDto
{
    /**
     * @param array<int, string> $gaps
     */
    public function __construct(
        public readonly array $gaps,
    ) {}

    /**
     * @return array{gaps: array<int, string>}
     */
    public function toArray(): array
    {
        return ['gaps' => $this->gaps];
    }
}

