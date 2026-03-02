<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\ScoringUseCase\CalculateScoreAction;

it('berechnet Score korrekt mit Matches und Gaps', function () {
    $action = new CalculateScoreAction();

    $matches = [
        ['requirement' => 'PHP', 'experience' => 'PHP Developer'],
        ['requirement' => 'Laravel', 'experience' => 'Laravel Specialist'],
    ];
    $gaps = ['Database Design', 'DevOps'];

    $result = $action->execute($matches, $gaps);

    // Score = 2 / (2 + 2) * 100 = 50%
    expect($result->percentage)->toBe(50);
    expect($result->matchCount)->toBe(2);
    expect($result->gapCount)->toBe(2);
    expect($result->rating)->toContain('Mittlere');
});

it('berechnet 100% Score wenn keine Gaps', function () {
    $action = new CalculateScoreAction();

    $matches = [
        ['requirement' => 'PHP', 'experience' => 'PHP'],
        ['requirement' => 'Laravel', 'experience' => 'Laravel'],
        ['requirement' => 'Database', 'experience' => 'PostgreSQL'],
    ];
    $gaps = [];

    $result = $action->execute($matches, $gaps);

    expect($result->percentage)->toBe(100);
    expect($result->rating)->toContain('Hohe');
    expect($result->barColor)->toBe('bg-green-500');
});

it('berechnet 0% Score wenn keine Matches', function () {
    $action = new CalculateScoreAction();

    $matches = [];
    $gaps = ['PHP', 'Laravel', 'Database'];

    $result = $action->execute($matches, $gaps);

    expect($result->percentage)->toBe(0);
    expect($result->rating)->toContain('Geringe');
    expect($result->barColor)->toBe('bg-red-500');
});

it('gibt korrekte Farben für verschiedene Scores', function () {
    $action = new CalculateScoreAction();

    // Hoher Score (>= 70%)
    $highScore = $action->execute(
        array_fill(0, 7, ['requirement' => 'X', 'experience' => 'Y']),
        array_fill(0, 3, 'Gap')
    );
    expect($highScore->percentage)->toBe(70);
    expect($highScore->barColor)->toBe('bg-green-500');

    // Mittlerer Score (40-70%)
    $mediumScore = $action->execute(
        array_fill(0, 5, ['requirement' => 'X', 'experience' => 'Y']),
        array_fill(0, 5, 'Gap')
    );
    expect($mediumScore->percentage)->toBe(50);
    expect($mediumScore->barColor)->toBe('bg-yellow-500');

    // Niedriger Score (< 40%)
    $lowScore = $action->execute(
        array_fill(0, 2, ['requirement' => 'X', 'experience' => 'Y']),
        array_fill(0, 8, 'Gap')
    );
    expect($lowScore->percentage)->toBe(20);
    expect($lowScore->barColor)->toBe('bg-red-500');
});
