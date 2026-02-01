<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadonlyPropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyPromotionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Laravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withExcludePaths([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/node_modules',
    ])
    ->withSets([
        LaravelSetList::LARAVEL_12_0,
        LevelSetList::UP_TO_PHP_81,
    ])
    ->withRules([
        ReadonlyPropertyRector::class,
        ClassPropertyPromotionRector::class,
    ])
    ->withPhpVersion('8.5')
    ->withSkip([
        __DIR__ . '/tests/Feature/ExampleTest.php',
        __DIR__ . '/tests/Unit/ExampleTest.php',
    ]);
