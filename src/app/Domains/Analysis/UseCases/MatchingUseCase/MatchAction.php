<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\MatchingUseCase;

/**
 * Action: Finde Übereinstimmungen zwischen Anforderungen und Erfahrungen
 */
class MatchAction
{
    /**
     * @param  array<int, string>                                         $requirements
     * @param  array<int, string>                                         $experiences
     * @return array<int, array{requirement: string, experience: string}>
     */
    public function execute(array $requirements, array $experiences): array
    {
        // TODO: Implementierung - aktuell gibt AnalyzeApplicationService die Matches zurück
        // Diese Action kann später erweitert werden für zusätzliche Matching-Logik
        return [];
    }
}
