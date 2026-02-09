<?php

declare(strict_types=1);

namespace App\Application\Commands;

use App\Domain\ValueObjects\Email;

final readonly class CreateResumeCommand
{
    public function __construct(
        public string $name,
        public Email $email,
    ) {
    }
}
