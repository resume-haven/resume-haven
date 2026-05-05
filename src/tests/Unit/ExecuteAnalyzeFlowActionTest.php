<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\AnalyzeFlowUseCase\ExecuteAnalyzeFlowAction;
use App\Domains\Analysis\Dto\RecommendationDto;
use App\Domains\Analysis\Dto\ScoreResultDto;
use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalysisComparisonAction;
use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalyzeViewDataAction;
use App\Domains\Analysis\UseCases\ScoringUseCase\ScoringUseCase;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\InputValidationException;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\ValidateInputAction;
use App\Domains\Analysis\UseCases\ValidateInputUseCase\ValidatedInputDto;
use App\Dto\AnalyzeResultDto;
use Illuminate\Bus\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

uses(RefreshDatabase::class);

describe('ExecuteAnalyzeFlowAction Edge Cases', function () {
    test('behandelt Unicode-Zeichen korrekt', function () {
        $action = app(ExecuteAnalyzeFlowAction::class);

        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('Wir suchen PHP-Entwickler 🚀 für München. ', 2),
            'cv_text' => str_repeat('5 Jahre Erfahrung mit Umlauten: äöü ßẞ. ', 2),
        ]);

        $result = $action->execute($request);

        expect($result->jobText)->toContain('🚀');
        expect($result->cvText)->toContain('äöü');
    });

    test('behandelt sehr lange Inputs nahe am Limit', function () {
        $action = app(ExecuteAnalyzeFlowAction::class);

        // 49KB Input (unter 50KB Limit), aber über 30 Zeichen
        $longText = str_repeat('Valid text with some content for testing. ', 1100);

        $request = Request::create('/analyze', 'POST', [
            'job_text' => $longText,
            'cv_text' => $longText,
        ]);

        $result = $action->execute($request);

        expect($result->error)->toBeNull();
    });

    test('behandelt Inputs mit mehrfachen Newline-Varianten', function () {
        $action = app(ExecuteAnalyzeFlowAction::class);

        $request = Request::create('/analyze', 'POST', [
            'job_text' => "Line 1\r\n\r\nLine 2\n\n\nLine 3 with enough content to pass validation",
            'cv_text' => "Text\r\nMit\nGemischten\r\n\r\nNewlines and some more content here",
        ]);

        $result = $action->execute($request);

        expect($result->error)->toBeNull();
    });

    test('gibt Fehler zurück bei Security-Validation-Failure', function () {
        $action = app(ExecuteAnalyzeFlowAction::class);

        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('x', 51 * 1024), // Über Limit
            'cv_text' => str_repeat('Valid CV text here. ', 10),
        ]);

        $result = $action->execute($request);

        expect($result->error)->not()->toBeNull();
        expect($result->error)->toContain('Sicherheitsvalidierung fehlgeschlagen');
    });
});

describe('ExecuteAnalyzeFlowAction Coverage Cases', function () {
    $withSession = static function (Request $request): Request {
        /** @var Store $session */
        $session = app('session')->driver();
        $session->start();
        $request->setLaravelSession($session);

        return $request;
    };

    $validatedInput = static function (string $input): ValidatedInputDto {
        return new ValidatedInputDto(
            originalInput: $input,
            sanitizedInput: $input,
            lengthBytes: strlen($input),
            hasSuspiciousPatterns: false,
            suspiciousPatterns: [],
        );
    };

    test('liefert validation-error view data wenn buildRequestDto null ergibt', function () {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $validateInput = Mockery::mock(ValidateInputAction::class);
        $scoring = Mockery::mock(ScoringUseCase::class);
        $comparison = app(BuildAnalysisComparisonAction::class);

        $validateInput->shouldReceive('execute')
            ->once()
            ->andThrow(new InputValidationException('boom'));

        $action = new ExecuteAnalyzeFlowAction(
            dispatcher: $dispatcher,
            validateInput: $validateInput,
            scoringUseCase: $scoring,
            buildComparison: $comparison,
            buildViewData: new BuildAnalyzeViewDataAction(),
        );

        $request = Request::create('/analyze', 'POST', [
            'job_text' => str_repeat('job text ', 5),
            'cv_text' => str_repeat('cv text ', 5),
        ]);

        $result = $action->execute($request);

        expect($result->error)->toContain('Sicherheitsvalidierung fehlgeschlagen');
    });

    test('setzt baseline-snapshot bei score und session wenn cv-source nicht competence ist', function () use ($validatedInput, $withSession) {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $validateInput = Mockery::mock(ValidateInputAction::class);
        $scoring = Mockery::mock(ScoringUseCase::class);
        $comparison = app(BuildAnalysisComparisonAction::class);

        $job = str_repeat('job text ', 5);
        $cv = str_repeat('cv text ', 5);

        $validateInput->shouldReceive('execute')->once()->with($job, 'job_text')->andReturn($validatedInput($job));
        $validateInput->shouldReceive('execute')->once()->with($cv, 'cv_text')->andReturn($validatedInput($cv));

        $analyzeResult = new AnalyzeResultDto(
            job_text: $job,
            cv_text: $cv,
            requirements: ['PHP'],
            experiences: ['Laravel'],
            matches: [['requirement' => 'PHP', 'experience' => 'Laravel']],
            gaps: ['Docker'],
            error: null,
            tags: ['matches' => [], 'gaps' => []],
            recommendations: [
                new RecommendationDto('Docker', 'high', 'Lerne Docker', 'Docker im CV nennen'),
                new RecommendationDto('', 'high', 'Invalid', 'Invalid'),
            ],
        );

        $dispatcher->shouldReceive('dispatch')->once()->andReturn($analyzeResult);

        $score = new ScoreResultDto(50, 'Mittlere Uebereinstimmung', 'bg', 'text', 'bar', 1, 1);
        $scoring->shouldReceive('handle')->once()->andReturn($score);

        $action = new ExecuteAnalyzeFlowAction(
            dispatcher: $dispatcher,
            validateInput: $validateInput,
            scoringUseCase: $scoring,
            buildComparison: $comparison,
            buildViewData: new BuildAnalyzeViewDataAction(),
        );

        $request = $withSession(Request::create('/analyze', 'POST', [
            'job_text' => $job,
            'cv_text' => $cv,
        ]));
        $request->session()->put('cv_source', 'original_cv');

        $viewData = $action->execute($request);
        $snapshot = $request->session()->get('analysis_baseline_snapshot');

        expect($viewData->error)->toBeNull();
        expect($snapshot)->toBeArray();
        expect($snapshot['score_percentage'] ?? null)->toBe(50);
        expect($snapshot['match_count'] ?? null)->toBe(1);
        expect($snapshot['gap_count'] ?? null)->toBe(1);
        expect($snapshot['recommendations'] ?? [])->toHaveCount(1);
    });

    test('liefert comparison-array wenn buildComparison daten erzeugt', function () use ($validatedInput, $withSession) {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $validateInput = Mockery::mock(ValidateInputAction::class);
        $scoring = Mockery::mock(ScoringUseCase::class);
        $comparison = app(BuildAnalysisComparisonAction::class);

        $job = str_repeat('job text ', 5);
        $cv = str_repeat('cv text ', 5);

        $validateInput->shouldReceive('execute')->once()->with($job, 'job_text')->andReturn($validatedInput($job));
        $validateInput->shouldReceive('execute')->once()->with($cv, 'cv_text')->andReturn($validatedInput($cv));

        $analyzeResult = new AnalyzeResultDto(
            job_text: $job,
            cv_text: $cv,
            requirements: ['PHP'],
            experiences: ['Laravel'],
            matches: [['requirement' => 'PHP', 'experience' => 'Laravel']],
            gaps: ['Docker'],
            error: null,
            tags: ['matches' => [], 'gaps' => []],
            recommendations: [new RecommendationDto('Docker', 'high', 'Lerne Docker', 'Docker im CV nennen')],
        );

        $dispatcher->shouldReceive('dispatch')->once()->andReturn($analyzeResult);

        $score = new ScoreResultDto(50, 'Mittlere Uebereinstimmung', 'bg', 'text', 'bar', 1, 1);
        $scoring->shouldReceive('handle')->once()->andReturn($score);

        $action = new ExecuteAnalyzeFlowAction(
            dispatcher: $dispatcher,
            validateInput: $validateInput,
            scoringUseCase: $scoring,
            buildComparison: $comparison,
            buildViewData: new BuildAnalyzeViewDataAction(),
        );

        $request = $withSession(Request::create('/analyze', 'POST', [
            'job_text' => $job,
            'cv_text' => $cv,
        ]));
        $request->session()->put('cv_source', 'competence_resume');
        $request->session()->put('analysis_baseline_snapshot', [
            'score_percentage' => 40,
            'match_count' => 0,
            'gap_count' => 2,
            'recommendations' => [
                ['gap' => 'Docker', 'priority' => 'high'],
            ],
        ]);

        $viewData = $action->execute($request);

        expect($viewData->comparison)->toBeArray();
        expect($viewData->comparison['has_comparison'] ?? null)->toBeTrue();
        expect($viewData->comparison['message'] ?? null)->toContain('Session-Fallback');
    });

    test('liefert null score und comparison wenn analyze-result einen fehler enthaelt', function () use ($validatedInput) {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $validateInput = Mockery::mock(ValidateInputAction::class);
        $scoring = Mockery::mock(ScoringUseCase::class);
        $comparison = app(BuildAnalysisComparisonAction::class);

        $job = str_repeat('job text ', 5);
        $cv = str_repeat('cv text ', 5);

        $validateInput->shouldReceive('execute')->once()->with($job, 'job_text')->andReturn($validatedInput($job));
        $validateInput->shouldReceive('execute')->once()->with($cv, 'cv_text')->andReturn($validatedInput($cv));

        $analyzeResult = new AnalyzeResultDto(
            job_text: $job,
            cv_text: $cv,
            requirements: [],
            experiences: [],
            matches: [],
            gaps: [],
            error: 'AI error',
            tags: null,
            recommendations: [],
        );

        $dispatcher->shouldReceive('dispatch')->once()->andReturn($analyzeResult);
        $scoring->shouldNotReceive('handle');

        $action = new ExecuteAnalyzeFlowAction(
            dispatcher: $dispatcher,
            validateInput: $validateInput,
            scoringUseCase: $scoring,
            buildComparison: $comparison,
            buildViewData: new BuildAnalyzeViewDataAction(),
        );

        $request = Request::create('/analyze', 'POST', [
            'job_text' => $job,
            'cv_text' => $cv,
        ]);

        $viewData = $action->execute($request);

        expect($viewData->score)->toBeNull();
        expect($viewData->comparison)->toBeNull();
        expect($viewData->error)->toBe('AI error');
    });
});
