<?php

declare(strict_types=1);

namespace App\Domains\Profile\Repositories;

use App\Domains\Profile\Dto\AnalysisBaselineDto;
use App\Models\AnalysisBaseline;

final class AnalysisBaselineRepository
{
    public function upsert(AnalysisBaselineDto $dto): void
    {
        AnalysisBaseline::query()->updateOrCreate(
            [
                'baseline_key' => $dto->baselineKey,
                'job_hash' => $dto->jobHash,
            ],
            [
                'score_percentage' => $dto->scorePercentage,
                'match_count' => $dto->matchCount,
                'gap_count' => $dto->gapCount,
                'recommendations' => $dto->recommendations,
            ]
        );
    }

    public function find(string $baselineKey, string $jobHash): ?AnalysisBaselineDto
    {
        /** @var AnalysisBaseline|null $baseline */
        $baseline = AnalysisBaseline::query()
            ->where('baseline_key', $baselineKey)
            ->where('job_hash', $jobHash)
            ->first();

        if ($baseline === null) {
            return null;
        }

        $recommendations = $this->normalizeRecommendations($baseline->recommendations);

        return new AnalysisBaselineDto(
            baselineKey: $baseline->baseline_key,
            jobHash: $baseline->job_hash,
            scorePercentage: $baseline->score_percentage,
            matchCount: $baseline->match_count,
            gapCount: $baseline->gap_count,
            recommendations: $recommendations,
        );
    }

    /**
     * @return array<int, array{gap: string, priority: 'high'|'medium'|'low'}>
     */
    private function normalizeRecommendations(mixed $recommendations): array
    {
        if (! is_array($recommendations)) {
            return [];
        }

        $normalized = [];

        foreach ($recommendations as $item) {
            if (! is_array($item)) {
                continue;
            }

            $gap = $item['gap'] ?? null;
            $priority = $item['priority'] ?? null;

            if (! is_string($gap) || ! is_string($priority)) {
                continue;
            }

            if (! in_array($priority, ['high', 'medium', 'low'], true)) {
                continue;
            }

            /** @var 'high'|'medium'|'low' $typedPriority */
            $typedPriority = $priority;

            $normalized[] = [
                'gap' => $gap,
                'priority' => $typedPriority,
            ];
        }

        return $normalized;
    }
}
