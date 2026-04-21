<?php

declare(strict_types=1);

namespace App\Domains\Auth\Dto;

use App\Enums\UserRole;

readonly class RegisterUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserRole $role,
    ) {}
}
