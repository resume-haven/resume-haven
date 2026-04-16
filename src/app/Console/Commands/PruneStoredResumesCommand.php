<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Profile\Repositories\ProfileRepository;
use Illuminate\Console\Command;

class PruneStoredResumesCommand extends Command
{
    protected $signature = 'profile:prune-stored-resumes';

    protected $description = 'Delete stored resumes that exceed the configured retention period.';

    public function __construct(
        private readonly ProfileRepository $profileRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $deleted = $this->profileRepository->pruneExpired();

        if ($deleted === 0) {
            $this->info('No expired stored resumes found.');

            return self::SUCCESS;
        }

        $entriesWord = $deleted === 1 ? 'entry' : 'entries';
        $this->info("Pruned {$deleted} expired stored resume {$entriesWord}.");

        return self::SUCCESS;
    }
}
