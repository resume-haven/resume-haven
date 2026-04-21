<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StoredResume;
use App\Models\User;

class ProfilePolicy
{
    public function view(User $user, StoredResume $resume): bool
    {
        return $user->isAdmin() || $resume->user_id === $user->id;
    }

    public function delete(User $user, StoredResume $resume): bool
    {
        return $user->isAdmin() || $resume->user_id === $user->id;
    }
}
