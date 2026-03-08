<?php

declare(strict_types=1);

use App\Dto\AnalyzeRequestDto;
use App\Services\AiAnalyzer\Actions\ParseAiResponseAction;

describe('ParseAiResponseAction', function () {
    test('parst valide Response zu AnalyzeResultDto', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP', 'Laravel'],
            'experiences' => ['5 years PHP'],
            'matches' => [
                ['requirement' => 'PHP', 'experience' => '5 years PHP'],
            ],
            'gaps' => ['Docker'],
        ];

        $result = $action->execute($data, $request);

        expect($result->requirements)->toBe(['PHP', 'Laravel']);
        expect($result->experiences)->toBe(['5 years PHP']);
        expect($result->error)->toBeNull();
    });

    test('lehnt Response ohne erforderliche Felder ab', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = ['requirements' => ['PHP']]; // Fehlen: experiences, matches, gaps

        expect(fn () => $action->execute($data, $request))
            ->toThrow(\RuntimeException::class);
    });

    test('lehnt Response ab, wenn Feld kein Array ist', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => 'PHP',  // Sollte Array sein
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => [],
        ];

        expect(fn () => $action->execute($data, $request))
            ->toThrow(\RuntimeException::class);
    });

    test('extrahiert Tags aus Response falls vorhanden', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years PHP'],
            'matches' => [],
            'gaps' => [],
            'tags' => [
                'matches' => [['requirement' => 'PHP', 'experience' => ['5 years']]],
                'gaps' => ['Docker'],
            ],
        ];

        $result = $action->execute($data, $request);

        expect($result->tags)->not()->toBeNull();
        expect($result->tags['matches'])->toHaveCount(1);
    });

    test('behandelt fehlende Tags-Struktur korrekt', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => [],
            'tags' => ['invalid' => 'structure'], // Fehlt matches/gaps
        ];

        $result = $action->execute($data, $request);

        expect($result->tags)->toBeNull();
    });

    test('behandelt tags als nicht-Array', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => [],
            'tags' => 'not an array',
        ];

        $result = $action->execute($data, $request);

        expect($result->tags)->toBeNull();
    });

    test('lehnt Response mit mehreren fehlenden Feldern ab', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = ['requirements' => ['PHP'], 'experiences' => []]; // Fehlen: matches, gaps

        expect(fn () => $action->execute($data, $request))
            ->toThrow(\RuntimeException::class, 'Feld \'matches\' fehlt');
    });

    test('parst recommendations aus Response', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => ['Docker'],
            'recommendations' => [
                [
                    'gap' => 'Docker',
                    'priority' => 'high',
                    'suggestion' => 'Lernen Sie Docker',
                    'example_phrase' => 'Docker-Kenntnisse',
                ],
            ],
        ];

        $result = $action->execute($data, $request);

        expect($result->recommendations)->toHaveCount(1);
        expect($result->recommendations[0])->toBeInstanceOf(\App\Domains\Analysis\Dto\RecommendationDto::class);
        expect($result->recommendations[0]->gap)->toBe('Docker');
        expect($result->recommendations[0]->priority)->toBe('high');
    });

    test('gibt leeres Array zurück wenn recommendations fehlen', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => [],
        ];

        $result = $action->execute($data, $request);

        expect($result->recommendations)->toBe([]);
    });

    test('überspringt ungültige recommendation-Einträge', function () {
        $action = new ParseAiResponseAction();
        $request = new AnalyzeRequestDto('job text', 'cv text');
        $data = [
            'requirements' => ['PHP'],
            'experiences' => ['5 years'],
            'matches' => [],
            'gaps' => ['Docker'],
            'recommendations' => [
                // Gültig
                ['gap' => 'Docker', 'priority' => 'high', 'suggestion' => 'Test', 'example_phrase' => 'Example'],
                // Ungültig: fehlendes Feld
                ['gap' => 'Git', 'priority' => 'low', 'suggestion' => 'Test'],
                // Ungültig: nicht-String-Werte
                ['gap' => 123, 'priority' => 'medium', 'suggestion' => 'Test', 'example_phrase' => 'Example'],
                // Ungültig: ungültige priority
                ['gap' => 'MySQL', 'priority' => 'critical', 'suggestion' => 'Test', 'example_phrase' => 'Example'],
            ],
        ];

        $result = $action->execute($data, $request);

        expect($result->recommendations)->toHaveCount(1);
        expect($result->recommendations[0]->gap)->toBe('Docker');
    });
});
