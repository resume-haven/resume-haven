<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer;

use App\Ai\Agents\Analyzer;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use Laravel\Ai\Responses\StructuredAgentResponse;

/**
 * Gemini AI Analyzer - Production Implementation
 *
 * Verwendet Laravel AI Package mit Gemini
 */
class GeminiAiAnalyzer implements AiAnalyzerInterface
{
    public function analyze(AnalyzeRequestDto $request): AnalyzeResultDto
    {
        try {
            $jsonData = json_encode($request);
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

            /** @var array<int, string> $requirements */
            $requirements = $data['requirements'];
            /** @var array<int, string> $experiences */
            $experiences = $data['experiences'];
            /** @var array<int, array{requirement: string, experience: string}> $matches */
            $matches = $data['matches'];
            /** @var array<int, string> $gaps */
            $gaps = $data['gaps'];

            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                $requirements,
                $experiences,
                $matches,
                $gaps,
                null
            );
        } catch (\Throwable $e) {
            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage()
            );
        }
    }

    public function isAvailable(): bool
    {
        return ! empty(config('ai.gemini.api_key'));
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }
}
