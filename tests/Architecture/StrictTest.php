<?php

declare(strict_types=1);

/**
 * Strict Architecture Tests
 *
 * Verwendet das offizielle Pest Strict Preset.
 * Erzwingt strenge Regeln für Code-Qualität.
 */

// Strict Preset
// Erzwingt: keine protected Methods, keine abstract Klassen, strict types,
// strict equality (===), finale Klassen, keine sleep/usleep
arch()
	->preset()
	->strict()
	->ignoring([
		'App\\Http\\Controllers\\Controller',
		'App\\Infrastructure\\Persistence',
	]);
