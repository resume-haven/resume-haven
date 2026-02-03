<?php

declare(strict_types=1);

/**
 * Remove declare(strict_types=1) from all PHP files
 *
 * This script removes strict type declarations from PHP files.
 * Useful for cleanup or troubleshooting.
 *
 * Usage: php scripts/remove-strict-types.php
 */
$directories = ['app', 'bootstrap', 'database', 'routes', 'tests'];
$filesProcessed = 0;

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
            $originalContent = $content;

            // Remove all "declare(strict_types=1);" lines
            $content = preg_replace("/^\s*declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;\s*\n?/m", '', $content);

            if ($content !== $originalContent) {
                file_put_contents($file->getPathname(), $content);
                echo '✓ Removed strict types: '.$file->getPathname()."\n";
                $filesProcessed++;
            }
        }
    }
}

echo "\n";
echo "Files processed: {$filesProcessed}\n";
echo "\nDone!\n";
