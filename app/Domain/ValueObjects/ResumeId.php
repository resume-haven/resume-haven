<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class ResumeId
{
    public int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Resume id cannot be negative.');
        }

        $this->value = $value;
    }
}
