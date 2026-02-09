<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Name;

final class ResumeService
{
    public function updateName(Resume $resume, Name $name): Resume
    {
        $resume->rename($name);

        return $resume;
    }
}
