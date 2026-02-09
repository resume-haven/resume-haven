<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Commands\CreateResumeCommand;
use App\Application\Handlers\CreateResumeHandler;
use App\Domain\Entities\Resume;
use App\Domain\ValueObjects\Email;

final class ResumeCommandService
{
    public function __construct(private CreateResumeHandler $handler)
    {
    }

    public function create(string $name, string $email): Resume
    {
        $command = new CreateResumeCommand($name, new Email($email));

        return $this->handler->handle($command);
    }
}
