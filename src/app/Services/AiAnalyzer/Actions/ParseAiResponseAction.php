<?php

declare(strict_types=1);

namespace App\Services\AiAnalyzer\Actions;

use App\Domains\Analysis\Dto\RecommendationDto;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;

/**
 * Parst die AI-Response in ein strukturiertes AnalyzeResultDto.
 */
class ParseAiResponseAction
{
    /**
     * @param array<mixed, mixed> $data
     */
    public function execute(array $data, AnalyzeRequestDto $request): AnalyzeResultDto
    {
        $this->validateStructure($data);

        /** @var array<int, string> $requirements */
        $requirements = $data['requirements'];
        /** @var array<int, string> $experiences */
        $experiences = $data['experiences'];
        /** @var array<int, array{requirement: string, experience: string}> $matches */
        $matches = $data['matches'];
        /** @var array<int, string> $gaps */
        $gaps = $data['gaps'];

        $tags = $this->extractTags($data);
        $recommendations = $this->parseRecommendations($data);

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
    }

    /**
     * @param array<mixed, mixed> $data
     */
    private function validateStructure(array $data): void
    {
        $requiredFields = ['requirements', 'experiences', 'matches', 'gaps'];

        foreach ($requiredFields as $field) {
            if (! isset($data[$field])) {
                throw new \RuntimeException("Ungültige KI-Antwort: Feld '$field' fehlt");
            }

            if (! is_array($data[$field])) {
                throw new \RuntimeException("Ungültige KI-Antwort: Feld '$field' ist kein Array");
            }
        }
    }

    /**
     * @param  array<string, mixed>                                                                                             $data
     * @return array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>}|null
     */
    private function extractTags(array $data): ?array
    {
        if (! isset($data['tags']) || ! is_array($data['tags'])) {
            return null;
        }

        if (! isset($data['tags']['matches'], $data['tags']['gaps'])) {
            return null;
        }

        /** @var array{matches: array<int, array{requirement: string, experience: array<string>}>, gaps: array<int, string>} $tags */
        $tags = $data['tags'];

        return $tags;
    }

    /**
     * Parst recommendations-Array aus AI-Response
     *
     * @param  array<string, mixed>          $data
     * @return array<int, RecommendationDto>
     */
    private function parseRecommendations(array $data): array
    {
        if (! isset($data['recommendations']) || ! is_array($data['recommendations'])) {
            return [];
        }

        $recommendations = [];

        foreach ($data['recommendations'] as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (! isset($item['gap'], $item['priority'], $item['suggestion'], $item['example_phrase'])) {
                continue;
            }

            // Type guards: Sicherstellen dass alle Felder Strings sind
            if (! is_string($item['gap']) || ! is_string($item['priority']) || ! is_string($item['suggestion']) || ! is_string($item['example_phrase'])) {
                continue;
            }

            // Validiere priority-Wert
            $priority = $item['priority'];
            if (! in_array($priority, ['high', 'medium', 'low'], true)) {
                continue; // Ungültiger priority-Wert, überspringe
            }

            /** @var 'high'|'medium'|'low' $priority */
            $recommendations[] = new RecommendationDto(
                gap: $item['gap'],
                priority: $priority,
                suggestion: $item['suggestion'],
                examplePhrase: $item['example_phrase'],
            );
        }

        return $recommendations;
    }
}
