<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * @param 'improved'|'same'|'worse' $direction
 */
final readonly class ScoreDeltaDto
{
    /**
     * @param 'improved'|'same'|'worse' $direction
     */
    public function __construct(
        public int $baseline,
        public int $current,
        public int $delta,
        public string $direction,
        public string $colorClass,
        public string $arrow,
    ) {}

    /**
     * @return array{baseline: int, current: int, delta: int, direction: 'improved'|'same'|'worse', color_class: string, arrow: string}
     */
    public function toArray(): array
    {
        return [
            'baseline' => $this->baseline,
            'current' => $this->current,
            'delta' => $this->delta,
            'direction' => $this->direction,
            'color_class' => $this->colorClass,
            'arrow' => $this->arrow,
        ];
    }
}
