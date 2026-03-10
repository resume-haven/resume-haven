<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\GenerateTokenAction;

it('generiert URL-safe Base64 Token', function () {
    $action = new GenerateTokenAction();

    $token = $action->execute();

    expect($token)->toBeString();
    expect($token)->toMatch('/^[A-Za-z0-9_-]+$/');
    expect(strlen($token))->toBeGreaterThanOrEqual(40);
});

it('generiert eindeutige Token bei mehreren Aufrufen', function () {
    $action = new GenerateTokenAction();

    $tokens = [];
    for ($i = 0; $i < 50; $i++) {
        $tokens[] = $action->execute();
    }

    expect(count(array_unique($tokens)))->toBe(50);
});
