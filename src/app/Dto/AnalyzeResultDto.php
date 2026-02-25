<?php

declare(strict_types=1);

namespace App\Dto;

class AnalyzeResultDto
{
    public string $job_text;
    public string $cv_text;
    public array $requirements;
    public array $experiences;
    public array $matches;
    public array $gaps;
    public ?string $error;

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
