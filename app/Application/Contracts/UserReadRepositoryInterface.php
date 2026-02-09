<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Infrastructure\ReadModels\UserReadModel;

interface UserReadRepositoryInterface
{
    public function findById(int $id): ?UserReadModel;
}
