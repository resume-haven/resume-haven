<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->composerLockPath = base_path('composer.lock');
    $this->packageLockPath = base_path('package-lock.json');
    $this->licensesPath = storage_path('app/licenses.json');

    $this->composerLockBackup = File::exists($this->composerLockPath)
        ? File::get($this->composerLockPath)
        : null;
    $this->packageLockBackup = File::exists($this->packageLockPath)
        ? File::get($this->packageLockPath)
        : null;
});

afterEach(function () {
    if (is_string($this->composerLockBackup)) {
        File::put($this->composerLockPath, $this->composerLockBackup);
    } else {
        File::delete($this->composerLockPath);
    }

    if (is_string($this->packageLockBackup)) {
        File::put($this->packageLockPath, $this->packageLockBackup);
    } else {
        File::delete($this->packageLockPath);
    }

    File::delete($this->licensesPath);
});

it('generiert licenses.json mit php und node struktur', function () {
    $path = $this->licensesPath;
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
    $path = $this->licensesPath;

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

it('generiert leere Paketlisten wenn Lock-Dateien fehlen', function () {
    File::delete($this->composerLockPath);
    File::delete($this->packageLockPath);

    $exitCode = Artisan::call('licenses:generate');
    $data = json_decode((string) File::get($this->licensesPath), true);
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($data)->toBeArray()
        ->and($data['php'] ?? null)->toBe([])
        ->and($data['node'] ?? null)->toBe([])
        ->and($output)->toContain('composer.lock nicht gefunden')
        ->and($output)->toContain('package-lock.json nicht gefunden');
});

it('behandelt ungültige Lock-Formate robust', function () {
    File::put($this->composerLockPath, json_encode([
        'packages' => 'ungueltig',
    ], JSON_THROW_ON_ERROR));
    File::put($this->packageLockPath, 'null');

    $exitCode = Artisan::call('licenses:generate');
    $data = json_decode((string) File::get($this->licensesPath), true);
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($data)->toBeArray()
        ->and($data['php'] ?? null)->toBe([])
        ->and($data['node'] ?? null)->toBe([])
        ->and($output)->toContain('composer.lock hat ungültiges Format')
        ->and($output)->toContain('package-lock.json hat ungültiges Format');
});

it('parst, normalisiert und sortiert Composer- und NPM-Pakete', function () {
    File::put($this->composerLockPath, json_encode([
        'packages' => [
            [
                'name' => 'z-package',
                'version' => '2.0.0',
                'license' => ['MIT', 'Apache-2.0'],
            ],
            'ungueltiger-eintrag',
            [
                'license' => null,
                'homepage' => 123,
            ],
            [
                'name' => 'a-package',
                'version' => '1.0.0',
                'license' => 'BSD-3-Clause',
                'homepage' => 'https://example.com/a-package',
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    File::put($this->packageLockPath, json_encode([
        'packages' => [
            '' => [
                'name' => 'root-package',
                'version' => '0.0.0',
            ],
            'node_modules/zeta' => [
                'version' => '3.0.0',
                'license' => 'MIT',
                'homepage' => 'https://example.com/zeta',
            ],
            'node_modules/alpha' => [
                'name' => 'alpha',
                'license' => 123,
                'homepage' => ['invalid'],
            ],
            'node_modules/skip-me' => 'ungueltig',
        ],
        'dependencies' => [
            'beta' => [
                'version' => '4.0.0',
                'license' => 'Apache-2.0',
                'homepage' => 'https://example.com/beta',
            ],
            'gamma' => 'ungueltig',
        ],
    ], JSON_THROW_ON_ERROR));

    $exitCode = Artisan::call('licenses:generate');
    $data = json_decode((string) File::get($this->licensesPath), true);

    expect($exitCode)->toBe(0)
        ->and($data)->toBeArray();

    $phpPackages = $data['php'] ?? [];
    $nodePackages = $data['node'] ?? [];

    expect($phpPackages)->toBeArray()->toHaveCount(3)
        ->and(array_column($phpPackages, 'name'))->toBe(['a-package', 'unknown', 'z-package'])
        ->and($phpPackages[0]['homepage'] ?? null)->toBe('https://example.com/a-package')
        ->and($phpPackages[1]['version'] ?? null)->toBe('unknown')
        ->and($phpPackages[1]['license'] ?? null)->toBe('unknown')
        ->and(array_key_exists('homepage', $phpPackages[1]))->toBeFalse()
        ->and($phpPackages[2]['license'] ?? null)->toBe('MIT, Apache-2.0');

    expect($nodePackages)->toBeArray()->toHaveCount(3)
        ->and(array_column($nodePackages, 'name'))->toBe(['alpha', 'beta', 'zeta'])
        ->and($nodePackages[0]['version'] ?? null)->toBe('unknown')
        ->and($nodePackages[0]['license'] ?? null)->toBe('unknown')
        ->and(array_key_exists('homepage', $nodePackages[0]))->toBeFalse()
        ->and($nodePackages[1]['homepage'] ?? null)->toBe('https://example.com/beta')
        ->and($nodePackages[2]['homepage'] ?? null)->toBe('https://example.com/zeta');
});

it('unterstuetzt package-lock v1 ohne packages-block', function () {
    File::put($this->composerLockPath, json_encode([
        'packages' => [],
    ], JSON_THROW_ON_ERROR));

    File::put($this->packageLockPath, json_encode([
        'dependencies' => [
            'z-package' => [
                'version' => '2.0.0',
                'license' => 'MIT',
            ],
            'a-package' => [
                'version' => '1.0.0',
                'license' => 'Apache-2.0',
                'homepage' => 'https://example.com/a-package',
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    $exitCode = Artisan::call('licenses:generate');
    $data = json_decode((string) File::get($this->licensesPath), true);

    expect($exitCode)->toBe(0)
        ->and($data)->toBeArray()
        ->and(array_column($data['node'] ?? [], 'name'))->toBe(['a-package', 'z-package'])
        ->and(($data['node'][0]['homepage'] ?? null))->toBe('https://example.com/a-package');
});

it('setzt leere Composer-Lizenzlisten auf unknown', function () {
    File::put($this->composerLockPath, json_encode([
        'packages' => [
            [
                'name' => 'package-with-empty-license',
                'version' => '1.2.3',
                'license' => [],
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    File::put($this->packageLockPath, json_encode([
        'packages' => [],
    ], JSON_THROW_ON_ERROR));

    Artisan::call('licenses:generate');

    $data = json_decode((string) File::get($this->licensesPath), true);

    expect($data['php'][0]['name'] ?? null)->toBe('package-with-empty-license');
    expect($data['php'][0]['license'] ?? null)->toBe('unknown');
});
