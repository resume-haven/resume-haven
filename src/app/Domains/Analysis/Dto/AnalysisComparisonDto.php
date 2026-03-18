<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

final readonly class AnalysisComparisonDto
{
    /**
     * @param array<int, RecommendationDeltaDto> $recommendationDeltas
     */
    public function __construct(
        public bool $hasComparison,
        public ?ScoreDeltaDto $scoreDelta,
        public int $matchDelta,
        public int $gapDelta,
        public array $recommendationDeltas,
        public ?string $message = null,
    ) {}

    /**
     * @return array{
     *   has_comparison: bool,
     *   score_delta: array{baseline: int, current: int, delta: int, direction: string, color_class: string, arrow: string}|null,
     *   match_delta: int,
     *   gap_delta: int,
     *   recommendation_deltas: array<int, array{gap: string, baseline_priority: string, current_priority: string, direction: string, color_class: string, arrow: string}>,
     *   message: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'has_comparison' => $this->hasComparison,
            'score_delta' => $this->scoreDelta?->toArray(),
            'match_delta' => $this->matchDelta,
            'gap_delta' => $this->gapDelta,
            'recommendation_deltas' => array_map(
                static fn (RecommendationDeltaDto $dto): array => $dto->toArray(),
                $this->recommendationDeltas
            ),
            'message' => $this->message,
        ];
    }
}
