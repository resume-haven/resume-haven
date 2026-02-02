<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class)->in('Feature');

// Helper functions for tests
expect()->extend('toBeWithinRange', fn(int $min, int $max) => $this->toBeGreaterThanOrEqual($min)->toBeLessThanOrEqual($max));
