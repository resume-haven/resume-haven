<?php

declare(strict_types=1);

use App\Domains\Analysis\Cache\Actions\GetCachedAnalysisAction;
use App\Domains\Analysis\Cache\Repositories\AnalysisCacheRepository;
use App\Dto\AnalyzeRequestDto;

describe('GetCachedAnalysisAction', function () {
    test('gibt null zurück wenn kein Cache-Treffer vorliegt', function () {
        $repo = Mockery::mock(AnalysisCacheRepository::class);
        $request = new AnalyzeRequestDto('job', 'cv');

        $repo->shouldReceive('getByHash')
            ->once()
            ->with($request->requestHash())
            ->andReturnNull();

        $action = new GetCachedAnalysisAction($repo);

        expect($action->execute($request))->toBeNull();
    });

    test('mapped Cache-Treffer in AnalyzeResultDto', function () {
        $repo = Mockery::mock(AnalysisCacheRepository::class);
        $request = new AnalyzeRequestDto('job', 'cv');

        $repo->shouldReceive('getByHash')
            ->once()
            ->with($request->requestHash())
            ->andReturn([
                'requirements' => ['PHP'],
                'experiences' => ['Laravel'],
                'matches' => [['requirement' => 'PHP', 'experience' => 'Laravel']],
                'gaps' => ['Docker'],
                'error' => null,
                'tags' => [
                    'matches' => [['requirement' => 'PHP', 'experience' => ['Laravel']]],
                    'gaps' => ['Docker'],
                ],
            ]);

        $action = new GetCachedAnalysisAction($repo);
        $result = $action->execute($request);

        expect($result)->not()->toBeNull();
        expect($result?->requirements)->toBe(['PHP']);
        expect($result?->experiences)->toBe(['Laravel']);
        expect($result?->gaps)->toBe(['Docker']);
        expect($result?->tags)->not()->toBeNull();
        expect($result?->error)->toBeNull();
    });

    test('mapped Cache-Treffer mit Error-Message', function () {
        $repo = Mockery::mock(AnalysisCacheRepository::class);
        $request = new AnalyzeRequestDto('job', 'cv');

        $repo->shouldReceive('getByHash')
            ->once()
            ->with($request->requestHash())
            ->andReturn([
                'requirements' => [],
                'experiences' => [],
                'matches' => [],
                'gaps' => [],
                'error' => 'API Error',
                'tags' => null,
            ]);

        $action = new GetCachedAnalysisAction($repo);
        $result = $action->execute($request);

        expect($result)->not()->toBeNull();
        expect($result?->error)->toBe('API Error');
        expect($result?->tags)->toBeNull();
    });
});


