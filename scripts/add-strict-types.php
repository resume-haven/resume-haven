<?php

declare(strict_types=1);

/**
 * Add declare(strict_types=1) to all PHP files
 *
 * This script adds strict type declarations to PHP files in the specified directories.
 * It skips files that already have the declaration to ensure idempotency.
 *
 * Usage: php scripts/add-strict-types.php
 */
$directories = ['app', 'bootstrap', 'database', 'routes', 'tests'];
$filesProcessed = 0;
$filesSkipped = 0;

foreach ($directories as $dir) {
    if (! is_dir($dir)) {
        echo "Directory '{$dir}' not found, skipping...\n";

        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());

            // Skip if already has declare(strict_types=1)
            if (preg_match('/^\s*declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;/m', $content)) {
                $filesSkipped++;

                continue;
            }

            // Add declare(strict_types=1) after <?php
            $content = preg_replace(
                '/^<\?php\s*$/m',
                "<?php\n\ndeclare(strict_types=1);",
                $content,
                1
            );

            file_put_contents($file->getPathname(), $content);
            echo '✓ Added strict types: '.$file->getPathname()."\n";
            $filesProcessed++;
        }
    }
}

echo "\n";
echo "Summary:\n";
echo "  Files processed: {$filesProcessed}\n";
echo "  Files skipped:   {$filesSkipped}\n";
echo "\nDone!\n";
