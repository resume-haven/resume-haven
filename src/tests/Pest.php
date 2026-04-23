<?php

declare(strict_types=1);
use Tests\TestCase;

// Pest global hooks and test setup

uses(TestCase::class)->in('Feature');
uses(TestCase::class)->in('Unit');
uses(TestCase::class)->in('Acceptance');
uses(TestCase::class)->in('Architecture');

// Optionale Helper, Plugins, etc.
