<?php

declare(strict_types=1);

namespace App\Domains\Auth\Handlers;

use App\Domains\Auth\Commands\RegisterUserCommand;
use App\Domains\Auth\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;

class RegisterUserHandler
{
    public function __construct(
        private UserRepository $users,
    ) {}

    public function handle(RegisterUserCommand $command): Authenticatable
    {
        return $this->users->createRegisteredUser($command->request);
    }
}
