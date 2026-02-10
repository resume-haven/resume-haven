<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateUserCommand;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Events\UserUpdatedEvent;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;

final class UpdateUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(UpdateUserCommand $command): ?User
    {
        $user = $this->users->findById($command->id);

        if ($user === null) {
            return null;
        }

        $user->rename(new Name($command->name));
        $user->changeEmail($command->email);

        if ($command->passwordHash !== null) {
            $user->changePasswordHash(new PasswordHash($command->passwordHash));
        }

        $this->users->save($user);
        event(new UserUpdatedEvent($user));

        return $user;
    }
}
