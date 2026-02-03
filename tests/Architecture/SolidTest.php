<?php

declare(strict_types=1);

/**
 * SOLID Principles Tests
 * 
 * Prüft die Einhaltung der SOLID-Prinzipien.
 * Diese Tests sind projektspezifisch und verwenden keine Presets.
 */

// Interface Segregation Principle
arch('interfaces are focused and not too large')
    ->expect('App\Contracts')
    ->toBeInterfaces();

// Dependency Inversion Principle
arch('high-level modules depend on abstractions')
    ->expect('App\Services')
    ->toOnlyDependOn([
        'App\Contracts',
        'App\Domain',
        'Illuminate\Contracts',
    ])
    ->not->toUse([
        'App\Infrastructure\Repositories', // Nur Interface nutzen
    ]);

arch('repositories use interface contracts')
    ->expect('App\Infrastructure\Repositories')
    ->toImplement('App\Domain\Contracts\RepositoryInterface')
    ->or->toUse('App\Domain\Contracts');
