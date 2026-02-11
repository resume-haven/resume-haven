<?php

declare(strict_types=1);

namespace App\Policies;

use App\Infrastructure\Persistence\UserModel;

final class UserPolicy
{
    public function update(UserModel $actor, UserModel $target): bool
    {
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    public function delete(UserModel $actor, UserModel $target): bool
    {
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    private function isAdmin(UserModel $user): bool
    {
        return $user->roles()->where('name', 'admin')->exists();
    }
}
