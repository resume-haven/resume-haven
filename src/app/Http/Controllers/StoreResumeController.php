<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Commands\StoreResumeCommand;
use App\Domains\Profile\Dto\StoreResumeDto;
use App\Http\Requests\StoreResumeRequest;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Domains\Profile\Dto\ResumeTokenDto;

class StoreResumeController extends Controller
{
    public function __invoke(StoreResumeRequest $request, Dispatcher $dispatcher): RedirectResponse
    {
        /** @var string $cvText */
        $cvText = $request->validated('cv_text');
        $authId = Auth::id();
        $userId = is_int($authId) ? $authId : null;

        /** @var ResumeTokenDto $tokenDto */
        $tokenDto = $dispatcher->dispatch(
            new StoreResumeCommand(new StoreResumeDto($cvText, $userId))
        );

        $request->session()->put('resume_token', $tokenDto->token);
        $request->session()->put('resume_claimed', Auth::check());

        return redirect()
            ->route('analyze')
            ->with('resume_token', $tokenDto->token)
            ->with('resume_link', route('profile.load', ['token' => $tokenDto->token]))
            ->with('success', 'Lebenslauf wurde sicher gespeichert.');
    }
}
