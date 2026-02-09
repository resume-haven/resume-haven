<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\ResumeReadRepositoryInterface;
use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\ReadModels\ResumeReadModel;

final class EloquentResumeReadRepository implements ResumeReadRepositoryInterface
{
    public function findById(int $id): ?ResumeReadModel
    {
        if ($id <= 0) {
            return null;
        }

        $model = ResumeModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return new ResumeReadModel(
            (int) $model->id,
            (string) $model->name,
            (string) $model->email,
        );
    }
}
