<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('erstellt einen Kompetenzlebenslauf aus CV-Text und speichert ihn in der Session', function () {
    $cvText = 'Seit 10 Jahren arbeite ich mit PHP, Laravel, PostgreSQL und Docker. '.
        'Ich arbeite in Scrum-Teams und uebernehme Mentoring in SaaS-Projekten.';

    $response = $this->post(route('profile.competence-resume'), [
        'cv_text' => $cvText,
    ]);

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHas('competence_resume');
    $response->assertSessionHas('success', 'Kompetenzlebenslauf wurde aus dem CV erstellt.');

    /** @var mixed $competenceResume */
    $competenceResume = $response->getSession()->get('competence_resume');
    expect($competenceResume)->toBeArray();

    /** @var array<string, mixed> $competenceResumeArray */
    $competenceResumeArray = $competenceResume;

    expect($competenceResumeArray)->toHaveKeys([
        'hard_skills',
        'soft_skills',
        'domains',
        'years_experience',
        'summary',
    ]);
});

it('zeigt die Kompetenzlebenslauf-Vorschau auf der Analyze-Seite an', function () {
    $response = $this->withSession([
        'competence_resume' => [
            'hard_skills' => ['PHP', 'Laravel'],
            'soft_skills' => ['Mentoring'],
            'domains' => ['SaaS'],
            'years_experience' => 10,
            'summary' => 'Berufserfahrung: 10+ Jahre | Technischer Fokus: PHP, Laravel',
        ],
    ])->get(route('analyze'));

    $response->assertStatus(200);
    $response->assertSee('Kompetenzlebenslauf (Vorschau)');
    $response->assertSee('Hard Skills');
    $response->assertSee('Soft Skills');
    $response->assertSee('Domainen');
    $response->assertSee('PHP');
    $response->assertSee('Laravel');
    $response->assertSee('SaaS');
    $response->assertSee('10+ Jahre');
});
