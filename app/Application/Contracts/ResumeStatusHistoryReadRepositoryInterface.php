<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Application\ReadModels\ResumeStatusHistoryReadModel;

interface ResumeStatusHistoryReadRepositoryInterface
{
    /**
     * @return list<ResumeStatusHistoryReadModel>
     */
    public function listForResume(int $resumeId): array;
}
