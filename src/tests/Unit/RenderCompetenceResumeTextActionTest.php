<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\RenderCompetenceResumeTextAction;
use App\Domains\Profile\Dto\CompetenceResumeDto;

it('rendert einen Kompetenzlebenslauf deterministisch als Analyse-Text', function () {
    $action = new RenderCompetenceResumeTextAction();

    $dto = new CompetenceResumeDto(
        hardSkills: ['PHP', 'Laravel'],
        softSkills: ['Mentoring'],
        domains: ['SaaS'],
        yearsExperience: 10,
        summary: 'Berufserfahrung: 10+ Jahre | Technischer Fokus: PHP, Laravel',
    );

    $text = $action->execute($dto);

    expect($text)->toContain('Kompetenzlebenslauf');
    expect($text)->toContain('Zusammenfassung: Berufserfahrung: 10+ Jahre | Technischer Fokus: PHP, Laravel');
    expect($text)->toContain('Berufserfahrung: 10+ Jahre');
    expect($text)->toContain('Hard Skills: PHP, Laravel');
    expect($text)->toContain('Soft Skills: Mentoring');
    expect($text)->toContain('Domainen: SaaS');
});

it('rendert leere Bereiche mit fallback-text', function () {
    $action = new RenderCompetenceResumeTextAction();

    $dto = new CompetenceResumeDto(
        hardSkills: [],
        softSkills: [],
        domains: [],
        yearsExperience: null,
        summary: 'Keine Cluster erkannt',
    );

    $text = $action->execute($dto);

    expect($text)->toContain('Hard Skills: Keine Angabe');
    expect($text)->toContain('Soft Skills: Keine Angabe');
    expect($text)->toContain('Domainen: Keine Angabe');
});
