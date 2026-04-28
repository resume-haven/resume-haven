<?php

declare(strict_types=1);

namespace App\Domains\Profile\Commands;

use App\Domains\Profile\Handlers\DeleteStoredResumeHandler;

final class DeleteStoredResumeCommand
{
    public function __construct(
        public readonly string $token,
    ) {}

    public function handle(DeleteStoredResumeHandler $handler): void
    {
        $handler->handle($this);
    }
}
