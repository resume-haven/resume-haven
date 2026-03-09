<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\PresentationUseCase;

use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Dto\AnalyzeViewDataDto;
use App\Domains\Analysis\Dto\ScoreResultDto;

/**
 * Baut View-Daten aus Domain-DTOs auf.
 */
class BuildAnalyzeViewDataAction
{
    public function fromResult(AnalyzeResultDto $result, ?ScoreResultDto $score): AnalyzeViewDataDto
    {
        return new AnalyzeViewDataDto(
            jobText: $result->job_text,
            cvText: $result->cv_text,
            result: $result->toArray(),
            error: $result->error,
            score: $score,
            tags: $result->tags,
        );
    }

    public function fromValidationError(string $jobText, string $cvText, string $message): AnalyzeViewDataDto
    {
        return new AnalyzeViewDataDto(
            jobText: $jobText,
            cvText: $cvText,
            result: null,
            error: $message,
            score: null,
            tags: null,
        );
    }
}
