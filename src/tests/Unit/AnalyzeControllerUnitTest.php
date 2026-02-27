<?php

declare(strict_types=1);

use App\Http\Controllers\AnalyzeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('AnalyzeController besitzt die Methode analyze', function () {
    $controller = new AnalyzeController();
    expect(method_exists($controller, 'analyze'))->toBeTrue();
});

describe('AnalyzeController::analyze', function () {
    it('liefert eine View mit Fehlern bei ungültigen Eingaben', function () {
        $controller = new AnalyzeController();
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

    it('liefert eine View mit Ergebnis bei gültigen Eingaben (Mock)', function () {
        $controller = new AnalyzeController();
        $mockCacheService = \Mockery::mock(\App\Services\AnalysisCacheService::class);
        $mockCacheService->shouldReceive('getByDto')->andReturn(null);
        $mockCacheService->shouldReceive('putByDto')->andReturnNull();
        app()->instance(\App\Services\AnalysisCacheService::class, $mockCacheService);
        $mockService = \Mockery::mock(\App\Services\AnalyzeApplicationService::class);
        $mockService->shouldReceive('analyze')->andReturn(
            new \App\Dto\AnalyzeResultDto(
                str_repeat('A', 31),
                str_repeat('B', 31),
                ['foo'],
                ['bar'],
                [['requirement' => 'foo', 'experience' => 'bar']],
                [],
                null
            )
        );
        app()->instance(\App\Services\AnalyzeApplicationService::class, $mockService);
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);
        // View-Facade faken, damit die View nicht wirklich gerendert wird
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
        expect($data['result']['matches'][0]['requirement'])->toBe('foo');
        expect($data['result']['matches'][0]['experience'])->toBe('bar');
        expect($data['result']['gaps'])->toBe([]);
        expect($data['error'])->toBeNull();
    });

    it('liefert eine View mit Fehlertext bei Exception', function () {
        $controller = new AnalyzeController();
        $mockService = \Mockery::mock(\App\Services\AnalyzeApplicationService::class);
        $mockService->shouldReceive('analyze')->andReturn(
            new \App\Dto\AnalyzeResultDto(
                str_repeat('A', 31),
                str_repeat('B', 31),
                [],
                [],
                [],
                [],
                'AI-Analyse fehlgeschlagen: Timeout!'
            )
        );
        app()->instance(\App\Services\AnalyzeApplicationService::class, $mockService);
        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('A', 31),
            'cv_text' => str_repeat('B', 31),
        ]);
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
        expect($data['result'])->toBeArray();
        expect($data['result']['requirements'])->toBe([]);
        expect($data['error'])->not()->toBeNull();
        expect((string) $data['error'])->toContain('AI-Analyse fehlgeschlagen');
    });
});
