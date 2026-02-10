<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\ResumeCommandService;
use App\Application\Services\ResumeQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class ResumeController extends Controller
{
    public function __construct(
        private ResumeQueryService $queries,
        private ResumeCommandService $commands,
    ) {
    }

    public function show(int $id): JsonResponse
    {
        $resume = $this->queries->getById($id);

        if ($resume === null) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return response()->json($resume);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $resume = $this->commands->create($data['name'], $data['email']);

        return response()->json([
            'id' => $resume->id->value,
            'name' => $resume->name->value,
            'email' => $resume->email->value,
        ], 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        if ($request->isMethod('patch')) {
            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:200'],
                'email' => ['sometimes', 'email', 'max:255'],
            ]);

            $validator->after(function ($validator) use ($request) {
                $fields = array_intersect_key($request->all(), array_flip(['name', 'email']));

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
            $resume = $this->commands->patch(
                $id,
                $data['name'] ?? null,
                $data['email'] ?? null,
            );
        } else {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'email' => ['required', 'email', 'max:255'],
            ]);

            $resume = $this->commands->update($id, $data['name'], $data['email']);
        }

        if ($resume === null) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return response()->json([
            'id' => $resume->id->value,
            'name' => $resume->name->value,
            'email' => $resume->email->value,
        ]);
    }

    public function destroy(int $id): Response|JsonResponse
    {
        $resume = $this->commands->delete($id);

        if ($resume === null) {
            return response()->json(['message' => 'Resume not found.'], 404);
        }

        return response()->noContent();
    }
}
