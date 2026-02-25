<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Ai\Agents\Analyzer;
use Laravel\Ai\Responses\StructuredAgentResponse;

class AnalyzeApplicationService
{
    /**
     * Führt die Analyse durch und gibt ein Ergebnis-DTO zurück.
     *
     * @throws \RuntimeException
     */
    public function analyze(AnalyzeRequestDto $dto): AnalyzeResultDto
    {
        try {
            /** @var StructuredAgentResponse $response */
            $response = (new Analyzer())->prompt(json_encode($dto));
            $data = $response?->toArray();
            if (! is_array($data) || ! isset($data['requirements'], $data['experiences'], $data['matches'], $data['gaps'])) {
                throw new \RuntimeException('Ungültige KI-Antwort: Kein gültiges JSON oder fehlende Felder.');
            }

            return new AnalyzeResultDto(
                $dto->job_text,
                $dto->cv_text,
                $data['requirements'],
                $data['experiences'],
                $data['matches'],
                $data['gaps'],
                null
            );
        } catch (\Throwable $e) {
            return new AnalyzeResultDto(
                $dto->job_text,
                $dto->cv_text,
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage()
            );
        }
    }
}
