<?php

declare(strict_types=1);

/**
 * Architecture Tests für ResumeHaven
 *
 * Diese Tests verwenden die offiziellen Pest ArchPresets für Laravel.
 * Das Laravel-Preset enthält Best Practices für Laravel-Anwendungen.
 */

// Laravel Preset
// Enthält: Traits, Enums, Models, Controllers, Jobs, Events, etc.
arch()
    ->preset()
    ->laravel()
    ->ignoring([
        'App\\Infrastructure\\Persistence',
    ]);

// PHP Preset
// Verhindert: var_dump, die, goto, global, ereg, mysql_* Funktionen, etc.
arch()
    ->preset()
    ->php()
    ->ignoring([
        'App\\Application\\Exceptions',
        'App\\Domain\\Exceptions',
    ]);

// Zusätzliche projektspezifische Regeln

arch('value objects are final and readonly')
    ->expect('App\Domain\ValueObjects')
    ->toBeFinal()
    ->toBeReadonly(); // PHP 8.2+ readonly classes

arch('DTOs are readonly')
    ->expect('App\Application\DTOs')
    ->toBeFinal()
    ->toBeReadonly();
