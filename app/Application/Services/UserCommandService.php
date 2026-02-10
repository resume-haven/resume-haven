<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateUserCommand;
use App\Application\Commands\DeleteUserCommand;
use App\Application\Commands\PatchUserCommand;
use App\Application\Commands\UpdateUserCommand;
use App\Application\Handlers\CreateUserHandler;
use App\Application\Handlers\DeleteUserHandler;
use App\Application\Handlers\PatchUserHandler;
use App\Application\Handlers\UpdateUserHandler;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;

final class UserCommandService
{
    public function __construct(
        private CreateUserHandler $createHandler,
        private UpdateUserHandler $updateHandler,
        private PatchUserHandler $patchHandler,
        private DeleteUserHandler $deleteHandler,
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

    public function patch(int $id, ?string $name, ?string $email, ?string $passwordHash): ?User
    {
        $command = new PatchUserCommand(
            $id,
            $name,
            $email !== null ? new Email($email) : null,
            $passwordHash,
        );

        return $this->patchHandler->handle($command);
    }

    public function delete(int $id): ?User
    {
        $command = new DeleteUserCommand($id);

        return $this->deleteHandler->handle($command);
    }
}
