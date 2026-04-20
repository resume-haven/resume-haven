<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\BuildCompetenceResumeAction;

it('leitet Hard Skills, Soft Skills, Domainen und Jahre aus CV-Text ab', function () {
    $action = new BuildCompetenceResumeAction();

    $cvText = 'Ich habe 8 Jahre Erfahrung mit PHP, Laravel, SQL und Docker. '.
        'In agilen Teams (Scrum) uebernehme ich Mentoring. '.
        'Im ERP- und SaaS-Umfeld habe ich APIs gebaut.';

    $dto = $action->execute($cvText);

    expect($dto->hardSkills)->toContain('PHP');
    expect($dto->hardSkills)->toContain('Laravel');
    expect($dto->hardSkills)->toContain('SQL');
    expect($dto->hardSkills)->toContain('Docker');

    expect($dto->softSkills)->toContain('Mentoring');
    expect($dto->softSkills)->toContain('Scrum');

    expect($dto->domains)->toContain('ERP');
    expect($dto->domains)->toContain('SaaS');

    expect($dto->yearsExperience)->toBe(8);
    expect($dto->summary)->toContain('Berufserfahrung: 8+ Jahre');
});

it('liefert fallback-summary wenn keine Skill-Keywords erkannt werden', function () {
    $action = new BuildCompetenceResumeAction();

    $dto = $action->execute('Dies ist ein neutraler Beispieltext ohne verwertbare Tech-Schluesselwoerter.');

    expect($dto->hardSkills)->toBe([]);
    expect($dto->softSkills)->toBe([]);
    expect($dto->domains)->toBe([]);
    expect($dto->yearsExperience)->toBeNull();
    expect($dto->summary)->toContain('keine klaren Kompetenzcluster');
});

it('nutzt die hoechste erkannte Jahresangabe und dedupliziert Alias-Treffer', function () {
    $action = new BuildCompetenceResumeAction();

    $cvText = 'Ich habe 3 years Erfahrung im Backend, 8+ Jahre mit APIs und 10 plus years in SaaS-Produkten. '.
        'Technisch arbeite ich mit postgres, postgresql, git, rest und clean architecture. '.
        'Fachlich kenne ich ecommerce und e-commerce sehr gut.';

    $dto = $action->execute($cvText);

    expect($dto->yearsExperience)->toBe(10);
    expect($dto->hardSkills)->toContain('PostgreSQL');
    expect(array_values(array_filter($dto->hardSkills, static fn (string $skill): bool => $skill === 'PostgreSQL')))->toHaveCount(1);
    expect($dto->domains)->toContain('E-Commerce');
    expect(array_values(array_filter($dto->domains, static fn (string $domain): bool => $domain === 'E-Commerce')))->toHaveCount(1);
    expect($dto->summary)->toContain('Berufserfahrung: 10+ Jahre');
});
