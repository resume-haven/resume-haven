<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;

final class Resume
{
    public function __construct(
        public int $id,
        public Name $name,
        public Email $email,
    ) {
    }

    public function rename(Name $name): void
    {
        $this->name = $name;
    }
}
