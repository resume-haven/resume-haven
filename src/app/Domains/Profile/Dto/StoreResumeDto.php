<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class StoreResumeDto
{
    public function __construct(
        public string $cvText,
    ) {}
}
