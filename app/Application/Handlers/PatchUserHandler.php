<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\PatchUserCommand;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Events\UserUpdatedEvent;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;

final class PatchUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(PatchUserCommand $command): ?User
    {
        $user = $this->users->findById($command->id);

        if ($user === null) {
            return null;
        }

        if ($command->name !== null) {
            $user->rename(new Name($command->name));
        }

        if ($command->email !== null) {
            $user->changeEmail($command->email);
        }

        if ($command->passwordHash !== null) {
            $user->changePasswordHash(new PasswordHash($command->passwordHash));
        }

        $this->users->save($user);
        event(new UserUpdatedEvent($user));

        return $user;
    }
}
