<?php

declare(strict_types=1);

namespace App\Domain\Events;

use App\Domain\Entities\Resume;

final readonly class ResumeUpdatedEvent
{
    public function __construct(public Resume $resume)
    {
    }
}
