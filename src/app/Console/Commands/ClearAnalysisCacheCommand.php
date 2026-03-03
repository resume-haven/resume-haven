<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AnalysisCache;
use Illuminate\Console\Command;

/**
 * Clear Analysis Cache Command
 *
 * Löscht Einträge aus der analysis_cache Tabelle.
 *
 * Verwendung:
 *   php artisan cache:clear-analysis                  # Alle Einträge löschen
 *   php artisan cache:clear-analysis --older-than=30  # Nur Einträge älter als 30 Tage
 *
 * Makefile:
 *   make cache-clear-analysis
 *
 * Cronjob (app/Console/Kernel.php):
 *   $schedule->command('cache:clear-analysis --older-than=30')->dailyAt('03:00');
 *
 * Exit Codes:
 *   0 - SUCCESS (Cache erfolgreich geleert)
 *   1 - FAILURE (Ungültige Parameter)
 */
class ClearAnalysisCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-analysis {--older-than= : Clear cache entries older than N days (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the analysis cache table. Optionally clear entries older than N days.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $olderThan = $this->option('older-than');

        if ($olderThan !== null) {
            // Implementierung für später
            $days = (int) $olderThan;
            if ($days < 0) {
                $this->error('Error: --older-than must be a positive number.');

                return self::FAILURE;
            }

            $date = now()->subDays($days);
            $deleted = AnalysisCache::where('updated_at', '<', $date)->delete();

            if ($deleted > 0) {
                $entriesWord = $deleted === 1 ? 'entry' : 'entries';
                $this->info("✓ Deleted {$deleted} cache {$entriesWord} older than {$days} days.");
            } else {
                $this->info("✓ No cache entries older than {$days} days found.");
            }
        } else {
            // MVP: Leere alle Einträge
            $count = AnalysisCache::query()->count();
            AnalysisCache::query()->truncate();

            if ($count > 0) {
                $entriesWord = $count === 1 ? 'entry' : 'entries';
                $this->info("✓ Cleared {$count} cache {$entriesWord}.");
            } else {
                $this->info('✓ Cache table is already empty.');
            }
        }

        return self::SUCCESS;
    }
}
