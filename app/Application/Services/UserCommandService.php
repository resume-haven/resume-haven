<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateUserCommand;
use App\Application\Commands\UpdateUserCommand;
use App\Application\Handlers\CreateUserHandler;
use App\Application\Handlers\UpdateUserHandler;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;

final class UserCommandService
{
    public function __construct(
        private CreateUserHandler $createHandler,
        private UpdateUserHandler $updateHandler,
    ) {
    }

    public function create(string $name, string $email, string $passwordHash): User
    {
        $command = new CreateUserCommand($name, new Email($email), $passwordHash);

        return $this->createHandler->handle($command);
    }

    public function update(int $id, string $name, string $email, ?string $passwordHash): ?User
    {
        $command = new UpdateUserCommand($id, $name, new Email($email), $passwordHash);

        return $this->updateHandler->handle($command);
    }
}
