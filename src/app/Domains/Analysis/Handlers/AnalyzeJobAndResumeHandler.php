<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Handlers;

use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
// use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractDataUseCase; // TODO: Für zukünftige Separation
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchingUseCase;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\GapAnalysisUseCase;
use App\Domains\Analysis\UseCases\GenerateTagsUseCase\GenerateTagsAction;
use App\Domains\Analysis\Cache\Actions\GetCachedAnalysisAction;
use App\Domains\Analysis\Cache\Actions\StoreCachedAnalysisAction;
use App\Dto\AnalyzeResultDto;
use App\Services\AnalyzeApplicationService;

/**
 * Handler: Verarbeite AnalyzeJobAndResumeCommand
 *
 * Orchestriert:
 * 1. Cache-Check
 * 2. Datenextraktion (AI)
 * 3. Matching
 * 4. Gap-Analyse
 * 5. Cache-Speicherung
 *
 * Hinweis: Score-Berechnung erfolgt im Controller, nicht hier
 */
class AnalyzeJobAndResumeHandler
{
    public function __construct(
        private MatchingUseCase $matchingUseCase,
        private GapAnalysisUseCase $gapAnalysisUseCase,
        private GenerateTagsAction $generateTagsAction,
        private GetCachedAnalysisAction $getCachedAnalysis,
        private StoreCachedAnalysisAction $storeCachedAnalysis,
        private AnalyzeApplicationService $analyzeService,
    ) {}

    public function handle(AnalyzeJobAndResumeCommand $command): AnalyzeResultDto
    {
        $request = $command->request;

        // 1. Check cache (unless in demo mode)
        if (! $command->demoMode) {
            $cached = $this->getCachedAnalysis->execute($request);
            if ($cached !== null) {
                return $cached;
            }
        }

        // 2. Perform AI analysis to extract data
        try {
            $analyzeResult = $this->analyzeService->analyze($request);

            // Wenn die AI-Analyse einen Fehler zurückgibt, geben wir diesen sofort zurück
            if ($analyzeResult->error !== null) {
                return $analyzeResult;
            }
        } catch (\Throwable $e) {
            return new AnalyzeResultDto(
                $request->jobText(),
                $request->cvText(),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: '.$e->getMessage()
            );
        }

        // 3) Primär: vom Service gelieferte Matches/Gaps verwenden
        $finalMatches = $analyzeResult->matches;
        $finalGaps = $analyzeResult->gaps;

        // 4) Fallback: nur neu berechnen, wenn Service nichts geliefert hat
        if ($finalMatches === [] && $finalGaps === [] && $analyzeResult->requirements !== [] && $analyzeResult->experiences !== []) {
            $matchingResult = $this->matchingUseCase->handle(
                $analyzeResult->requirements,
                $analyzeResult->experiences
            );

            $gapResult = $this->gapAnalysisUseCase->handle(
                $analyzeResult->requirements,
                $matchingResult->matches
            );

            $finalMatches = $matchingResult->matches;
            $finalGaps = $gapResult->gaps;
        }

        // 5) Tags übernehmen oder aus finalen Daten erzeugen
        $tags = $analyzeResult->tags;
        if ($tags === null) {
            $tags = $this->generateTagsAction->execute($finalMatches, $finalGaps);
        }

        // 6) Combine results
        $result = new AnalyzeResultDto(
            $request->jobText(),
            $request->cvText(),
            $analyzeResult->requirements,
            $analyzeResult->experiences,
            $finalMatches,
            $finalGaps,
            null,
            $tags
        );

        // 7. Cache result (unless in demo mode)
        if (! $command->demoMode) {
            $this->storeCachedAnalysis->execute($request, $result);
        }

        return $result;
    }
}
