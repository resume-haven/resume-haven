<?php

declare(strict_types=1);

/**
 * Security Architecture Tests
 *
 * Verwendet das offizielle Pest Security Preset.
 * Prüft auf unsichere Funktionen wie eval, exec, md5, sha1, etc.
 */

// Security Preset
// Verhindert: md5, sha1, uniqid, rand, eval, exec, shell_exec, system,
// passthru, create_function, unserialize, extract, parse_str, etc.
arch()->preset()->security();

// Zusätzliche projektspezifische Security-Regeln

arch('no raw SQL queries')
    ->expect('App')
    ->not->toUse([
        'DB::raw',
        'DB::select',
        'DB::statement',
    ]);

arch('models use fillable or guarded')
    ->expect('App\Models')
    ->toHaveProperty('fillable')
    ->or->toHaveProperty('guarded');

arch('CSRF protection enabled')
    ->expect('App\Http\Middleware')
    ->toInclude('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken');
