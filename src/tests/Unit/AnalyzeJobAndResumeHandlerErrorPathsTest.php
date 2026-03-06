<?php

declare(strict_types=1);

use App\Domains\Analysis\Cache\Actions\GetCachedAnalysisAction;
use App\Domains\Analysis\Cache\Actions\StoreCachedAnalysisAction;
use App\Domains\Analysis\Commands\AnalyzeJobAndResumeCommand;
use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\GapAnalysisUseCase;
use App\Domains\Analysis\UseCases\GenerateTagsUseCase\GenerateTagsAction;
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchingUseCase;
use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Services\AnalyzeApplicationService;

describe('AnalyzeJobAndResumeHandler Error Paths', function () {
    test('gibt Error-Result zurück bei Exception im AnalyzeService', function () {
        $request = new AnalyzeRequestDto('job', 'cv');
        $command = new AnalyzeJobAndResumeCommand($request, demoMode: false);

        $matchingUseCase = Mockery::mock(MatchingUseCase::class);
        $gapAnalysisUseCase = Mockery::mock(GapAnalysisUseCase::class);
        $generateTagsAction = Mockery::mock(GenerateTagsAction::class);
        $getCachedAnalysis = Mockery::mock(GetCachedAnalysisAction::class);
        $storeCachedAnalysis = Mockery::mock(StoreCachedAnalysisAction::class);
        $analyzeService = Mockery::mock(AnalyzeApplicationService::class);

        $getCachedAnalysis->shouldReceive('execute')->once()->andReturnNull();
        $analyzeService->shouldReceive('analyze')->once()->andThrow(new \RuntimeException('AI Service down'));

        $handler = new AnalyzeJobAndResumeHandler(
            $matchingUseCase,
            $gapAnalysisUseCase,
            $generateTagsAction,
            $getCachedAnalysis,
            $storeCachedAnalysis,
            $analyzeService
        );

        $result = $handler->handle($command);

        expect($result->error)->toContain('AI-Analyse fehlgeschlagen');
        expect($result->error)->toContain('AI Service down');
        expect($result->requirements)->toBe([]);
        expect($result->experiences)->toBe([]);
    });

    test('gibt Error-Result direkt zurück wenn AI einen Fehler liefert', function () {
        $request = new AnalyzeRequestDto('job', 'cv');
        $command = new AnalyzeJobAndResumeCommand($request, demoMode: false);

        $matchingUseCase = Mockery::mock(MatchingUseCase::class);
        $gapAnalysisUseCase = Mockery::mock(GapAnalysisUseCase::class);
        $generateTagsAction = Mockery::mock(GenerateTagsAction::class);
        $getCachedAnalysis = Mockery::mock(GetCachedAnalysisAction::class);
        $storeCachedAnalysis = Mockery::mock(StoreCachedAnalysisAction::class);
        $analyzeService = Mockery::mock(AnalyzeApplicationService::class);

        $errorResult = new AnalyzeResultDto('job', 'cv', [], [], [], [], 'Timeout error', null);

        $getCachedAnalysis->shouldReceive('execute')->once()->andReturnNull();
        $analyzeService->shouldReceive('analyze')->once()->andReturn($errorResult);

        $handler = new AnalyzeJobAndResumeHandler(
            $matchingUseCase,
            $gapAnalysisUseCase,
            $generateTagsAction,
            $getCachedAnalysis,
            $storeCachedAnalysis,
            $analyzeService
        );

        $result = $handler->handle($command);

        expect($result->error)->toBe('Timeout error');
        expect($result)->toBe($errorResult);
    });

    test('verwendet Fallback Matching/Gap wenn AI leere Arrays liefert aber Requirements/Experiences vorhanden', function () {
        $request = new AnalyzeRequestDto('job', 'cv');
        $command = new AnalyzeJobAndResumeCommand($request, demoMode: false);

        $matchingUseCase = Mockery::mock(MatchingUseCase::class);
        $gapAnalysisUseCase = Mockery::mock(GapAnalysisUseCase::class);
        $generateTagsAction = Mockery::mock(GenerateTagsAction::class);
        $getCachedAnalysis = Mockery::mock(GetCachedAnalysisAction::class);
        $storeCachedAnalysis = Mockery::mock(StoreCachedAnalysisAction::class);
        $analyzeService = Mockery::mock(AnalyzeApplicationService::class);

        $aiResult = new AnalyzeResultDto('job', 'cv', ['PHP'], ['Laravel'], [], [], null, null);

        $getCachedAnalysis->shouldReceive('execute')->once()->andReturnNull();
        $analyzeService->shouldReceive('analyze')->once()->andReturn($aiResult);

        $matchingResult = new \App\Domains\Analysis\Dto\MatchingResultDto([
            ['requirement' => 'PHP', 'experience' => 'Laravel'],
        ]);
        $gapResult = new \App\Domains\Analysis\Dto\GapAnalysisResultDto([]);

        $matchingUseCase->shouldReceive('handle')->once()->andReturn($matchingResult);
        $gapAnalysisUseCase->shouldReceive('handle')->once()->andReturn($gapResult);
        $generateTagsAction->shouldReceive('execute')->once()->andReturn([
            'matches' => [['requirement' => 'PHP', 'experience' => ['Laravel']]],
            'gaps' => [],
        ]);
        $storeCachedAnalysis->shouldReceive('execute')->once();

        $handler = new AnalyzeJobAndResumeHandler(
            $matchingUseCase,
            $gapAnalysisUseCase,
            $generateTagsAction,
            $getCachedAnalysis,
            $storeCachedAnalysis,
            $analyzeService
        );

        $result = $handler->handle($command);

        expect($result->matches)->toHaveCount(1);
        expect($result->gaps)->toHaveCount(0);
        expect($result->error)->toBeNull();
    });

    test('überspringt Cache bei demoMode=true', function () {
        $request = new AnalyzeRequestDto('job', 'cv');
        $command = new AnalyzeJobAndResumeCommand($request, demoMode: true);

        $matchingUseCase = Mockery::mock(MatchingUseCase::class);
        $gapAnalysisUseCase = Mockery::mock(GapAnalysisUseCase::class);
        $generateTagsAction = Mockery::mock(GenerateTagsAction::class);
        $getCachedAnalysis = Mockery::mock(GetCachedAnalysisAction::class);
        $storeCachedAnalysis = Mockery::mock(StoreCachedAnalysisAction::class);
        $analyzeService = Mockery::mock(AnalyzeApplicationService::class);

        $aiResult = new AnalyzeResultDto(
            'job',
            'cv',
            ['PHP'],
            ['Laravel'],
            [['requirement' => 'PHP', 'experience' => 'Laravel']],
            [],
            null,
            ['matches' => [], 'gaps' => []]
        );

        // Cache darf NICHT aufgerufen werden
        $getCachedAnalysis->shouldNotReceive('execute');
        $storeCachedAnalysis->shouldNotReceive('execute');

        $analyzeService->shouldReceive('analyze')->once()->andReturn($aiResult);

        $handler = new AnalyzeJobAndResumeHandler(
            $matchingUseCase,
            $gapAnalysisUseCase,
            $generateTagsAction,
            $getCachedAnalysis,
            $storeCachedAnalysis,
            $analyzeService
        );

        $result = $handler->handle($command);

        expect($result->error)->toBeNull();
    });
});

