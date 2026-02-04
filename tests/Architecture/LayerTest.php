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
 *
 * Rules:
 * - Domain depends on nothing (pure business logic)
 * - Application depends only on Domain
 * - Infrastructure implements Domain interfaces
 * - UI depends on Application, never on Infrastructure directly
 */

// ============================================================================
// DOMAIN LAYER - The Heart of the Application
// ============================================================================

arch('domain layer is framework-independent')
    ->expect('App\Domain')
    ->not->toUse([
        'Illuminate',
        'Laravel',
    ])
    ->ignoring([
        'App\Domain\Contracts', // Can use Illuminate\Contracts
    ]);

arch('domain layer does not depend on infrastructure')
    ->expect('App\Domain')
    ->not->toUse('App\Infrastructure');

arch('domain layer does not depend on UI')
    ->expect('App\Domain')
    ->not->toUse([
        'App\Http',
        'App\Console',
    ]);

arch('domain layer does not depend on application')
    ->expect('App\Domain')
    ->not->toUse('App\Application');

// Domain Entities
arch('entities are in domain')
    ->expect('App\Domain\Entities')
    ->toBeClasses()
    ->not->toBeAbstract()
    ->not->toBeReadonly(); // Entities are mutable

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

arch('application layer uses only domain and contracts')
    ->expect('App\Application')
    ->not->toUse([
        'App\Infrastructure',
        'App\Http',
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Support\Facades\DB',
    ])
    ->ignoring([
        'App\Application\Exceptions', // Can use framework exceptions
    ]);

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

arch('infrastructure can use Eloquent')
    ->expect('App\Infrastructure\Persistence')
    ->toUse('Illuminate\Database\Eloquent\Model');

arch('eloquent models are only in infrastructure')
    ->expect('Illuminate\Database\Eloquent\Model')
    ->toOnlyBeUsedIn([
        'App\Infrastructure',
        'App\Models', // Legacy models
        'Database',
        'Tests',
    ]);

// ============================================================================
// UI LAYER - Controllers & Presentation
// ============================================================================

arch('controllers use only application layer')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'App\Infrastructure',
        'App\Domain\Entities', // Use DTOs instead
        'Illuminate\Support\Facades\DB',
    ])
    ->ignoring([
        'App\Http\Controllers\Controller', // Base controller
    ]);

arch('controllers are final')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toBeFinal()
    ->ignoring([
        'App\Http\Controllers\Controller', // Base controller is abstract
    ]);

arch('no database access in controllers')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Database\Eloquent\Model',
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

arch('HTTP layer only used in UI and tests')
    ->expect('App\Http')
    ->toOnlyBeUsedIn([
        'App\Http',
        'Tests',
    ]);

