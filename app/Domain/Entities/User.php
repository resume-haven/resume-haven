<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\PasswordHash;
use App\Domain\ValueObjects\UserId;

final class User
{
    public function __construct(
        public UserId $id,
        public Name $name,
        public Email $email,
        public PasswordHash $passwordHash,
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

    public function changePasswordHash(PasswordHash $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }
}
