<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\PresentationUseCase\BuildAnalyzeViewDataAction;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Dto\ScoreResultDto;

describe('BuildAnalyzeViewDataAction', function () {
    test('baut View-Daten aus erfolgreicher Analyse', function () {
        $action = new BuildAnalyzeViewDataAction();
        $comparison = [
            'has_comparison' => true,
            'match_delta' => 2,
        ];

        $result = new AnalyzeResultDto(
            'Job Text',
            'CV Text',
            ['PHP', 'Laravel'],
            ['5 years PHP'],
            [['requirement' => 'PHP', 'experience' => '5 years']],
            ['Docker'],
            null
        );

        $score = new ScoreResultDto(
            percentage: 75,
            rating: 'Gute Übereinstimmung',
            bgColor: 'bg-green-50',
            textColor: 'text-green-900',
            barColor: 'bg-green-500',
            matchCount: 1,
            gapCount: 1
        );

        $viewData = $action->fromResult($result, $score, $comparison);

        expect($viewData->jobText)->toBe('Job Text');
        expect($viewData->error)->toBeNull();
        expect($viewData->score)->toBe($score);
        expect($viewData->comparison)->toBe($comparison);
    });

    test('baut View-Daten aus Validierungs-Fehler', function () {
        $action = new BuildAnalyzeViewDataAction();

        $viewData = $action->fromValidationError(
            'Job Text',
            'CV Text',
            'Validation failed'
        );

        expect($viewData->error)->toBe('Validation failed');
        expect($viewData->result)->toBeNull();
        expect($viewData->score)->toBeNull();
    });

    test('toArray gibt vollständige Struktur zurück', function () {
        $action = new BuildAnalyzeViewDataAction();
        $comparison = [
            'has_comparison' => true,
            'score_delta' => [
                'delta' => 10,
            ],
        ];

        $result = new AnalyzeResultDto(
            'Job',
            'CV',
            ['Req1'],
            ['Exp1'],
            [],
            [],
            null
        );

        $viewData = $action->fromResult($result, null, $comparison);
        $array = $viewData->toArray();

        expect($array)
            ->toHaveKeys(['job_text', 'cv_text', 'result', 'error', 'score', 'tags', 'comparison'])
            ->and($array['comparison'])->toBe($comparison);
    });
});
