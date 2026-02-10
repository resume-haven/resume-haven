<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Domain\ValueObjects\Name;

final class UpdateResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(UpdateResumeCommand $command): ?Resume
    {
        $resume = $this->resumes->findById($command->id);

        if ($resume === null) {
            return null;
        }

        $resume->rename(new Name($command->name));
        $resume->changeEmail($command->email);

        $this->resumes->save($resume);
        event(new ResumeUpdatedEvent($resume));

        return $resume;
    }
}
