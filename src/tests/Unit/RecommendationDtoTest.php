<?php

declare(strict_types=1);

use App\Domains\Analysis\Dto\RecommendationDto;

describe('RecommendationDto', function () {
    test('erstellt DTO mit allen Properties', function () {
        $dto = new RecommendationDto(
            gap: 'Laravel Framework',
            priority: 'high',
            suggestion: 'Lernen Sie Laravel',
            examplePhrase: 'Erfahrung mit Laravel'
        );

        expect($dto->gap)->toBe('Laravel Framework');
        expect($dto->priority)->toBe('high');
        expect($dto->suggestion)->toBe('Lernen Sie Laravel');
        expect($dto->examplePhrase)->toBe('Erfahrung mit Laravel');
    });

    test('toArray() liefert korrektes Array', function () {
        $dto = new RecommendationDto(
            gap: 'Docker',
            priority: 'medium',
            suggestion: 'Lernen Sie Docker',
            examplePhrase: 'Docker-Kenntnisse'
        );

        $array = $dto->toArray();

        expect($array)->toBe([
            'gap' => 'Docker',
            'priority' => 'medium',
            'suggestion' => 'Lernen Sie Docker',
            'example_phrase' => 'Docker-Kenntnisse',
        ]);
    });

    test('getColor() liefert korrekte Farben', function () {
        expect((new RecommendationDto('test', 'high', 'sug', 'ex'))->getColor())->toBe('red');
        expect((new RecommendationDto('test', 'medium', 'sug', 'ex'))->getColor())->toBe('yellow');
        expect((new RecommendationDto('test', 'low', 'sug', 'ex'))->getColor())->toBe('green');
    });

    test('getBadgeClasses() liefert korrekte CSS-Klassen', function () {
        $high = new RecommendationDto('test', 'high', 'sug', 'ex');
        expect($high->getBadgeClasses())->toContain('bg-red-100');
        expect($high->getBadgeClasses())->toContain('text-red-800');

        $medium = new RecommendationDto('test', 'medium', 'sug', 'ex');
        expect($medium->getBadgeClasses())->toContain('bg-yellow-100');
        expect($medium->getBadgeClasses())->toContain('text-yellow-800');

        $low = new RecommendationDto('test', 'low', 'sug', 'ex');
        expect($low->getBadgeClasses())->toContain('bg-green-100');
        expect($low->getBadgeClasses())->toContain('text-green-800');
    });

    test('getPriorityLabel() liefert deutsche Labels', function () {
        expect((new RecommendationDto('test', 'high', 'sug', 'ex'))->getPriorityLabel())->toBe('Hoch');
        expect((new RecommendationDto('test', 'medium', 'sug', 'ex'))->getPriorityLabel())->toBe('Mittel');
        expect((new RecommendationDto('test', 'low', 'sug', 'ex'))->getPriorityLabel())->toBe('Niedrig');
    });

    test('DTO ist immutable (readonly)', function () {
        $dto = new RecommendationDto('gap', 'high', 'sug', 'ex');

        // Versuche Property zu ändern sollte Error werfen
        // @phpstan-ignore-next-line (erwarteter Fehler bei readonly-Property)
        expect(fn () => $dto->gap = 'new value')
            ->toThrow(\Error::class);
    });
});
