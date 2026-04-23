<?php

declare(strict_types=1);

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function makeLoginRequest(array $input = [], string $ip = '127.0.0.1'): LoginRequest
{
    /** @var LoginRequest $request */
    $request = LoginRequest::create(
        '/login',
        'POST',
        $input,
        [],
        [],
        ['REMOTE_ADDR' => $ip],
    );

    $request->setContainer(app());

    return $request;
}

describe('LoginRequest', function (): void {
    test('authorize returns true and rules contain required fields', function (): void {
        $request = makeLoginRequest();

        expect($request->authorize())->toBeTrue();
        expect($request->rules())->toBe([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    });

    test('throttle key falls back to empty email when email is not a string', function (): void {
        $request = makeLoginRequest(['email' => ['invalid']], '10.0.0.7');

        expect($request->throttleKey())->toBe('|10.0.0.7');
    });

    test('ensureIsNotRateLimited passes when attempts are below threshold', function (): void {
        $request = makeLoginRequest(['email' => 'user@example.com'], '10.0.0.8');
        $key = $request->throttleKey();

        RateLimiter::clear($key);

        $request->ensureIsNotRateLimited();

        expect(RateLimiter::tooManyAttempts($key, 5))->toBeFalse();
    });

    test('ensureIsNotRateLimited throws validation exception and dispatches lockout event', function (): void {
        Event::fake([Lockout::class]);

        $request = makeLoginRequest(['email' => 'locked@example.com'], '10.0.0.9');
        $key = $request->throttleKey();

        RateLimiter::clear($key);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($key);
        }

        expect(function () use ($request): void {
            $request->ensureIsNotRateLimited();
        })
            ->toThrow(ValidationException::class);

        Event::assertDispatched(Lockout::class);

        RateLimiter::clear($key);
    });

    test('authenticate increments rate limiter and throws validation exception on failed credentials', function (): void {
        $request = makeLoginRequest([
            'email' => 'missing@example.com',
            'password' => 'invalid-password',
        ], '10.0.0.10');

        $key = $request->throttleKey();
        RateLimiter::clear($key);

        expect(function () use ($request): void {
            $request->authenticate();
        })
            ->toThrow(ValidationException::class);

        expect(RateLimiter::attempts($key))->toBe(1);

        RateLimiter::clear($key);
    });

    test('authenticate clears rate limiter after successful login', function (): void {
        $user = User::factory()->create([
            'email' => 'valid@example.com',
            'password' => 'Password123!',
        ]);

        $request = makeLoginRequest([
            'email' => $user->email,
            'password' => 'Password123!',
            'remember' => '1',
        ], '10.0.0.11');

        $key = $request->throttleKey();

        RateLimiter::clear($key);
        RateLimiter::hit($key);

        expect(RateLimiter::attempts($key))->toBeGreaterThan(0);

        $request->authenticate();

        expect(RateLimiter::attempts($key))->toBe(0);
    });
});
