<?php

declare(strict_types=1);

namespace App\Application\Commands;

use App\Domain\ValueObjects\Email;

final readonly class PatchUserCommand
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?Email $email,
        public ?string $passwordHash,
    ) {
    }
}
