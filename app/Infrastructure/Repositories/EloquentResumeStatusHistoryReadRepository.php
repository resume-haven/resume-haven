<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\ResumeStatusHistoryReadRepositoryInterface;
use App\Application\ReadModels\ResumeStatusHistoryReadModel;
use App\Infrastructure\Persistence\ResumeStatusHistoryModel;

final class EloquentResumeStatusHistoryReadRepository implements ResumeStatusHistoryReadRepositoryInterface
{
    public function listForResume(int $resumeId): array
    {
        if ($resumeId <= 0) {
            return [];
        }

        $entries = ResumeStatusHistoryModel::query()
            ->where('resume_id', $resumeId)
            ->orderBy('changed_at')
            ->get(['id', 'resume_id', 'from_status', 'to_status', 'changed_at']);

        return $entries->map(static function (ResumeStatusHistoryModel $model): ResumeStatusHistoryReadModel {
            return new ResumeStatusHistoryReadModel(
                (int) $model->id,
                (int) $model->resume_id,
                (string) $model->from_status,
                (string) $model->to_status,
                (string) $model->changed_at->toISOString(),
            );
        })->all();
    }
}
