<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\UserCommandService;
use App\Application\Services\UserQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class UserController extends Controller
{
    public function __construct(
        private UserQueryService $queries,
        private UserCommandService $commands,
    ) {
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->queries->getById($id);

        if ($user === null) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = $this->commands->create(
            $data['name'],
            $data['email'],
            Hash::make($data['password']),
        );

        return response()->json([
            'id' => $user->id,
            'name' => $user->name->value,
            'email' => $user->email->value,
        ], 201);
    }
}
