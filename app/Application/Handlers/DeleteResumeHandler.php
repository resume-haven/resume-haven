<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\DeleteResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Events\ResumeDeletedEvent;

final class DeleteResumeHandler
{
    public function __construct(private ResumeRepositoryInterface $resumes)
    {
    }

    public function handle(DeleteResumeCommand $command): ?Resume
    {
        $resume = $this->resumes->findById($command->id);

        if ($resume === null) {
            return null;
        }

        $this->resumes->delete($resume->id->value);
        event(new ResumeDeletedEvent($resume));

        return $resume;
    }
}
