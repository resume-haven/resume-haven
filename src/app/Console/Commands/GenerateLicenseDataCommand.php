<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLicenseDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licenses:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generiert eine JSON-Datei mit allen PHP- und Node-Package-Lizenzen';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📦 Generiere Lizenzdaten...');

        $licenses = [
            'php' => $this->parseComposerLock(),
            'node' => $this->parsePackageLockJson(),
            'generated_at' => now()->toIso8601String(),
        ];

        $outputPath = storage_path('app/licenses.json');
        $json = json_encode($licenses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            $this->error('❌ Fehler beim Encodieren der JSON-Daten');

            return self::FAILURE;
        }

        File::put($outputPath, $json);

        $phpCount = count($licenses['php']);
        $nodeCount = count($licenses['node']);

        $this->info("✅ Lizenzdaten generiert: {$phpCount} PHP-Pakete, {$nodeCount} Node-Pakete");
        $this->line("📄 Gespeichert in: {$outputPath}");

        return self::SUCCESS;
    }

    /**
     * Parst composer.lock und extrahiert Package-Informationen.
     *
     * @return array<int, array{name: string, version: string, license: string, homepage?: string}>
     */
    private function parseComposerLock(): array
    {
        $composerLockPath = base_path('composer.lock');

        if (! File::exists($composerLockPath)) {
            $this->warn('⚠️ composer.lock nicht gefunden');

            return [];
        }

        $content = File::get($composerLockPath);
        $composerLock = json_decode($content, true);

        if (! is_array($composerLock) || ! isset($composerLock['packages'])) {
            $this->error('❌ composer.lock hat ungültiges Format');

            return [];
        }

        $packages = $composerLock['packages'];
        assert(is_array($packages));

        $result = [];

        foreach ($packages as $package) {
            if (! is_array($package)) {
                continue;
            }

            $name = is_string($package['name'] ?? null) ? $package['name'] : 'unknown';
            $version = is_string($package['version'] ?? null) ? $package['version'] : 'unknown';
            $license = $this->formatLicense($package['license'] ?? []);
            $homepage = is_string($package['homepage'] ?? null) ? $package['homepage'] : null;

            $packageData = [
                'name' => $name,
                'version' => $version,
                'license' => $license,
            ];

            if ($homepage !== null) {
                $packageData['homepage'] = $homepage;
            }

            $result[] = $packageData;
        }

        // Sortiere alphabetisch nach Name
        usort($result, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $result;
    }

    /**
     * Parst package-lock.json und extrahiert Package-Informationen.
     *
     * @return array<int, array{name: string, version: string, license: string, homepage?: string}>
     */
    private function parsePackageLockJson(): array
    {
        $packageLockPath = base_path('package-lock.json');

        if (! File::exists($packageLockPath)) {
            $this->warn('⚠️ package-lock.json nicht gefunden');

            return [];
        }

        $content = File::get($packageLockPath);
        $packageLock = json_decode($content, true);

        if (! is_array($packageLock)) {
            $this->error('❌ package-lock.json hat ungültiges Format');

            return [];
        }

        $packages = [];

        // NPM v2+ Format: packages.<name>
        if (isset($packageLock['packages']) && is_array($packageLock['packages'])) {
            foreach ($packageLock['packages'] as $packagePath => $package) {
                if (! is_array($package) || $packagePath === '') {
                    continue; // Root-Package überspringen
                }

                $name = is_string($package['name'] ?? null) ? $package['name'] : basename($packagePath);
                $version = is_string($package['version'] ?? null) ? $package['version'] : 'unknown';
                $license = is_string($package['license'] ?? null) ? $package['license'] : 'unknown';
                $homepage = is_string($package['homepage'] ?? null) ? $package['homepage'] : null;

                $packageData = [
                    'name' => $name,
                    'version' => $version,
                    'license' => $license,
                ];

                if ($homepage !== null) {
                    $packageData['homepage'] = $homepage;
                }

                $packages[] = $packageData;
            }
        }

        // NPM v1 Format: dependencies.<name>
        if (isset($packageLock['dependencies']) && is_array($packageLock['dependencies'])) {
            foreach ($packageLock['dependencies'] as $name => $package) {
                if (! is_array($package)) {
                    continue;
                }

                $version = is_string($package['version'] ?? null) ? $package['version'] : 'unknown';
                $license = is_string($package['license'] ?? null) ? $package['license'] : 'unknown';
                $homepage = is_string($package['homepage'] ?? null) ? $package['homepage'] : null;

                $packageData = [
                    'name' => $name,
                    'version' => $version,
                    'license' => $license,
                ];

                if ($homepage !== null) {
                    $packageData['homepage'] = $homepage;
                }

                $packages[] = $packageData;
            }
        }

        // Sortiere alphabetisch nach Name
        usort($packages, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $packages;
    }

    /**
     * Formatiert License-Feld (kann String oder Array sein).
     *
     * @param array<string>|string|null $license
     */
    private function formatLicense(array|string|null $license): string
    {
        if (is_array($license)) {
            return implode(', ', $license);
        }

        if (is_string($license)) {
            return $license;
        }

        return 'unknown';
    }
}
