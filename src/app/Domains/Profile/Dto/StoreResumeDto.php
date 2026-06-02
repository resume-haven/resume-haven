<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class StoreResumeDto
{
    public function __construct(
        public string $cvText,
        public ?int $userId = null,
        public ?string $fileName = null,
        public ?string $originalFilename = null,
    ) {}
}
