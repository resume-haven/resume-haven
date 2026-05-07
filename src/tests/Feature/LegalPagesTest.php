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

test('Lizenzen-Seite nutzt Storage-Fallback wenn storage app Datei fehlt', function () {
    Storage::fake();

    $targetPath = storage_path('app/licenses.json');
    File::delete($targetPath);

    Storage::put('licenses.json', json_encode([
        'php' => [['name' => 'fallback/package', 'version' => '1.0.0', 'license' => 'MIT']],
        'node' => [],
        'generated_at' => '2026-05-05T08:00:00+00:00',
    ], JSON_THROW_ON_ERROR));

    $response = $this->get(route('legal.lizenzen'));

    $response->assertStatus(200);
    $response->assertViewHas('php');
    $response->assertViewHas('generated_at', '2026-05-05T08:00:00+00:00');
});

test('Lizenzen-Seite ignoriert ungueltiges JSON robust', function () {
    $targetPath = storage_path('app/licenses.json');
    $backupPath = storage_path('app/licenses.json.bak-invalid-json');

    if (File::exists($backupPath)) {
        File::delete($backupPath);
    }

    if (File::exists($targetPath)) {
        File::copy($targetPath, $backupPath);
    }

    try {
        File::put($targetPath, '{invalid json');

        $response = $this->get(route('legal.lizenzen'));

        $response->assertStatus(200);
        $response->assertViewHas('php', []);
        $response->assertViewHas('node', []);
        $response->assertViewHas('generated_at', null);
    } finally {
        File::delete($targetPath);

        if (File::exists($backupPath)) {
            File::move($backupPath, $targetPath);
        }
    }
});

test('Lizenzen-Seite verwendet Defaultwerte bei partiellen Keys', function () {
    $targetPath = storage_path('app/licenses.json');
    $backupPath = storage_path('app/licenses.json.bak-partial');

    if (File::exists($backupPath)) {
        File::delete($backupPath);
    }

    if (File::exists($targetPath)) {
        File::copy($targetPath, $backupPath);
    }

    try {
        File::put($targetPath, json_encode([
            'node' => [['name' => 'node-only', 'version' => '1.0.0', 'license' => 'MIT']],
        ], JSON_THROW_ON_ERROR));

        $response = $this->get(route('legal.lizenzen'));

        $response->assertStatus(200);
        $response->assertViewHas('php', []);
        $response->assertViewHas('node');
        $response->assertViewHas('generated_at', null);
    } finally {
        File::delete($targetPath);

        if (File::exists($backupPath)) {
            File::move($backupPath, $targetPath);
        }
    }
});
