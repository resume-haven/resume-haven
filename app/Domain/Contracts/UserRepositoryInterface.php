<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Entities\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?User;

    /**
     * @param User $entity
     */
    public function save(object $entity): void;
}
