<?php

declare(strict_types=1);

use App\Domains\Analysis\UseCases\GenerateTagsUseCase\GenerateTagsAction;

it('GenerateTagsAction gruppiert Matches korrekt', function () {
    $action = new GenerateTagsAction();

    $matches = [
        ['requirement' => 'Frontend', 'experience' => 'React'],
        ['requirement' => 'Frontend', 'experience' => 'Vue'],
        ['requirement' => 'Backend', 'experience' => 'Laravel'],
    ];

    $gaps = ['Docker', 'Kubernetes'];

    $result = $action->execute($matches, $gaps);

    expect($result)->toHaveKey('matches');
    expect($result)->toHaveKey('gaps');
    expect($result['matches'])->toHaveCount(2);
    expect($result['gaps'])->toBe(['Docker', 'Kubernetes']);
});

it('GenerateTagsAction kombiniert mehrere Experiences pro Requirement', function () {
    $action = new GenerateTagsAction();

    $matches = [
        ['requirement' => 'Frontend', 'experience' => 'React'],
        ['requirement' => 'Frontend', 'experience' => 'Vue'],
        ['requirement' => 'Frontend', 'experience' => 'Angular'],
    ];

    $gaps = [];

    $result = $action->execute($matches, $gaps);

    expect($result['matches'])->toHaveCount(1);
    expect($result['matches'][0]['requirement'])->toBe('Frontend');
    expect($result['matches'][0]['experience'])->toBe(['React', 'Vue', 'Angular']);
});

it('GenerateTagsAction behandelt leere Matches korrekt', function () {
    $action = new GenerateTagsAction();

    $matches = [];
    $gaps = ['Gap1', 'Gap2'];

    $result = $action->execute($matches, $gaps);

    expect($result['matches'])->toBeEmpty();
    expect($result['gaps'])->toBe(['Gap1', 'Gap2']);
});

it('GenerateTagsAction behandelt leere Gaps korrekt', function () {
    $action = new GenerateTagsAction();

    $matches = [
        ['requirement' => 'PHP', 'experience' => '5 Jahre'],
    ];

    $gaps = [];

    $result = $action->execute($matches, $gaps);

    expect($result['matches'])->toHaveCount(1);
    expect($result['gaps'])->toBeEmpty();
});

it('GenerateTagsAction behält Reihenfolge der Gaps bei', function () {
    $action = new GenerateTagsAction();

    $matches = [
        ['requirement' => 'PHP', 'experience' => 'Laravel'],
    ];

    $gaps = ['Docker', 'Kubernetes', 'AWS', 'CI/CD'];

    $result = $action->execute($matches, $gaps);

    expect($result['gaps'])->toBe(['Docker', 'Kubernetes', 'AWS', 'CI/CD']);
});

