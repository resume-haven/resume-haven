<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
    Storage::fake();

    $response = $this->get(route('legal.lizenzen'));

    $response->assertStatus(200);
});

test('Lizenzen-Seite verarbeitet gueltige licenses.json', function () {
    $targetPath = storage_path('app/licenses.json');
    $backupPath = storage_path('app/licenses.json.bak-test');

    if (File::exists($backupPath)) {
        File::delete($backupPath);
    }

    if (File::exists($targetPath)) {
        File::copy($targetPath, $backupPath);
    }

    try {
        File::put($targetPath, json_encode([
            'php' => [['name' => 'laravel/framework', 'version' => '12.0', 'license' => 'MIT']],
            'node' => [],
            'generated_at' => '2026-03-10T12:00:00+00:00',
        ], JSON_THROW_ON_ERROR));

        $response = $this->get(route('legal.lizenzen'));

        $response->assertStatus(200);
        $response->assertSee('laravel/framework');
        $response->assertSee('2026-03-10T12:00:00+00:00');
        $response->assertDontSee('Version');
    } finally {
        File::delete($targetPath);

        if (File::exists($backupPath)) {
            File::move($backupPath, $targetPath);
        }
    }
});

test('Lizenzen-Seite zeigt Versionsspalte fuer Admins', function () {
    $targetPath = storage_path('app/licenses.json');
    $backupPath = storage_path('app/licenses.json.bak-test');

    if (File::exists($backupPath)) {
        File::delete($backupPath);
    }

    if (File::exists($targetPath)) {
        File::copy($targetPath, $backupPath);
    }

    $admin = User::factory()->admin()->create();

    try {
        File::put($targetPath, json_encode([
            'php' => [['name' => 'laravel/framework', 'version' => '12.0', 'license' => 'MIT']],
            'node' => [],
            'generated_at' => '2026-03-10T12:00:00+00:00',
        ], JSON_THROW_ON_ERROR));

        $response = $this->actingAs($admin)->get(route('legal.lizenzen'));

        $response->assertStatus(200);
        $response->assertSee('Version');
        $response->assertSee('12.0');
    } finally {
        File::delete($targetPath);

        if (File::exists($backupPath)) {
            File::move($backupPath, $targetPath);
        }
    }
});
