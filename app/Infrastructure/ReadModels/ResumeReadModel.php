<?php

declare(strict_types=1);

namespace App\Infrastructure\ReadModels;

final class ResumeReadModel
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {
    }
}
