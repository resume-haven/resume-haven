<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\UserDTO;
use App\Domain\Contracts\UserRepositoryInterface;

final class UserApplicationService
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function getById(int $id): ?UserDTO
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            return null;
        }

        return new UserDTO($user->id, $user->name->value, $user->email->value);
    }
}
