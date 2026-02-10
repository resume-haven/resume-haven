<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Application\ReadModels\ResumeReadModel;

interface ResumeReadRepositoryInterface
{
    public function findById(int $id): ?ResumeReadModel;

    /**
     * @return list<ResumeReadModel>
     */
    public function list(int $limit, int $offset): array;

    public function countAll(): int;
}
