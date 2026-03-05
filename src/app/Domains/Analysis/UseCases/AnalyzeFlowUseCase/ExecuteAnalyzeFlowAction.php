<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\AnalyzeFlowUseCase;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
use App\Domains\Analysis\Dto\AnalyzeViewDataDto;
use App\Domains\Analysis\Dto\ScoreResultDto;
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

        return $this->buildViewData->fromResult($result, $score);
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
}
