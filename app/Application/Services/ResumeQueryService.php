<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\DTOs\ResumeDTO;

final class ResumeQueryService
{
    public function __construct(private ResumeReadRepositoryInterface $resumes)
    {
    }

    public function getById(int $id): ?ResumeDTO
    {
        $resume = $this->resumes->findById($id);

        if ($resume === null) {
            return null;
        }

        return new ResumeDTO($resume->id, $resume->name, $resume->email, $resume->status);
    }
}
