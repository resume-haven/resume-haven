<?php

declare(strict_types=1);

/**
 * CQRS Architecture Tests
 *
 * Tests Command Query Responsibility Segregation patterns:
 * - Commands: Write operations (mutations)
 * - Queries: Read operations (no side effects)
 * - Handlers: Process commands/queries
 * - Events: Domain events for side effects
 */

// Commands should be in Command namespace
arch('commands are in correct namespace')
    ->expect('App\Application\Commands')
    ->toBeClasses()
    ->toHaveSuffix('Command');

arch('commands are readonly')
    ->expect('App\Application\Commands')
    ->toBeReadonly();

// Queries should be in Query namespace
arch('queries are in correct namespace')
    ->expect('App\Application\Queries')
    ->toBeClasses()
    ->toHaveSuffix('Query');

arch('queries are readonly')
    ->expect('App\Application\Queries')
    ->toBeReadonly();

// Handlers process commands and queries
arch('command handlers have correct naming')
    ->expect('App\Application\Handlers')
    ->classes()
    ->toHaveSuffix('Handler');

// Events for domain events
arch('domain events are in correct namespace')
    ->expect('App\Domain\Events')
    ->toBeClasses()
    ->toHaveSuffix('Event');

arch('domain events are readonly')
    ->expect('App\Domain\Events')
    ->toBeReadonly();

// DTOs (Data Transfer Objects)
arch('DTOs are readonly')
    ->expect('App\Application\DTOs')
    ->toBeReadonly()
    ->ignoring('App\Application\DTOs\Builders');

arch('DTOs have correct suffix')
    ->expect('App\Application\DTOs')
    ->classes()
    ->toHaveSuffix('DTO')
    ->ignoring('App\Application\DTOs\Builders');

// Read Models for queries
arch('read models are final and correctly named')
    ->expect('App\Infrastructure\ReadModels')
    ->classes()
    ->toBeFinal()
    ->toHaveSuffix('ReadModel');
