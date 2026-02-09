<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\UserReadRepositoryInterface;
use App\Application\DTOs\UserDTO;

final class UserApplicationService
{
    public function __construct(private UserReadRepositoryInterface $users)
    {
    }

    public function getById(int $id): ?UserDTO
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            return null;
        }

        return new UserDTO($user->id, $user->name, $user->email);
    }
}
