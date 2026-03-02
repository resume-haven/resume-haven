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
            $jsonData = json_encode($dto);
            if ($jsonData === false) {
                throw new \RuntimeException('JSON-Encoding fehlgeschlagen');
            }

            /** @var StructuredAgentResponse $response */
            $response = (new Analyzer())->prompt($jsonData);
            $data = $response->toArray();

            if (! isset($data['requirements'], $data['experiences'], $data['matches'], $data['gaps'])) {
                throw new \RuntimeException('Ungültige KI-Antwort: Fehlende Felder.');
            }

            // Validiere Array-Typen
            if (! is_array($data['requirements']) || ! is_array($data['experiences']) ||
                ! is_array($data['matches']) || ! is_array($data['gaps'])) {
                throw new \RuntimeException('Ungültige KI-Antwort: Felder sind keine Arrays.');
            }

            return new AnalyzeResultDto(
                $dto->jobText(),
                $dto->cvText(),
                $data['requirements'],
                $data['experiences'],
                $data['matches'],
                $data['gaps'],
                null
            );
        } catch (\Throwable $e) {
            return new AnalyzeResultDto(
                $dto->jobText(),
                $dto->cvText(),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage()
            );
        }
    }
}
