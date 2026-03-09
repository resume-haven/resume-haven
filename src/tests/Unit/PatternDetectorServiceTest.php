<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\ValidateInputUseCase\PatternDetectorService;

describe('PatternDetectorService', function () {
    test('erkennt keine Pattern bei normalem Text', function () {
        $service = new PatternDetectorService();

        $result = $service->detect('Das ist ein normaler Bewerbungstext ohne Risiko.');

        expect($result)->toBe([]);
    });

    test('erkennt SQL-Keywords', function () {
        $service = new PatternDetectorService();

        $result = $service->detect('SELECT * FROM users WHERE id = 1');

        expect($result)->toContain('SQL Keywords');
    });

    test('erkennt mehrere unterschiedliche Pattern ohne Duplikate', function () {
        $service = new PatternDetectorService();

        $result = $service->detect('SELECT * FROM users <script>alert(1)</script> SELECT');

        expect($result)->toContain('SQL Keywords');
        expect($result)->toContain('Script Tags');
        expect($result)->toHaveCount(2);
    });
});
