<?php

declare(strict_types=1);

/**
 * Layer Architecture Tests
 * 
 * Testet die Einhaltung der Layer-Architektur gemäß DDD:
 * - Domain Layer (Kernlogik, unabhängig)
 * - Application Layer (Use Cases, Services)
 * - Infrastructure Layer (Externe Abhängigkeiten)
 * - UI Layer (HTTP, CLI, API)
 */

arch('domain layer does not depend on infrastructure')
    ->expect('App\Domain')
    ->not->toUse('App\Infrastructure');

arch('domain layer does not depend on UI')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Http',
        'App\Console',
    ]);

arch('application layer uses domain layer')
    ->expect('App\Application')
    ->toUse('App\Domain')
    ->not->toUse('App\Infrastructure'); // Nur über Interfaces

arch('infrastructure implements domain interfaces')
    ->expect('App\Infrastructure')
    ->toImplement('App\Domain\Contracts')
    ->or->toUse('App\Domain');

arch('HTTP layer only in UI')
    ->expect('App\Http')
    ->toOnlyBeUsedIn([
        'App\Http',
        'Tests',
    ]);

arch('controllers use application services')
    ->expect('App\Http\Controllers')
    ->toUse([
        'App\Application\Services',
        'App\Application\Actions',
    ])
    ->not->toUse('App\Infrastructure'); // Keine direkten Infrastructure-Calls

arch('no database access in controllers')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Database\Eloquent\Model',
    ]);
