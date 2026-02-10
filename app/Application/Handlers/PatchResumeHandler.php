<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\PatchResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Domain\ValueObjects\Name;

final class PatchResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(PatchResumeCommand $command): ?Resume
    {
        $resume = $this->resumes->findById($command->id);

        if ($resume === null) {
            return null;
        }

        if ($command->name !== null) {
            $resume->rename(new Name($command->name));
        }

        if ($command->email !== null) {
            $resume->changeEmail($command->email);
        }

        $this->resumes->save($resume);
        event(new ResumeUpdatedEvent($resume));

        return $resume;
    }
}
