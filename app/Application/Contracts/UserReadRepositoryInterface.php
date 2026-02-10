<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Application\ReadModels\UserReadModel;

interface UserReadRepositoryInterface
{
    public function findById(int $id): ?UserReadModel;

    /**
     * @return list<UserReadModel>
     */
    public function list(int $limit, int $offset): array;

    public function countAll(): int;
}
