<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Ergebnis des Matchings zwischen Anforderungen und Erfahrungen
 */
class MatchingResultDto
{
    /**
     * @param array<int, array{requirement: string, experience: string}> $matches
     */
    public function __construct(
        public readonly array $matches,
    ) {}

    /**
     * @return array{matches: array<int, array{requirement: string, experience: string}>}
     */
    public function toArray(): array
    {
        return ['matches' => $this->matches];
    }
}
