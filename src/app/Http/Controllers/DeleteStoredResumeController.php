<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Profile\Commands\DeleteStoredResumeCommand;
use App\Domains\Profile\Repositories\ProfileRepository;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

final class DeleteStoredResumeController extends Controller
{
    public function __construct(
        private ProfileRepository $profileRepository,
        private ResumeTokenSession $resumeTokenSession,
    ) {}

    public function __invoke(string $token, Request $request, Dispatcher $dispatcher): RedirectResponse
    {
        $resume = $this->profileRepository->getByToken($token);
        abort_if($resume === null, 404);

        Gate::authorize('delete', $resume);

        $dispatcher->dispatch(new DeleteStoredResumeCommand($token));
        $this->resumeTokenSession->remove($request->session(), $token);

        return redirect()
            ->route('profile.index')
            ->with('success', 'Lebenslauf wurde geloescht.');
    }
}
