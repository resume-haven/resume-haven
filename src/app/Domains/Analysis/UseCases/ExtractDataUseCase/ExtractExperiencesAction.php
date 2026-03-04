<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ExtractDataUseCase;

/**
 * Action: Extrahiere Erfahrungen aus Lebenslauf
 */
class ExtractExperiencesAction
{
    /**
     * @return array<int, string>
     */
    public function execute(string $cvText): array
    {
        // TODO: Implementierung - aktuell gibt AnalyzeApplicationService die Erfahrungen zurück
        // Diese Action kann später erweitert werden für zusätzliche Logik
        return [];
    }
}

