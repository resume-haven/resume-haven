<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateResumeCommand;
use App\Application\Commands\DeleteResumeCommand;
use App\Application\Commands\PatchResumeCommand;
use App\Application\Commands\UpdateResumeCommand;
use App\Application\Handlers\CreateResumeHandler;
use App\Application\Handlers\DeleteResumeHandler;
use App\Application\Handlers\PatchResumeHandler;
use App\Application\Handlers\UpdateResumeHandler;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;

final class ResumeCommandService
{
    public function __construct(
        private CreateResumeHandler $createHandler,
        private UpdateResumeHandler $updateHandler,
        private PatchResumeHandler $patchHandler,
        private DeleteResumeHandler $deleteHandler,
    ) {
    }

    public function create(string $name, string $email): Resume
    {
        $command = new CreateResumeCommand($name, new Email($email));

        return $this->createHandler->handle($command);
    }

    public function update(int $id, string $name, string $email): ?Resume
    {
        $command = new UpdateResumeCommand($id, $name, new Email($email));

        return $this->updateHandler->handle($command);
    }

    public function patch(int $id, ?string $name, ?string $email): ?Resume
    {
        $command = new PatchResumeCommand(
            $id,
            $name,
            $email !== null ? new Email($email) : null,
        );

        return $this->patchHandler->handle($command);
    }

    public function delete(int $id): ?Resume
    {
        $command = new DeleteResumeCommand($id);

        return $this->deleteHandler->handle($command);
    }
}
