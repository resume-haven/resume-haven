<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Actions\RenderCompetenceResumeTextAction;
use App\Domains\Profile\Commands\BuildCompetenceResumeCommand;
use App\Domains\Profile\Dto\CompetenceResumeDto;
use App\Http\Requests\BuildCompetenceResumeRequest;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

final class BuildCompetenceResumeController extends Controller
{
    public function __invoke(
        BuildCompetenceResumeRequest $request,
        Dispatcher $dispatcher,
        RenderCompetenceResumeTextAction $renderCompetenceResumeText,
    ): RedirectResponse {
        /** @var string $cvText */
        $cvText = $request->validated('cv_text');

        /** @var CompetenceResumeDto $competenceResume */
        $competenceResume = $dispatcher->dispatch(new BuildCompetenceResumeCommand($cvText));
        $competenceResumeText = $renderCompetenceResumeText->execute($competenceResume);

        return redirect()
            ->route('analyze')
            ->with('loaded_cv', $cvText)
            ->with('original_cv_text', $cvText)
            ->with('competence_resume', $competenceResume->toArray())
            ->with('competence_resume_text', $competenceResumeText)
            ->with('success', 'Kompetenzlebenslauf wurde aus dem CV erstellt.');
    }
}
