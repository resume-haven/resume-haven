<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Cache\Repositories;

use App\Models\AnalysisCache;

/**
 * Repository: Persistence-Layer für Analyse-Cache
 * Abstrahiert Datenbankzugriff für Caching-Logik
 */
class AnalysisCacheRepository
{
    /**
     * Hole gecachte Analyse-Ergebnisse nach Hash
     *
     * @return array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error?: string|null}|null
     */
    public function getByHash(string $hash): ?array
    {
        $entry = AnalysisCache::where('request_hash', $hash)->first();

        return $entry?->result;
    }

    /**
     * Speichere Analyse-Ergebnisse im Cache
     *
     * @param array{requirements: array<int, string>, experiences: array<int, string>, matches: array<int, array{requirement: string, experience: string}>, gaps: array<int, string>, error?: string|null} $result
     */
    public function store(string $hash, string $jobText, string $cvText, array $result): void
    {
        AnalysisCache::updateOrCreate(
            ['request_hash' => $hash],
            [
                'job_text' => $jobText,
                'cv_text' => $cvText,
                'result' => $result,
            ]
        );
    }
}
