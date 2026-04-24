<?php

declare(strict_types=1);

use App\Support\Session\ResumeTokenSession;
use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Session\Store;

function makeArraySession(): SessionContract
{
    /** @var Store $session */
    $session = app('session')->driver('array');
    $session->start();

    return $session;
}

describe('ResumeTokenSession', function (): void {
    test('adds token, deduplicates values and keeps latest token as current', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $manager->add($session, 'TOKEN-1');
        $manager->add($session, 'TOKEN-2');
        $manager->add($session, 'TOKEN-1');

        expect($manager->all($session))->toBe(['TOKEN-1', 'TOKEN-2']);
        expect($session->get(ResumeTokenSession::CURRENT_TOKEN_KEY))->toBe('TOKEN-1');
    });

    test('removes token and resets current token to latest remaining value', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $manager->add($session, 'TOKEN-1');
        $manager->add($session, 'TOKEN-2');
        $manager->remove($session, 'TOKEN-2');

        expect($manager->all($session))->toBe(['TOKEN-1']);
        expect($session->get(ResumeTokenSession::CURRENT_TOKEN_KEY))->toBe('TOKEN-1');
    });

    test('falls back to legacy current token when array key is missing', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $session->put(ResumeTokenSession::CURRENT_TOKEN_KEY, 'LEGACY-TOKEN');

        expect($manager->all($session))->toBe(['LEGACY-TOKEN']);
    });

    test('normalizes mixed token values and merges legacy current token without duplicates', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $session->put(ResumeTokenSession::TOKENS_KEY, ['TOKEN-1', '', 123, 'TOKEN-1']);
        $session->put(ResumeTokenSession::CURRENT_TOKEN_KEY, 'TOKEN-2');

        expect($manager->all($session))->toBe(['TOKEN-1', 'TOKEN-2']);
    });

    test('removes last remaining token and clears session keys', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $manager->add($session, 'TOKEN-1');
        $manager->remove($session, 'TOKEN-1');

        expect($manager->all($session))->toBe([]);
        expect($session->has(ResumeTokenSession::TOKENS_KEY))->toBeFalse();
        expect($session->has(ResumeTokenSession::CURRENT_TOKEN_KEY))->toBeFalse();
    });

    test('removing a non-current token keeps the current token unchanged', function (): void {
        $session = makeArraySession();
        $manager = new ResumeTokenSession();

        $manager->add($session, 'TOKEN-1');
        $manager->add($session, 'TOKEN-2');
        $manager->remove($session, 'TOKEN-1');

        expect($manager->all($session))->toBe(['TOKEN-2']);
        expect($session->get(ResumeTokenSession::CURRENT_TOKEN_KEY))->toBe('TOKEN-2');
    });

    test('normalize returns an empty list for non-array values', function (): void {
        $manager = new ResumeTokenSession();

        expect($manager->normalize('not-an-array'))->toBe([]);
    });
});
