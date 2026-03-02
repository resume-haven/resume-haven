<?php

declare(strict_types=1);

use App\Http\Controllers\AnalyzeController;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase;
use App\Services\AnalyzeApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('AnalyzeController besitzt die Methode analyze', function () {
    $mockScoringUseCase = \Mockery::mock(ScoringUseCase::class);
    $controller = new AnalyzeController(
        app(\Illuminate\Bus\Dispatcher::class),
        $mockScoringUseCase
    );
    expect(method_exists($controller, 'analyze'))->toBeTrue();
});

describe('AnalyzeController::analyze', function () {
    it('liefert eine View mit Fehlern bei ungültigen Eingaben', function () {
        $mockScoringUseCase = \Mockery::mock(ScoringUseCase::class);
        $controller = new AnalyzeController(
            app(\Illuminate\Bus\Dispatcher::class),
            $mockScoringUseCase
        );
        $request = Request::create('/analyze', 'POST', [
            'job_text' => '',
            'cv_text' => '',
        ]);
        $caught = false;
        try {
            $controller->analyze($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $caught = true;
            expect($e->errors())->toHaveKey('job_text');
            expect($e->errors())->toHaveKey('cv_text');
        }
        expect($caught)->toBeTrue();
    });

    it('liefert eine View mit Ergebnis bei gültigen Eingaben', function () {
        // Mock der AnalyzeApplicationService
        $mockService = \Mockery::mock(AnalyzeApplicationService::class);
        $mockService->shouldReceive('analyze')->andReturn(
            new AnalyzeResultDto(
                str_repeat('A', 31),
                str_repeat('B', 31),
                ['foo'],
                ['bar'],
                [],
                [],
                null
            )
        );
        app()->instance(AnalyzeApplicationService::class, $mockService);

        // Mock ScoringUseCase
        $mockScoring = \Mockery::mock(ScoringUseCase::class);
        $mockScoring->shouldReceive('handle')->andReturn(
            new \App\Domains\Analysis\Dto\ScoreResultDto(
                percentage: 100,
                rating: 'Hohe Übereinstimmung',
                bgColor: 'bg-green-50',
                textColor: 'text-green-900',
                barColor: 'bg-green-500',
                matchCount: 1,
                gapCount: 0
            )
        );

        $controller = new AnalyzeController(
            app(\Illuminate\Bus\Dispatcher::class),
            $mockScoring
        );
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);

        // Mock View-Facade
        \Illuminate\Support\Facades\View::shouldReceive('make')->andReturnUsing(function ($view, $data) {
            return new class ($data) extends \Illuminate\View\View {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function getData()
                {
                    return $this->data;
                }

                public function name()
                {
                    return 'result';
                }

                public function render(?callable $callback = null): string
                {
                    return '';
                }

                public function with($key, $value = null)
                {
                    return $this;
                }
            };
        });

        $view = $controller->analyze($request);
        $data = $view->getData();

        expect($data['result'])->not()->toBeNull();
        expect($data['result']['requirements'])->toBe(['foo']);
        expect($data['result']['experiences'])->toBe(['bar']);
        expect($data['error'])->toBeNull();
        expect($data['score'])->not()->toBeNull();
    });

    it('liefert eine View mit Fehlertext bei Exception', function () {
        // Mock der AnalyzeApplicationService, um Exception zu werfen
        $mockService = \Mockery::mock(AnalyzeApplicationService::class);
        $mockService->shouldReceive('analyze')->andThrow(new Exception('Timeout!'));
        app()->instance(AnalyzeApplicationService::class, $mockService);

        // Mock ScoringUseCase - wird NICHT aufgerufen, da error !== null
        $mockScoring = \Mockery::mock(ScoringUseCase::class);
        $mockScoring->shouldReceive('handle')->never();

        $controller = new AnalyzeController(
            app(\Illuminate\Bus\Dispatcher::class),
            $mockScoring
        );
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);

        // Mock View-Facade
        \Illuminate\Support\Facades\View::shouldReceive('make')->andReturnUsing(function ($view, $data) {
            return new class ($data) extends \Illuminate\View\View {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function getData()
                {
                    return $this->data;
                }

                public function name()
                {
                    return 'result';
                }

                public function render(?callable $callback = null): string
                {
                    return '';
                }

                public function with($key, $value = null)
                {
                    return $this;
                }
            };
        });

        $view = $controller->analyze($request);
        $data = $view->getData();

        expect($data['error'])->toBeString();
        expect($data['error'])->toContain('AI-Analyse fehlgeschlagen');
    });
});
