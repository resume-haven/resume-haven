<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class PasswordHash
{
    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('Password hash cannot be empty.');
        }

        if (strlen($trimmed) > 255) {
            throw new \InvalidArgumentException('Password hash is too long.');
        }

        $this->value = $trimmed;
    }
}
