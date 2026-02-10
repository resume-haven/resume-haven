<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateResumeCommand;
use App\Application\Commands\UpdateResumeCommand;
use App\Application\Handlers\CreateResumeHandler;
use App\Application\Handlers\UpdateResumeHandler;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;

final class ResumeCommandService
{
    public function __construct(
        private CreateResumeHandler $createHandler,
        private UpdateResumeHandler $updateHandler,
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
}
