<?php

declare(strict_types=1);

namespace App\Dto;

class AnalyzeRequestDto
{
    private string $job_text;
    private string $cv_text;

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

    public function jobText(): string
    {
        return $this->job_text;
    }

    public function cvText(): string
    {
        return $this->cv_text;
    }

    public function requestHash(): string
    {
        return hash('sha256', $this->job_text.$this->cv_text);
    }
}
