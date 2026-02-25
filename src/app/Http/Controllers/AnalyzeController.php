<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AnalyzeApplicationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyzeController extends Controller
{
    public function analyze(Request $request): \Illuminate\View\View
    {
        $validated = $request->validate([
            'job_text' => ['required', 'min:30'],
            'cv_text' => ['required', 'min:30'],
        ]);

        $dto = AnalyzeRequestDto::fromArray($validated);
        $service = app(AnalyzeApplicationService::class);
        /** @var AnalyzeResultDto $result */
        $result = $service->analyze($dto);

        return view('result', [
            'job_text' => $result->job_text,
            'cv_text' => $result->cv_text,
            'result' => [
                'requirements' => $result->requirements,
                'experiences' => $result->experiences,
                'matches' => $result->matches,
                'gaps' => $result->gaps,
            ],
            'error' => $result->error,
        ]);
    }
}
