<?php

declare(strict_types=1);

namespace App\Policies;

use App\Infrastructure\Persistence\UserModel;

final class AdminPolicy
{
    public function dashboard(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    public function viewUsers(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    public function viewResumes(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    public function viewResume(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    public function updateResume(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteResume(UserModel $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(UserModel $user): bool
    {
        return $user->roles()->where('name', 'admin')->exists();
    }
}
