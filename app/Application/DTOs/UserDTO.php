<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final readonly class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $created_at,
    ) {
    }
}
