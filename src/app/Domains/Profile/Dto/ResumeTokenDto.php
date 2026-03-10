<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class ResumeTokenDto
{
    public function __construct(
        public string $token,
    ) {}
}
