<?php

declare(strict_types=1);

namespace App\Policies;

use App\Infrastructure\Persistence\ResumeModel;
use App\Infrastructure\Persistence\UserModel;

final class ResumePolicy
{
    public function update(UserModel $actor, ResumeModel $resume): bool
    {
        return $this->isAdmin($actor) || $resume->user_id === $actor->id;
    }

    public function delete(UserModel $actor, ResumeModel $resume): bool
    {
        return $this->isAdmin($actor) || $resume->user_id === $actor->id;
    }

    private function isAdmin(UserModel $user): bool
    {
        return $user->roles()->where('name', 'admin')->exists();
    }
}
