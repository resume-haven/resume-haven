<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\GetResumeQuery;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;

final class GetResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(GetResumeQuery $query): ?Resume
    {
        return $this->resumes->findById($query->id);
    }
}
