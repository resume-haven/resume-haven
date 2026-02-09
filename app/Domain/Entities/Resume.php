<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;

final class Resume
{
    public function __construct(
        public int $id,
        public string $name,
        public Email $email,
    ) {
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}
