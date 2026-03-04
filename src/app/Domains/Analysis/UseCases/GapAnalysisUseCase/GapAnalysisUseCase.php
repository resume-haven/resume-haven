<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\GapAnalysisUseCase;

use App\Domains\Analysis\Dto\GapAnalysisResultDto;

/**
 * UseCase: Führe Gap-Analyse durch
 * Orchestriert FindGapsAction
 */
class GapAnalysisUseCase
{
    public function __construct(
        private FindGapsAction $findGapsAction,
    ) {}

    /**
     * @param array<int, string>                                         $requirements
     * @param array<int, array{requirement: string, experience: string}> $matches
     */
    public function handle(array $requirements, array $matches): GapAnalysisResultDto
    {
        $gaps = $this->findGapsAction->execute($requirements, $matches);

        return new GapAnalysisResultDto($gaps);
    }
}
