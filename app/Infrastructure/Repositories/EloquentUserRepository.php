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
        $model = $this->findModel($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    /**
     * @param User $entity
     */
    public function save(object $entity): void
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException('Expected User entity.');
        }

        $model = $this->findModel($entity->id) ?? new UserModel();
        $this->applyEntity($entity, $model);
        $model->save();
    }

    public function delete(int $id): void
    {
        UserModel::query()->whereKey($id)->delete();
    }

    private function findModel(int $id): ?UserModel
    {
        if ($id <= 0) {
            return null;
        }

        return UserModel::query()->find($id);
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            (int) $model->id,
            (string) $model->name,
            new Email((string) $model->email),
            (string) $model->password,
        );
    }

    private function applyEntity(User $entity, UserModel $model): void
    {
        $model->name = $entity->name;
        $model->email = $entity->email->value;
        $model->password = $entity->passwordHash;
    }
}
