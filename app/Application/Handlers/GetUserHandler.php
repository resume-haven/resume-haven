<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\GetUserQuery;
use App\Application\Contracts\UserReadRepositoryInterface;
use App\Infrastructure\ReadModels\UserReadModel;

final class GetUserHandler
{
    public function __construct(private UserReadRepositoryInterface $users)
    {
    }

    public function handle(GetUserQuery $query): ?UserReadModel
    {
        return $this->users->findById($query->id);
    }
}
