<?php

declare(strict_types=1);

use App\Domains\Analysis\Dto\AnalyzeViewDataDto;
use App\Domains\Analysis\UseCases\AnalyzeFlowUseCase\ExecuteAnalyzeFlowAction;
use App\Http\Controllers\AnalyzeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('AnalyzeController besitzt die Methode __invoke', function () {
    $mockExecuteAnalyzeFlow = \Mockery::mock(ExecuteAnalyzeFlowAction::class);
    $controller = new AnalyzeController($mockExecuteAnalyzeFlow);

    expect(method_exists($controller, '__invoke'))->toBeTrue();
});

describe('AnalyzeController::__invoke', function () {
    it('delegiert den Request an ExecuteAnalyzeFlowAction', function () {
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);

        $viewDataDto = new AnalyzeViewDataDto(
            jobText: str_repeat('A', 31),
            cvText: str_repeat('B', 31),
            result: [
                'requirements' => ['foo'],
                'experiences' => ['bar'],
                'matches' => [],
                'gaps' => [],
                'error' => null,
                'tags' => null,
            ],
            error: null,
            score: null,
            tags: null,
        );

        $mockExecuteAnalyzeFlow = \Mockery::mock(ExecuteAnalyzeFlowAction::class);
        $mockExecuteAnalyzeFlow
            ->shouldReceive('execute')
            ->once()
            ->with(\Mockery::type(Request::class))
            ->andReturn($viewDataDto);

        $controller = new AnalyzeController($mockExecuteAnalyzeFlow);
        $response = $controller->__invoke($request);

        expect($response)->toBeInstanceOf(\Illuminate\Contracts\View\View::class);
    });

    it('gibt bei Action-Fehlerdaten weiterhin die result-View zurueck', function () {
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);

        $viewDataDto = new AnalyzeViewDataDto(
            jobText: str_repeat('A', 31),
            cvText: str_repeat('B', 31),
            result: null,
            error: 'Sicherheitsvalidierung fehlgeschlagen: Ungueltige Eingabe',
            score: null,
            tags: null,
        );

        $mockExecuteAnalyzeFlow = \Mockery::mock(ExecuteAnalyzeFlowAction::class);
        $mockExecuteAnalyzeFlow
            ->shouldReceive('execute')
            ->once()
            ->andReturn($viewDataDto);

        $controller = new AnalyzeController($mockExecuteAnalyzeFlow);
        $response = $controller->__invoke($request);

        expect($response)->toBeInstanceOf(\Illuminate\Contracts\View\View::class);
    });
});
