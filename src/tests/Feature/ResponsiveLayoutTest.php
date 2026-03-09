<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('home page hat viewport meta tag für responsive design', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('<meta name="viewport" content="width=device-width, initial-scale=1.0">', false);
});

test('layout hat mobile menu button (hamburger icon)', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // Prüfe auf Hamburger-Icon SVG-Path
    $response->assertSee('M4 6h16M4 12h16M4 18h16', false);
    // Prüfe auf Mobile-Menu-Button-Klasse
    $response->assertSee('md:hidden', false);
});

test('analyze form nutzt responsive grid layout', function () {
    $response = $this->get('/analyze');

    $response->assertStatus(200);
    // Prüfe auf Grid: 1 Column Mobile, 2 Columns Desktop
    $response->assertSee('grid-cols-1 lg:grid-cols-2', false);
});

test('result view rendert responsive score panel', function () {
    // Mock AI-Response
    config(['ai.provider' => 'mock', 'ai.mock.scenario' => 'realistic']);

    $response = $this->post('/analyze', [
        'job_text' => 'Test Job Description with some requirements that need to be tested for responsive layout '.str_repeat('x', 50),
        'cv_text' => 'Test CV content with experience and skills that should match or not match with the job posting '.str_repeat('x', 50),
    ]);

    $response->assertStatus(200);
    // Prüfe auf responsive Font-Sizes
    $response->assertSee('text-5xl sm:text-6xl lg:text-7xl', false);
});

test('footer nutzt responsive layout (vertical stack mobile, horizontal desktop)', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // Prüfe auf responsive Flex-Direction
    $response->assertSee('flex-col sm:flex-row', false);
});

test('alpine js ist für mobile menu geladen', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('alpinejs', false);
    $response->assertSee('x-data', false);
});
