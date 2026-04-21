<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('aborts with 403 when no authenticated user exists', function (): void {
    $request = Request::create('/admin', 'GET');
    $request->setUserResolver(static fn (): ?User => null);

    $middleware = new EnsureUserIsAdmin();

    expect(static fn (): Response => $middleware->handle(
        $request,
        static fn (Request $request): Response => new Response('ok', 200)
    ))->toThrow(HttpException::class);
});

it('aborts with 403 when authenticated user is not admin', function (): void {
    $request = Request::create('/admin', 'GET');

    $user = new User();
    $user->role = UserRole::User;

    $request->setUserResolver(static fn () => $user);

    $middleware = new EnsureUserIsAdmin();

    expect(static fn (): Response => $middleware->handle(
        $request,
        static fn (Request $request): Response => new Response('ok', 200)
    ))->toThrow(HttpException::class);
});

it('passes request to next middleware when authenticated user is admin', function (): void {
    $request = Request::create('/admin', 'GET');

    $admin = new User();
    $admin->role = UserRole::Admin;

    $request->setUserResolver(static fn () => $admin);

    $middleware = new EnsureUserIsAdmin();
    $nextWasCalled = false;

    $response = $middleware->handle(
        $request,
        static function (Request $request) use (&$nextWasCalled): Response {
            $nextWasCalled = true;

            return new Response('ok', 200);
        }
    );

    expect($nextWasCalled)->toBeTrue();
    expect($response->getStatusCode())->toBe(200);
    expect($response->getContent())->toBe('ok');
});
