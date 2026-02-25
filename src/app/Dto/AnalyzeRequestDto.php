<?php

declare(strict_types=1);

namespace App\Dto;

class AnalyzeRequestDto
{
    public string $job_text;
    public string $cv_text;

    public function __construct(string $job_text, string $cv_text)
    {
        $this->job_text = $job_text;
        $this->cv_text = $cv_text;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['job_text'] ?? '',
            $data['cv_text'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'job_text' => $this->job_text,
            'cv_text' => $this->cv_text,
        ];
    }
}
