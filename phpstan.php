<?php

declare(strict_types=1);

return \Larastan\Larastan\Configuration::make()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database/factories',
        __DIR__ . '/database/migrations',
        __DIR__ . '/database/seeders',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withExcludePaths([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/node_modules',
        __DIR__ . '/tests/Feature/ExampleTest.php',
        __DIR__ . '/tests/Unit/ExampleTest.php',
    ])
    // Strict Level: 8 = sehr strikt (fast Maximum)
    // Levels: 0 (most lenient) ... 9 (strictest)
    ->withLevel(8)
    // Strict Mode aktiviert für zusätzliche Checks
    ->withStrictRules(true)
    // Bleeding Edge für PHP 8.5+ Features
    ->withBleeding(true)
    // Baseline für bekannte Fehler
    ->withBaseline(__DIR__ . '/phpstan-baseline.neon')
    // Type Declaration Checks
    ->withCheckMissingIterableValueType(true)
    ->withCheckGenericClassInNonGenericObjectType(true)
    // PHPDoc Checks
    ->withCheckPhpDocMissingReturn(true)
    ->withCheckExplicitMixedMissingReturn(true)
    // Error Reporting
    ->withReportUnmatchedIgnoredErrors(true)
    // Casing Check
    ->withCheckInternalClassCasing(true)
    // Cache für schnellere Analysen
    ->withCachePath(__DIR__ . '/storage/phpstan')
    ->toArray();
