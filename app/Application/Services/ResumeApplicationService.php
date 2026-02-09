<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\ResumeDTO;
use App\Domain\Contracts\ResumeRepositoryInterface;

final class ResumeApplicationService
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function getById(int $id): ?ResumeDTO
    {
        $resume = $this->resumes->findById($id);

        if ($resume === null) {
            return null;
        }

        return new ResumeDTO($resume->id, $resume->name->value, $resume->email->value);
    }
}
