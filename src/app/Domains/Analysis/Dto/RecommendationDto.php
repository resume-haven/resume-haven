<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Immutable DTO für eine Verbesserungsempfehlung
 */
readonly class RecommendationDto
{
    /**
     * @param string                $gap           Fehlende Anforderung
     * @param 'high'|'medium'|'low' $priority      Priorität
     * @param string                $suggestion    Konkreter Verbesserungsvorschlag
     * @param string                $examplePhrase Beispiel-Formulierung für Lebenslauf
     */
    public function __construct(
        public string $gap,
        public string $priority,
        public string $suggestion,
        public string $examplePhrase,
    ) {}

    /**
     * @return array{gap: string, priority: string, suggestion: string, example_phrase: string}
     */
    public function toArray(): array
    {
        return [
            'gap' => $this->gap,
            'priority' => $this->priority,
            'suggestion' => $this->suggestion,
            'example_phrase' => $this->examplePhrase,
        ];
    }

    /**
     * Gibt Farbe basierend auf Priorität zurück
     */
    public function getColor(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
        };
    }

    /**
     * Gibt Badge-CSS-Klassen basierend auf Priorität zurück
     */
    public function getBadgeClasses(): string
    {
        return match ($this->priority) {
            'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        };
    }

    /**
     * Gibt Prioritäts-Label in Deutsch zurück
     */
    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'high' => 'Hoch',
            'medium' => 'Mittel',
            'low' => 'Niedrig',
        };
    }
}
