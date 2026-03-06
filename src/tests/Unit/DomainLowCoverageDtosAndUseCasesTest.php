<?php

declare(strict_types=1);

use App\Domains\Analysis\Dto\ExtractDataResultDto;
use App\Domains\Analysis\Dto\GapAnalysisResultDto;
use App\Domains\Analysis\Dto\MatchingResultDto;
use App\Domains\Analysis\Dto\ScoreResultDto;
use App\Domains\Analysis\Dto\TagMatchDto;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractDataUseCase;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractExperiencesAction;
use App\Domains\Analysis\UseCases\ExtractDataUseCase\ExtractRequirementsAction;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\FindGapsAction;
use App\Domains\Analysis\UseCases\GapAnalysisUseCase\GapAnalysisUseCase;
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchAction;
use App\Domains\Analysis\UseCases\MatchingUseCase\MatchingUseCase;

describe('Low Coverage Domain DTOs', function () {
    test('ExtractDataResultDto toArray', function () {
        $dto = new ExtractDataResultDto(['req'], ['exp']);
        expect($dto->toArray())->toBe([
            'requirements' => ['req'],
            'experiences' => ['exp'],
        ]);
    });

    test('MatchingResultDto toArray', function () {
        $matches = [['requirement' => 'php', 'experience' => 'php8']];
        $dto = new MatchingResultDto($matches);
        expect($dto->toArray())->toBe(['matches' => $matches]);
    });

    test('GapAnalysisResultDto toArray', function () {
        $dto = new GapAnalysisResultDto(['docker']);
        expect($dto->toArray())->toBe(['gaps' => ['docker']]);
    });

    test('TagMatchDto toArray', function () {
        $dto = new TagMatchDto('Backend', ['Laravel', 'PHP']);
        expect($dto->toArray())->toBe([
            'requirement' => 'Backend',
            'experience' => ['Laravel', 'PHP'],
        ]);
    });

    test('ScoreResultDto toArray', function () {
        $dto = new ScoreResultDto(80, 'Gut', 'bg', 'text', 'bar', 4, 1);
        expect($dto->toArray())->toBe([
            'percentage' => 80,
            'rating' => 'Gut',
            'bgColor' => 'bg',
            'textColor' => 'text',
            'barColor' => 'bar',
            'matchCount' => 4,
            'gapCount' => 1,
        ]);
    });
});

describe('Low Coverage Domain UseCases/Actions', function () {
    test('Extract actions liefern aktuell leere Arrays', function () {
        $extractReq = new ExtractRequirementsAction();
        $extractExp = new ExtractExperiencesAction();

        expect($extractReq->execute('job text'))->toBe([]);
        expect($extractExp->execute('cv text'))->toBe([]);
    });

    test('ExtractDataUseCase orchestriert beide actions', function () {
        $useCase = new ExtractDataUseCase(new ExtractRequirementsAction(), new ExtractExperiencesAction());
        $result = $useCase->handle('job', 'cv');

        expect($result->requirements)->toBe([]);
        expect($result->experiences)->toBe([]);
    });

    test('MatchAction liefert aktuell leeres Ergebnis', function () {
        $action = new MatchAction();
        expect($action->execute(['php'], ['php']))->toBe([]);
    });

    test('MatchingUseCase orchestriert MatchAction', function () {
        $useCase = new MatchingUseCase(new MatchAction());
        $result = $useCase->handle(['php'], ['php']);

        expect($result->matches)->toBe([]);
        expect($result->toArray())->toBe(['matches' => []]);
    });

    test('FindGapsAction liefert aktuell leeres Ergebnis', function () {
        $action = new FindGapsAction();
        expect($action->execute(['php'], []))->toBe([]);
    });

    test('GapAnalysisUseCase orchestriert FindGapsAction', function () {
        $useCase = new GapAnalysisUseCase(new FindGapsAction());
        $result = $useCase->handle(['php'], []);

        expect($result->gaps)->toBe([]);
        expect($result->toArray())->toBe(['gaps' => []]);
    });
});

