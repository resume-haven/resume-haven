<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

/**
 * Immutable DTO fuer den erzeugten Kompetenzlebenslauf.
 *
 * @param array<int, string> $hardSkills
 * @param array<int, string> $softSkills
 * @param array<int, string> $domains
 */
final readonly class CompetenceResumeDto
{
    /**
     * @param array<int, string> $hardSkills
     * @param array<int, string> $softSkills
     * @param array<int, string> $domains
     */
    public function __construct(
        public array $hardSkills,
        public array $softSkills,
        public array $domains,
        public ?int $yearsExperience,
        public string $summary,
    ) {}

    /**
     * @return array{hard_skills: array<int, string>, soft_skills: array<int, string>, domains: array<int, string>, years_experience: int|null, summary: string}
     */
    public function toArray(): array
    {
        return [
            'hard_skills' => $this->hardSkills,
            'soft_skills' => $this->softSkills,
            'domains' => $this->domains,
            'years_experience' => $this->yearsExperience,
            'summary' => $this->summary,
        ];
    }
}
