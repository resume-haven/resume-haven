<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Contracts\ResumeStatusHistoryRepositoryInterface;
use App\Infrastructure\Persistence\ResumeStatusHistoryModel;
use DateTimeImmutable;

final class ResumeStatusHistoryRepository implements ResumeStatusHistoryRepositoryInterface
{
    public function record(int $resumeId, string $fromStatus, string $toStatus, DateTimeImmutable $changedAt): void
    {
        if ($resumeId <= 0) {
            return;
        }

        ResumeStatusHistoryModel::query()->create([
            'resume_id' => $resumeId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_at' => $changedAt,
        ]);
    }
}
