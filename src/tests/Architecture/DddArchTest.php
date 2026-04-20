<?php

declare(strict_types=1);

/**
 * DDD Architecture Tests
 *
 * Prüft die Bounded-Context-Grenzen und Domain-Isolation.
 *
 * Bekannte akzeptierte Kopplung:
 *   Analysis\UseCases\PresentationUseCase\BuildAnalysisComparisonAction
 *   verwendet Profile\Dto und Profile\Repositories für den Delta-Vergleich.
 *   Diese Kopplung ist bewusst und dokumentiert (Commit 25 – Delta-Engine).
 *   Daher wird nur auf granularer Ebene (Commands / Handlers) geprüft.
 */
arch('Profile domain has no dependency on Analysis domain')
    ->expect('App\Domains\Profile')
    ->not->toUse('App\Domains\Analysis');

arch('Analysis Commands have no dependency on Profile domain')
    ->expect('App\Domains\Analysis\Commands')
    ->not->toUse('App\Domains\Profile');

arch('Analysis Handlers have no dependency on Profile domain')
    ->expect('App\Domains\Analysis\Handlers')
    ->not->toUse('App\Domains\Profile');

arch('Profile Commands have no dependency on Analysis domain')
    ->expect('App\Domains\Profile\Commands')
    ->not->toUse('App\Domains\Analysis');

arch('Profile Queries have no dependency on Analysis domain')
    ->expect('App\Domains\Profile\Queries')
    ->not->toUse('App\Domains\Analysis');

arch('Domain classes do not access HTTP layer')
    ->expect('App\Domains')
    ->not->toUse('App\Http');

arch('Eloquent Models are not used directly in Domain UseCases')
    ->expect('App\Domains\Analysis\UseCases')
    ->not->toUse('App\Models');

arch('Eloquent Models are not used directly in Analysis Commands or Handlers')
    ->expect('App\Domains\Analysis\Commands')
    ->not->toUse('App\Models');

arch('Analysis Handlers do not use Eloquent Models directly')
    ->expect('App\Domains\Analysis\Handlers')
    ->not->toUse('App\Models');
