<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class UserId
{
    public int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('User id cannot be negative.');
        }

        $this->value = $value;
    }
}
