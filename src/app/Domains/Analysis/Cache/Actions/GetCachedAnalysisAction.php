<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Cache\Actions;

use App\Domains\Analysis\Dto\RecommendationDto;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Cache\Repositories\AnalysisCacheRepository;

/**
 * Action: Rufe gecachte Analyse-Ergebnisse ab
 */
class GetCachedAnalysisAction
{
    public function __construct(
        private AnalysisCacheRepository $repository,
    ) {}

    public function execute(AnalyzeRequestDto $request): ?AnalyzeResultDto
    {
        $cached = $this->repository->getByHash($request->requestHash());

        if ($cached === null) {
            return null;
        }

        /** @var array<int, array{gap: string, priority: 'high'|'medium'|'low', suggestion: string, example_phrase: string}> $cachedRecommendations */
        $cachedRecommendations = $cached['recommendations'] ?? [];

        $recommendations = [];
        foreach ($cachedRecommendations as $item) {
            $recommendations[] = new RecommendationDto(
                gap: $item['gap'],
                priority: $item['priority'],
                suggestion: $item['suggestion'],
                examplePhrase: $item['example_phrase']
            );
        }

        return new AnalyzeResultDto(
            $request->jobText(),
            $request->cvText(),
            $cached['requirements'],
            $cached['experiences'],
            $cached['matches'],
            $cached['gaps'],
            $cached['error'] ?? null,
            $cached['tags'] ?? null,
            $recommendations
        );
    }
}
