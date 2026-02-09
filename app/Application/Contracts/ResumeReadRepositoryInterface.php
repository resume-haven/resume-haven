<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Application\ReadModels\ResumeReadModel;

interface ResumeReadRepositoryInterface
{
    public function findById(int $id): ?ResumeReadModel;
}
