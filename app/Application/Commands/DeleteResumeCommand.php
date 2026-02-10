<?php

declare(strict_types=1);

namespace App\Application\Commands;

final readonly class DeleteResumeCommand
{
    public function __construct(public int $id)
    {
    }
}
