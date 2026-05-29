<?php

declare(strict_types=1);

namespace App\Domains\Profile\Queries;

use App\Domains\Profile\Dto\StoredResumePageDto;
use App\Domains\Profile\Handlers\ListStoredResumesHandler;

final class ListStoredResumesQuery
{
    public function __construct(
        public readonly int $userId,
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly ?string $currentToken = null,
        public readonly ?string $search = null,
        public readonly string $sort = 'updated_at',
        public readonly string $direction = 'desc',
    ) {}

    public function handle(ListStoredResumesHandler $handler): StoredResumePageDto
    {
        return $handler->handle($this);
    }
}
