<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

final readonly class RecommendationDeltaDto
{
    /**
     * @param 'improved'|'same'|'worse' $direction
     * @param 'high'|'medium'|'low'     $baselinePriority
     * @param 'high'|'medium'|'low'     $currentPriority
     */
    public function __construct(
        public string $gap,
        public string $baselinePriority,
        public string $currentPriority,
        public string $direction,
        public string $colorClass,
        public string $arrow,
    ) {}

    /** @return array{gap: string, baseline_priority: string, current_priority: string, direction: string, color_class: string, arrow: string} */
    public function toArray(): array
    {
        return [
            'gap' => $this->gap,
            'baseline_priority' => $this->baselinePriority,
            'current_priority' => $this->currentPriority,
            'direction' => $this->direction,
            'color_class' => $this->colorClass,
            'arrow' => $this->arrow,
        ];
    }
}
