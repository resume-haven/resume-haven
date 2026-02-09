<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateUserCommand;
use App\Application\Handlers\CreateUserHandler;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;

final class UserCommandService
{
    public function __construct(private CreateUserHandler $handler)
    {
    }

    public function create(string $name, string $email, string $passwordHash): User
    {
        $command = new CreateUserCommand($name, new Email($email), $passwordHash);

        return $this->handler->handle($command);
    }
}
