<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;

/**
 * Application Service für AI-Analyse
 *
 * Delegiert an injizierte AiAnalyzerInterface Implementation
 * (MockAiAnalyzer oder GeminiAiAnalyzer)
 */
class AnalyzeApplicationService
{
    public function __construct(
        private AiAnalyzerInterface $aiAnalyzer
    ) {}

    /**
     * Führt die Analyse durch und gibt ein Ergebnis-DTO zurück.
     */
    public function analyze(AnalyzeRequestDto $dto): AnalyzeResultDto
    {
        return $this->aiAnalyzer->analyze($dto);
    }

    /**
     * Prüfe ob AI-Service verfügbar ist
     */
    public function isAvailable(): bool
    {
        return $this->aiAnalyzer->isAvailable();
    }

    /**
     * Gib aktuellen Provider-Namen zurück
     */
    public function getProviderName(): string
    {
        return $this->aiAnalyzer->getProviderName();
    }
}
