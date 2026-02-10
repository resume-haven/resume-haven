<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use DateTimeImmutable;

interface ResumeStatusHistoryRepositoryInterface
{
    public function record(int $resumeId, string $fromStatus, string $toStatus, DateTimeImmutable $changedAt): void;
}
