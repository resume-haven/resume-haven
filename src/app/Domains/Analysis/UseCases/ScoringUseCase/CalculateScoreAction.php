<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ScoringUseCase;

use App\Domains\Analysis\Dto\ScoreResultDto;

/**
 * Action: Berechne Score basierend auf Matches und Gaps
 *
 * Formel: Score = (Matches / (Matches + Gaps)) * 100
 * - Wenn Gaps = 0: Score = 100
 * - Wenn Matches = 0: Score = 0
 */
class CalculateScoreAction
{
    /**
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string>                                         $gaps
     */
    public function execute(array $matches, array $gaps): ScoreResultDto
    {
        $matchCount = count($matches);
        $gapCount = count($gaps);
        $totalItems = $matchCount + $gapCount;

        // Berechne Prozentsatz
        if ($totalItems === 0) {
            $percentage = 0;
        } else {
            $percentage = (int) round(($matchCount / $totalItems) * 100);
        }

        // Bestimme Bewertung und Farben basierend auf Prozentsatz
        [$rating, $bgColor, $textColor, $barColor] = $this->getScoreColors($percentage);

        return new ScoreResultDto(
            percentage: $percentage,
            rating: $rating,
            bgColor: $bgColor,
            textColor: $textColor,
            barColor: $barColor,
            matchCount: $matchCount,
            gapCount: $gapCount,
        );
    }

    /**
     * Bestimme Bewertungstext und Farben basierend auf Score
     *
     * @return array{0: string, 1: string, 2: string, 3: string}
     */
    private function getScoreColors(int $percentage): array
    {
        if ($percentage >= 70) {
            return [
                'Hohe Übereinstimmung',
                'bg-green-50',      // Hintergrund
                'text-green-900',   // Text
                'bg-green-500',     // Balken
            ];
        } elseif ($percentage >= 40) {
            return [
                'Mittlere Übereinstimmung',
                'bg-yellow-50',
                'text-yellow-900',
                'bg-yellow-500',
            ];
        } else {
            return [
                'Geringe Übereinstimmung',
                'bg-red-50',
                'text-red-900',
                'bg-red-500',
            ];
        }
    }
}
