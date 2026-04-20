<?php

declare(strict_types=1);

// Pest global hooks and test setup

uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit');
uses(Tests\TestCase::class)->in('Acceptance');
uses(Tests\TestCase::class)->in('Architecture');

// Optionale Helper, Plugins, etc.
