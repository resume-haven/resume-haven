<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;

describe('AnalyzeRequestDto', function () {
    test('erstellt DTO aus Konstruktor und liefert Getter', function () {
        $dto = new AnalyzeRequestDto('job', 'cv');

        expect($dto->jobText())->toBe('job');
        expect($dto->cvText())->toBe('cv');
    });

    test('fromArray erstellt DTO aus validem Array', function () {
        $dto = AnalyzeRequestDto::fromArray([
            'job_text' => 'job-text',
            'cv_text' => 'cv-text',
        ]);

        expect($dto->jobText())->toBe('job-text');
        expect($dto->cvText())->toBe('cv-text');
    });

    test('fromArray nutzt default leere Strings falls keys fehlen', function () {
        $dto = AnalyzeRequestDto::fromArray([]);

        expect($dto->jobText())->toBe('');
        expect($dto->cvText())->toBe('');
    });

    test('fromArray wirft Exception bei ungültigen Typen', function () {
        expect(fn () => AnalyzeRequestDto::fromArray([
            'job_text' => ['not-a-string'],
            'cv_text' => 'ok',
        ]))->toThrow(InvalidArgumentException::class);
    });

    test('toArray und requestHash funktionieren konsistent', function () {
        $dto = new AnalyzeRequestDto('job', 'cv');

        expect($dto->toArray())->toBe([
            'job_text' => 'job',
            'cv_text' => 'cv',
        ]);

        expect($dto->requestHash())->toBe(hash('sha256', 'jobcv'));
    });
});

