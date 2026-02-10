<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\ResumeStatus;

final class ResumeStatusService
{
    public function apply(Resume $resume, ResumeStatus $status): Resume
    {
        $resume->changeStatus($status);

        return $resume;
    }
}
