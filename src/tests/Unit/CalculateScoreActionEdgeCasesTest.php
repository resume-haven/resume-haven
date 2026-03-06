<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\ScoringUseCase\CalculateScoreAction;

describe('CalculateScoreAction Edge Cases', function () {
    test('gibt 0% bei 0 Matches und 0 Gaps', function () {
        $action = new CalculateScoreAction();
        $result = $action->execute([], []);

        expect($result->percentage)->toBe(0);
        expect($result->matchCount)->toBe(0);
        expect($result->gapCount)->toBe(0);
        expect($result->rating)->toBe('Geringe Übereinstimmung');
    });

    test('gibt 100% bei nur Matches ohne Gaps', function () {
        $action = new CalculateScoreAction();
        $matches = [
            ['requirement' => 'PHP', 'experience' => 'PHP'],
            ['requirement' => 'Laravel', 'experience' => 'Laravel'],
        ];

        $result = $action->execute($matches, []);

        expect($result->percentage)->toBe(100);
        expect($result->matchCount)->toBe(2);
        expect($result->gapCount)->toBe(0);
        expect($result->rating)->toBe('Hohe Übereinstimmung');
        expect($result->bgColor)->toBe('bg-green-50');
    });

    test('gibt 0% bei nur Gaps ohne Matches', function () {
        $action = new CalculateScoreAction();

        $result = $action->execute([], ['Docker', 'Kubernetes']);

        expect($result->percentage)->toBe(0);
        expect($result->matchCount)->toBe(0);
        expect($result->gapCount)->toBe(2);
        expect($result->rating)->toBe('Geringe Übereinstimmung');
        expect($result->bgColor)->toBe('bg-red-50');
    });

    test('berechnet 70% korrekt (Hohe Übereinstimmung)', function () {
        $action = new CalculateScoreAction();
        $matches = [
            ['requirement' => 'PHP', 'experience' => 'PHP'],
            ['requirement' => 'Laravel', 'experience' => 'Laravel'],
            ['requirement' => 'MySQL', 'experience' => 'MySQL'],
            ['requirement' => 'Git', 'experience' => 'Git'],
            ['requirement' => 'REST', 'experience' => 'REST'],
            ['requirement' => 'TDD', 'experience' => 'TDD'],
            ['requirement' => 'OOP', 'experience' => 'OOP'],
        ];
        $gaps = ['Docker', 'Kubernetes', 'AWS'];

        $result = $action->execute($matches, $gaps);

        expect($result->percentage)->toBe(70);
        expect($result->rating)->toBe('Hohe Übereinstimmung');
        expect($result->bgColor)->toBe('bg-green-50');
        expect($result->textColor)->toBe('text-green-900');
        expect($result->barColor)->toBe('bg-green-500');
    });

    test('berechnet 50% korrekt (Mittlere Übereinstimmung)', function () {
        $action = new CalculateScoreAction();
        $matches = [
            ['requirement' => 'PHP', 'experience' => 'PHP'],
            ['requirement' => 'Laravel', 'experience' => 'Laravel'],
        ];
        $gaps = ['Docker', 'Kubernetes'];

        $result = $action->execute($matches, $gaps);

        expect($result->percentage)->toBe(50);
        expect($result->rating)->toBe('Mittlere Übereinstimmung');
        expect($result->bgColor)->toBe('bg-yellow-50');
        expect($result->textColor)->toBe('text-yellow-900');
        expect($result->barColor)->toBe('bg-yellow-500');
    });

    test('berechnet 30% korrekt (Geringe Übereinstimmung)', function () {
        $action = new CalculateScoreAction();
        $matches = [
            ['requirement' => 'PHP', 'experience' => 'PHP'],
        ];
        $gaps = ['Docker', 'Kubernetes', 'AWS'];

        $result = $action->execute($matches, $gaps);

        expect($result->percentage)->toBe(25);
        expect($result->rating)->toBe('Geringe Übereinstimmung');
        expect($result->bgColor)->toBe('bg-red-50');
        expect($result->textColor)->toBe('text-red-900');
        expect($result->barColor)->toBe('bg-red-500');
    });

    test('rundet Prozentsatz korrekt', function () {
        $action = new CalculateScoreAction();
        $matches = [
            ['requirement' => 'PHP', 'experience' => 'PHP'],
            ['requirement' => 'Laravel', 'experience' => 'Laravel'],
        ];
        $gaps = ['Docker', 'Kubernetes', 'AWS', 'CI/CD', 'Terraform'];

        $result = $action->execute($matches, $gaps);

        // 2 / (2+5) = 2/7 = 0.2857 => 29%
        expect($result->percentage)->toBe(29);
    });
});

