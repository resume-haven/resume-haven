<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Impressum-Seite ist erreichbar', function () {
    $response = $this->get(route('legal.impressum'));

    $response->assertStatus(200);
    $response->assertViewIs('legal.impressum');
});

test('Datenschutz-Seite ist erreichbar', function () {
    $response = $this->get(route('legal.datenschutz'));

    $response->assertStatus(200);
    $response->assertViewIs('legal.datenschutz');
});

test('Lizenzen-Seite ist erreichbar', function () {
    $response = $this->get(route('legal.lizenzen'));

    $response->assertStatus(200);
    $response->assertViewIs('legal.lizenzen');
});

test('Lizenzen-Seite zeigt Fallback wenn keine licenses.json vorhanden', function () {
    // Storage-Datei sicherstellen, dass sie nicht existiert
    \Illuminate\Support\Facades\Storage::fake();

    $response = $this->get(route('legal.lizenzen'));

    $response->assertStatus(200);
});

test('Lizenzen-Seite verarbeitet gueltige licenses.json', function () {
    \Illuminate\Support\Facades\Storage::fake('local');
    \Illuminate\Support\Facades\Storage::put('licenses.json', json_encode([
        'php' => [['name' => 'laravel/framework', 'version' => '12.0', 'license' => 'MIT', 'description' => 'The Laravel Framework']],
        'node' => [],
        'generated_at' => '2026-03-10T12:00:00+00:00',
    ], JSON_THROW_ON_ERROR));

    $response = $this->get(route('legal.lizenzen'));

    $response->assertStatus(200);
});
