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

            // Parse Tags falls vorhanden
            $tags = null;
            if (isset($data['tags']) && is_array($data['tags'])) {
                // Prüfe dass tags die richtige Struktur hat
                if (isset($data['tags']['matches']) && isset($data['tags']['gaps'])) {
                    /** @var array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>} $tags */
                    $tags = $data['tags'];
                }
            }

            // Parse Recommendations falls vorhanden (optional)
            $recommendations = null;
            if (isset($data['recommendations']) && is_array($data['recommendations'])) {
                $recommendations = $this->validateRecommendations($data['recommendations']);
            }

            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                $requirements,
                $experiences,
                $matches,
                $gaps,
                null,
                $tags,
                $recommendations
            );
        } catch (\Throwable $e) {
            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage(),
                null,
                null
            );
        }
    }

    /**
     * Validiere und normalisiere Recommendations aus der AI-Response
     *
     * @param array<mixed> $recommendations
     * @return array<int, array{gap: string, recommendation: string, example: string, category: string, priority: string, confidence: float}>|null
     */
    private function validateRecommendations(array $recommendations): ?array
    {
        $validated = [];
        $validPriorities = ['critical', 'high', 'medium', 'low'];
        $validCategories = ['skills', 'tools', 'architecture', 'process', 'leadership', 'general'];

        foreach ($recommendations as $rec) {
            if (! is_array($rec)) {
                continue;
            }

            if (! isset($rec['gap'], $rec['recommendation'], $rec['example'], $rec['category'], $rec['priority'], $rec['confidence'])) {
                continue;
            }

            if (! is_string($rec['gap']) || ! is_string($rec['recommendation']) || ! is_string($rec['example']) || ! is_string($rec['category']) || ! is_string($rec['priority'])) {
                continue;
            }

            if (! is_numeric($rec['confidence'])) {
                continue;
            }

            $gap = $rec['gap'];
            $recommendation = $rec['recommendation'];
            $example = $rec['example'];
            $category = $rec['category'];
            $priority = $rec['priority'];
            $confidence = (float) $rec['confidence'];

            if (! in_array($category, $validCategories, true)) {
                $category = 'general';
            }

            if (! in_array($priority, $validPriorities, true)) {
                $priority = 'medium';
            }

            $confidence = max(0.0, min(1.0, $confidence));

            if ($gap !== '' && $recommendation !== '') {
                $validated[] = [
                    'gap' => $gap,
                    'recommendation' => $recommendation,
                    'example' => $example,
                    'category' => $category,
                    'priority' => $priority,
                    'confidence' => $confidence,
                ];
            }
        }

        return count($validated) > 0 ? $validated : null;
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

