<?php

declare(strict_types=1);

namespace App\Domains\Profile\Queries;

use App\Domains\Profile\Dto\LoadedResumeDto;
use App\Domains\Profile\Handlers\GetResumeByTokenHandler;

class GetResumeByTokenQuery
{
    public function __construct(
        public readonly string $token,
    ) {}

    public function handle(GetResumeByTokenHandler $handler): ?LoadedResumeDto
    {
        return $handler->handle($this);
    }
}
