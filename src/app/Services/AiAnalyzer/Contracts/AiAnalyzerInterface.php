<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer\Contracts;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;

/**
 * Interface für AI-Analyzer Services
 *
 * Ermöglicht verschiedene Implementierungen:
 * - GeminiAiAnalyzer (Production)
 * - MockAiAnalyzer (Development/Testing)
 * - OpenAiAnalyzer (Alternative, für später)
 */
interface AiAnalyzerInterface
{
    /**
     * Analysiere Job-Ausschreibung und Lebenslauf
     *
     * @param  AnalyzeRequestDto $request Job-Text und CV-Text
     * @return AnalyzeResultDto  Analyse-Ergebnis mit requirements, experiences, matches, gaps
     */
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto;

    /**
     * Prüfe ob Service verfügbar/konfiguriert ist
     */
    public function isAvailable(): bool;

    /**
     * Gib Provider-Namen zurück (für Logging/Debugging)
     */
    public function getProviderName(): string;
}
