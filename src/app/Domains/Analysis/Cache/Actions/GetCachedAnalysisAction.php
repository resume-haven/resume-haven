<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Cache\Actions;

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

        return new AnalyzeResultDto(
            $request->jobText(),
            $request->cvText(),
            $cached['requirements'],
            $cached['experiences'],
            $cached['matches'],
            $cached['gaps'],
            $cached['error'] ?? null,
            $cached['tags'] ?? null
        );
    }
}
