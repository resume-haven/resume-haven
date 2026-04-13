<?php

declare(strict_types=1);

use App\Domains\Analysis\Handlers\AnalyzeJobAndResumeHandler;
use App\Services\AiAnalyzer\Contracts\AiAnalyzerInterface;
use App\Services\AiAnalyzer\MockAiAnalyzer;
use App\Services\AnalyzeApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$configureMockAnalyzer = static function (string $scenario = 'realistic'): void {
    config([
        'ai.provider' => 'mock',
        'ai.mock.scenario' => $scenario,
        'ai.mock.delay_ms' => 0,
    ]);

    app()->forgetInstance(AiAnalyzerInterface::class);
    app()->forgetInstance(MockAiAnalyzer::class);
    app()->forgetInstance(AnalyzeApplicationService::class);
    app()->forgetInstance(AnalyzeJobAndResumeHandler::class);
};

beforeEach(function () use ($configureMockAnalyzer): void {
    $configureMockAnalyzer();
});

test('nutzende koennen einen kompetenzlebenslauf erzeugen, uebernehmen und fuer die analyse verwenden', function () {
    $jobText = str_repeat('Gesucht wird Laravel, PHP, Docker, API-Design und SaaS-Erfahrung im Backend. ', 2);
    $cvText = 'Seit 10 Jahren arbeite ich mit PHP, Laravel, Docker und REST APIs. '.
        'Ich uebernehme Mentoring in SaaS-Projekten und arbeite in Scrum-Teams.';

    $createResponse = $this->post(route('profile.competence-resume'), [
        'cv_text' => $cvText,
    ]);

    $createResponse
        ->assertRedirect(route('analyze'))
        ->assertSessionHas('competence_resume')
        ->assertSessionHas('competence_resume_text')
        ->assertSessionHas('loaded_cv', $cvText);

    /** @var array<string, mixed> $sessionData */
    $sessionData = $createResponse->getSession()->all();

    $this->withSession($sessionData)
        ->get(route('analyze'))
        ->assertOk()
        ->assertSee('Kompetenzlebenslauf (Vorschau)')
        ->assertSee('Kompetenzlebenslauf fuer Analyse verwenden')
        ->assertSee('Analyse-Artefakt')
        ->assertSee('PHP')
        ->assertSee('Laravel')
        ->assertSee('SaaS')
        ->assertSee('10+ Jahre');

    $useResponse = $this->withSession($sessionData)
        ->post(route('profile.competence-resume.use'));

    $useResponse
        ->assertRedirect(route('analyze'))
        ->assertSessionHas('cv_source', 'competence_resume')
        ->assertSessionHas('loaded_cv')
        ->assertSessionHas('success', 'Kompetenzlebenslauf wurde als Analysegrundlage übernommen.');

    /** @var string $loadedCv */
    $loadedCv = $useResponse->getSession()->get('loaded_cv');
    /** @var array<string, mixed> $useSession */
    $useSession = $useResponse->getSession()->all();

    $this->withSession($useSession)
        ->post(route('analyze.submit'), [
            'job_text' => $jobText,
            'cv_text' => $loadedCv,
        ])
        ->assertOk()
        ->assertViewIs('result')
        ->assertSee('Kompetenzlebenslauf')
        ->assertSee('Empfehlungen')
        ->assertSee('Lebenslauf (Roh-Text)');
});

test('nutzende erhalten einen klaren fehler wenn kein kompetenzlebenslauf zur analyse verfuegbar ist', function () {
    $this->followingRedirects()
        ->post(route('profile.competence-resume.use'))
        ->assertOk()
        ->assertSee('Kein Kompetenzlebenslauf für die Analyse verfügbar.')
        ->assertSee('Bitte prüfe deinen Token oder lade die Seite neu');
});
