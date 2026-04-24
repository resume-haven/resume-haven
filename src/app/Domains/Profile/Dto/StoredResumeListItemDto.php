<?php

declare(strict_types=1);

namespace App\Domains\Profile\Dto;

readonly class StoredResumeListItemDto
{
    public function __construct(
        public string $token,
        public string $preview,
        public string $updatedAt,
        public bool $isCurrent,
    ) {}

    /** @return array{token: string, preview: string, updated_at: string, is_current: bool} */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'preview' => $this->preview,
            'updated_at' => $this->updatedAt,
            'is_current' => $this->isCurrent,
        ];
    }
}
