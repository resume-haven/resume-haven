<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\ResumeId;

final class Resume
{
    public function __construct(
        public ResumeId $id,
        public Name $name,
        public Email $email,
    ) {
    }

    public function rename(Name $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }
}
