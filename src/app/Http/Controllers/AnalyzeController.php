<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AnalyzeRequestDto;
use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
use App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyzeController extends Controller
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ScoringUseCase $scoringUseCase,
    ) {}

    public function analyze(Request $request): View
    {
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        $dto = AnalyzeRequestDto::fromArray($validated);
        $demoMode = false;  // TODO: Move to config or request parameter if needed

        /** @var \App\Dto\AnalyzeResultDto $result */
        $result = $this->dispatcher->dispatch(
            new AnalyzeJobAndResumeCommand($dto, $demoMode)
        );

        // Berechne Score für die Ergebnis-Anzeige (nur wenn kein Error)
        $score = null;
        if ($result->error === null) {
            $score = $this->scoringUseCase->handle(
                $result->matches,
                $result->gaps
            );
        }

        return view('result', [
            'job_text' => $result->job_text,
            'cv_text' => $result->cv_text,
            'result' => $result->toArray(),
            'error' => $result->error,
            'score' => $score,
        ]);
    }
}
