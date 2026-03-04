<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Cache\Actions;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Cache\Repositories\AnalysisCacheRepository;

/**
 * Action: Speichere Analyse-Ergebnisse im Cache
 */
class StoreCachedAnalysisAction
{
    public function __construct(
        private AnalysisCacheRepository $repository,
    ) {}

    public function execute(AnalyzeRequestDto $request, AnalyzeResultDto $result): void
    {
        $this->repository->store(
            $request->requestHash(),
            $request->jobText(),
            $request->cvText(),
            $result->toArray()
        );
    }
}
