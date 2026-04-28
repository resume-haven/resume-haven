<?php

declare(strict_types=1);

namespace App\Domains\Profile\Handlers;

use App\Domains\Profile\Commands\DeleteStoredResumeCommand;
use App\Domains\Profile\Repositories\ProfileRepository;

final class DeleteStoredResumeHandler
{
    public function __construct(
        private ProfileRepository $repository,
    ) {}

    public function handle(DeleteStoredResumeCommand $command): void
    {
        $this->repository->deleteByToken($command->token);
    }
}
