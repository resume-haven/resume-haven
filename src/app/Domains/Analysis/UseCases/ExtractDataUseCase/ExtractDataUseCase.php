<?php

declare(strict_types=1);

namespace App\Domains\Analysis\UseCases\ExtractDataUseCase;

use App\Domains\Analysis\Dto\ExtractDataResultDto;

/**
 * UseCase: Extrahiere Anforderungen und Erfahrungen
 * Orchestriert ExtractRequirementsAction und ExtractExperiencesAction
 */
class ExtractDataUseCase
{
    public function __construct(
        private ExtractRequirementsAction $extractRequirements,
        private ExtractExperiencesAction $extractExperiences,
    ) {}

    public function handle(string $jobText, string $cvText): ExtractDataResultDto
    {
        $requirements = $this->extractRequirements->execute($jobText);
        $experiences = $this->extractExperiences->execute($cvText);

        return new ExtractDataResultDto($requirements, $experiences);
    }
}
