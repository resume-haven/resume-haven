<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class EmailVerificationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (! $user instanceof MustVerifyEmail) {
            return response()->json(['message' => 'Email verification is not supported.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.'], 202);
    }
}
