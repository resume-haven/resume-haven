<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateUserCommand;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\Name;

final class CreateUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(CreateUserCommand $command): User
    {
        $user = new User(0, new Name($command->name), $command->email, $command->passwordHash);
        $this->users->save($user);

        return $user;
    }
}
