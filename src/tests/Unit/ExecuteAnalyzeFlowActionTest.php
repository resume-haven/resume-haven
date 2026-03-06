<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\AnalyzeFlowUseCase\ExecuteAnalyzeFlowAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

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
