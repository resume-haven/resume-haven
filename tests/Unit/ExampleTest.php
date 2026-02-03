<?php

declare(strict_types=1);

// Pest Test - Modern Format
test('true is true', function () {
    expect(true)->toBeTrue();
});

test('basic assertion', function () {
    expect(1 + 1)->toBe(2);
});
