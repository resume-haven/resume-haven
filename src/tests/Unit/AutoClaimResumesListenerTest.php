<?php

declare(strict_types=1);

use App\Domains\Profile\Repositories\ProfileRepository;
use App\Enums\UserRole;
use App\Listeners\AutoClaimResumesListener;
use App\Models\User;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

function bindCurrentRequest(Request $request): void
{
    app()->instance('request', $request);
}

function makeLoginRequestWithSession(array $sessionData = []): Request
{
    $request = Request::create('/login', 'POST');

    /** @var Store $session */
    $session = app('session')->driver('array');
    $session->start();

    foreach ($sessionData as $key => $value) {
        $session->put($key, $value);
    }

    $request->setLaravelSession($session);

    return $request;
}

describe('AutoClaimResumesListener', function (): void {
    test('returns early when request has no session', function (): void {
        $request = Request::create('/login', 'POST');
        bindCurrentRequest($request);

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldNotReceive('claimByToken');

        $listener = new AutoClaimResumesListener($repository, new ResumeTokenSession());

        $user = new User();
        $user->id = 42;
        $user->role = UserRole::User;

        $listener->handle(new Login('web', $user, false));

        expect(true)->toBeTrue();
    });

    test('returns early when resume tokens are missing in session', function (): void {
        $request = makeLoginRequestWithSession();
        bindCurrentRequest($request);

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldNotReceive('claimByToken');

        $listener = new AutoClaimResumesListener($repository, new ResumeTokenSession());

        $user = new User();
        $user->id = 77;
        $user->role = UserRole::User;

        $listener->handle(new Login('web', $user, false));

        expect($request->session()->has('resume_claimed'))->toBeFalse();
    });

    test('returns early when auth identifier is not numeric', function (): void {
        $request = makeLoginRequestWithSession(['resume_tokens' => ['TOKEN-123']]);
        bindCurrentRequest($request);

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldNotReceive('claimByToken');

        $listener = new AutoClaimResumesListener($repository, new ResumeTokenSession());

        $user = new class () extends User {
            public function getAuthIdentifierName(): string
            {
                return 'id';
            }

            public function getAuthIdentifier(): mixed
            {
                return 'not-numeric';
            }
        };
        $user->role = UserRole::User;

        $listener->handle(new Login('web', $user, false));

        expect($request->session()->has('resume_claimed'))->toBeFalse();
    });

    test('claims all tokens and marks session when auth identifier is valid numeric string', function (): void {
        $request = makeLoginRequestWithSession(['resume_tokens' => ['TOKEN-456', 'TOKEN-789']]);
        bindCurrentRequest($request);

        $repository = Mockery::mock(ProfileRepository::class);
        $repository->shouldReceive('claimByToken')
            ->once()
            ->with('TOKEN-456', 1234);
        $repository->shouldReceive('claimByToken')
            ->once()
            ->with('TOKEN-789', 1234);

        $listener = new AutoClaimResumesListener($repository, new ResumeTokenSession());

        $user = new class () extends User {
            public function getAuthIdentifierName(): string
            {
                return 'id';
            }

            public function getAuthIdentifier(): mixed
            {
                return '1234';
            }
        };
        $user->role = UserRole::User;

        $listener->handle(new Login('web', $user, false));

        expect($request->session()->get('resume_claimed'))->toBeTrue();
        expect($request->session()->get('resume_claimed_notice'))->toBe('Dein gespeicherter Lebenslauf wurde automatisch deinem Konto zugeordnet.');
    });
});
