<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Dto;

/**
 * Immutable DTO fuer die Datenuebergabe an die Ergebnis-View.
 */
final class AnalyzeViewDataDto
{
    /**
     * @param array<string, mixed>|null $result
     * @param array<string, mixed>|null $tags
     */
    public function __construct(
        public readonly string $jobText,
        public readonly string $cvText,
        public readonly ?array $result,
        public readonly ?string $error,
        public readonly ?ScoreResultDto $score,
        public readonly ?array $tags = null,
    ) {}

    /**
     * @return array{job_text: string, cv_text: string, result: array<string, mixed>|null, error: string|null, score: ScoreResultDto|null, tags: array<string, mixed>|null}
     */
    public function toArray(): array
    {
        return [
            'job_text' => $this->jobText,
            'cv_text' => $this->cvText,
            'result' => $this->result,
            'error' => $this->error,
            'score' => $this->score,
            'tags' => $this->tags,
        ];
    }
}
