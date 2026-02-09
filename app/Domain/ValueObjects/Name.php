<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class Name
{
    public function __construct(public string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }

        if (strlen($trimmed) > 200) {
            throw new \InvalidArgumentException('Name is too long.');
        }

        $this->value = $trimmed;
    }
}
