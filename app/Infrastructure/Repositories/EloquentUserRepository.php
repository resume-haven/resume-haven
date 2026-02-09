<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;
use App\Infrastructure\Persistence\UserModel;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $model = UserModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return new User(
            (int) $model->id,
            (string) $model->name,
            new Email((string) $model->email),
            (string) $model->password,
        );
    }

    /**
     * @param User $entity
     */
    public function save(object $entity): void
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException('Expected User entity.');
        }

        $model = UserModel::query()->find($entity->id) ?? new UserModel();
        $model->name = $entity->name;
        $model->email = $entity->email->value;
        $model->password = $entity->passwordHash;
        $model->save();
    }

    public function delete(int $id): void
    {
        UserModel::query()->whereKey($id)->delete();
    }
}
