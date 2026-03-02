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

    /**
     * Erstellt ein DTO aus einem Array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $jobText = $data['job_text'] ?? '';
        $cvText = $data['cv_text'] ?? '';

        if (! is_string($jobText) || ! is_string($cvText)) {
            throw new \InvalidArgumentException('job_text und cv_text müssen Strings sein');
        }

        return new self($jobText, $cvText);
    }

    /**
     * Konvertiert das DTO zu einem Array.
     *
     * @return array<string, string>
     */
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
