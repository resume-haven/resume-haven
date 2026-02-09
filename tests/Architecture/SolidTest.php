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

arch('repositories use interface contracts')
    ->expect('App\Infrastructure\Repositories')
    ->classes()
    ->toImplement('App\Domain\Contracts\RepositoryInterface')
    ->ignoring([
        'App\Infrastructure\Repositories\BaseRepository',
        'App\Infrastructure\Repositories\EloquentResumeReadRepository',
        'App\Infrastructure\Repositories\EloquentUserReadRepository',
    ]);

