<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\UserModel;
use App\Support\AuthAuditLogger;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class TokenController extends Controller
{
    /**
     * Create a new API token for the given user credentials.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = UserModel::query()->where('email', $credentials['email'])->first();

        if ($user === null || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        AuthAuditLogger::log('auth.token.created', $user, [
            'device_name' => $credentials['device_name'],
        ]);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    /**
     * Revoke all API tokens for the authenticated user.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if ($user === null) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->tokens()->delete();

        AuthAuditLogger::log('auth.token.revoked', $user);

        return response()->json(['message' => 'All tokens revoked.']);
    }
}
