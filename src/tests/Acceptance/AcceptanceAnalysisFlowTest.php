<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Szenarienbasierter End-to-End-Test für den vollständigen Analyse-Flow:
 * - User gibt Job-Text und CV ein
 * - Analyse wird durchgeführt
 * - Ergebnis, Kompetenzlebenslauf und Delta/Erklärbarkeit werden angezeigt
 */
test('User kann eine vollständige Analyse durchführen und Delta/Erklärbarkeit sehen', function () {
    // Schritt 1: Baseline-Analyse (ohne Kompetenzlebenslauf)
    $jobText = 'Gesucht: PHP-Entwickler mit Erfahrung in Laravel, Docker, SaaS.';
    $cvTextBaseline = 'Erste Projekte mit PHP und Laravel.';
    $this->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => $cvTextBaseline,
    ])->assertStatus(200);

    // Schritt 2: Kompetenzlebenslauf erzeugen (optimierter CV)
    $cvTextOptimiert = '10 Jahre Erfahrung mit PHP, Laravel, Docker. Mentoring in SaaS-Projekten.';
    $this->post(route('profile.competence-resume'), [
        'cv_text' => $cvTextOptimiert,
    ])->assertRedirect(route('analyze'));

    // Schritt 3: Optimierte Analyse mit Kompetenzlebenslauf und Vergleich
    $this->withSession([
        'competence_resume' => [
            'hard_skills' => ['PHP', 'Laravel'],
            'soft_skills' => ['Mentoring'],
            'domains' => ['SaaS'],
            'years_experience' => 10,
            'summary' => '10+ Jahre PHP, Laravel',
        ],
        'competence_resume_text' => 'Kompetenzlebenslauf\nHard Skills: PHP, Laravel',
        'cv_source' => 'competence_resume',
    ])->post(route('analyze.submit'), [
        'job_text' => $jobText,
        'cv_text' => $cvTextOptimiert,
    ])->assertStatus(200)
        ->assertViewIs('result')
        ->assertViewHasAll(['comparison', 'job_text', 'cv_text'])
        ->assertSee('Kompetenzlebenslauf')
        ->assertSee('Delta')
        ->assertSee('Score')
        ->assertSee('Empfehlungen');
});
