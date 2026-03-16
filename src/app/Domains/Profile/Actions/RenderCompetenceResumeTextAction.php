<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Dto\CompetenceResumeDto;

/**
 * Rendert einen Kompetenzlebenslauf in ein kanonisches Textartefakt fuer die Analyse.
 */
final class RenderCompetenceResumeTextAction
{
    public function execute(CompetenceResumeDto $competenceResume): string
    {
        $sections = [];

        $sections[] = 'Kompetenzlebenslauf';
        $sections[] = 'Zusammenfassung: '.$competenceResume->summary;

        if ($competenceResume->yearsExperience !== null) {
            $sections[] = 'Berufserfahrung: '.$competenceResume->yearsExperience.'+ Jahre';
        }

        $sections[] = 'Hard Skills: '.$this->renderList($competenceResume->hardSkills);
        $sections[] = 'Soft Skills: '.$this->renderList($competenceResume->softSkills);
        $sections[] = 'Domainen: '.$this->renderList($competenceResume->domains);

        return implode(PHP_EOL, $sections);
    }

    /**
     * @param array<int, string> $items
     */
    private function renderList(array $items): string
    {
        if ($items === []) {
            return 'Keine Angabe';
        }

        return implode(', ', $items);
    }
}
