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

    test('mapped Cache-Treffer mit Recommendations korrekt', function () {
        $repo = Mockery::mock(AnalysisCacheRepository::class);
        $request = new AnalyzeRequestDto('job', 'cv');

        $repo->shouldReceive('getByHash')
            ->once()
            ->andReturn([
                'requirements' => ['PHP'],
                'experiences' => ['Laravel'],
                'matches' => [],
                'gaps' => ['Docker'],
                'error' => null,
                'tags' => null,
                'recommendations' => [
                    [
                        'gap' => 'Docker',
                        'priority' => 'high',
                        'suggestion' => 'Docker-Kenntnisse aufbauen',
                        'example_phrase' => 'Ich habe Erfahrung mit Docker.',
                    ],
                ],
            ]);

        $action = new GetCachedAnalysisAction($repo);
        $result = $action->execute($request);

        expect($result)->not()->toBeNull();
        expect($result?->recommendations)->toHaveCount(1);
        expect($result?->recommendations[0]->gap)->toBe('Docker');
        expect($result?->recommendations[0]->priority)->toBe('high');
    });

    test('mapped Cache-Treffer ohne Recommendations-Key korrekt', function () {
        $repo = Mockery::mock(AnalysisCacheRepository::class);
        $request = new AnalyzeRequestDto('job', 'cv');

        $repo->shouldReceive('getByHash')
            ->once()
            ->andReturn([
                'requirements' => [],
                'experiences' => [],
                'matches' => [],
                'gaps' => [],
                'error' => null,
                'tags' => null,
                // kein 'recommendations'-Key
            ]);

        $action = new GetCachedAnalysisAction($repo);
        $result = $action->execute($request);

        expect($result)->not()->toBeNull();
        expect($result?->recommendations)->toBe([]);
    });
});
