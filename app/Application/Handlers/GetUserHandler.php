<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\GetUserQuery;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;

final class GetUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(GetUserQuery $query): ?User
    {
        return $this->users->findById($query->id);
    }
}
