<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Resume;

final class ResumeService
{
    public function updateName(Resume $resume, string $name): Resume
    {
        $resume->rename($name);

        return $resume;
    }
}
