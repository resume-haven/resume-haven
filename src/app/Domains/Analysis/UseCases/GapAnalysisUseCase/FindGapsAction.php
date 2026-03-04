<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\GapAnalysisUseCase;

/**
 * Action: Finde Lücken (nicht erfüllte Anforderungen)
 */
class FindGapsAction
{
    /**
     * @param array<int, string> $requirements
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @return array<int, string>
     */
    public function execute(array $requirements, array $matches): array
    {
        // TODO: Implementierung - aktuell gibt AnalyzeApplicationService die Gaps zurück
        // Diese Action kann später erweitert werden für zusätzliche Gap-Analysis-Logik
        return [];
    }
}

