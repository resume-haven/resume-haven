<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\UserReadRepositoryInterface;
use App\Application\ReadModels\UserReadModel;
use App\Infrastructure\Persistence\UserModel;

final class EloquentUserReadRepository implements UserReadRepositoryInterface
{
    public function findById(int $id): ?UserReadModel
    {
        if ($id <= 0) {
            return null;
        }

        $model = UserModel::query()
            ->select(['id', 'name', 'email'])
            ->find($id);

        if ($model === null) {
            return null;
        }

        return new UserReadModel(
            (int) $model->id,
            (string) $model->name,
            (string) $model->email,
        );
    }
}
