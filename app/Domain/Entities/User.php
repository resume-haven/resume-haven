<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;

final class User
{
    public function __construct(
        public int $id,
        public string $name,
        public Email $email,
        public string $passwordHash,
    ) {
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}
