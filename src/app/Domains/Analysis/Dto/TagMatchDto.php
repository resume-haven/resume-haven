<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * DTO für ein Tag-Match
 *
 * Repräsentiert eine gruppierte Anforderung mit ihren entsprechenden Erfahrungen
 */
readonly class TagMatchDto
{
    /**
     * @param string $requirement Die Anforderung (z.B. "Frontend")
     * @param array<int, string> $experience Die entsprechenden Erfahrungen (z.B. ["React", "Vue"])
     */
    public function __construct(
        public string $requirement,
        public array $experience,
    ) {}

    /**
     * Konvertiere zu Array für JSON-Serialisierung
     *
     * @return array{requirement: string, experience: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'requirement' => $this->requirement,
            'experience' => $this->experience,
        ];
    }
}

