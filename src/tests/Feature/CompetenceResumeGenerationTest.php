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
    $response->assertSessionHas('competence_resume_text');
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

    /** @var mixed $competenceResumeText */
    $competenceResumeText = $response->getSession()->get('competence_resume_text');
    expect($competenceResumeText)->toBeString();
    expect((string) $competenceResumeText)->toContain('Kompetenzlebenslauf');
    expect((string) $competenceResumeText)->toContain('Hard Skills:');
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
        'competence_resume_text' => "Kompetenzlebenslauf\nHard Skills: PHP, Laravel",
    ])->get(route('analyze'));

    $response->assertStatus(200);
    $response->assertSee('Kompetenzlebenslauf (Vorschau)');
    $response->assertSee('Kompetenzlebenslauf fuer Analyse verwenden');
    $response->assertSee('Analyse-Artefakt');
    $response->assertSee('Hard Skills');
    $response->assertSee('Soft Skills');
    $response->assertSee('Domainen');
    $response->assertSee('PHP');
    $response->assertSee('Laravel');
    $response->assertSee('SaaS');
    $response->assertSee('10+ Jahre');
});

it('zeigt einen stabilen CTA-Button fuer Kompetenzlebenslauf auf analyze', function () {
    $response = $this->get(route('analyze'));

    $response->assertStatus(200);
    $response->assertSee('Kompetenzlebenslauf erstellen');
    $response->assertSee('btn-competence-resume', false);
    $response->assertSee('background-color:#4f46e5;color:#ffffff;', false);
});

it('uebernimmt den Kompetenzlebenslauf als Analysegrundlage', function () {
    $competenceResumeText = implode(PHP_EOL, [
        'Kompetenzlebenslauf',
        'Zusammenfassung: Berufserfahrung: 10+ Jahre | Technischer Fokus: PHP, Laravel',
        'Hard Skills: PHP, Laravel',
    ]);

    $response = $this->withSession([
        'competence_resume_text' => $competenceResumeText,
    ])->post(route('profile.competence-resume.use'));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHas('loaded_cv', $competenceResumeText);
    $response->assertSessionHas('cv_source', 'competence_resume');
    $response->assertSessionHas('success', 'Kompetenzlebenslauf wurde als Analysegrundlage übernommen.');
});

it('zeigt Fehler, wenn kein Kompetenzlebenslauf fuer die Analyse vorhanden ist', function () {
    $response = $this->post(route('profile.competence-resume.use'));

    $response->assertRedirect(route('analyze'));
    $response->assertSessionHasErrors([
        'competence_resume' => 'Kein Kompetenzlebenslauf für die Analyse verfügbar.',
    ]);
});
