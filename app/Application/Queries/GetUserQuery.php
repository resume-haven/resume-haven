<?php

declare(strict_types=1);

namespace App\Application\Queries;

final readonly class GetUserQuery
{
    public function __construct(public int $id)
    {
    }
}
