<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Analysis\UseCases\AnalyzeFlowUseCase\ExecuteAnalyzeFlowAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnalyzeController extends Controller
{
    public function __construct(
        private ExecuteAnalyzeFlowAction $executeAnalyzeFlow,
    ) {}

    public function __invoke(Request $request): View
    {
        $viewData = $this->executeAnalyzeFlow->execute($request);

        return view('result', $viewData->toArray());
    }
}
