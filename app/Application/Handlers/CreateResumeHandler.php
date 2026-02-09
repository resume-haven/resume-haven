<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Name;

final class CreateResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(CreateResumeCommand $command): Resume
    {
        $resume = new Resume(0, new Name($command->name), $command->email);
        $this->resumes->save($resume);

        return $resume;
    }
}
