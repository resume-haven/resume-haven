<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\GetResumeQuery;
use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\ReadModels\ResumeReadModel;

final class GetResumeHandler
{
    public function __construct(private ResumeReadRepositoryInterface $resumes)
    {
    }

    public function handle(GetResumeQuery $query): ?ResumeReadModel
    {
        return $this->resumes->findById($query->id);
    }
}
