<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Application\ReadModels\ResumeReadModel;
use App\Infrastructure\Persistence\ResumeModel;

final class EloquentResumeReadRepository implements ResumeReadRepositoryInterface
{
    public function findById(int $id): ?ResumeReadModel
    {
        if ($id <= 0) {
            return null;
        }

        $model = ResumeModel::query()
            ->select(['id', 'name', 'email', 'status'])
            ->find($id);

        if ($model === null) {
            return null;
        }

        return new ResumeReadModel(
            (int) $model->id,
            (string) $model->name,
            (string) $model->email,
            (string) ($model->status ?? 'draft'),
        );
    }

    public function list(int $limit, int $offset): array
    {
        if ($limit <= 0 || $offset < 0) {
            return [];
        }

        $models = ResumeModel::query()
            ->select(['id', 'name', 'email', 'status'])
            ->orderByDesc('id')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models->map(static function (ResumeModel $model): ResumeReadModel {
            return new ResumeReadModel(
                (int) $model->id,
                (string) $model->name,
                (string) $model->email,
                (string) ($model->status ?? 'draft'),
            );
        })->all();
    }

    public function countAll(): int
    {
        return (int) ResumeModel::query()->count();
    }
}
