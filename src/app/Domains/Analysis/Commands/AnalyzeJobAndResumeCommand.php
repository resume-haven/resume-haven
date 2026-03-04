<?php

declare(strict_types=1);

namespace App\Domains\Analysis\Commands;

use App\Dto\AnalyzeRequestDto;
use App\Dto\AnalyzeResultDto;
use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;

/**
 * Command: Analysiere Job-Ausschreibung und Lebenslauf
 */
class AnalyzeJobAndResumeCommand
{
    public function __construct(
        public readonly AnalyzeRequestDto $request,
        public readonly bool $demoMode = false,
    ) {}

    /**
     * Handle the command by delegating to the Handler
     */
    public function handle(AnalyzeJobAndResumeHandler $handler): AnalyzeResultDto
    {
        return $handler->handle($this);
    }
}


