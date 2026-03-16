<?php

declare(strict_types=1);

namespace App\Domains\Profile\Commands;

use App\Domains\Profile\Dto\CompetenceResumeDto;
use App\Domains\Profile\Handlers\BuildCompetenceResumeHandler;

final class BuildCompetenceResumeCommand
{
    public function __construct(
        public readonly string $cvText,
    ) {}

    public function handle(BuildCompetenceResumeHandler $handler): CompetenceResumeDto
    {
        return $handler->handle($this);
    }
}
