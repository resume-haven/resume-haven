<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\UserReadRepositoryInterface;
use App\Application\DTOs\UserDTO;

final class UserQueryService
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

        return new UserDTO($user->id, $user->name, $user->email, $user->created_at);
    }

    /**
     * @return list<UserDTO>
     */
    public function list(int $limit, int $offset): array
    {
        return array_map(
            static fn ($user): UserDTO => new UserDTO(
                $user->id,
                $user->name,
                $user->email,
                $user->created_at,
            ),
            $this->users->list($limit, $offset),
        );
    }

    public function getTotal(): int
    {
        return $this->users->countAll();
    }
}
