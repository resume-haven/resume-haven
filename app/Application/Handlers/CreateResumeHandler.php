<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Events\ResumeCreatedEvent;
use App\Domain\ValueObjects\Name;
use App\Domain\ValueObjects\ResumeId;
use App\Domain\ValueObjects\ResumeStatus;

final class CreateResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(CreateResumeCommand $command): Resume
    {
        $resume = new Resume(
            new ResumeId(0),
            new Name($command->name),
            $command->email,
            ResumeStatus::draft(),
        );
        $this->resumes->save($resume);
        event(new ResumeCreatedEvent($resume));

        return $resume;
    }
}
