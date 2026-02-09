<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Entities\Resume;

interface ResumeRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Resume;

    /**
     * @param Resume $entity
     */
    public function save(object $entity): void;
}
