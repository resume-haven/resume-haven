<?php

declare(strict_types=1);

/**
 * Layer Architecture Tests (DDD)
 *
 * Tests Domain-Driven Design layered architecture:
 * - Domain Layer: Core business logic, framework-independent
 * - Application Layer: Use cases, orchestration, CQRS handlers
 * - Infrastructure Layer: External dependencies, persistence, APIs
 * - UI Layer: HTTP controllers, CLI commands, API endpoints
 */

// ============================================================================
// DOMAIN LAYER - The Heart of the Application
// ============================================================================

// Domain Entities
arch('entities are in domain')
    ->expect('App\Domain\Entities')
    ->toBeClasses()
    ->not->toBeAbstract();

arch('entities do not extend Eloquent')
    ->expect('App\Domain\Entities')
    ->not->toExtend('Illuminate\Database\Eloquent\Model');

// Value Objects
arch('value objects are readonly and final')
    ->expect('App\Domain\ValueObjects')
    ->toBeReadonly()
    ->toBeFinal();

// Domain Services
arch('domain services have correct suffix')
    ->expect('App\Domain\Services')
    ->classes()
    ->toHaveSuffix('Service')
    ->toBeFinal();

// Repository Interfaces
arch('repository interfaces are in domain')
    ->expect('App\Domain\Contracts')
    ->toBeInterfaces();

// ============================================================================
// APPLICATION LAYER - Use Cases & Orchestration
// ============================================================================

arch('application services are final')
    ->expect('App\Application\Services')
    ->classes()
    ->toBeFinal();

// ============================================================================
// INFRASTRUCTURE LAYER - External Dependencies
// ============================================================================

arch('infrastructure implements domain interfaces')
    ->expect('App\Infrastructure\Repositories')
    ->classes()
    ->toImplement('App\Domain\Contracts\RepositoryInterface')
    ->ignoring([
        'App\Infrastructure\Repositories\BaseRepository',
    ]);

// ============================================================================
// UI LAYER - Controllers & Presentation
// ============================================================================

arch('controllers are final')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toBeFinal()
    ->ignoring([
        'App\Http\Controllers\Controller', // Base controller is abstract
    ]);

// ============================================================================
// CROSS-CUTTING CONCERNS
// ============================================================================

arch('exceptions have correct suffix')
    ->expect([
        'App\Domain\Exceptions',
        'App\Application\Exceptions',
    ])
    ->classes()
    ->toHaveSuffix('Exception')
    ->toExtend('Exception');

