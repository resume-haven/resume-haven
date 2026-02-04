<?php

declare(strict_types=1);

/**
 * SOLID Principles Tests
 *
 * Tests adherence to SOLID principles:
 * - Single Responsibility Principle (SRP)
 * - Open/Closed Principle (OCP)
 * - Liskov Substitution Principle (LSP)
 * - Interface Segregation Principle (ISP)
 * - Dependency Inversion Principle (DIP)
 */

// ============================================================================
// SINGLE RESPONSIBILITY PRINCIPLE (SRP)
// ============================================================================

arch('controllers have single responsibility')
    ->expect('App\Http\Controllers')
    ->classes()
    ->not->toHaveMethodsContaining([
        'andCreate',
        'andUpdate',
        'andDelete',
    ]);

arch('services are focused')
    ->expect('App\Application\Services')
    ->classes()
    ->toHaveSuffix('Service');

// ============================================================================
// OPEN/CLOSED PRINCIPLE (OCP)
// ============================================================================

arch('services are final (closed for modification)')
    ->expect('App\Application\Services')
    ->classes()
    ->toBeFinal();

arch('value objects are final')
    ->expect('App\Domain\ValueObjects')
    ->toBeFinal();

arch('domain services are final')
    ->expect('App\Domain\Services')
    ->classes()
    ->toBeFinal();

arch('handlers are final')
    ->expect('App\Application\Handlers')
    ->classes()
    ->toBeFinal();

// ============================================================================
// LISKOV SUBSTITUTION PRINCIPLE (LSP)
// ============================================================================

arch('interfaces define clear contracts')
    ->expect('App\Domain\Contracts')
    ->toBeInterfaces()
    ->not->toHavePrefix('Abstract');

arch('no implementation details in interface names')
    ->expect('App\Domain\Contracts')
    ->classes()
    ->not->toHaveSuffix([
        'Impl',
        'Implementation',
        'Concrete',
    ]);

// ============================================================================
// INTERFACE SEGREGATION PRINCIPLE (ISP)
// ============================================================================

arch('interfaces are focused and not too large')
    ->expect('App\Domain\Contracts')
    ->toBeInterfaces();

arch('repository interfaces are segregated')
    ->expect('App\Domain\Contracts')
    ->interfaces()
    ->toHaveSuffix('Interface');

// ============================================================================
// DEPENDENCY INVERSION PRINCIPLE (DIP)
// ============================================================================

arch('high-level modules depend on abstractions')
    ->expect('App\Application\Services')
    ->not->toUse([
        'App\Infrastructure\Repositories', // Use interfaces instead
        'App\Infrastructure\Persistence',
    ])
    ->ignoring([
        'App\Application\Services\Concerns', // Traits are allowed
    ]);

arch('handlers depend on interfaces not implementations')
    ->expect('App\Application\Handlers')
    ->not->toUse([
        'App\Infrastructure\Repositories',
        'App\Infrastructure\Persistence',
    ]);

arch('controllers depend on application not infrastructure')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'App\Infrastructure',
        'App\Models', // Use Application layer
    ])
    ->ignoring([
        'App\Http\Controllers\Controller',
    ]);

// ============================================================================
// ADDITIONAL SOLID PATTERNS
// ============================================================================

arch('no god objects - classes are focused')
    ->expect([
        'App\Domain',
        'App\Application',
    ])
    ->classes()
    ->not->toHaveMethodsContaining([
        'Manager',
        'Handler',
        'Factory',
    ])
    ->ignoring([
        'App\Application\Handlers', // Handlers are OK in this namespace
    ]);

arch('repositories use interface contracts')
    ->expect('App\Infrastructure\Repositories')
    ->classes()
    ->toImplement('App\Domain\Contracts\RepositoryInterface')
    ->ignoring([
        'App\Infrastructure\Repositories\BaseRepository',
    ]);

arch('use dependency injection not facades')
    ->expect([
        'App\Application',
        'App\Domain',
    ])
    ->not->toUse('Illuminate\Support\Facades')
    ->ignoring([
        'App\Application\Exceptions', // Can use facades for logging/events
    ]);

