<?php

declare(strict_types=1);

namespace App\Domains\Auth\Commands;

use App\Domains\Auth\Dto\RegisterUserDto;
use App\Domains\Auth\Handlers\RegisterUserHandler;
use Illuminate\Contracts\Auth\Authenticatable;

class RegisterUserCommand
{
    public function __construct(
        public readonly RegisterUserDto $request,
    ) {}

    public function handle(RegisterUserHandler $handler): Authenticatable
    {
        return $handler->handle($this);
    }
}
