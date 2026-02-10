<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final readonly class ResumeStatusHistoryDTO
{
    public function __construct(
        public int $id,
        public int $resume_id,
        public string $from_status,
        public string $to_status,
        public string $changed_at,
    ) {
    }
}
