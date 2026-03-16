<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class UseCompetenceResumeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $competenceResumeText = $request->session()->get('competence_resume_text');

        if (! is_string($competenceResumeText) || trim($competenceResumeText) === '') {
            return redirect()
                ->route('analyze')
                ->withErrors(['competence_resume' => 'Kein Kompetenzlebenslauf für die Analyse verfügbar.']);
        }

        return redirect()
            ->route('analyze')
            ->with('loaded_cv', $competenceResumeText)
            ->with('cv_source', 'competence_resume')
            ->with('success', 'Kompetenzlebenslauf wurde als Analysegrundlage übernommen.');
    }
}
