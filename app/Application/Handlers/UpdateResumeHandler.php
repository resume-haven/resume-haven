<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateResumeCommand;
use App\Domain\Contracts\ResumeRepositoryInterface;
use App\Domain\Entities\Resume;
use App\Domain\Events\ResumeStatusChangedEvent;
use App\Domain\Events\ResumeUpdatedEvent;
use App\Domain\Services\ResumeStatusService;
use App\Domain\ValueObjects\Name;

final class UpdateResumeHandler
{
    public function __construct(
        private ResumeRepositoryInterface $resumes,
        private ResumeStatusService $statusService,
    ) {
    }

    public function handle(UpdateResumeCommand $command): ?Resume
    {
        $resume = $this->resumes->findById($command->id);

        if ($resume === null) {
            return null;
        }

        $previousStatus = $resume->status->value;

        $resume->rename(new Name($command->name));
        $resume->changeEmail($command->email);

        if ($command->status !== null) {
            $this->statusService->apply($resume, $command->status);
        }

        $this->resumes->save($resume);
        event(new ResumeUpdatedEvent($resume));

        if ($command->status !== null && $previousStatus !== $resume->status->value) {
            event(new ResumeStatusChangedEvent($resume, $previousStatus, $resume->status->value));
        }

        return $resume;
    }
}
