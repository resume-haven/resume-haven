<?php

declare(strict_types=1);

namespace App\Domains\Profile\Handlers;

use App\Domains\Profile\Actions\BuildCompetenceResumeAction;
use App\Domains\Profile\Commands\BuildCompetenceResumeCommand;
use App\Domains\Profile\Dto\CompetenceResumeDto;

final class BuildCompetenceResumeHandler
{
    public function __construct(
        private BuildCompetenceResumeAction $buildCompetenceResume,
    ) {}

    public function handle(BuildCompetenceResumeCommand $command): CompetenceResumeDto
    {
        return $this->buildCompetenceResume->execute($command->cvText);
    }
}
