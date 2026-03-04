<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ExtractDataUseCase;

/**
 * Action: Extrahiere Anforderungen aus Job-Ausschreibung
 */
class ExtractRequirementsAction
{
    /**
     * @return array<int, string>
     */
    public function execute(string $jobText): array
    {
        // TODO: Implementierung - aktuell gibt AnalyzeApplicationService die Anforderungen zurück
        // Diese Action kann später erweitert werden für zusätzliche Logik
        return [];
    }
}

