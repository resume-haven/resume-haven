<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\UserCommandService;
use App\Application\Services\UserQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        $read = $this->queries->getById($user->id->value);

        return response()->json($read ?? [
            'id' => $user->id->value,
            'name' => $user->name->value,
            'email' => $user->email->value,
        ], 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        if ($request->isMethod('patch')) {
            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:200'],
                'email' => ['sometimes', 'email', 'max:255'],
                'password' => ['sometimes', 'string', 'min:8', 'max:255'],
            ]);

            $validator->after(function ($validator) use ($request) {
                $fields = array_intersect_key($request->all(), array_flip(['name', 'email', 'password']));

                if ($fields === []) {
                    $validator->errors()->add('fields', 'At least one field must be provided.');
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $data = $validator->validated();
            $password = $data['password'] ?? null;
            $passwordHash = $password !== null ? Hash::make($password) : null;

            $user = $this->commands->patch(
                $id,
                $data['name'] ?? null,
                $data['email'] ?? null,
                $passwordHash,
            );
        } else {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['nullable', 'string', 'min:8', 'max:255'],
            ]);

            $password = $data['password'] ?? null;
            $passwordHash = $password !== null ? Hash::make($password) : null;

            $user = $this->commands->update(
                $id,
                $data['name'],
                $data['email'],
                $passwordHash,
            );
        }

        if ($user === null) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $read = $this->queries->getById($user->id->value);

        return response()->json($read ?? [
            'id' => $user->id->value,
            'name' => $user->name->value,
            'email' => $user->email->value,
        ]);
    }

    public function destroy(int $id): Response|JsonResponse
    {
        $user = $this->commands->delete($id);

        if ($user === null) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->noContent();
    }
}
