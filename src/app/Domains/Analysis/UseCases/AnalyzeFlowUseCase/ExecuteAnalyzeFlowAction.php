<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\AnalyzeFlowUseCase;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
use App\Domains\Analysis\Dto\AnalyzeViewDataDto;
use App\Domains\Analysis\Dto\ScoreResultDto;
use App\Domains\Analysis\Dto\RecommendationDto;
use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalysisComparisonAction;
use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalyzeViewDataAction;
use App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\InputValidationException;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\ValidateInputAction;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;

/**
 * Orchestriert den kompletten Analyze-Flow ausserhalb des Controllers.
 */
class ExecuteAnalyzeFlowAction
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ValidateInputAction $validateInput,
        private ScoringUseCase $scoringUseCase,
        private BuildAnalysisComparisonAction $buildComparison,
        private BuildAnalyzeViewDataAction $buildViewData,
    ) {}

    public function execute(Request $request): AnalyzeViewDataDto
    {
        $validated = $this->validateRequest($request);
        $requestDto = $this->buildRequestDto($validated);

        if ($requestDto === null) {
            return $this->buildViewData->fromValidationError(
                $validated['job_text'],
                $validated['cv_text'],
                'Sicherheitsvalidierung fehlgeschlagen: Ungueltige Eingabe'
            );
        }

        $result = $this->dispatchAnalyzeCommand($requestDto);
        $score = $this->calculateScore($result);
        $comparison = $this->buildComparisonData($request, $validated['job_text'], $result, $score);

        return $this->buildViewData->fromResult($result, $score, $comparison);
    }

    /**
     * @return array{job_text: string, cv_text: string}
     */
    private function validateRequest(Request $request): array
    {
        /** @var array{job_text: string, cv_text: string} $validated */
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        return $validated;
    }

    /**
     * @param array{job_text: string, cv_text: string} $validated
     */
    private function buildRequestDto(array $validated): ?AnalyzeRequestDto
    {
        try {
            $jobValidated = $this->validateInput->execute($validated['job_text'], 'job_text');
            $cvValidated = $this->validateInput->execute($validated['cv_text'], 'cv_text');

            return new AnalyzeRequestDto($jobValidated->sanitizedInput, $cvValidated->sanitizedInput);
        } catch (InputValidationException) {
            return null;
        }
    }

    private function dispatchAnalyzeCommand(AnalyzeRequestDto $requestDto): AnalyzeResultDto
    {
        /** @var AnalyzeResultDto $result */
        $result = $this->dispatcher->dispatch(new AnalyzeJobAndResumeCommand($requestDto, false));

        return $result;
    }

    private function calculateScore(AnalyzeResultDto $result): ?ScoreResultDto
    {
        if ($result->error !== null) {
            return null;
        }

        return $this->scoringUseCase->handle($result->matches, $result->gaps);
    }

    /** @return array<string, mixed>|null */
    private function buildComparisonData(Request $request, string $jobText, AnalyzeResultDto $result, ?ScoreResultDto $score): ?array
    {
        if ($score === null || $result->error !== null || ! $request->hasSession()) {
            return null;
        }

        $typedRecommendations = $this->extractRecommendationPriorities($result->recommendations);

        if ($request->session()->get('cv_source') !== 'competence_resume') {
            $request->session()->put('analysis_baseline_snapshot', [
                'score_percentage' => $score->percentage,
                'match_count' => count($result->matches),
                'gap_count' => count($result->gaps),
                'recommendations' => $typedRecommendations,
            ]);
        }

        $comparison = $this->buildComparison->execute(
            request: $request,
            jobText: $jobText,
            score: $score,
            matchCount: count($result->matches),
            gapCount: count($result->gaps),
            currentRecommendations: $typedRecommendations,
        );

        if ($comparison === null) {
            return null;
        }

        return $comparison->toArray();
    }

    /**
     * @param  array<int, RecommendationDto>                                   $recommendations
     * @return array<int, array{gap: string, priority: 'high'|'medium'|'low'}>
     */
    private function extractRecommendationPriorities(array $recommendations): array
    {
        $normalized = [];

        foreach ($recommendations as $recommendation) {
            if (! in_array($recommendation->priority, ['high', 'medium', 'low'], true)) {
                continue;
            }

            if ($recommendation->gap === '') {
                continue;
            }

            /** @var 'high'|'medium'|'low' $priority */
            $priority = $recommendation->priority;

            $normalized[] = [
                'gap' => $recommendation->gap,
                'priority' => $priority,
            ];
        }

        return $normalized;
    }
}
