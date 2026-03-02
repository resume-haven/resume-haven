<?php

declare(strict_types=1);

namespace App\Dto;

class AnalyzeResultDto
{
    public string $job_text;
    public string $cv_text;

    /** @var array<int, string> */
    public array $requirements;

    /** @var array<int, string> */
    public array $experiences;

    /** @var array<int, array{requirement: string, experience: string}> */
    public array $matches;

    /** @var array<int, string> */
    public array $gaps;

    public ?string $error;

    /**
     * @param array<int, string>                                         $requirements
     * @param array<int, string>                                         $experiences
     * @param array<int, array{requirement: string, experience: string}> $matches
     * @param array<int, string>                                         $gaps
     */
    public function __construct(
        string $job_text,
        string $cv_text,
        array $requirements,
        array $experiences,
        array $matches,
        array $gaps,
        ?string $error = null
    ) {
        $this->job_text = $job_text;
        $this->cv_text = $cv_text;
        $this->requirements = $requirements;
        $this->experiences = $experiences;
        $this->matches = $matches;
        $this->gaps = $gaps;
        $this->error = $error;
    }

    /**
     * Konvertiert das DTO zu einem Array.
     *
     * @return array{job_text: string, cv_text: string, requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error: string|null}
     */
    public function toArray(): array
    {
        return [
            'job_text' => $this->job_text,
            'cv_text' => $this->cv_text,
            'requirements' => $this->requirements,
            'experiences' => $this->experiences,
            'matches' => $this->matches,
            'gaps' => $this->gaps,
            'error' => $this->error,
        ];
    }
}
