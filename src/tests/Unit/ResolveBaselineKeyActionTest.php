<?php

declare(strict_types=1);

use App\Domains\Profile\Actions\ResolveBaselineKeyAction;
use App\Support\Session\ResumeTokenSession;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

function makeAnalyzeRequestWithSessionData(array $sessionData = []): Request
{
    $request = Request::create('/analyze', 'POST');

    /** @var Store $session */
    $session = app('session')->driver('array');
    $session->start();

    foreach ($sessionData as $key => $value) {
        $session->put($key, $value);
    }

    $request->setLaravelSession($session);

    return $request;
}

describe('ResolveBaselineKeyAction', function (): void {
    test('uses current resume token when present', function (): void {
        $request = makeAnalyzeRequestWithSessionData([
            'resume_token' => 'TOKEN-BASELINE',
        ]);

        $action = new ResolveBaselineKeyAction(new ResumeTokenSession());

        expect($action->execute($request))->toBe('token:TOKEN-BASELINE');
    });

    test('falls back to latest resume_tokens value when current key is missing', function (): void {
        $request = makeAnalyzeRequestWithSessionData([
            'resume_tokens' => ['TOKEN-OLD', 'TOKEN-NEW'],
        ]);

        $action = new ResolveBaselineKeyAction(new ResumeTokenSession());

        expect($action->execute($request))->toBe('token:TOKEN-NEW');
    });

    test('falls back to session id when no token is available', function (): void {
        $request = makeAnalyzeRequestWithSessionData();

        $action = new ResolveBaselineKeyAction(new ResumeTokenSession());

        expect($action->execute($request))->toBe('session:'.$request->session()->getId());
    });
});
