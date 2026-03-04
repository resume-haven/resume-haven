<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\MatchingUseCase;

use App\Domains\Analysis\Dto\MatchingResultDto;

/**
 * UseCase: Führe Matching durch
 * Orchestriert MatchAction
 */
class MatchingUseCase
{
    public function __construct(
        private MatchAction $matchAction,
    ) {}

    /**
     * @param array<int, string> $requirements
     * @param array<int, string> $experiences
     */
    public function handle(array $requirements, array $experiences): MatchingResultDto
    {
        $matches = $this->matchAction->execute($requirements, $experiences);

        return new MatchingResultDto($matches);
    }
}
