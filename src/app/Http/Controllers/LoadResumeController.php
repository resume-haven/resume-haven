<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Queries\GetResumeByTokenQuery;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use App\Domains\Profile\Dto\LoadedResumeDto;

class LoadResumeController extends Controller
{
    public function __invoke(string $token, Dispatcher $dispatcher): RedirectResponse
    {
        if (! preg_match('/^[A-Za-z0-9_-]{30,128}$/', $token)) {
            return redirect()->route('analyze')->withErrors(['resume_token' => 'Ungueltiger Resume-Token.']);
        }

        /** @var LoadedResumeDto|null $loadedResume */
        $loadedResume = $dispatcher->dispatch(new GetResumeByTokenQuery($token));

        if ($loadedResume === null) {
            return redirect()->route('analyze')->withErrors(['resume_token' => 'Kein gespeicherter Lebenslauf fuer diesen Token gefunden.']);
        }

        session()->put('resume_token', $loadedResume->token);
        session()->put('resume_claimed', false);

        return redirect()
            ->route('analyze')
            ->with('loaded_cv', $loadedResume->cvText)
            ->with('loaded_token', $loadedResume->token)
            ->with('success', 'Gespeicherter Lebenslauf wurde geladen.');
    }
}
