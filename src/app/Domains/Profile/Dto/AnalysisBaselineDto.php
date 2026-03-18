<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

final readonly class AnalysisBaselineDto
{
    /**
     * @param array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $recommendations
     */
    public function __construct(
        public string $baselineKey,
        public string $jobHash,
        public int $scorePercentage,
        public int $matchCount,
        public int $gapCount,
        public array $recommendations,
    ) {}
}
