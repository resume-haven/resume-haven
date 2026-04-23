<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domains\Profile\Repositories\ProfileRepository;
use Illuminate\Auth\Events\Login;

class AutoClaimResumesListener
{
    public function __construct(
        private ProfileRepository $repository,
    ) {}

    public function handle(Login $event): void
    {
        $request = request();

        if (! $request->hasSession()) {
            return;
        }

        $token = $request->session()->get('resume_token');

        if (! is_string($token) || $token === '') {
            return;
        }

        $authIdentifier = $event->user->getAuthIdentifier();

        if (! is_int($authIdentifier) && ! (is_string($authIdentifier) && ctype_digit($authIdentifier))) {
            return;
        }

        $this->repository->claimByToken($token, (int) $authIdentifier);
        $request->session()->put('resume_claimed', true);
    }
}
