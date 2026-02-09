<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;
use App\Infrastructure\Persistence\ResumeModel;

final class EloquentResumeRepository implements ResumeRepositoryInterface
{
    public function findById(int $id): ?Resume
    {
        $model = ResumeModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return new Resume(
            (int) $model->id,
            (string) $model->name,
            new Email((string) $model->email),
        );
    }

    /**
     * @param Resume $entity
     */
    public function save(object $entity): void
    {
        if (!$entity instanceof Resume) {
            throw new \InvalidArgumentException('Expected Resume entity.');
        }

        $model = ResumeModel::query()->find($entity->id) ?? new ResumeModel();
        $model->name = $entity->name;
        $model->email = $entity->email->value;
        $model->save();
    }

    public function delete(int $id): void
    {
        ResumeModel::query()->whereKey($id)->delete();
    }
}
