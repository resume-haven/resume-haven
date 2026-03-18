<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\PresentationUseCase;

use App\Domains\Analysis\Dto\AnalysisComparisonDto;
use App\Domains\Analysis\Dto\RecommendationDeltaDto;
use App\Domains\Analysis\Dto\ScoreDeltaDto;
use App\Domains\Analysis\Dto\ScoreResultDto;
use App\Domains\Profile\Actions\ResolveBaselineKeyAction;
use App\Domains\Profile\Dto\AnalysisBaselineDto;
use App\Domains\Profile\Repositories\AnalysisBaselineRepository;
use Illuminate\Http\Request;

final class BuildAnalysisComparisonAction
{
    public function __construct(
        private AnalysisBaselineRepository $baselineRepository,
        private ResolveBaselineKeyAction $resolveBaselineKey,
    ) {}

    /**
     * @param array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $currentRecommendations
     */
    public function execute(
        Request $request,
        string $jobText,
        ?ScoreResultDto $score,
        int $matchCount,
        int $gapCount,
        array $currentRecommendations,
    ): ?AnalysisComparisonDto {
        if ($score === null) {
            return null;
        }

        $baselineKey = $this->resolveBaselineKey->execute($request);
        $jobHash = $this->hashJob($jobText);

        $isCompetenceSource = $request->session()->get('cv_source') === 'competence_resume';

        if (! $isCompetenceSource) {
            $this->storeBaseline($baselineKey, $jobHash, $score, $matchCount, $gapCount, $currentRecommendations);

            return null;
        }

        $baseline = $this->baselineRepository->find($baselineKey, $jobHash);

        if ($baseline === null) {
            return $this->buildFromSessionFallback($request, $score, $matchCount, $gapCount, $currentRecommendations);
        }

        return $this->buildComparison($baseline, $score, $matchCount, $gapCount, $currentRecommendations, null);
    }

    /**
     * @param array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $recommendations
     */
    private function storeBaseline(
        string $baselineKey,
        string $jobHash,
        ScoreResultDto $score,
        int $matchCount,
        int $gapCount,
        array $recommendations,
    ): void {
        $dto = new AnalysisBaselineDto(
            baselineKey: $baselineKey,
            jobHash: $jobHash,
            scorePercentage: $score->percentage,
            matchCount: $matchCount,
            gapCount: $gapCount,
            recommendations: $recommendations,
        );

        $this->baselineRepository->upsert($dto);
    }

    /**
     * @param array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $currentRecommendations
     */
    private function buildFromSessionFallback(
        Request $request,
        ScoreResultDto $score,
        int $matchCount,
        int $gapCount,
        array $currentRecommendations,
    ): ?AnalysisComparisonDto {
        $snapshot = $request->session()->get('analysis_baseline_snapshot');

        if (! is_array($snapshot)) {
            return null;
        }

        $baselineScore = $snapshot['score_percentage'] ?? null;
        $baselineMatchCount = $snapshot['match_count'] ?? null;
        $baselineGapCount = $snapshot['gap_count'] ?? null;
        $baselineRecommendations = $snapshot['recommendations'] ?? null;

        if (! is_int($baselineScore) || ! is_int($baselineMatchCount) || ! is_int($baselineGapCount) || ! is_array($baselineRecommendations)) {
            return null;
        }

        $baseline = new AnalysisBaselineDto(
            baselineKey: 'session-fallback',
            jobHash: 'session-fallback',
            scorePercentage: $baselineScore,
            matchCount: $baselineMatchCount,
            gapCount: $baselineGapCount,
            recommendations: $this->normalizeRecommendations($baselineRecommendations),
        );

        return $this->buildComparison(
            $baseline,
            $score,
            $matchCount,
            $gapCount,
            $currentRecommendations,
            'Vergleich aus Session-Fallback (keine persistente Baseline gefunden).'
        );
    }

    /**
     * @param array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $currentRecommendations
     */
    private function buildComparison(
        AnalysisBaselineDto $baseline,
        ScoreResultDto $score,
        int $matchCount,
        int $gapCount,
        array $currentRecommendations,
        ?string $message,
    ): AnalysisComparisonDto {
        $scoreDelta = $this->buildScoreDelta($baseline->scorePercentage, $score->percentage);

        return new AnalysisComparisonDto(
            hasComparison: true,
            scoreDelta: $scoreDelta,
            matchDelta: $matchCount - $baseline->matchCount,
            gapDelta: $gapCount - $baseline->gapCount,
            recommendationDeltas: $this->buildRecommendationDeltas($baseline->recommendations, $currentRecommendations),
            message: $message,
        );
    }

    private function buildScoreDelta(int $baseline, int $current): ScoreDeltaDto
    {
        $delta = $current - $baseline;

        [$direction, $colorClass, $arrow] = $this->resolveDeltaMeta($delta, true);

        return new ScoreDeltaDto(
            baseline: $baseline,
            current: $current,
            delta: $delta,
            direction: $direction,
            colorClass: $colorClass,
            arrow: $arrow,
        );
    }

    /**
     * @param  array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $baselineRecommendations
     * @param  array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $currentRecommendations
     * @return array<int, RecommendationDeltaDto>
     */
    private function buildRecommendationDeltas(array $baselineRecommendations, array $currentRecommendations): array
    {
        $baselineMap = $this->mapRecommendations($baselineRecommendations);
        $currentMap = $this->mapRecommendations($currentRecommendations);

        $deltas = [];

        foreach ($currentMap as $gap => $currentPriority) {
            if (! isset($baselineMap[$gap])) {
                continue;
            }

            $baselinePriority = $baselineMap[$gap];
            $priorityDelta = $this->priorityWeight($currentPriority) - $this->priorityWeight($baselinePriority);

            [$direction, $colorClass, $arrow] = $this->resolveDeltaMeta($priorityDelta, false);

            $deltas[] = new RecommendationDeltaDto(
                gap: $gap,
                baselinePriority: $baselinePriority,
                currentPriority: $currentPriority,
                direction: $direction,
                colorClass: $colorClass,
                arrow: $arrow,
            );
        }

        return $deltas;
    }

    /**
     * @param  int                                                       $delta positive means better if $higherIsBetter=true
     * @return array{0: 'improved'|'same'|'worse', 1: string, 2: string}
     */
    private function resolveDeltaMeta(int $delta, bool $higherIsBetter): array
    {
        if ($delta === 0) {
            return ['same', 'text-blue-700 dark:text-blue-300', '→'];
        }

        $isImproved = $higherIsBetter ? $delta > 0 : $delta < 0;

        if ($isImproved) {
            return ['improved', 'text-green-700 dark:text-green-300', '↑'];
        }

        return ['worse', 'text-red-700 dark:text-red-300', '↓'];
    }

    /**
     * @param  array<int, array{gap: string, priority: 'high'|'medium'|'low'}> $recommendations
     * @return array<string, 'high'|'medium'|'low'>
     */
    private function mapRecommendations(array $recommendations): array
    {
        $map = [];

        foreach ($recommendations as $recommendation) {
            $map[$recommendation['gap']] = $recommendation['priority'];
        }

        return $map;
    }

    private function priorityWeight(string $priority): int
    {
        return match ($priority) {
            'high' => 3,
            'medium' => 2,
            default => 1,
        };
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

            if (! is_string($gap) || ! is_string($priority) || ! in_array($priority, ['high', 'medium', 'low'], true)) {
                continue;
            }

            /** @var 'high'|'medium'|'low' $typedPriority */
            $typedPriority = $priority;
            $normalized[] = ['gap' => $gap, 'priority' => $typedPriority];
        }

        return $normalized;
    }

    private function hashJob(string $jobText): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $jobText) ?? $jobText);

        return hash('sha256', $normalized);
    }
}
