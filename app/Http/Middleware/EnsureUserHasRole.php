<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    /**
     * @param string $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user === null || ! method_exists($user, 'roles')) {
            abort(403, 'Forbidden.');
        }

        if (! $user->roles()->where('name', $role)->exists()) {
            abort(403, 'Forbidden.');
        }

        return $next($request);
    }
}
