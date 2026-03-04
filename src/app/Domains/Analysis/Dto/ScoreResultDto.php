<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Score-Berechnung für Analyse-Ergebnis
 *
 * Score = (Matches / (Matches + Gaps)) * 100
 */
class ScoreResultDto
{
    /**
     * @param int    $percentage Score als Prozentsatz (0-100)
     * @param string $rating     Bewertungstext ("Geringe", "Mittlere", "Hohe" Übereinstimmung)
     * @param string $bgColor    Hintergrund-Farbe (Tailwind)
     * @param string $textColor  Text-Farbe (Tailwind)
     * @param string $barColor   Fortschrittsbalken-Farbe (Tailwind)
     */
    public function __construct(
        public readonly int $percentage,
        public readonly string $rating,
        public readonly string $bgColor,
        public readonly string $textColor,
        public readonly string $barColor,
        public readonly int $matchCount,
        public readonly int $gapCount,
    ) {}

    /**
     * @return array{percentage: int, rating: string, bgColor: string, textColor: string, barColor: string, matchCount: int, gapCount: int}
     */
    public function toArray(): array
    {
        return [
            'percentage' => $this->percentage,
            'rating' => $this->rating,
            'bgColor' => $this->bgColor,
            'textColor' => $this->textColor,
            'barColor' => $this->barColor,
            'matchCount' => $this->matchCount,
            'gapCount' => $this->gapCount,
        ];
    }
}

