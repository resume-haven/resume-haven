<?php

declare(strict_types=1);

namespace App\Domain\Events;

use App\Domain\Entities\Resume;

final readonly class ResumeStatusChangedEvent
{
    public function __construct(
        public Resume $resume,
        public string $from,
        public string $to,
    ) {
    }
}
