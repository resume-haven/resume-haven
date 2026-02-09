<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;
use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\ReadModels\ResumeReadModel;

final class EloquentResumeRepository implements ResumeRepositoryInterface
{
    public function findById(int $id): ?Resume
    {
        $model = $this->findModel($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findReadModelById(int $id): ?ResumeReadModel
    {
        $model = $this->findModel($id);

        if ($model === null) {
            return null;
        }

        return $this->toReadModel($model);
    }

    /**
     * @param Resume $entity
     */
    public function save(object $entity): void
    {
        if (!$entity instanceof Resume) {
            throw new \InvalidArgumentException('Expected Resume entity.');
        }

        $model = $this->findModel($entity->id) ?? new ResumeModel();
        $this->applyEntity($entity, $model);
        $model->save();
    }

    public function delete(int $id): void
    {
        ResumeModel::query()->whereKey($id)->delete();
    }

    private function findModel(int $id): ?ResumeModel
    {
        if ($id <= 0) {
            return null;
        }

        return ResumeModel::query()->find($id);
    }

    private function toEntity(ResumeModel $model): Resume
    {
        return new Resume(
            (int) $model->id,
            (string) $model->name,
            new Email((string) $model->email),
        );
    }

    private function applyEntity(Resume $entity, ResumeModel $model): void
    {
        $model->name = $entity->name;
        $model->email = $entity->email->value;
    }

    private function toReadModel(ResumeModel $model): ResumeReadModel
    {
        return new ResumeReadModel(
            (int) $model->id,
            (string) $model->name,
            (string) $model->email,
        );
    }
}
