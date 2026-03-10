<?php

declare(strict_types=1);

namespace App\Domains\Profile\Commands;

use App\Domains\Profile\Dto\ResumeTokenDto;
use App\Domains\Profile\Dto\StoreResumeDto;
use App\Domains\Profile\Handlers\StoreResumeHandler;

class StoreResumeCommand
{
    public function __construct(
        public readonly StoreResumeDto $request,
    ) {}

    public function handle(StoreResumeHandler $handler): ResumeTokenDto
    {
        return $handler->handle($this);
    }
}
