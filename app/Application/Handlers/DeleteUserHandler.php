<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\DeleteUserCommand;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\Events\UserDeletedEvent;

final class DeleteUserHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function handle(DeleteUserCommand $command): ?User
    {
        $user = $this->users->findById($command->id);

        if ($user === null) {
            return null;
        }

        $this->users->delete($user->id->value);
        event(new UserDeletedEvent($user));

        return $user;
    }
}
