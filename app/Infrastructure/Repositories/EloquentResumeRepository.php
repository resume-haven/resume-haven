<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Infrastructure\Persistence\ResumeModel;

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
            new Name((string) $model->name),
            new Email((string) $model->email),
        );
    }

    private function applyEntity(Resume $entity, ResumeModel $model): void
    {
        $model->name = $entity->name->value;
        $model->email = $entity->email->value;
    }

}
