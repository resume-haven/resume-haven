<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateUserCommand;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Events\UserCreatedEvent;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;
use App\Domain\ValueObjects\UserId;

final class CreateUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(CreateUserCommand $command): User
    {
        $user = new User(
            new UserId(0),
            new Name($command->name),
            $command->email,
            new PasswordHash($command->passwordHash)
        );
        $this->users->save($user);
        event(new UserCreatedEvent($user));

        return $user;
    }
}
