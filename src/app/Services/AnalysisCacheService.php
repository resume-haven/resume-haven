<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AnalysisCache;
use App\Dto\AnalyzeRequestDto;

class AnalysisCacheService
{
    /**
     * Gibt das gecachte Analyseergebnis zurück oder null.
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error?: string|null}|null
     */
    public function getByDto(AnalyzeRequestDto $dto): ?array
    {
        $entry = AnalysisCache::where('request_hash', $dto->requestHash())->first();

        return $entry?->result;
    }

    /**
     * Speichert das Analyseergebnis im Cache.
     *
     * @param array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error?: string|null} $result
     */
    public function putByDto(AnalyzeRequestDto $dto, array $result): void
    {
        AnalysisCache::updateOrCreate(
            [
                'request_hash' => $dto->requestHash(),
            ],
            [
                'job_text' => $dto->jobText(),
                'cv_text' => $dto->cvText(),
                'result' => $result,
            ]
        );
    }
}
