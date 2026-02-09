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

// NOTE: Property-level checks on model classes are unreliable in the current
// Pest Arch preset version. Keep model fillable rules in code review instead.

arch('CSRF protection enabled')
    ->expect('App\\Http\\Middleware\\VerifyCsrfToken')
    ->toExtend('Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken');
