<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadonlyPropertyRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LevelSetList::UP_TO_PHP_85,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_FACTORIES,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_STATIC_TO_INJECTION,
        LaravelSetList::LARAVEL_TESTING,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
    ])
    ->withRules([
        // PHP 8.x Features
        ReadonlyPropertyRector::class,
        AddOverrideAttributeToOverriddenMethodsRector::class,

        // Type Declarations
        DeclareStrictTypesRector::class,
        TypedPropertyFromAssignsRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        naming: false,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: false,
    )
    ->withPhpVersion(PhpVersion::PHP_85)
    ->withSkip([
        __DIR__.'/tests/Feature/ExampleTest.php',
        __DIR__.'/tests/Unit/ExampleTest.php',

        // Skip specific rules if needed
        AddOverrideAttributeToOverriddenMethodsRector::class => [
            __DIR__.'/app/Providers',
        ],
    ])
    ->withParallel(
        maxNumberOfProcess: 8,
        jobSize: 15,
    )
    ->withCache(
        cacheDirectory: __DIR__.'/storage/rector',
    );
