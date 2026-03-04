<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ScoringUseCase;

use App\Domains\Analysis\Dto\ScoreResultDto;

/**
 * UseCase: Berechne Score für Analyse-Ergebnis
 * Orchestriert CalculateScoreAction
 */
class ScoringUseCase
{
    public function __construct(
        private CalculateScoreAction $calculateScoreAction,
    ) {}

    /**
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string> $gaps
     */
    public function handle(array $matches, array $gaps): ScoreResultDto
    {
        return $this->calculateScoreAction->execute($matches, $gaps);
    }
}

