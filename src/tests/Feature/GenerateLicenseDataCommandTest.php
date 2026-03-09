<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('generiert licenses.json mit php und node struktur', function () {
    $path = storage_path('app/licenses.json');
    File::delete($path);

    $exitCode = Artisan::call('licenses:generate');

    expect($exitCode)->toBe(0);
    expect(File::exists($path))->toBeTrue();

    $data = json_decode((string) File::get($path), true);

    expect($data)->toBeArray();
    expect($data)->toHaveKeys(['php', 'node', 'generated_at']);
    expect($data['php'])->toBeArray();
    expect($data['node'])->toBeArray();
    expect($data['generated_at'])->toBeString();
});

it('enthaelt homepage nur wenn vorhanden', function () {
    $path = storage_path('app/licenses.json');

    Artisan::call('licenses:generate');

    expect(File::exists($path))->toBeTrue();

    $data = json_decode((string) File::get($path), true);

    expect($data)->toBeArray();

    $phpPackages = $data['php'] ?? [];
    $nodePackages = $data['node'] ?? [];

    expect($phpPackages)->toBeArray();
    expect($nodePackages)->toBeArray();

    foreach ($phpPackages as $package) {
        expect($package)->toHaveKeys(['name', 'version', 'license']);

        if (array_key_exists('homepage', $package)) {
            expect($package['homepage'])->toBeString();
            expect($package['homepage'])->not->toBe('');
        }
    }

    foreach ($nodePackages as $package) {
        expect($package)->toHaveKeys(['name', 'version', 'license']);

        if (array_key_exists('homepage', $package)) {
            expect($package['homepage'])->toBeString();
            expect($package['homepage'])->not->toBe('');
        }
    }
});
