<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

interface RepositoryInterface
{
    public function findById(int $id): ?object;

    public function save(object $entity): void;

    public function delete(int $id): void;
}
