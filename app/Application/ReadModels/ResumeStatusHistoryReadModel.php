<?php

declare(strict_types=1);

namespace App\Application\ReadModels;

final readonly class ResumeStatusHistoryReadModel
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
