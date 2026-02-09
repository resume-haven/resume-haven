<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class Email
{
    public function __construct(public string $value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email address.');
        }
    }
}
