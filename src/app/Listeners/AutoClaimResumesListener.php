<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domains\Profile\Repositories\ProfileRepository;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Auth\Events\Login;

class AutoClaimResumesListener
{
    public function __construct(
        private ProfileRepository $repository,
        private ResumeTokenSession $resumeTokenSession,
    ) {}

    public function handle(Login $event): void
    {
        $request = request();

        if (! $request->hasSession()) {
            return;
        }

        $tokens = $this->resumeTokenSession->all($request->session());

        if ($tokens === []) {
            return;
        }

        $authIdentifier = $event->user->getAuthIdentifier();

        if (! is_int($authIdentifier) && ! (is_string($authIdentifier) && ctype_digit($authIdentifier))) {
            return;
        }

        foreach ($tokens as $token) {
            $this->repository->claimByToken($token, (int) $authIdentifier);
        }

        $request->session()->put('resume_claimed', true);
    }
}
