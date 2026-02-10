<?php

declare(strict_types=1);

namespace App\Application\ReadModels;

final readonly class ResumeReadModel
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $status,
    ) {
    }
}
