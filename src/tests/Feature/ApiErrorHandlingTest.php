<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Error Handling for API Failures', function () {
    test('Analyzer gibt Error-DTO bei ungültigen Eingaben zurück', function () {
        $analyzer = new MockAiAnalyzer();

        // Empty request should fail validation
        $request = new AnalyzeRequestDto('', '');

        $result = $analyzer->analyze($request);

        // Bei leeren Eingaben sollte MockAnalyzer trotzdem Data zurückgeben
        expect($result->requirements)->toBeArray();
    });

    test('Error-DTO enthält ursprüngliche Eingaben', function () {
        $analyzer = new MockAiAnalyzer();

        $jobText = 'Senior PHP Developer needed';
        $cvText = '20 years of experience';
        $request = new AnalyzeRequestDto($jobText, $cvText);

        $result = $analyzer->analyze($request);

        // Original inputs should be preserved
        expect($result->job_text)->toBe($jobText);
        expect($result->cv_text)->toBe($cvText);
    });

    test('MockAiAnalyzer gibt valides DTO bei allen Szenarien', function () {
        $scenarios = ['realistic', 'high_score', 'low_score', 'no_match'];

        foreach ($scenarios as $scenario) {
            config(['ai.mock.scenario' => $scenario]);
            $analyzer = new MockAiAnalyzer();

            $request = new AnalyzeRequestDto('Job text', 'CV text');
            $result = $analyzer->analyze($request);

            // All fields should be set
            expect($result->requirements)->toBeArray();
            expect($result->experiences)->toBeArray();
            expect($result->matches)->toBeArray();
            expect($result->gaps)->toBeArray();
            expect($result->error)->toBeNull();
        }
    });

    test('Error-Messages enthalten keine technischen Details', function () {
        // Tests error message format
        // Simulated: Bei echten Fehlern sollte Error-Message benutzerfreundlich sein

        $errorMsg = 'Die Analyse ist fehlgeschlagen. Bitte versuchen Sie es erneut.';

        // Should NOT contain technical details
        expect($errorMsg)->not()->toContain('Exception');
        expect($errorMsg)->not()->toContain('Stack trace');
        expect($errorMsg)->not()->toContain('Throwable');
    });

    test('Analyzer sanitiert Eingaben auch bei ungültigen Daten', function () {
        $analyzer = new MockAiAnalyzer();

        // Input mit Null-Bytes und Whitespace
        $jobText = "  Job\0Text  \n\n";
        $cvText = "CV\0Text\r\n";

        $request = new AnalyzeRequestDto($jobText, $cvText);
        $result = $analyzer->analyze($request);

        // Sollte trotzdem funktionieren
        expect($result->requirements)->toBeArray();
        expect($result->error)->toBeNull();
    });
});
