<?php

declare(strict_types=1);

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Dto\RegisterUserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function createRegisteredUser(RegisterUserDto $dto): User
    {
        /** @var User $user */
        $user = User::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role,
        ]);

        return $user;
    }
}
