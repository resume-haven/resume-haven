<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Use TestCase for all Feature tests
uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/**
 * Custom Pest Expectations
 * Extend expect() with custom assertions
 */

// Check if value is within a range
expect()->extend('toBeWithinRange', function (int $min, int $max) {
    return $this
        ->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);
});

// Check if value is a valid UUID
expect()->extend('toBeValidUuid', function () {
    $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    return $this->toMatch($uuidPattern);
});

// Check if response has specific status
expect()->extend('toHaveStatus', function (int $status) {
    return $this->response->assertStatus($status);
});

// Check if response is successful (2xx)
expect()->extend('toBeSuccessful', function () {
    return $this->response->assertSuccessful();
});

// Check if response is OK (200)
expect()->extend('toBeOk', function () {
    return $this->response->assertOk();
});

// Check if response is created (201)
expect()->extend('toBeCreated', function () {
    return $this->response->assertCreated();
});

// Check if response is unauthorized (401)
expect()->extend('toBeUnauthorized', function () {
    return $this->response->assertUnauthorized();
});

// Check if response is forbidden (403)
expect()->extend('toBeForidden', function () {
    return $this->response->assertForbidden();
});

// Check if response is not found (404)
expect()->extend('toBeNotFound', function () {
    return $this->response->assertNotFound();
});
