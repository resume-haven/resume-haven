<?php

declare(strict_types=1);

/**
 * CQRS Architecture Tests
 *
 * Prüft die strikte Trennung von Commands (Write) und Queries (Read).
 *
 * CQRS-Regeln:
 *   - Commands ändern Zustand, dürfen keine Query-Klassen verwenden
 *   - Queries lesen Daten, dürfen keine Command-Klassen verwenden
 *   - Handler-Klassen bleiben innerhalb ihres Domains
 */
arch('Analysis Commands do not use Query classes')
    ->expect('App\Domains\Analysis\Commands')
    ->not->toUse('App\Domains\Analysis\Queries');

arch('Profile Commands do not use Query classes')
    ->expect('App\Domains\Profile\Commands')
    ->not->toUse('App\Domains\Profile\Queries');

arch('Profile Queries do not use Command classes')
    ->expect('App\Domains\Profile\Queries')
    ->not->toUse('App\Domains\Profile\Commands');

arch('Profile Queries do not use Analysis Commands')
    ->expect('App\Domains\Profile\Queries')
    ->not->toUse('App\Domains\Analysis\Commands');

arch('Commands are in Commands namespace')
    ->expect('App\Domains\Analysis\Commands')
    ->toHaveSuffix('Command');

arch('Profile Commands are in Commands namespace')
    ->expect('App\Domains\Profile\Commands')
    ->toHaveSuffix('Command');

arch('Profile Queries are in Queries namespace')
    ->expect('App\Domains\Profile\Queries')
    ->toHaveSuffix('Query');

arch('Analysis Handlers are in Handlers namespace')
    ->expect('App\Domains\Analysis\Handlers')
    ->toHaveSuffix('Handler');

arch('Profile Handlers are in Handlers namespace')
    ->expect('App\Domains\Profile\Handlers')
    ->toHaveSuffix('Handler');
