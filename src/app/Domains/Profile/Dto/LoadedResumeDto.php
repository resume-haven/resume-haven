<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class LoadedResumeDto
{
    public function __construct(
        public string $token,
        public string $cvText,
    ) {}
}
